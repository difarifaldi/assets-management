@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail Submission Checkout Asset -
                                    #{{ $submission->id }}
                            </div>
                        </div>
                        <!-- form start -->
                        <div class="card-body">
                            <div class="col-md-12">
                                <!-- Date and Time -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Submission Date and Time</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ date('d F Y H:i:s', strtotime($submission->created_at)) }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Loan Application Asset Date</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ date('d F Y', strtotime($submission->submissionFormsCheckoutDate->loan_application_asset_date)) }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Return Asset Date</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ date('d F Y', strtotime($submission->submissionFormsCheckoutDate->return_asset_date)) }}
                                    </div>
                                </div>
                                <!-- Description -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Description</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ $submission->description ?? '-' }}
                                    </div>
                                </div>
                                <!-- Attachment -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Attachment</label>
                                    <div class="col-sm-9 col-form-label">
                                        @if (!is_null($submission->attachment))
                                            <a href="{{ asset($submission->attachment) }}" target="_blank">
                                                <i class="fas fa-download mr-1"></i>
                                                Attachment Document
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <!-- Status -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Approval Status</label>
                                    <div class="col-sm-9 col-form-label">
                                        @if (!is_null($submission->approved_by) && !is_null($submission->approved_at))
                                            <span class="badge badge-success">Approved By
                                                {{ $submission->approvedBy->name }}</span>
                                        @elseif(!is_null($submission->rejected_by) && !is_null($submission->rejected_at))
                                            <span class="badge badge-danger">Rejected By
                                                {{ $submission->rejectedBy->name }}</span>
                                        @else
                                            <span class="badge badge-warning">Process</span>
                                        @endif
                                    </div>
                                </div>
                                @if (!is_null($submission->rejected_by) && !is_null($submission->rejected_at))
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Reason Rejection</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $submission->reason ?? '-' }}
                                        </div>
                                    </div>
                                @endif
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered datatable" id="asset">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Asset
                                                </th>
                                                <th width='10%'>
                                                    Barcode
                                                </th>
                                                <th>
                                                    Category
                                                </th>
                                                <th width='10%'>
                                                    Status Asset
                                                </th>
                                                <th width='15%'>
                                                    Status Checked
                                                </th>
                                                @role('staff')
                                                    @if (!is_null($submission->approved_by) && !is_null($submission->approved_at))
                                                        <th width='15%'>
                                                            Action
                                                        </th>
                                                    @endif
                                                @endrole
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">
                                            @foreach ($submission->submissionFormItemAsset as $item_asset)
                                                <tr>
                                                    <td>
                                                        {{ $item_asset->asset->name }}
                                                    </td>
                                                    <td>
                                                        {{ $item_asset->asset->barcode_code }}
                                                    </td>
                                                    <td>
                                                        {{ $item_asset->asset->category->name }}
                                                    </td>
                                                    <td>
                                                        @if ($item_asset->asset->status == 1)
                                                            <span class="badge badge-success">Good Condition</span>
                                                        @elseif($item_asset->asset->status == 2)
                                                            <span class="badge badge-warning">Minor Damage</span>
                                                        @elseif($item_asset->asset->status == 3)
                                                            <span class="badge badge-danger">Major Damage</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (
                                                            !is_null(
                                                                $submission->historyCheckOut->where('assets_id', $item_asset->asset->id)->whereNull('check_in_by')->first()))
                                                            <span class="badge badge-success">Check Out</span>
                                                        @elseif (
                                                            !is_null(
                                                                $submission->historyCheckOut->where('assets_id', $item_asset->asset->id)->where('check_in_by')->first()))
                                                            <span class="badge badge-info">Check In</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    @role('staff')
                                                        @if (
                                                            !is_null($submission->approved_by) &&
                                                                !is_null($submission->approved_at) &&
                                                                is_null($submission->historyCheckOut->where('assets_id', $item_asset->asset->id)->first()))
                                                            <td>
                                                                @php
                                                                    $currentDate = \Carbon\Carbon::now()->toDateString();
                                                                    $loanDate = \Carbon\Carbon::parse(
                                                                        $submission->submissionFormsCheckoutDate
                                                                            ->loan_application_asset_date,
                                                                    )->toDateString();
                                                                    $returnDate = \Carbon\Carbon::parse(
                                                                        $submission->submissionFormsCheckoutDate
                                                                            ->return_asset_date,
                                                                    )->toDateString();
                                                                @endphp
                                                                @if ($currentDate < $loanDate)
                                                                    <span class="badge badge-info">Not Available Yet</span>
                                                                @elseif ($currentDate > $returnDate)
                                                                    <span class="badge badge-danger">Expired</span>
                                                                @else
                                                                    @if ($item_asset->asset->status != 4 && $item_asset->asset->status != 5)
                                                                        @if (is_null($item_asset->asset->assign_to) &&
                                                                                is_null($item_asset->asset->assign_at) &&
                                                                                (is_null($item_asset->asset->check_out_by) && is_null($item_asset->asset->check_out_at)))
                                                                            <button class="btn btn-sm btn-primary"
                                                                                data-toggle="modal"
                                                                                data-target="#check_out{{ $item_asset->asset->id }}">
                                                                                Check Out
                                                                            </button>
                                                                        @else
                                                                            <span class="badge badge-danger">Unavailable</span>
                                                                        @endif
                                                                    @elseif ($item_asset->asset->status == 4)
                                                                        <span class="badge badge-danger">On Maintenance</span>
                                                                    @elseif ($item_asset->asset->status == 5)
                                                                        <span class="badge badge-danger">License Expired</span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @elseif (isset($item_asset->asset->check_out_by) && isset($item_asset->asset->check_out_at))
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" data-toggle="modal"
                                                                    data-target="#check_in{{ $item_asset->asset->id }}">
                                                                    Check In
                                                                </button>
                                                            </td>
                                                        @else
                                                            <td> -</td>
                                                        @endif
                                                    @endrole
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between py-3">
                                <a href="{{ route('submission.index') }}" class="btn btn-danger">Back</a>
                                @role('admin')
                                    <div class="d-flex">
                                        @if (is_null($submission->approved_by) &&
                                                is_null($submission->approved_at) &&
                                                is_null($submission->rejected_by) &&
                                                is_null($submission->rejected_at))
                                            <button class="btn btn-sm btn-danger ml-2"
                                                onclick="rejectedRecord({{ $submission->id }})"
                                                title="Rejected">Rejected</button>
                                            <button class="btn btn-sm btn-success ml-2"
                                                onclick="approvedRecord({{ $submission->id }})"
                                                title="Approve">Approve</button>
                                        @endif
                                    </div>
                                @endrole
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach ($submission->submissionFormItemAsset as $item_asset)
        {{-- Check Out --}}
        <div class="modal fade" id="check_out{{ $item_asset->asset->id }}">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="POST" id="assign-form-{{ $item_asset->asset->id }}"
                        action="{{ route('submission.checkOut', ['id' => $submission->id]) }}" class="forms-control"
                        enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="assets_id" value="{{ $item_asset->asset->id }}">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLongTitle">Add Checkout and Proof Checkout</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="attachment">Proof Checkout <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="attachment[]" id="documentInput"
                                    accept="image/*;capture=camera" multiple="true" required>
                                <p class="text-danger py-1">* .png .jpg .jpeg</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-primary mx-2">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- Check In --}}
        <div class="modal fade" id="check_in{{ $item_asset->asset->id }}">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="POST" id="checkIn-form-{{ $item_asset->asset->id }}"
                        action="{{ route('submission.checkIn', ['id' => $submission->id]) }}" class="forms-control"
                        enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="assets_id" value="{{ $item_asset->asset->id }}">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLongTitle">Add CheckIn and Proof CheckIn</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="attachment">Proof CheckIn <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="attachment[]" id="documentInput"
                                    accept="image/*;capture=camera" multiple="true" required>
                                <p class="text-danger py-1">* .png .jpg .jpeg</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-primary mx-2">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    @push('javascript-bottom')
        @include('javascript.submission.script')
    @endpush
@endsection
