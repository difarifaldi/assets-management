<?php

namespace App\Http\Controllers\submission;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
use App\Models\history\HistoryAssign;
use App\Models\history\HistoryCheckInOut;
use App\Models\master\User;
use App\Models\submission\SubmissionForm;
use App\Models\submission\SubmissionFormItemAsset;
use App\Models\submission\SubmissionFormsCheckoutDate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SubmissionFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datatable_route = route('submission.dataTable');

        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('submission.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All submission
         */
        if (User::find(Auth::user()->id)->hasRole('staff')) {
            $submission = SubmissionForm::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('created_by', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $submission = SubmissionForm::whereNull('deleted_by')->whereNull('deleted_at')->orderBy('created_at', 'desc')->get();
        }

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($submission)
            ->addIndexColumn()
            ->addColumn('created_at', function ($data) {
                return date('d F Y H:i:s', strtotime($data->created_at));
            })
            ->addColumn('type', function ($data) {
                return $data->type == 1 ? 'Assign' : ($data->type == 2 ? 'Checkout ' : '-');
            })
            ->addColumn('status', function ($data) {
                if ($data->approved_by != null && $data->approved_at != null) {
                    return '<div class="badge badge-success">Approved</div>';
                } elseif ($data->rejected_by != null && $data->rejected_at != null) {
                    return '<div class="badge badge-danger">Rejected</div>';
                } else {
                    return '<div class="badge badge-warning">Process</div>';
                }
            })
            ->addColumn('created_by', function ($data) {
                return $data->createdBy->name;
            })

            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('submission.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole('staff')) {
                    if (!isset($data->approved_at) && !isset($data->rejected_at)) {
                        if ($data->type == 1) {
                            $btn_action .= '<a href="' . route('submission.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                        } else {
                            $btn_action .= '<a href="' . route('submission.edit', ['type' => 'checkout', 'id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                        }

                        $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                    }
                } else {
                    if (!isset($data->approved_at) && !isset($data->rejected_at)) {
                        $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="rejectedRecord(' . $data->id . ')"title="Rejected">Rejected</button>';
                        $btn_action .= '<button class="btn btn-sm btn-success ml-2" onclick="approvedRecord(' . $data->id . ')" title="Approve">Approve</button>';
                    }
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['type', 'description', 'status', 'created_at', 'created_by', 'action'])
            ->rawColumns(['status', 'action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, string $type)
    {
        try {
            if (in_array($type, ['assign', 'checkouts'])) {
                if (isset($request->asset)) {
                    $asset = Asset::find($request->asset);

                    if (!is_null($asset)) {
                        return view('submission.' . $type . '.asset.create', compact('asset'));
                    } else {
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Invalid Request!']);
                    }
                } else {
                    if ($type == 'assign') {
                        $assets = Asset::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereNull('assign_to')
                            ->whereNull('assign_at')
                            ->whereNull('check_out_by')
                            ->whereNull('check_out_at')
                            ->whereNotIn('status', [3, 4, 5])
                            ->get();
                    } else {
                        $assets = Asset::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereNull('assign_to')
                            ->whereNull('assign_at')
                            ->whereNull('check_out_by')
                            ->whereNull('check_out_at')
                            ->whereNotIn('status', [3, 4, 5])
                            ->where('type', 1)
                            ->get();
                    }

                    return view('submission.' . $type . '.form.create', compact('assets'));
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $type, Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'description' => 'required',
            ]);

            /**
             * Checking Type Requested
             */
            if (in_array($type, [1, 2])) {
                DB::beginTransaction();

                /**
                 * Create SubmissionForm Record
                 */
                $submission = SubmissionForm::lockforUpdate()->create([
                    'type' => $type,
                    'description' => $request->description,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create SubmissionForm Record
                 */
                if ($submission) {
                    /**
                     * Has Attachment
                     */
                    if ($request->hasFile('attachment')) {
                        $path = 'public/submission/' . $submission->id;
                        $path_store = 'storage/submission/' . $submission->id;

                        if (!Storage::exists($path)) {
                            Storage::makeDirectory($path);
                        }

                        $file_name = $submission->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $request->file('attachment')->getClientOriginalExtension();
                        $request->file('attachment')->storePubliclyAs($path, $file_name);
                        $attachment = $path_store . '/' . $file_name;

                        $submision_attachment = $submission->update([
                            'attachment' => $attachment,
                        ]);

                        $assets_request = [];
                        foreach ($request->assets as $asset) {
                            array_push($assets_request, [
                                'submission_form_id' => $submission->id,
                                'assets_id' => $asset['id'],
                            ]);
                        }

                        $submission_form_item_assets = SubmissionFormItemAsset::insert($assets_request);

                        if ($submision_attachment && $submission_form_item_assets) {
                            if (Storage::exists($path . '/' . $file_name)) {
                                /**
                                 * Form as Checkout
                                 */
                                if ($type == 2) {
                                    $date_request = [];
                                    array_push($date_request, [
                                        'submission_form_id' => $submission->id,
                                        'loan_application_asset_date' => $request->loan_application_asset_date,
                                        'return_asset_date' => $request->return_asset_date,
                                    ]);

                                    $submissionFormCheckoutDate = SubmissionFormsCheckoutDate::insert($date_request);

                                    if ($submissionFormCheckoutDate) {
                                        DB::commit();
                                        return redirect()
                                            ->route('submission.index')
                                            ->with(['success' => 'Successfully Added Submission Checkout']);
                                    } else {
                                        /**
                                         * Failed Store Record
                                         */
                                        DB::rollBack();
                                        return redirect()
                                            ->back()
                                            ->with(['failed' => 'Failed Added Submission Checkout'])
                                            ->withInput();
                                    }
                                } else {
                                    DB::commit();
                                    return redirect()
                                        ->route('submission.index')
                                        ->with(['success' => 'Successfully Added Submission Assign']);
                                }
                            } else {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Failed Upload Attachment'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Added Submission'])
                                ->withInput();
                        }
                    } else {
                        $assets_request = [];
                        foreach ($request->assets as $asset) {
                            array_push($assets_request, [
                                'submission_form_id' => $submission->id,
                                'assets_id' => $asset['id'],
                            ]);
                        }

                        $submission_form_item_assets = SubmissionFormItemAsset::insert($assets_request);

                        if ($submission_form_item_assets) {
                            /**
                             * Form as Checkout
                             */
                            if ($type == 2) {
                                $date_request = [];
                                array_push($date_request, [
                                    'submission_form_id' => $submission->id,
                                    'loan_application_asset_date' => $request->loan_application_asset_date,
                                    'return_asset_date' => $request->loan_application_asset_date,
                                ]);

                                $submissionFormCheckoutDate = SubmissionFormsCheckoutDate::insert($date_request);

                                if ($submissionFormCheckoutDate) {
                                    DB::commit();
                                    return redirect()
                                        ->route('submission.index')
                                        ->with(['success' => 'Successfully Added Submission Checkout']);
                                } else {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Failed Added Submission Checkout'])
                                        ->withInput();
                                }
                            } else {
                                DB::commit();
                                return redirect()
                                    ->route('submission.index')
                                    ->with(['success' => 'Successfully Added Submission Assign']);
                            }
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Added Submission'])
                                ->withInput();
                        }
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Added Submission'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'description' => 'required',
            ]);

            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                DB::beginTransaction();

                // Update deskripsi
                $submission_update = SubmissionForm::where('id', $id)->update([
                    'description' => $request->description,
                ]);

                if ($submission_update) {
                    if ($request->hasFile('attachment')) {
                        $path = 'public/submission/' . $submission->id;
                        $path_store = 'storage/submission/' . $submission->id;

                        if (!Storage::exists($path)) {
                            Storage::makeDirectory($path);
                        }

                        $file_name = $submission->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $request->file('attachment')->getClientOriginalExtension();

                        // Hapus file yang sudah ada jika ada
                        if (Storage::exists($path . '/' . $file_name)) {
                            Storage::delete($path . '/' . $file_name);
                        }

                        // Simpan file yang diunggah
                        $request->file('attachment')->storePubliclyAs($path, $file_name);
                        $attachment = $path_store . '/' . $file_name;

                        // Update lampiran
                        $submission_attachment = $submission->update([
                            'attachment' => $attachment,
                        ]);
                    }

                    // Hapus semua aset lama
                    SubmissionFormItemAsset::where('submission_form_id', $submission->id)->delete();

                    // Menyimpan aset baru
                    if (is_array($request->assets) && !empty($request->assets)) {
                        foreach ($request->assets as $asset) {
                            // Memastikan bahwa $asset adalah array dan memiliki kunci 'id'
                            if (is_array($asset) && isset($asset['id'])) {
                                SubmissionFormItemAsset::create([
                                    'submission_form_id' => $submission->id,
                                    'assets_id' => $asset['id'],
                                ]);
                            } else {
                                return redirect()->back()->with(['failed' => 'Invalid asset format']);
                            }
                        }
                    }

                    // Menangani pengisian form checkout
                    if ($submission->type == 2) {
                        $date_request = [
                            'submission_form_id' => $submission->id,
                            'loan_application_asset_date' => $request->loan_application_asset_date,
                            'return_asset_date' => $request->return_asset_date,
                        ];

                        // Hapus tanggal checkout yang lama
                        SubmissionFormsCheckoutDate::where('submission_form_id', $submission->id)->delete();
                        $submissionFormCheckoutDate = SubmissionFormsCheckoutDate::create($date_request);

                        if ($submissionFormCheckoutDate) {
                            DB::commit();
                            return redirect()
                                ->route('submission.index')
                                ->with(['success' => 'Successfully Updated Submission Checkout']);
                        } else {
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Updated Submission Checkout'])
                                ->withInput();
                        }
                    } else {
                        DB::commit();
                        return redirect()
                            ->route('submission.index')
                            ->with(['success' => 'Successfully Updated Submission Assign']);
                    }
                } else {
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Update Submission'])
                        ->withInput();
                }
            } else {
                return redirect()->back()->with(['failed' => 'Invalid Request']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
        }
    }


    public function edit(Request $request, string $id)
    {
        try {
            $submission = SubmissionForm::find($id);
            $submissionItems = SubmissionFormItemAsset::where('submission_form_id', $submission->id)->get();
            if (!is_null($submission)) {
                $submissionAssetIds = $submissionItems->pluck('assets_id')->toArray();
                if ($submission->type == 1) {
                    $type = 'assign';
                    $assets = Asset::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereNull('assign_to')
                        ->whereNull('assign_at')
                        ->whereNull('check_out_by')
                        ->whereNull('check_out_at')
                        ->whereNotIn('status', [3, 4, 5])
                        ->whereNotIn('id', $submissionAssetIds)
                        ->get();
                } else {
                    $type = 'checkouts';
                    $assets = Asset::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereNull('assign_to')
                        ->whereNull('assign_at')
                        ->whereNull('check_out_by')
                        ->whereNull('check_out_at')
                        ->whereNotIn('status', [3, 4, 5])
                        ->where('type', 1)
                        ->whereNotIn('id', $submissionAssetIds)
                        ->get();
                }
                return view('submission.' . $type . '.form.edit', compact('assets', 'submission'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
        }
    }
    public function show(request $request, string $id)
    {
        try {
            /**
             * Get Submission Record from id
             */
            $submission = SubmissionForm::find($id);

            /**
             * Validation Submission id
             */
            if (!is_null($submission)) {

                if (User::find(Auth::user()->id)->hasRole('staff')) {
                    if ($submission->created_by != Auth::user()->id) {
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Invalid Request!']);
                    }
                }

                if ($submission->type == 1) {
                    return view('submission.assign.detail', compact('submission'));
                } else {
                    return view('submission.checkouts.detail', compact('submission'));
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    public function approve(Request $request)
    {
        try {
            $submission = SubmissionForm::find($request->id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update SubmissionForm Record
                 */
                $approved_submission = $submission->update([
                    'approved_by' => Auth::user()->id,
                    'approved_at' => now(),
                ]);

                /**
                 * Validation Update SubmissionForm Record
                 */
                if ($approved_submission) {
                    DB::commit();
                    $submission_result = SubmissionForm::find($request->id);
                    session()->flash('success', 'Submission Successfully Approved');
                    return response()->json(['data', $submission_result], 200);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Submission Failed Approved');
                    return response()->json(['message', 'Failed'], 400);
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
            return response()->json(['message', 'Failed!'], 400);
        }
    }

    public function reject(Request $request)
    {
        try {
            $submission = SubmissionForm::find($request->id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update SubmissionForm Record
                 */
                $rejected_submission = $submission->update([
                    'rejected_by' => Auth::user()->id,
                    'rejected_at' => now(),
                    'reason' => $request->reason,
                ]);

                /**
                 * Validation Update SubmissionForm Record
                 */
                if ($rejected_submission) {
                    DB::commit();
                    $submission_result = SubmissionForm::find($request->id);
                    session()->flash('success', 'Submission Successfully Rejected');
                    return response()->json(['data', $submission_result], 200);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Submission Failed Rejected');
                    return response()->json(['message', 'Failed'], 400);
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
            return response()->json(['message', 'Failed!'], 400);
        }
    }

    public function assignTo(Request $request, string $id)
    {
        try {
            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Assign Status Asset Record
                 */
                $add_assign = Asset::where('id', $request->assets_id)->update([
                    'assign_to' => $submission->created_by,
                    'assign_at' => now(),
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($add_assign) {
                    $path = 'public/asset/physical/proof_assign';
                    $path_store = 'storage/asset/physical/proof_assign';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_assign_attachment = [];

                    foreach ($request->file('attachment') as $file) {
                        // File Upload Configuration
                        $file_name = $request->assets_id . '-proof-assign-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_assign_attachment['proof_assign'][] = $path_store . '/' . $file_name;
                        } else {
                            // Failed and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Upload Attachment'])
                                ->withInput();
                        }
                    }

                    if (empty($proof_assign_attachment)) {
                        // Update Record for Attachment
                        $proof_assign_attachment = null;
                    } else {
                        // Update Record for Attachment
                        $proof_assign_attachment = json_encode($proof_assign_attachment);
                    }

                    $history_assign = HistoryAssign::create([
                        'assets_id' => $request->assets_id,
                        'submission_form_id' => $id,
                        'assign_to' => $submission->created_by,
                        'assign_at' => now(),
                        'latest' => true,
                        'attachment' => $proof_assign_attachment,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Add history Record
                     */
                    if ($history_assign) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Assign Successfully Add');
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Failed Add Record Assign');
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Failed Add Assign');
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function checkOut(Request $request, string $id)
    {
        try {
            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Assign Status Asset Record
                 */
                $add_check_out = Asset::where('id', $request->assets_id)->update([
                    'check_out_by' => $submission->created_by,
                    'check_out_at' => now(),
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($add_check_out) {
                    $path = 'public/asset/physical/proof_checkout';
                    $path_store = 'storage/asset/physical/proof_checkout';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_check_out_attachment = [];

                    foreach ($request->file('attachment') as $file) {
                        // File Upload Configuration
                        $file_name = $request->assets_id . '-proof-check-out-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_check_out_attachment['proof_checkout'][] = $path_store . '/' . $file_name;
                        } else {
                            // Failed and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Upload Attachment'])
                                ->withInput();
                        }
                    }

                    if (empty($proof_check_out_attachment)) {
                        // Update Record for Attachment
                        $proof_check_out_attachment = null;
                    } else {
                        // Update Record for Attachment
                        $proof_check_out_attachment = json_encode($proof_check_out_attachment);
                    }

                    $history_check_out = HistoryCheckInOut::create([
                        'assets_id' => $request->assets_id,
                        'submission_form_id' => $id,
                        'check_out_by' => $submission->created_by,
                        'check_out_at' => now(),
                        'latest' => true,
                        'attachment' => $proof_check_out_attachment,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Add history Record
                     */
                    if ($history_check_out) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Check Out Successfully Add');
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Failed Add Record Check Out');
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Failed Add Check Out');
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function checkIn(Request $request, string $id)
    {

        try {
            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                $last_check_out = HistoryCheckInOut::where('assets_id', $request->assets_id)->whereNull('deleted_by')->whereNull('check_in_by')->whereNull('check_in_at')->whereNull('deleted_by')->whereNotNull('latest')->first();
                /**
                 * Begin Transaction
                 */

                DB::beginTransaction();

                $remove_check_out = Asset::where('id', $request->assets_id)->update([
                    'check_out_by' => null,
                    'check_out_at' => null,
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($remove_check_out) {
                    $path = 'public/asset/physical/proof_check_in';
                    $path_store = 'storage/asset/physical/proof_check_in';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_check_in_attachment['proof_checkout'] = json_decode($last_check_out->attachment)->proof_checkout;



                    foreach ($request->file('attachment') as $file) {
                        // File Upload Configuration
                        $file_name = $request->assets_id . '-proof-check-in-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_check_in_attachment['proof_check_in'][] = $path_store . '/' . $file_name;
                        } else {
                            // Failed and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Upload Attachment'])
                                ->withInput();
                        }
                    }

                    $proof_check_in_attachment = json_encode($proof_check_in_attachment);

                    $history_check_in = HistoryCheckInOut::where('assets_id', $request->assets_id)
                        ->whereNull('deleted_by')
                        ->whereNull('check_in_by')
                        ->whereNull('check_in_at')
                        ->whereNull('deleted_by')
                        ->whereNotNull('latest')
                        ->update([
                            'check_in_by' => Auth::user()->id,
                            'check_in_at' => now(),
                            'latest' => null,
                            'attachment' => $proof_check_in_attachment,
                            'updated_by' => Auth::user()->id,
                        ]);

                    /**
                     * Validation Add history Record
                     */
                    if ($history_check_in) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Check In Successfully Add');
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Failed Add Check In');
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Failed Add Check In');
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()->back()->with('failed', 'Invalid Request!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $submission_destroy = SubmissionForm::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update brand Record
             */
            if ($submission_destroy) {
                DB::commit();
                session()->flash('success', 'brand Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete brand');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
