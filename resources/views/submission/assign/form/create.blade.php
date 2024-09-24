@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Submission Assign Form</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('submission.store', ['type' => 1]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description" required>{{ old('description') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="attachment">Attachment</label>
                                    <input type="file" class="form-control" required name="attachment"
                                        id="documentInput">
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
                                                    Status
                                                </th>
                                                <th width="5%">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">
                                            <tr id="form_asset">
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
                                                    <input type="text" class="form-control" readonly id="barcode">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" readonly id="category">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" readonly id="status">
                                                </td>
                                                <td align="center">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        onclick="addPhysicalAsset()">Add</button>
                                                </td>
                                            </tr>

                                            @if (!is_null(old('assets')))
                                                @foreach (old('assets') as $index => $list_asset)
                                                    <tr id='asset_tr_{{ $index }}'>
                                                        <td>
                                                            <input type='hidden' class='form-control'
                                                                name='assets[{{ $index }}][id]'
                                                                id='asset_id_{{ $index }}'
                                                                value='{{ $list_asset['id'] }}'>
                                                            <input type='text' class='form-control'
                                                                name='assets[{{ $index }}][asset]'
                                                                id='asset_name_{{ $index }}'
                                                                value='{{ $list_asset['asset'] }}' readonly>
                                                        </td>
                                                        <td>
                                                            <input type='number' class='form-control'
                                                                name='assets[{{ $index }}][barcode]'
                                                                value='{{ $list_asset['barcode'] }}' readonly>
                                                        </td>
                                                        <td>
                                                            <input type='number' class='form-control'
                                                                name='assets[{{ $index }}][category]'
                                                                value='{{ $list_asset['category'] }}' readonly>
                                                        </td>
                                                        <td>
                                                            <input type='number' class='form-control'
                                                                name='assets[{{ $index }}][status]'
                                                                value='{{ $list_asset['status'] }}' readonly>
                                                        </td>
                                                        <td align='center'>
                                                            <button type='button' class='delete-row btn btn-sm btn-danger'
                                                                value='Delete'>Delete</button>
                                                            <input type='hidden' class='form-control'
                                                                name='asset_item_check[]' value='{{ $list_asset['id'] }}'>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pt-3 d-flex">
                                    <a href="{{ route('master.user.index') }}" class="btn btn-danger mr-2"> Back</a>
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
        @include('javascript.submission.script')
    @endpush
@endsection
