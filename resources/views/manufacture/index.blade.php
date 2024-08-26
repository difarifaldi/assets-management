@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-5">
                            <div class="d-flex justify-content-between">
                                <div class="p-2">
                                    <h4 class="card-title">Manufacture</h4>
                                </div>
                                @if ($can_create)
                                    <div class="p-2">
                                        <a href="{{ route('manufacture.create') }}" class="btn btn-sm btn-primary">
                                            Add Manufacture
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive pt-3">
                                <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                                <table class="table table-bordered datatable" id="dt-manufacture">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Name
                                            </th>
                                            <th>
                                                Address
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
        @include('javascript.manufacture.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
