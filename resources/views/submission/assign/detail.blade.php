@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail Submission Assign Asset -
                                    #{{ $submission->id }}
                            </div>
                        </div>
                        <!-- form start -->
                        <div class="card-body">
                            <div class="col-md-12">
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
                                            <span class="badge badge-warning text-white">Process</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="table-responsive mt-5">
                                    <table class="table table-bordered datatable" id="asset">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Asset
                                                </th>
                                                <th>
                                                    Barcode
                                                </th>
                                                <th>
                                                    Category
                                                </th>
                                                <th>
                                                    Status Asset
                                                </th>
                                                <th>
                                                    Status Check Out
                                                </th>
                                                @role('admin')
                                                    <th width="10%">
                                                        Action
                                                    </th>
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
                                                        -
                                                    </td>
                                                    @role('admin')
                                                        <td>
                                                            <button class="btn btn-sm btn-primary">Assigning</button>
                                                        </td>
                                                    @endrole
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex pt-3 gap-2">
                                <a href="{{ route('submission.index') }}" class="btn btn-danger mr-2">Back</a>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
