@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Edit Aset Fisik - {{ $physical->nama }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" id="edit-form"
                            action="{{ route('asset.physical.update', ['id' => $physical->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="categories">Kategori </label>
                                    <select class="form-control select2bs4" id="id_kategori_aset" name="id_kategori_aset">
                                        @foreach ($categories as $category)
                                            @if ($physical->kategori->id == $category->id)
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
                                            @if ($physical->brand->id == $brand->id)
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
                                            @if (!is_null($physical->manufacture) && $physical->manufacture->id == $manufacture->id)
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
                                        placeholder="Barcode" value="{{ $physical->barcode_code }}">
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Nama" value="{{ $physical->nama }}">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4" id="status" name="status" required>
                                        <option disabled hidden selected>Pilih Status</option>
                                        <option value="1" {{ $physical->status == 1 ? 'selected' : '' }}>
                                            Kondisi Bagus</option>
                                        <option value="2" {{ $physical->status == 2 ? 'selected' : '' }}>Kerusakan
                                            Ringan
                                        </option>
                                        <option value="3" {{ $physical->status == 3 ? 'selected' : '' }}>Kerusakan
                                            Berat
                                        </option>
                                        <option value="4" {{ $physical->status == 4 ? 'selected' : '' }}>Dalam
                                            Perbaikan
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nilai">Harga</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="nilai" name="nilai"
                                            placeholder="Harga" value="{{ $physical->nilai }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="expired_at">Expired</label>
                                    <input type="date" class="form-control" id="expired_at" name="expired_at"
                                        placeholder="Expired" value="{{ $physical->expired_at }}">
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_pengambilan">Tanggal Pengambilan</label>
                                    <input type="date" class="form-control" id="tanggal_pengambilan"
                                        name="tanggal_pengambilan" placeholder="Tanggal Pengambilan"
                                        value="{{ $physical->tanggal_pengambilan }}">
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_akhir_garansi">Tanggal Akhir Garansi</label>
                                    <input type="date" class="form-control" id="tanggal_akhir_garansi"
                                        name="tanggal_akhir_garansi" placeholder="Expired"
                                        value="{{ $physical->tanggal_akhir_garansi }}">
                                </div>

                                <div class="form-group">
                                    <label for="durasi_garansi">Durasi Garansi <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="durasi_garansi"
                                            name="durasi_garansi" placeholder="Durasi Garansi"
                                            value="{{ $physical->durasi_garansi }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Month</span>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="description">Deskripsi</label>
                                    <textarea class="form-control" name="description" id="description" cols="10" rows="3"
                                        placeholder="Deskripsi">{{ $physical->deskripsi }}</textarea>
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
