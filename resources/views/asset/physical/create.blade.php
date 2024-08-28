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
                        <form method="post" action="{{ route('asset.physical.store') }}">
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="categories">Category </label>
                                    <select class="form-control" id="category_asset_id" name="category_asset_id">
                                        <option disabled hidden selected>Choose Category</option>
                                        @foreach ($categories as $category)
                                            @if (!is_null(old('category_asset_id')) && old('category_asset_id') == $category->id)
                                                <option value="{{ $category->id }}" selected>{{ $category->name }}
                                                </option>
                                            @else
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="merks">Merk <span class="text-danger">*</span></label>
                                    <select class="form-control" id="merk_id" name="merk_id">
                                        <option disabled hidden selected>Choose Merk</option>
                                        @foreach ($merks as $merk)
                                            @if (!is_null(old('merk_id')) && old('merk_id') == $merk->id)
                                                <option value="{{ $merk->id }}" selected>{{ $merk->name }}
                                                </option>
                                            @else
                                                <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="type">Type <span class="text-danger">*</span></label>
                                    <select class="form-control " id="type_edit" name="type" required>
                                        <option disabled hidden selected>Choose Type</option>
                                        <option value="1">
                                            Physical Asset</option>
                                        <option value="2">License Asset
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="barcode_code">Barcode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="barcode_code" name="barcode_code"
                                        placeholder="Barcode" value="{{ old('barcode_code') }}">
                                </div>
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="status" name="status"
                                        placeholder="Status" value="{{ old('status') }}">
                                </div>
                                <div class="form-group">
                                    <label for="value">Value</label>
                                    <input type="text" class="form-control" id="value" name="value"
                                        placeholder="Value" value="{{ old('value') }}">
                                </div>

                                <div class="form-group">
                                    <label for="exipired_at">Expired</label>
                                    <input type="date" class="form-control" id="exipired_at" name="exipired_at"
                                        placeholder="Expired" value="{{ old('exipired_at') }}">
                                </div>

                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                        placeholder="Purchase Date" value="{{ old('purchase_date') }}">
                                </div>

                                <div class="form-group">
                                    <label for="warranty_end_date">Warranty End Date</label>
                                    <input type="date" class="form-control" id="warranty_end_date"
                                        name="warranty_end_date" placeholder="Expired"
                                        value="{{ old('warranty_end_date') }}">
                                </div>

                                <div class="form-group">
                                    <label for="warranty_duration">Warranty Duration <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="warranty_duration"
                                        name="warranty_duration" placeholder="Warranty Duration"
                                        value="{{ old('warranty_duration') }}">
                                </div>

                                <div class="form-group">
                                    <label>Assign To</label>
                                    <select class="form-control select2bs4" style="width: 100%;" name="assign_to">
                                        <option disabled hidden selected>Choose User</option>
                                        @foreach ($users as $user)
                                            @if (!is_null(old('assign_to')) && old('assign_to') == $user->id)
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
                                        placeholder="Expired" value="{{ old('assign_at') }}">
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description">{{ old('description') }}</textarea>
                                </div>


                                <div class="mb-3">
                                    <label for="attachment">Attachment <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="attachment" id="documentInput"
                                        accept=".pdf,.doc,.docx,.txt,.xls,.xlsx" multiple="true" required>
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
