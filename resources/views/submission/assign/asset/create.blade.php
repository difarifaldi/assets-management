@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold">Pengajuan Penugasan Aset - {{ $asset->nama }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" id="form-create" action="{{ route('submission.store', ['tipe' => 1]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="assets[{{ $asset->id }}][id]" value="{{ $asset->id }}">
                            <input type='hidden' class='form-control' name='asset_item_check[]'
                                value='{{ $asset->id }}'>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="barcode">Barcode </label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ $asset->barcode_code }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name">Nama </label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ $asset->nama }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status </label>
                                    <div class="col-sm-9 col-form-label">
                                        @if ($asset->status == 1)
                                            Kondisi Bagus
                                        @elseif($asset->status == 2)
                                            Kerusakan Ringan
                                        @elseif($asset->status == 3)
                                            Kerusakan Berat
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="deskripsi" id="deskripsi" cols="10" rows="3" placeholder="Deskripsi"
                                        required>{{ old('deskripsi') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="lampiran">Lampiran</label>
                                    <input type="file" class="form-control" name="lampiran" id="documentInput">
                                </div>
                                <div class="pt-3 d-flex">
                                    <a href="{{ route('submission.index') }}" class="btn btn-danger mr-2"> Kembali</a>
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
        @include('javascript.submission.script')
    @endpush
@endsection
