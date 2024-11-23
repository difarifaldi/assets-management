@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Tambah Pengguna</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('master.user.store') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Username" value="{{ old('username') }}">
                                </div>

                                <div class="form-group">
                                    <label for="nik">Nomor Pegawai <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik" name="nik"
                                        placeholder="Nomor Pegawai" value="{{ old('nik') }}">
                                </div>

                                <div class="form-group">
                                    <label for="divisi">Divisi <span class="text-danger">*</span></label>
                                    <select class="form-control" id="id_divisi" name="id_divisi" required>
                                        <option disabled hidden selected>Pilih Divisi</option>
                                        @foreach ($divisi as $dv)
                                            @if (!is_null(old('id_divisi')) && old('id_divisi') == $dv->id)
                                                <option value="{{ $dv->id }}" selected>{{ $dv->nama }}
                                                </option>
                                            @else
                                                <option value="{{ $dv->id }}">{{ $dv->nama }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="nama">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Nama" value="{{ old('nama') }}">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Email" value="{{ old('email') }}">
                                </div>

                                <div class="form-group">
                                    <label for="roles">Peran <span class="text-danger">*</span></label>
                                    <select class="form-control" id="roles" name="roles" required>
                                        <option disabled hidden selected>Pilih Peran</option>
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
                                    <label for="noHP">Nomor HP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="noHP" name="noHP"
                                        placeholder="Nomor HP" value="{{ old('noHP') }}">
                                </div>

                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password" value="{{ old('password') }}">
                                </div>

                                <div class="form-group">
                                    <label for="re_password">Re Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="re_password" name="re_password"
                                        placeholder="Re Password" value="{{ old('re_password') }}">
                                </div>


                                <div class="form-group">
                                    <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="alamat" id="alamat" cols="10" rows="3" placeholder="Alamat">{{ old('alamat') }}</textarea>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ route('master.user.index') }}" class="btn btn-danger mr-2"> Kembali</a>
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
