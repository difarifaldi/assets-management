@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Edit {{ $user->hasRole('admin') ? 'User' : 'Account' }} -
                                {{ $user->name }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post"
                            @if ($user->hasRole('admin')) action="{{ route('master.user.update', ['id' => $user->id]) }}" @else action="{{ route('my-account.update', ['id' => $user->id]) }}" @endif>
                            @csrf
                            @method('patch')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Username" value="{{ $user->username }}">
                                </div>

                                <div class="form-group">
                                    <label for="nik">Employee Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik" name="nik"
                                        placeholder="Employee Number" value="{{ $user->nik }}">
                                </div>

                                <div class="form-group">
                                    <label for="division">Division <span class="text-danger">*</span></label>

                                    <select class="form-control" name="division_id" id="division_id">
                                        @foreach ($division as $dv)
                                            @if ($user->division_id === $dv->id)
                                                <option value="{{ $dv->id }}" selected>
                                                    {{ $dv->name }}
                                                </option>
                                            @else
                                                <option value="{{ $dv->id }}">
                                                    {{ $dv->name }}
                                                </option>
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


                                @if (Auth::user()->hasRole('admin'))
                                    <div class="form-group">
                                        <label for="roles">Role <span class="text-danger">*</span></label>
                                        <select class="form-control" id="roles" name="roles" {{ $role_disabled }}>
                                            <option hidden>Choose Role</option>
                                            @foreach ($roles as $role)
                                                @if ($user->getRoleNames()[0] == $role->name)
                                                    <option value="{{ $role->name }}" selected>{{ $role->name }}
                                                    </option>
                                                @else
                                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" id="roles" name="roles" value="staff">
                                @endif
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="Phone" value="{{ $user->phone }}">
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <label for="re_password">Re Password</label>
                                    <input type="password" class="form-control" id="re_password" name="re_password"
                                        placeholder="Re Password">
                                </div>

                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" id="address" cols="10" rows="3"
                                        placeholder="Location Address">{{ $user->address }}</textarea>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ url()->previous() }}" class="btn btn-danger mr-2">Back</a>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
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
