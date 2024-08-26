@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">

                                <a href="{{ route('master.user.index') }}" class="btn btn-tool">
                                    <i class="fas fa-chevron-left"></i> Back
                                </a>
                                <h3 class="card-title">Edit User | {{ $user->name }}</h3>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('master.user.update', ['id' => $user->id]) }}">
                            @csrf
                            @method('patch')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Username" value="{{ $user->username }}">
                                </div>

                                <div class="form-group">
                                    <label for="nik">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik" name="nik"
                                        placeholder="NIK" value="{{ $user->nik }}">
                                </div>

                                <div class="form-group">
                                    <label for="division">Division <span class="text-danger">*</span></label>
                                    <select class="form-control" id="division_id" name="division_id" required>
                                        <option disabled hidden selected>Choose Division</option>
                                        @foreach ($division as $dv)
                                            @if (!is_null(old('division_id')) && old('division_id') == $dv->id)
                                                <option value="{{ $dv->id }}" selected>{{ $dv->name }}
                                                </option>
                                            @else
                                                <option value="{{ $dv->id }}">{{ $dv->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ $user->name }}">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Email" value="{{ $user->email }}">
                                </div>

                                <div class="form-group">
                                    <label for="roles">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" id="roles" name="roles" required>
                                        <option disabled hidden selected>Choose Role</option>
                                        @foreach ($roles as $role)
                                            @if (!is_null(old('roles')) && old('roles') == $role->name)
                                                <option value="{{ $role->name }}" selected>{{ $role->name }}
                                                </option>
                                            @else
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Phone" value="{{ $user->phone }}">
                                </div>

                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password" value="{{ $user->password }}">
                                </div>

                                <div class="form-group">
                                    <label for="re_password">Re Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="re_password" name="re_password"
                                        placeholder="Re Password" value="{{ old('re_password') }}">
                                </div>


                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" id="address" cols="10" rows="3"
                                        placeholder="Location Address">{{ $user->address }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            <!-- /.card-body -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.master.user.script')
    @endpush
@endsection
