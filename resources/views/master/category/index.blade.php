@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex  align-items-center">

                                <h3 class="card-title"> Category</h3>
                            </div>
                        </div>
                        <div class="card-body p-5">
                            <div class="d-flex justify-content-between">
                                @if ($can_create)
                                    <div>
                                        <a href="{{ route('master.category.create') }}" class="btn btn-sm btn-primary">
                                            Add Category
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive pt-3">
                                <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                                <table class="table table-bordered datatable" id="dt-category">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Name
                                            </th>
                                            <th>
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.master.category.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
