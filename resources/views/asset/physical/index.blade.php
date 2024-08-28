@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex  align-items-center">

                                <h3 class="card-title"> Physical Asset</h3>
                            </div>
                        </div>
                        <div class="card-body p-5">
                            <div class="d-flex justify-content-between">

                                <div>
                                    <a href="{{ route('asset.physical.create') }}" class="btn btn-sm btn-primary">
                                        Add Physical Asset
                                    </a>
                                </div>

                            </div>
                            <div class="table-responsive pt-3">
                                <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                                <table class="table table-bordered datatable" id="dt-physical">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Name
                                            </th>
                                            <th>
                                                Merk
                                            </th>
                                            <th>
                                                Category
                                            </th>
                                            <th>
                                                Assign To
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
        @include('javascript.asset.physical.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
