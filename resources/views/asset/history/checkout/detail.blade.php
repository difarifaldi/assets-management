@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail History Check Out Asset
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Loan Application Asset Date</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $historyCheckInOut->submission->submissionFormsCheckoutDate->loan_application_asset_date }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Return Asset Date</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $historyCheckInOut->submission->submissionFormsCheckoutDate->return_asset_date }}
                                </div>
                            </div>

                            {{-- Check Out --}}
                            <hr>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Check Out At</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ date('d F Y H:i:s', strtotime($historyCheckInOut->check_out_at)) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Check Out By</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $historyCheckInOut->checkOut->name }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Attachment</label>
                                @if (!is_null($historyCheckInOut->attachment))
                                    <div class="col-md-12 pt-3">
                                        <div class="row justify-content-start mt-3">

                                            @foreach (json_decode($historyCheckInOut->attachment)->proof_checkout as $index => $attachment)
                                                <div class="col-md-3" id="attachment_{{ $index }}">
                                                    <div class="card shadow">
                                                        <input type="hidden" id="file_name_{{ $index }}"
                                                            value="{{ $attachment }}">
                                                        <div style="width:100%;overflow:hidden">
                                                            <a href="{{ asset($attachment) }}" class="text-black">
                                                                <img src="{{ asset($attachment) }}"
                                                                    onerror="this.onerror=null;this.src='{{ asset('img/image-not-found.jpg') }}'"
                                                                    width="100%"
                                                                    style="height:350px;object-fit: cover;" />
                                                            </a>
                                                        </div>
                                                        <div class="card-body text-right">
                                                            <div class="dropdown text-black">
                                                                <a class="text-black-50" id="dropdownMenuButton"
                                                                    title="Detail" data-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </a>
                                                                <div class="dropdown-menu"
                                                                    aria-labelledby="dropdownMenuLink">
                                                                    <a class="dropdown-item"
                                                                        href="{{ asset($attachment) }}"
                                                                        download>Download</a>
                                                                    @role('admin')
                                                                        <a class="dropdown-item"
                                                                            onclick="destroyFile({{ $index }}, 'physical')">Remove</a>
                                                                    @endrole
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <span class="mr-3">No Attachment</span>
                                            @role('admin')
                                                <button class="input-group-append btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#addAttachment">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endrole
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Check In --}}
                            @if (!is_null($historyCheckInOut->check_in_at) && !is_null($historyCheckInOut->check_in_by))
                                <hr>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Check In At</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ !is_null($historyCheckInOut->check_in_at) ? date('d F Y H:i:s', strtotime($historyCheckInOut->check_in_at)) : '-' }}
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Check In By</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ $historyCheckInOut->checkIn->name }}
                                    </div>
                                </div>
                                <!-- Attachment -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Attachment</label>
                                    @if (!is_null($historyCheckInOut->attachment))
                                        <div class="col-md-12 pt-3">
                                            <div class="row justify-content-start mt-3">
                                                @foreach (json_decode($historyCheckInOut->attachment)->proof_check_in as $index => $attachment)
                                                    <div class="col-md-3" id="attachment_{{ $index }}">
                                                        <div class="card shadow">
                                                            <input type="hidden" id="file_name_{{ $index }}"
                                                                value="{{ $attachment }}">
                                                            <div style="width:100%;overflow:hidden">
                                                                <a href="{{ asset($attachment) }}" class="text-black">
                                                                    <img src="{{ asset($attachment) }}"
                                                                        onerror="this.onerror=null;this.src='{{ asset('img/image-not-found.jpg') }}'"
                                                                        width="100%"
                                                                        style="height:350px;object-fit: cover;" />
                                                                </a>
                                                            </div>
                                                            <div class="card-body text-right">
                                                                <div class="dropdown text-black">
                                                                    <a class="text-black-50" id="dropdownMenuButton"
                                                                        title="Detail" data-toggle="dropdown">
                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                    </a>
                                                                    <div class="dropdown-menu"
                                                                        aria-labelledby="dropdownMenuLink">
                                                                        <a class="dropdown-item"
                                                                            href="{{ asset($attachment) }}"
                                                                            download>Download</a>
                                                                        @role('admin')
                                                                            <a class="dropdown-item"
                                                                                onclick="destroyFile({{ $index }}, 'physical')">Remove</a>
                                                                        @endrole
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <span class="mr-3">No Attachment</span>
                                                @role('admin')
                                                    <button class="input-group-append btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#addAttachment">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @endrole
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif


                            <div class="d-flex pt-3 gap-2">
                                <a href="{{ route('asset.physical.index') }}" class="btn btn-danger mr-2">Back</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
