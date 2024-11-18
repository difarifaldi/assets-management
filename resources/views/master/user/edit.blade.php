@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Edit {{ $user->hasRole('admin') ? 'Pengguna' : 'Akun' }}
                                -
                                {{ $user->nama }}</h3>
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
                                    <label for="nik">Nomor Pegawai <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik" name="nik"
                                        placeholder="Nomor Pegawai" value="{{ $user->nik }}">
                                </div>

                                <div class="form-group">
                                    <label for="divisi">Divisi <span class="text-danger">*</span></label>

                                    <select class="form-control" name="id_divisi" id="id_divisi">
                                        @foreach ($divisi as $dv)
                                            @if ($user->id_divisi === $dv->id)
                                                <option value="{{ $dv->id }}" selected>
                                                    {{ $dv->nama }}
                                                </option>
                                            @else
                                                <option value="{{ $dv->id }}">
                                                    {{ $dv->nama }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="nama">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Nama" value="{{ $user->nama }}">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Email" value="{{ $user->email }}">
                                </div>


                                @if (Auth::user()->hasRole('admin'))
                                    <div class="form-group">
                                        <label for="roles">Peran <span class="text-danger">*</span></label>
                                        <select class="form-control" id="roles" name="roles" {{ $role_disabled }}>
                                            <option hidden>Pilih Peran</option>
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
                                    <label for="noHP">Nomor HP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="noHP" name="noHP"
                                        placeholder="Nomor HP" value="{{ $user->noHP }}">
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
                                    <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="alamat" id="alamat" cols="10" rows="3" placeholder="Alamat Lokasi">{{ $user->alamat }}</textarea>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ url()->previous() }}" class="btn btn-danger mr-2">Kembali</a>
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
