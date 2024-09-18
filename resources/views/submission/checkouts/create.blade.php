@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Submission Check Out </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('submission.form.checkouts.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="loan_application_asset_date">Loan Application Asset Date</label>
                                    <input type="date" class="form-control" id="loan_application_asset_date"
                                        name="loan_application_asset_date" placeholder="Purchase Date"
                                        value="{{ old('loan_application_asset_date') }}">
                                </div>
                                <div class="form-group">
                                    <label for="return_asset_date">Return Asset Date </label>
                                    <input type="date" class="form-control" id="return_asset_date"
                                        name="return_asset_date" placeholder="Purchase Date"
                                        value="{{ old('return_asset_date') }}">
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description" required>{{ old('description') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="attachment">Attachment </label>
                                    <input type="file" class="form-control" required name="attachment"
                                        id="documentInput">
                                </div>

                                <div class="table-responsive mt-5">
                                    <table class="table table-bordered datatable" id="physical_asset">
                                        <thead>
                                            <tr>
                                                <th width="20%">
                                                    Asset
                                                </th>
                                                <th>
                                                    Category
                                                </th>
                                                <th>
                                                    Status
                                                </th>
                                                <th>
                                                    Barcode
                                                </th>
                                                <th>
                                                    Value
                                                </th>

                                                <th width="5%">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">
                                            <tr id="form_physical_asset">
                                                <td>

                                                    <select class="form-control select2bs4" id="asset_id" name="asset_id">
                                                        <option value="" disabled hidden selected>Choose Asset
                                                        </option>
                                                        @foreach ($assets as $asset)
                                                            <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                                                        @endforeach

                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control" readonly id="category">

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <input type="text" class="form-control" readonly id="status">

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex">

                                                        <input type="text" class="form-control" readonly id="barcode">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <span class="input-group-text bg-default p-2">Rp.</span>
                                                        <input type="text" class="form-control" readonly id="value">
                                                    </div>
                                                </td>

                                                <td align="center">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        onclick="addPhysicalAsset()">Add</button>
                                                </td>
                                            </tr>

                                            @if (!is_null(old('physical_asset')))
                                                @foreach (old('physical_asset') as $index => $physical_asset)
                                                    <tr>
                                                        <td>
                                                            <input type='text' class='form-control'
                                                                name='physical_asset[][asset]' value='' required>
                                                        </td>
                                                        <td>
                                                            <div class='d-flex'>
                                                                <input type='number' class='form-control'
                                                                    name='physical_asset[][category]' value=''
                                                                    required>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class='d-flex'>
                                                                <input type='number' class='form-control'
                                                                    name='physical_asset[][status]' value='' required>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class='d-flex'>

                                                                <input type='number' class='form-control'
                                                                    name='physical_asset[][barcode]' value=''
                                                                    required>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class='d-flex'>
                                                                <span class='input-group-text bg-default p-2'>Rp.</span>
                                                                <input type='number' class='form-control'
                                                                    name='physical_asset[][value]' value='' required>
                                                            </div>
                                                        </td>

                                                        <td align='center'>
                                                            <button type='button'
                                                                class='delete-row btn btn-sm btn-danger'
                                                                value='Delete'>Delete</button>
                                                            <input type='hidden' class='form-control'
                                                                name='asset_item_check[]'
                                                                value='{{ $physical_asset['asset'] }}'>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pt-3 d-flex">
                                    <a href="#" class="btn btn-danger mr-2"> Back</a>
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
        @include('javascript.submission.checkouts.script')
    @endpush
@endsection
