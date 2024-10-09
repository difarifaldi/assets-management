@extends('layouts.main')
@section('section')

    <body class="hold-transition login-page">
        <div class="login-box">
            <!-- /.login-logo -->
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <label class="h5 pb-3"><i class="fas fa-list mr-2"></i>Asset Management System</label>
                    <p>Confirmation Email</p>
                    <form action="{{ route('forgot.confirmation') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Email">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="row pt-3 justify-content-end">
                            <div class="col-5">
                                <button type="submit" class="btn btn-primary btn-block font-weight-bold">Confirmation</button>
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
