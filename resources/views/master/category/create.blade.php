@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Tambah Kategori</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('master.category.store') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nama">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Nama" value="{{ old('nama') }}">
                                </div>

                                <div class="form-group">
                                    <label for="tipe">Tipe <span class="text-danger">*</span></label>
                                    <select class="form-control " id="type_edit" name="tipe" required>
                                        <option disabled hidden selected>Pilih Tipe</option>
                                        <option value="1">
                                            Aset Fisik</option>
                                        <option value="2">Aset Lisensi
                                        </option>
                                    </select>
                                </div>


                                <div class="pt-3 d-flex">
                                    <a href="{{ route('master.category.index') }}" class="btn btn-danger mr-2"> Kembali</a>
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
        @include('javascript.master.category.script')
    @endpush
@endsection
