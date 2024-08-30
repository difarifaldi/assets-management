@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">

                                <a href="{{ route('asset.physical.index') }}" class="btn btn-tool">
                                    <i class="fas fa-chevron-left"></i> Back
                                </a>
                                <h3 class="card-title">Add Physical Asset</h3>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('asset.physical.update', ['id' => $physical->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="categories">Category </label>
                                    <select class="form-control" id="category_asset_id" name="category_asset_id">
                                        @foreach ($categories as $category)
                                            @if ($physical->category->id == $category->id)
                                                <option value="{{ $category->id }}" selected>{{ $category->name }}
                                                </option>
                                            @else
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="brand_id">Brand <span class="text-danger">*</span></label>
                                    <select class="form-control" id="brand_id" name="brand_id">
                                        @foreach ($brands as $brand)
                                            @if ($physical->brand->id == $brand->id)
                                                <option value="{{ $brand->id }}" selected>{{ $brand->name }}
                                                </option>
                                            @else
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="type">Type <span class="text-danger">*</span></label>
                                    <select class="form-control " id="type_edit" name="type" required>

                                        <option value="1" {{ $physical->type == 1 ? 'selected' : '' }}>
                                            Physical Asset</option>

                                        <option value="2" {{ $physical->type == 2 ? 'selected' : '' }}>License Asset
                                        </option>


                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="barcode_code">Barcode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="barcode_code" name="barcode_code"
                                        placeholder="Barcode" value="{{ $physical->barcode_code }}">
                                </div>
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ $physical->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="status" name="status"
                                        placeholder="Status" value="{{ $physical->status }}">
                                </div>
                                <div class="form-group">
                                    <label for="value">Value</label>
                                    <input type="text" class="form-control" id="value" name="value"
                                        placeholder="Value" value="{{ $physical->value }}">
                                </div>

                                <div class="form-group">
                                    <label for="exipired_at">Expired</label>
                                    <input type="date" class="form-control" id="exipired_at" name="exipired_at"
                                        placeholder="Expired" value="{{ $physical->exipired_at }}">
                                </div>

                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                        placeholder="Purchase Date" value="{{ $physical->purchase_date }}">
                                </div>

                                <div class="form-group">
                                    <label for="warranty_end_date">Warranty End Date</label>
                                    <input type="date" class="form-control" id="warranty_end_date"
                                        name="warranty_end_date" placeholder="Expired"
                                        value="{{ $physical->warranty_end_date }}">
                                </div>

                                <div class="form-group">
                                    <label for="warranty_duration">Warranty Duration <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="warranty_duration"
                                        name="warranty_duration" placeholder="Warranty Duration"
                                        value="{{ $physical->warranty_duration }}">
                                </div>

                                <div class="form-group">
                                    <label>Assign To</label>
                                    <select class="form-control select2bs4" style="width: 100%;" name="assign_to">

                                        @foreach ($users as $user)
                                            @if ($physical->assignTo->id == $user->id)
                                                <option value="{{ $user->id }}" selected>{{ $user->name }}
                                                </option>
                                            @else
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="assign_at">Assign At</label>
                                    <input type="date" class="form-control" id="assign_at" name="assign_at"
                                        placeholder="Expired" value="{{ $physical->assign_at }}">
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description">{{ $physical->description }}</textarea>
                                </div>


                                <div class="mb-3">
                                    <label for="attachment">Attachment <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="attachment[]" id="documentInput"
                                        accept="image/*" multiple="true">
                                    <p class="text-danger py-1">* .pdf .docx .xlsx .pptx (Max 10 MB)</p>
                                    <iframe id="documentPreview" class="w-100 mt-3 d-none"
                                        style="height: 600px;"></iframe>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            <!-- /.card-body -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.asset.physical.script')
    @endpush
@endsection
