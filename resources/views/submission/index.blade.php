@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"> Submission Form</h3>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                @role('staff')
                                    <div>
                                        <a type="button" onclick="openOptions()" class="btn btn-sm btn-primary">
                                            Add Submission
                                        </a>
                                    </div>
                                @endrole
                            </div>
                            <div class="table-responsive pt-3">
                                <input type="hidden" id="url_dt" value="{{ $datatable_route }}">
                                <table class="table table-bordered datatable" id="dt-submission">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Created At
                                            </th>
                                            <th>
                                                Type
                                            </th>
                                            <th>
                                                Deskripsi
                                            </th>
                                            <th>
                                                Status
                                            </th>
                                            @role('admin')
                                                <th>
                                                    Request By
                                                </th>
                                            @endrole
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
        @include('javascript.submission.script')
        @role('admin')
            <script>
                dataTable();
            </script>
        @else
            <script>
                dataTableStaff();

                function openOptions() {
                    Swal.fire({
                        title: "Choose Submission",
                        icon: "question",
                        showConfirmButton: false,
                        showCancelButton: false,
                        html: `<div class="d-flex justify-content-center my-2">
                <a href='{{ route('submission.create', ['type' => 'assign']) }}' class="btn btn-primary mr-2">Assign To Me</a>
                <a href='{{ route('submission.create', ['type' => 'checkouts']) }}' class="btn btn-primary mr-2">Checkout</a>
                </div>
            `
                    });
                }
            </script>
        @endrole
    @endpush
@endsection
