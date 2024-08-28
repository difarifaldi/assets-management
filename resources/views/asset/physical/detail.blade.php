@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">

                                <h3 class="card-title">Physical Asset Details | {{ $asset->name }}</h3>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <div class="card-body">
                            <!-- Category -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Category</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->category ? $asset->category->name : '-' }}
                                </div>
                            </div>

                            <!-- Merk -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Merk</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->merk ? $asset->merk->name : '-' }}
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Type</label>
                                <div class="col-sm-9 col-form-label">
                                    @if ($asset->type == 1)
                                        Physical Asset
                                    @elseif($asset->type == 2)
                                        License Asset
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <!-- Barcode -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Barcode</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->barcode_code ?? '-' }}
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->name ?? '-' }}
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->status ?? '-' }}
                                </div>
                            </div>

                            <!-- Value -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Value</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->value ?? '-' }}
                                </div>
                            </div>

                            <!-- Expired At -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Expired</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->exipired_at ?? '-' }}
                                </div>
                            </div>

                            <!-- Purchase Date -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Purchase Date</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->purchase_date ?? '-' }}
                                </div>
                            </div>

                            <!-- Warranty End Date -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Warranty End Date</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->warranty_end_date ?? '-' }}
                                </div>
                            </div>

                            <!-- Warranty Duration -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Warranty Duration</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->warranty_duration ?? '-' }}
                                </div>
                            </div>

                            <!-- Assign To -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Assign To</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->assignTo ? $asset->assignTo->name : '-' }}
                                </div>
                            </div>

                            <!-- Assign At -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Assign At</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->assign_at ?? '-' }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $asset->description ?? '-' }}
                                </div>
                            </div>

                            <!-- Attachment -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Attachment</label>
                                <div class="col-sm-9 col-form-label">
                                    @if ($asset->attachment)
                                        <a href="{{ asset($asset->attachment) }}" target="_blank">View Attachment</a>
                                    @else
                                        No Attachment
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('asset.physical.index') }}" class="btn btn-primary"> Back</a>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
