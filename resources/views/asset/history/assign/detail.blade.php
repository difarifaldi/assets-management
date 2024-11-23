@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail Riwayat Penugasan Aset
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ditugaskan Pada</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ date('d F Y H:i:s', strtotime($historyAssign->ditugaskan_pada)) }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ditugaskan Ke</label>
                                <div class="col-sm-9 col-form-label">
                                    {{ $historyAssign->assignTo->nama }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Lampiran</label>
                                @if (!is_null($historyAssign->lampiran))
                                    <div class="col-md-12 pt-3">
                                        <div class="row justify-content-start mt-3">
                                            @foreach (json_decode($historyAssign->lampiran)->bukti_penugasan as $index => $lampiran)
                                                <div class="col-md-3" id="lampiran_{{ $index }}">
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

                            @if (!is_null($historyAssign->dikembalikan_pada) && !is_null($historyAssign->dikembalikan_oleh))
                                <hr>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Dikembalikan Pada</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ !is_null($historyAssign->dikembalikan_pada) ? date('d F Y H:i:s', strtotime($historyAssign->dikembalikan_pada)) : '-' }}
                                    </div>
                                </div>
                                <!-- Lampiran -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Lampiran</label>
                                    @if (!is_null($historyAssign->lampiran))
                                        <div class="col-md-12 pt-3">
                                            <div class="row justify-content-start mt-3">
                                                @foreach (json_decode($historyAssign->lampiran)->proof_return_assign as $index => $lampiran)
                                                    <div class="col-md-3" id="lampiran_{{ $index }}">
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
                                                                        <a class="dropdown-item"
                                                                            href="{{ asset($lampiran) }}"
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
                                                    <button class="input-group-append btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#addAttachment">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @endrole
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif


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
