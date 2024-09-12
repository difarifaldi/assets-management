<?php

namespace App\Http\Controllers;

use App\Models\SubmissionForm;


use App\Models\asset\Asset;
use App\Models\master\User;
use App\Models\SubmisssionFormItemAsset;
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
        $datatable_route = route('submission.submission.dataTable');


        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('submission.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All submission
         */
        if (User::find(Auth::user()->id)->hasRole('staff')) {
            $submission = SubmissionForm::whereNull('deleted_by')->whereNull('deleted_at')->where('created_by', Auth::user()->id)->get();
        } else {
            $submission = SubmissionForm::whereNull('deleted_by')->whereNull('deleted_at')->get();
        }

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($submission)
            ->addIndexColumn()
            ->addColumn('type', function ($data) {
                return $data->type == 1 ? 'Checkout' : ($data->type == 2 ? 'Assign ' : '-');
            })
            ->addColumn('status', function ($data) {
                if ($data->aproved_by != null && $data->aproved_at != null) {
                    return '<div class="badge badge-success">Approved</div>';
                } elseif ($data->rejected_by != null && $data->rejected_at != null) {
                    return '<div class="badge badge-danger">Rejected</div>';
                } else {
                    return  '<div class="badge badge-warning text-white">Process</div>';
                }
            })
            ->addColumn('created_by', function ($data) {
                return $data->createdBy->name;
            })

            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole('staff')) {
                    if (!isset($data->aproved_at) &&  !isset($data->rejected_at)) {
                        $btn_action .= '<a href="'  . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    }
                } else {
                    if (!isset($data->aproved_at) &&  !isset($data->rejected_at)) {
                        $btn_action .= '<button class="btn btn-sm btn-success ml-2" onclick="approvedRecord(' . $data->id . ')" title="Approve">Approve</button>';
                        $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="rejectedRecord(' . $data->id . ')"title="Rejected">Rejected</button>';
                    }
                    if (!is_null($data->attachment)) {
                        // Assuming $data->attachment is a file URL or path
                        $btn_action .= '<a href="' . asset($data->attachment) . '" target="_blank" class="btn btn-sm btn-info ml-2" title="File">File</a>';
                    }
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['type', 'description', 'status', 'created_by', 'action'])
            ->rawColumns(['status', 'action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $type, Asset $asset)
    {
        try {

            if (!is_null($asset)) {
                $users = User::whereNull('deleted_at')->role('staff')->get();

                return view('submission.' . $type . '.create', compact('asset', 'users'));
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

            DB::beginTransaction();

            /**
             * Create SubmissionForm Record
             */
            $submission = SubmissionForm::lockforUpdate()->create([
                'type' => $request->type,
                'description' => $request->description,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create SubmissionForm Record
             */

            if ($submission) {
                $tesAttachment = $request->hasFile('attachment');
                if ($tesAttachment) {

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
                            'assets_id' => $asset
                        ]);
                    }

                    $submission_form_item_asssets = SubmisssionFormItemAsset::insert($assets_request);

                    if ($submision_attachment && $submission_form_item_asssets) {
                        if (Storage::exists($path . '/' . $file_name)) {
                            DB::commit();
                            return redirect()
                                ->route('submission.index')
                                ->with(['success' => 'Successfully Checkout']);
                        } else {
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
                            ->with(['failed' => 'Failed Checkout'])
                            ->withInput();
                    }
                } else {
                    DB::commit();
                    return redirect()
                        ->route('submission.index')
                        ->with(['success' => 'Successfully Checkout']);
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Checkout'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
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
                    'aproved_by' => Auth::user()->id,
                    'aproved_at' => now(),
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
}
