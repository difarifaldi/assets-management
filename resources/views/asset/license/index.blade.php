@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex  align-items-center">
                                <h3 class="card-title font-weight-bold">Aset Lisensi</h3>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                @role('admin')
                                    <div>
                                        <a href="{{ route('asset.license.create') }}" class="btn btn-sm btn-primary">
                                            Tambah Aset Lisensi
                                        </a>
                                    </div>
                                @endrole
                            </div>
                            <div class="table-responsive pt-3">
                                <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                                <table class="table table-bordered datatable" id="dt-license">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Nama
                                            </th>
                                            <th>
                                                Brand
                                            </th>
                                            <th>
                                                Kategori
                                            </th>
                                            <th>
                                                Status
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
        @include('javascript.asset.license.script')
        <script>
            dataTable();
        </script>
    @endpush
@endsection
