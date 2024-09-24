@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Submission Assign Asset - {{ $asset->name }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('submission.store', ['type' => 1]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="assets[{{ $asset->id }}][id]" value="{{ $asset->id }}">
                            <input type='hidden' class='form-control' name='asset_item_check[]'
                                value='{{ $asset->id }}'>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="barcode">Barcode </label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ $asset->barcode_code }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name </label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ $asset->name }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status </label>
                                    <div class="col-sm-9 col-form-label">
                                        @if ($asset->status == 1)
                                            Good Condition
                                        @elseif($asset->status == 2)
                                            Minor Damage
                                        @elseif($asset->status == 3)
                                            Major Damage
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Description" required>{{ old('description') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="attachment">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="documentInput">
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
