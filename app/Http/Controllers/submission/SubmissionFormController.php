<?php

namespace App\Http\Controllers\Submission;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
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
                    return '<div class="badge badge-warning text-white">Process</div>';
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
                        $btn_action .= '<a href="' . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                        $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                    }
                } else {
                    if (!isset($data->approved_at) && !isset($data->rejected_at)) {
                        $btn_action .= '<button class="btn btn-sm btn-success ml-2" onclick="approvedRecord(' . $data->id . ')" title="Approve">Approve</button>';
                        $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="rejectedRecord(' . $data->id . ')"title="Rejected">Rejected</button>';
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
                    $assets = Asset::whereNull('deleted_by')->whereNull('deleted_at')->whereNull('assign_to')->whereNull('assign_at')->whereNull('check_out_by')->whereNull('check_out_at')->get();
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

                        $file_name = $submission->id . '_' . uniqid() . '_' . $request->file('attachment')->getClientOriginalName();
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
