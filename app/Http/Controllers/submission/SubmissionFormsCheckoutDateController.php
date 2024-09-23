<?php

namespace App\Http\Controllers\Submission;

use App\Http\Controllers\Controller;
use App\Models\submissionFormsCheckoutDate;
use App\Http\Requests\UpdatesubmissionFormsCheckoutDateRequest;
use App\Models\asset\Asset;
use App\Models\SubmissionForm;
use App\Models\SubmisssionFormItemAsset;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionFormsCheckoutDateController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'description' => 'required',
                'loan_application_asset_date' => 'required',
                'return_asset_date' => 'required',
            ]);

            if ($request->return_asset_date < $request->loan_application_asset_date) {
                return redirect()
                    ->back()
                    ->with(['failed' => 'The Return Date Must Be After The Loan Application Date'])
                    ->withInput();
            }

            DB::beginTransaction();

            $submission = SubmissionForm::lockforUpdate()->create([
                'description' => $request->description,
                'type' => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            if ($submission) {
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

                    $submission_form_item_asssets = SubmisssionFormItemAsset::insert($assets_request);

                    $date_request = [];
                    array_push($date_request, [
                        'submission_form_id' => $submission->id,
                        'loan_application_asset_date' => $request->loan_application_asset_date,
                        'return_asset_date' => $request->loan_application_asset_date,
                    ]);

                    $submissionFormCheckoutDate = submissionFormsCheckoutDate::insert($date_request);

                    if ($submission_form_item_asssets && $submision_attachment && $submissionFormCheckoutDate) {
                        if (Storage::exists($path . '/' . $file_name)) {
                            DB::commit();
                            return redirect()
                                ->route('submission.index')
                                ->with(['success' => 'Successfully Added Submission Check Out']);
                        } else {
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Upload Attachment'])
                                ->withInput();
                        }
                    } else {
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Check Out'])
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

                    $submission_form_item_asssets = SubmisssionFormItemAsset::insert($assets_request);

                    $date_request = [];
                    array_push($date_request, [
                        'submission_form_id' => $submission->id,
                        'loan_application_asset_date' => $request->loan_application_asset_date,
                        'return_asset_date' => $request->loan_application_asset_date,
                    ]);

                    $submissionFormCheckoutDate = submissionFormsCheckoutDate::insert($date_request);

                    if ($submission_form_item_asssets && $submissionFormCheckoutDate) {
                        DB::commit();
                        return redirect()
                            ->route('submission.index')
                            ->with(['success' => 'Successfully Added Submission Check Out']);
                    } else {
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Check Out'])
                            ->withInput();
                    }
                }
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }
}
