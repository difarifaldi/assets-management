@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Edit License Asset - {{ $license->name }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" id="edit-form"
                            action="{{ route('asset.license.update', ['id' => $license->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="categories">Category </label>
                                    <select class="form-control" id="category_asset_id" name="category_asset_id">
                                        @foreach ($categories as $category)
                                            @if ($license->category->id == $category->id)
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
                                            @if ($license->brand->id == $brand->id)
                                                <option value="{{ $brand->id }}" selected>{{ $brand->name }}
                                                </option>
                                            @else
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="barcode_code">Barcode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="barcode_code" name="barcode_code"
                                        placeholder="Barcode" value="{{ $license->barcode_code }}">
                                </div>
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ $license->name }}">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="status" name="status" required>
                                        <option disabled hidden selected>Choose Status</option>
                                        <option value="1" {{ $license->status == 1 ? 'selected' : '' }}>
                                            Good Condition</option>
                                        <option value="5" {{ $license->status == 5 ? 'selected' : '' }}>License
                                            Expired
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="value">Value</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="value" name="value"
                                            placeholder="Value" value="{{ $license->value }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="expired_at">Expired</label>
                                    <input type="date" class="form-control" id="expired_at" name="expired_at"
                                        placeholder="Expired" value="{{ $license->expired_at }}">
                                </div>

                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                        placeholder="Purchase Date" value="{{ $license->purchase_date }}">
                                </div>

                                <div class="form-group">
                                    <label for="warranty_end_date">Warranty End Date</label>
                                    <input type="date" class="form-control" id="warranty_end_date"
                                        name="warranty_end_date" placeholder="Expired"
                                        value="{{ $license->warranty_end_date }}">
                                </div>

                                <div class="form-group">
                                    <label for="warranty_duration">Warranty Duration <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="warranty_duration"
                                            name="warranty_duration" placeholder="Warranty Duration"
                                            value="{{ $license->warranty_duration }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Month</span>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description">{{ $license->description }}</textarea>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ route('asset.license.index') }}" class="btn btn-danger mr-2">
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
        @include('javascript.asset.license.script')
    @endpush
@endsection
