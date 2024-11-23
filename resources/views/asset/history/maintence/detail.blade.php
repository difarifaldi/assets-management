@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail Riwayat Maintence Aset
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tanggal</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ date('d F Y H:i:s', strtotime($historyMaintence->tanggal)) }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9 col-form-label">
                                    <td>
                                        @if ($historyMaintence->status == 1)
                                            Kondisi Bagus
                                        @elseif($historyMaintence->status == 2)
                                            Kerusakan Ringan
                                        @elseif($historyMaintence->status == 3)
                                            Kerusakan Berat
                                        @endif
                                    </td>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Deskripsi</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $historyMaintence->deskripsi }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Lampiran</label>
                                @if (!is_null($historyMaintence->lampiran))
                                    <div class="col-md-12 pt-3">
                                        <div class="row justify-content-start mt-3">
                                            @foreach (json_decode($historyMaintence->lampiran)->bukti_pemeliharaan as $index => $lampiran)
                                                <div class="col-md-3" id="attachment_{{ $index }}">
                                                    <div class="card shadow">
                                                        <input type="hidden" id="file_name_{{ $index }}"
                                                            value="{{ $lampiran }}">
                                                        <div style="width:100%;overflow:hidden">
                                                            <a href="{{ asset($lampiran) }}" class="text-black">
                                                                <img src="{{ asset($lampiran) }}"
                                                                    onerror="this.onerror=null;this.src='{{ asset('img/image-not-found.jpg') }}'"
                                                                    width="100%"
                                                                    style="height:350px;object-fit: cover;" />
                                                            </a>
                                                        </div>
                                                        <div class="card-body text-right">
                                                            <div class="dropdown text-black">
                                                                <a class="text-black-50" id="dropdownMenuButton"
                                                                    title="Detail" data-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </a>
                                                                <div class="dropdown-menu"
                                                                    aria-labelledby="dropdownMenuLink">
                                                                    <a class="dropdown-item" href="{{ asset($lampiran) }}"
                                                                        download>Download</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <span class="mr-3">Tidak Ada Lampiran</span>
                                            @role('admin')
                                                <button class="input-group-append btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#addAttachment">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endrole
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex pt-3 gap-2">
                                <a href="{{ route('asset.physical.index') }}" class="btn btn-danger mr-2">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
