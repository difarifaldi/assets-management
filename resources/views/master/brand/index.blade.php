@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Brand</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                @if ($can_create)
                                    <div>
                                        <a href="{{ route('master.brand.create') }}" class="btn btn-sm btn-primary">
                                            Tambah Brand
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="table-responsive pt-3">
                                <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                                <table class="table table-bordered datatable" id="dt-brand">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Nama
                                            </th>
                                            <th>
                                                Aksi
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
        @include('javascript.master.brand.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
