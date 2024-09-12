@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail License Asset - {{ $asset->name }}
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <div class="card-body">

                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" data-toggle="tab" data-target="#nav-detail"
                                        type="button" role="tab" aria-controls="nav-detail"
                                        aria-selected="true">Detail</button>
                                    <button class="nav-link" data-toggle="tab" data-target="#nav-assign" type="button"
                                        role="tab" aria-controls="nav-assign" aria-selected="false">History
                                        Assign</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane pt-3 fade show active" id="nav-detail" role="tabpanel">
                                    <input type="hidden" id="asset" value="{{ $asset->id }}">
                                    <!-- Category -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Category</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->category ? $asset->category->name : '-' }}
                                        </div>
                                    </div>

                                    <!-- Brand -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Brand</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->brand ? $asset->brand->name : '-' }}
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
                                            @if ($asset->status == 1)
                                                <span class="badge badge-success">Good Condition</span>
                                            @elseif($asset->status == 2)
                                                <span class="badge badge-warning">Minor Damage</span>
                                            @elseif($asset->status == 3)
                                                <span class="badge badge-danger">Major Damage</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Check Status -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Availability Status</label>
                                        <div class="col-sm-9 col-form-label">
                                            @if (!is_null($asset->assign_to))
                                                <span class="badge badge-danger">Assign To
                                                    {{ $asset->assignTo->name }}</span>
                                            @elseif(!is_null($asset->check_out_by))
                                                <span class="badge badge-danger">Check Out By
                                                    {{ $asset->checkOutBy->name }}</span>
                                            @else
                                                <span class="badge badge-success">Available</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Value -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Value</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ 'Rp.' . number_format($asset->value, 0, ',', '.') . ',00' }}
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
                                        @if (!is_null($asset->attachmentArray))
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <button class="input-group-append btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#addAttachment">
                                                        <i class="fas fa-plus mr-2 my-auto"></i>
                                                        Add Attachment
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt-3">
                                                <div class="row justify-content-start mt-3">
                                                    @foreach ($asset->attachmentArray as $index => $attachment)
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
                                                                            <a class="dropdown-item"
                                                                                onclick="destroyFile({{ $index }}, 'license')">Remove</a>
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
                                                    <button class="input-group-append btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#addAttachment">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane pt-3 fade" id="nav-assign" role="tabpanel">...</div>
                            </div>
                            <div class="d-flex pt-3 gap-2">
                                <a href="{{ route('asset.physical.index') }}" class="btn btn-danger mr-2">Back</a>
                                <a href="{{ route('asset.physical.index') }}" class="btn btn-primary">Assign
                                    To</a>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addAttachment">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('asset.license.uploadImage', ['id' => $asset->id]) }}"
                    class="forms-upload" enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle">Add Attachment</h4>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="date">Attachment <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="attachment[]" id="documentInput"
                                accept="image/*" multiple="true" multiple="true" required>
                            <p class="text-danger py-1">* .png .jpg .jpeg (Max 10 MB)</p>
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
    @push('javascript-bottom')
        @include('javascript.asset.physical.script')
    @endpush
@endsection
