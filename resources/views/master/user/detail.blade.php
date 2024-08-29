@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail User - {{ $user->name }}</h3>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user->username }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="nik">NIK </label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user->nik }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="division">Division </label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user->division->name ?? '-' }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Name </label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user->name }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="email">Email </label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user->email }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="roles">Role </label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user_role }}
                                </div>

                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="phone">Phone </label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user->phone }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="address">Address </label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $user->address ? $user->address : '-' }}
                                </div>
                            </div>

                            <div class="pt-3">
                                <a href="{{ route('master.user.index') }}" class="btn btn-danger"> Back</a>
                            </div>
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
