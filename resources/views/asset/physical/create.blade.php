@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">

                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Tambah Aset Fisik</h3>
                        </div>

                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" id="create-form" action="{{ route('asset.physical.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="categories">Kategori </label>
                                    <select class="form-control select2bs4" id="id_kategori_aset" name="id_kategori_aset">
                                        <option disabled hidden selected>Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            @if (!is_null(old('id_kategori_aset')) && old('id_kategori_aset') == $category->id)
                                                <option value="{{ $category->id }}" selected>{{ $category->nama }}
                                                </option>
                                            @else
                                                <option value="{{ $category->id }}">{{ $category->nama }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_brand">Brand <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="id_brand" name="id_brand">
                                        <option disabled hidden selected>Pilih Brand</option>
                                        @foreach ($brands as $brand)
                                            @if (!is_null(old('id_brand')) && old('id_brand') == $brand->id)
                                                <option value="{{ $brand->id }}" selected>{{ $brand->nama }}
                                                </option>
                                            @else
                                                <option value="{{ $brand->id }}">{{ $brand->nama }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_manufaktur">Manufaktur <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="id_manufaktur" name="id_manufaktur">
                                        <option disabled hidden selected>Pilih Manufaktur</option>
                                        @foreach ($manufactures as $manufacture)
                                            @if (!is_null(old('id_manufaktur')) && old('id_manufaktur') == $manufacture->id)
                                                <option value="{{ $manufacture->id }}" selected>{{ $manufacture->nama }}
                                                </option>
                                            @else
                                                <option value="{{ $manufacture->id }}">{{ $manufacture->nama }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="barcode_code">Barcode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="barcode_code" name="barcode_code"
                                        placeholder="Barcode" value="{{ old('barcode_code') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Nama" value="{{ old('nama') }}">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="status" name="status" required>
                                        <option disabled hidden selected>Pilih Status</option>
                                        <option value="1">
                                            Kondisi Bagus</option>
                                        <option value="2">Kerusakan Ringan
                                        </option>
                                        <option value="3">Kerusakan Berat
                                        </option>
                                        <option value="4">Dalam Perbaikan
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="nilai">Nilai</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="nilai" name="nilai"
                                            placeholder="Nilai" value="{{ old('nilai') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_pengambilan">Tanggal Pengambilan</label>
                                    <input type="date" class="form-control" id="tanggal_pengambilan"
                                        name="tanggal_pengambilan" placeholder="Tanggal Pengambilan"
                                        value="{{ old('tanggal_pengambilan') }}">
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_akhir_garansi">Tanggal Akhir Garansi</label>
                                    <input type="date" class="form-control" id="tanggal_akhir_garansi"
                                        name="tanggal_akhir_garansi" placeholder="Expired"
                                        value="{{ old('tanggal_akhir_garansi') }}">
                                </div>

                                <div class="form-group">
                                    <label for="durasi_garansi">Durasi Garansi</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="durasi_garansi" name="durasi_garansi"
                                            placeholder="Durasi Garansi" value="{{ old('durasi_garansi') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Bulan</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" id="deskripsi" cols="10" rows="3"
                                        placeholder="Deskripsi">{{ old('deskripsi') }}</textarea>
                                </div>


                                <div class="mb-3">
                                    <label for="lampiran">Lampiran <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="lampiran[]" id="documentInput"
                                        accept="image/*;capture=camera" multiple="true" required>
                                    <p class="text-danger py-1">* .jpg .jpeg .png</p>
                                    <iframe id="documentPreview" class="w-100 mt-3 d-none"
                                        style="height: 600px;"></iframe>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ route('asset.physical.index') }}" class="btn btn-danger mr-2">
                                        Kembali</a>
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
        @include('javascript.asset.physical.script')
    @endpush
@endsection
