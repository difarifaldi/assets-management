@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">

                                <a href="{{ route('manufacture.index') }}" class="btn btn-tool">
                                    <i class="fas fa-chevron-left"></i> Back
                                </a>
                                <h3 class="card-title">Edit Manufacture | {{ $manufacture->name }}</h3>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('manufacture.update', ['id' => $manufacture->id]) }}">
                            @csrf
                            @method('patch')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ $manufacture->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" id="address" cols="10" rows="3"
                                        placeholder="Location Address">{{ $manufacture->address }}</textarea>
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
        @include('javascript.manufacture.script')
    @endpush
@endsection
