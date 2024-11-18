@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Edit Aset Lisensi - {{ $license->nama }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" id="edit-form"
                            action="{{ route('asset.license.update', ['id' => $license->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="categories">Kategori </label>
                                    <select class="form-control select2bs4" id="id_kategori_aset" name="id_kategori_aset">
                                        @foreach ($categories as $category)
                                            @if ($license->category->id == $category->id)
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
                                        @foreach ($brands as $brand)
                                            @if ($license->brand->id == $brand->id)
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
                                        @foreach ($manufactures as $manufacture)
                                            @if (!is_null($license->manufacture) && $license->manufacture->id == $manufacture->id)
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
                                        placeholder="Barcode" value="{{ $license->barcode_code }}">
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Nama" value="{{ $license->nama }}">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="status" name="status" required>
                                        <option disabled hidden selected>Choose Status</option>
                                        <option value="1" {{ $license->status == 1 ? 'selected' : '' }}>
                                            Kondisi Bagus</option>
                                        <option value="2" {{ $license->status == 2 ? 'selected' : '' }}>Kerusakan
                                            Sedikit
                                        </option>
                                        <option value="3" {{ $license->status == 3 ? 'selected' : '' }}>Kerusakan
                                            Banyak
                                        </option>
                                        <option value="5" {{ $license->status == 5 ? 'selected' : '' }}>Lisensi
                                            Expired
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nilai">Nilai</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="nilai" name="nilai"
                                            placeholder="Nilai" value="{{ $license->nilai }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="expired_pada">Expired</label>
                                    <input type="date" class="form-control" id="expired_pada" name="expired_pada"
                                        placeholder="Expired" value="{{ $license->expired_pada }}">
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_pengambilan">Tanggal Pengambilan</label>
                                    <input type="date" class="form-control" id="tanggal_pengambilan"
                                        name="tanggal_pengambilan" placeholder="Tanggal Pengambilan"
                                        value="{{ $license->tanggal_pengambilan }}">
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_akhir_garansi">Tanggal Terakhir Garansi</label>
                                    <input type="date" class="form-control" id="tanggal_akhir_garansi"
                                        name="tanggal_akhir_garansi" placeholder="Tanggal Terakhir Garansi"
                                        value="{{ $license->tanggal_akhir_garansi }}">
                                </div>

                                <div class="form-group">
                                    <label for="durasi_garansi">Durasi Garansi <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="durasi_garansi"
                                            name="durasi_garansi" placeholder="Durasi Garansi"
                                            value="{{ $license->durasi_garansi }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Bulan</span>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" id="deskripsi" cols="10" rows="3"
                                        placeholder="Deskripsi">{{ $license->deskripsi }}</textarea>
                                </div>

                                <div class="pt-3 d-flex">
                                    <a href="{{ route('asset.license.index') }}" class="btn btn-danger mr-2">
                                        Back</a>
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
        @include('javascript.asset.license.script')
    @endpush
@endsection
