@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Edit Brand - {{ $brand->name }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('master.brand.update', ['id' => $brand->id]) }}">
                            @csrf
                            @method('patch')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ $brand->name }}">
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ route('master.brand.index') }}" class="btn btn-danger mr-2"> Back</a>
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
        @include('javascript.master.brand.script')
    @endpush
@endsection
