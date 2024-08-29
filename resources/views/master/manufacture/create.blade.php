@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Add Manufacture</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('master.manufacture.store') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address </label>
                                    <textarea class="form-control" name="address" id="address" cols="10" rows="3"
                                        placeholder="Location Address">{{ old('address') }}</textarea>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ route('master.manufacture.index') }}" class="btn btn-danger mr-2"> Back</a>
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
        @include('javascript.master.manufacture.script')
    @endpush
@endsection
