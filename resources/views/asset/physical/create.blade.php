@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">

                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Add Physical Asset</h3>
                        </div>

                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" id="create-form" action="{{ route('asset.physical.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="categories">Category </label>
                                    <select class="form-control select2bs4" id="category_asset_id" name="category_asset_id">
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
                                    <label for="brand_id">Brand <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="brand_id" name="brand_id">
                                        <option disabled hidden selected>Choose Brand</option>
                                        @foreach ($brands as $brand)
                                            @if (!is_null(old('brand_id')) && old('brand_id') == $brand->id)
                                                <option value="{{ $brand->id }}" selected>{{ $brand->name }}
                                                </option>
                                            @else
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="manufacture_id">Manufacture <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="manufacture_id" name="manufacture_id">
                                        <option disabled hidden selected>Choose Manufacture</option>
                                        @foreach ($manufactures as $manufacture)
                                            @if (!is_null(old('manufacture_id')) && old('manufacture_id') == $manufacture->id)
                                                <option value="{{ $manufacture->id }}" selected>{{ $manufacture->name }}
                                                </option>
                                            @else
                                                <option value="{{ $manufacture->id }}">{{ $manufacture->name }}</option>
                                            @endif
                                        @endforeach
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
                                    <select class="form-control select2bs4" id="status" name="status" required>
                                        <option disabled hidden selected>Choose Status</option>
                                        <option value="1">
                                            Good Condition</option>
                                        <option value="2">Minor Damage
                                        </option>
                                        <option value="3">Major Damage
                                        </option>
                                        <option value="4">On Maintence
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="value">Value</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="value" name="value"
                                            placeholder="Value" value="{{ old('value') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    </div>
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
                                    <label for="warranty_duration">Warranty Duration</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="warranty_duration"
                                            name="warranty_duration" placeholder="Warranty Duration"
                                            value="{{ old('warranty_duration') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Month</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description">{{ old('description') }}</textarea>
                                </div>


                                <div class="mb-3">
                                    <label for="attachment">Attachment <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="attachment[]" id="documentInput"
                                        accept="image/*;capture=camera" multiple="true" required>
                                    <p class="text-danger py-1">* .jpg .jpeg .png</p>
                                    <iframe id="documentPreview" class="w-100 mt-3 d-none"
                                        style="height: 600px;"></iframe>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ route('asset.physical.index') }}" class="btn btn-danger mr-2">
                                        Back</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
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
