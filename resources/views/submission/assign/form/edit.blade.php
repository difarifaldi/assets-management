@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Edit Submission Assign#{{ $submission->id }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" id="form-edit"
                            action="{{ route('submission.update', ['id' => $submission->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description" required>{{ $submission->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="attachment">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="documentInput">
                                    @if (!is_null($submission->attachment))
                                        <label class="m-2">
                                            <a href="{{ asset($submission->attachment) }}" target="_blank">
                                                <i class="fas fa-download mr-1"></i>
                                                Attachment Document
                                            </a>
                                        </label>
                                    @endif
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
                                            @foreach ($submission->submissionFormItemAsset as $item_asset)
                                                <tr id='asset_tr_{{ $item_asset->asset->id }}'>
                                                    <td>
                                                        <input type='hidden' class='form-control'
                                                            id='asset_id_{{ $item_asset->asset->id }}'
                                                            name='assets[{{ $item_asset->asset->id }}][id]'
                                                            value='{{ $item_asset->asset->id }}'>

                                                        <input type='text' class='form-control'
                                                            id='asset_name_{{ $item_asset->asset->id }}'
                                                            name='assets[{{ $item_asset->asset->id }}][name]'
                                                            value='{{ $item_asset->asset->name }}' readonly>
                                                    </td>
                                                    <td>
                                                        <input type='text' class='form-control'
                                                            name='assets[{{ $item_asset->asset->id }}][barcode]'
                                                            value='{{ $item_asset->asset->barcode_code }}' readonly>
                                                    </td>
                                                    <td>
                                                        <input type='text' class='form-control'
                                                            name='assets[{{ $item_asset->asset->id }}][category]'
                                                            value='{{ $item_asset->asset->category->name }}' readonly>
                                                    </td>
                                                    <td>
                                                        <input type='text' class='form-control'
                                                            name='assets[{{ $item_asset->asset->id }}][status]'
                                                            value='{{ $item_asset->asset->status === 1 ? 'Good Condition' : ($item_asset->asset->status === 2 ? 'Minor Damage' : 'Major Damage') }}'
                                                            readonly>
                                                    </td>
                                                    <td align='center'>
                                                        <button type='button' class='btn btn-sm btn-danger'
                                                            onclick='deleteRow({{ $item_asset->asset->id }})'
                                                            title='Delete'>Delete</button>
                                                        <input type='hidden' class='form-control' name='asset_item_check[]'
                                                            value='{{ $item_asset->asset->id }}'>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pt-3 d-flex">
                                    <a href="{{ route('submission.index') }}" class="btn btn-danger mr-2"> Back</a>
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
