<?php

namespace App\Http\Controllers;

use App\Models\submissionFormsCheckoutDate;
use App\Http\Requests\StoresubmissionFormsCheckoutDateRequest;
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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::whereNull('deleted_at')->whereNull('check_out_by')->where('type', 1)->get();
        return view('submission.checkouts.create', compact('assets'));
    }

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

            $submission = SubmissionForm::lockforUpdate()->create([
                'description' => $request->description,
                'type' => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,

            ]);
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

                    foreach ($request->physical_asset as $asset)

                        array_push($assets_request, [
                            'submission_form_id' => $submission->id,
                            'assets_id' => $asset['asset'],

                        ]);
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
                                ->with(['success' => 'Successfully Checkout']);
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
                            ->with(['failed' => 'Failed Checkout '])
                            ->withInput();
                    }
                } else {
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Checkout '])
                        ->withInput();
                }
            }
        } catch (Exception $e) {
            dd([$e->getLine(), $e->getMessage(), $e->getFile()]);
            // return redirect()
            //     ->back()
            //     ->with(['failed' => $e->getMessage()])
            //     ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(submissionFormsCheckoutDate $submissionFormsCheckoutDate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(submissionFormsCheckoutDate $submissionFormsCheckoutDate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatesubmissionFormsCheckoutDateRequest $request, submissionFormsCheckoutDate $submissionFormsCheckoutDate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(submissionFormsCheckoutDate $submissionFormsCheckoutDate)
    {
        //
    }
}
