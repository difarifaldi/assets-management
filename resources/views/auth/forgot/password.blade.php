@extends('layouts.main')
@section('section')

    <body class="hold-transition login-page">
        <div class="login-box">
            <!-- /.login-logo -->
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <label class="h5 pb-3"><i class="fas fa-list mr-2"></i>Asset Management System</label>
                    <p>Setting Up New Password</p>
                    <form action="{{ route('forgot.setPassword', ['id' => $user->id]) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" name="re_password"
                                    class="form-control" placeholder="Confirmation Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-3 justify-content-end">
                            <div class="col-5">
                                <button type="submit"
                                    class="btn btn-primary btn-block font-weight-bold">Change</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.login-box -->
    @endsection
