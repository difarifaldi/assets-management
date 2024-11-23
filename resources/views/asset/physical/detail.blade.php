@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail Aset Fisik - {{ $asset->nama }}
                            </div>
                        </div>
                        <!-- form start -->
                        <div class="card-body">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" data-toggle="tab" data-target="#nav-detail"
                                        type="button" role="tab" aria-controls="nav-detail"
                                        aria-selected="true">Detail</button>
                                    @role('admin')
                                        <button class="nav-link" data-toggle="tab" data-target="#nav-maintence" type="button"
                                            role="tab" aria-controls="nav-maintence" aria-selected="false">Riwayat
                                            Perbaikan</button>
                                        <button class="nav-link" data-toggle="tab" data-target="#nav-assign" type="button"
                                            role="tab" aria-controls="nav-assign" aria-selected="false">Riwayat
                                            Penugasan</button>
                                        <button class="nav-link" data-toggle="tab" data-target="#nav-check" type="button"
                                            role="tab" aria-controls="nav-check" aria-selected="false">Riwayat
                                            Peminjaman</button>
                                    @endrole
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane pt-3 fade show active" id="nav-detail" role="tabpanel">
                                    <!-- /.card-header -->
                                    <input type="hidden" id="asset" value="{{ $asset->id }}">
                                    <!-- Kategori -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Kategori</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->category ? $asset->category->nama : '-' }}
                                        </div>
                                    </div>

                                    <!-- Brand -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Brand</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->brand ? $asset->brand->nama : '-' }}
                                        </div>
                                    </div>

                                    <!-- Manufacture -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Manufaktur</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->manufaktur ? $asset->manufaktur->nama : '-' }}
                                        </div>
                                    </div>

                                    <!-- Barcode -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Barcode</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->barcode_code ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- Name -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->nama ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Status</label>
                                        <div class="col-sm-9 col-form-label">
                                            @if ($asset->status == 1)
                                                <span class="badge badge-success">Kondisi Bagus</span>
                                            @elseif($asset->status == 2)
                                                <span class="badge badge-warning">Kerusakan Ringan </span>
                                            @elseif($asset->status == 3)
                                                <span class="badge badge-danger">Kerusakan Berat </span>
                                            @elseif($asset->status == 4)
                                                <span class="badge badge-danger">Dalam Perbaikan</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if (in_array($asset->status, [1, 2]))
                                        <!-- Check Status -->
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Ketersediaan Status</label>
                                            <div class="col-sm-9 col-form-label">
                                                @if (!is_null($asset->ditugaskan_ke))
                                                    <span class="badge badge-danger">Ditugaskan Ke
                                                        {{ $asset->assignTo->nama }}</span>
                                                @elseif(!is_null($asset->dipinjam_oleh))
                                                    <span class="badge badge-danger">Dipinjam Oleh
                                                        {{ $asset->checkOut->nama }}</span>
                                                @elseif ($asset->status == 4)
                                                    <span class="badge badge-danger">Dalam Perbaikan
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">Tersedia</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Value -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nilai</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ 'Rp.' . number_format($asset->nilai, 0, ',', '.') . ',00' }}
                                        </div>
                                    </div>

                                    <!-- Purchase Date -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Tanggal Pengambilan</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ !is_null($asset->tanggal_pengambilan) ? date('d F Y', strtotime($asset->tanggal_pengambilan)) : '-' }}
                                        </div>
                                    </div>

                                    <!-- Warranty End Date -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Tanggal Akhir Garansi</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ !is_null($asset->tanggal_akhir_garansi) ? date('d F Y', strtotime($asset->tanggal_akhir_garansi)) : '-' }}
                                        </div>
                                    </div>

                                    <!-- Warranty Duration -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Durasi Garansi</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ !is_null($asset->tanggal_akhir_garansi) ? $asset->tanggal_akhir_garansi . ' Month' : '-' }}
                                        </div>
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Deskripsi</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $asset->description ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- Attachment -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Attachment</label>
                                        @if (!is_null($asset->attachmentArray))
                                            @role('admin')
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <button class="input-group-append btn btn-sm btn-primary"
                                                            data-toggle="modal" data-target="#addAttachment">
                                                            <i class="fas fa-plus mr-2 my-auto"></i>
                                                            Add Attachment
                                                        </button>
                                                    </div>
                                                </div>
                                            @endrole
                                            <div class="col-md-12 pt-3">
                                                <div class="row justify-content-start mt-3">
                                                    @foreach ($asset->attachmentArray as $index => $lampiran)
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
                                                                            @role('admin')
                                                                                <a class="dropdown-item"
                                                                                    onclick="destroyFile({{ $index }}, 'physical')">Remove</a>
                                                                            @endrole
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
                                                    <span class="mr-3">No Attachment</span>
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
                                </div>
                                <div class="tab-pane pt-3 maintence" id="nav-maintence" role="tabpanel">
                                    <div class="table-responsive py-3">
                                        <table id="table-maintence" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        #
                                                    </th>
                                                    <th>
                                                        Date
                                                    </th>
                                                    <th>
                                                        Deskripsi
                                                    </th>
                                                    <th>
                                                        Status
                                                    </th>
                                                    <th>
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($asset->historyMaintence as $index => $history_maintence)
                                                    <tr>
                                                        <td>
                                                            {{ $index + 1 }}
                                                        </td>
                                                        <td>
                                                            {{ date('d F Y ', strtotime($history_maintence->date)) }}
                                                        </td>
                                                        <td>
                                                            {{ $history_maintence->description }}
                                                        </td>
                                                        <td>
                                                            @if ($history_maintence->status == 1)
                                                                <span class="badge badge-success">Kondisi Bagus</span>
                                                            @elseif($history_maintence->status == 2)
                                                                <span class="badge badge-warning">Kerusakan</span>
                                                            @elseif($history_maintence->status == 3)
                                                                <span class="badge badge-danger">Kerusakan</span>
                                                            @endif
                                                        </td>

                                                        <td align="center">
                                                            <a href="{{ route('history.maintence.show', ['id' => $history_maintence->id]) }}"
                                                                class="btn btn-sm btn-primary">Detail</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane pt-3 fade" id="nav-assign" role="tabpanel">
                                    <div class="table-responsive py-3">
                                        <table id="table-assign" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        #
                                                    </th>
                                                    <th>
                                                        Assigned At
                                                    </th>
                                                    <th>
                                                        Assigned To
                                                    </th>
                                                    <th>
                                                        Returned At
                                                    </th>
                                                    <th>
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($asset->historyAssign as $index => $history_assign)
                                                    <tr>
                                                        <td>
                                                            {{ $index + 1 }}
                                                        </td>
                                                        <td>
                                                            {{ date('d F Y H:i:s', strtotime($history_assign->assign_at)) }}
                                                        </td>
                                                        <td>
                                                            {{ $history_assign->assignTo->nama }}
                                                        </td>
                                                        <td>
                                                            {{ !is_null($history_assign->return_at) ? date('d F Y H:i:s', strtotime($history_assign->return_at)) : '-' }}
                                                        </td>
                                                        <td align="center">
                                                            <a href="{{ route('history.assign.show', ['id' => $history_assign->id]) }}"
                                                                class="btn btn-sm btn-primary">Detail</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane pt-3 fade" id="nav-check" role="tabpanel">
                                    <div class="table-responsive py-3">
                                        <table id="table-check" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        #
                                                    </th>
                                                    <th>
                                                        Check Out At
                                                    </th>
                                                    <th>
                                                        Check Out By
                                                    </th>
                                                    <th>
                                                        Check In At
                                                    </th>

                                                    <th>
                                                        Check In By
                                                    </th>
                                                    <th>
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($asset->historyCheck as $index => $history_check)
                                                    <tr>
                                                        <td>
                                                            {{ $index + 1 }}
                                                        </td>
                                                        <td>
                                                            {{ date('d F Y H:i:s', strtotime($history_check->check_out_at)) }}
                                                        </td>
                                                        <td>
                                                            {{ $history_check->checkOut->nama }}
                                                        </td>
                                                        <td>
                                                            {{ !is_null($history_check->check_in_at) ? date('d F Y H:i:s', strtotime($history_check->check_in_at)) : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ !is_null($history_check->checkIn) ? $history_check->checkIn->nama : '-' }}
                                                        </td>
                                                        <td align="center">
                                                            <a href="{{ route('history.checkout.show', ['id' => $history_check->id]) }}"
                                                                class="btn btn-sm btn-primary">Detail</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex pt-3 gap-2">
                                <a href="{{ route('asset.physical.index') }}" class="btn btn-danger mr-2">Back</a>
                                @role('admin')
                                    @if ($asset->status != 4)
                                        @if (is_null($asset->ditugaskan_ke) &&
                                                is_null($asset->assign_at) &&
                                                (is_null($asset->dipinjam_oleh) && is_null($asset->check_out_at)))
                                            <button class="btn btn-warning mr-2" data-toggle="modal"
                                                data-target="#maintence">Maintence</button>
                                            @if (in_array($asset->status, [1, 2]))
                                                <button data-toggle="modal" data-target="#assignTo"
                                                    class="btn btn-primary">Assign
                                                    To</button>
                                            @endif
                                        @elseif(
                                            !is_null($asset->ditugaskan_ke) &&
                                                !is_null($asset->assign_at) &&
                                                (is_null($asset->dipinjam_oleh) && is_null($asset->check_out_at)))
                                            <button data-toggle="modal" data-target="#returnAsset"
                                                class="btn btn-primary">Return
                                                Asset</button>
                                        @endif
                                    @endif
                                @endrole
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Attachment Asset --}}
    <div class="modal fade" id="addAttachment">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post" id="add-lampiran"
                    action="{{ route('asset.physical.uploadImage', ['id' => $asset->id]) }}" class="forms-upload"
                    enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle">Add Attachment</h4>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="date">Attachment <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="lampiran[]" id="documentInput"
                                accept="image/*;capture=camera" multiple="true" multiple="true" required>
                            <p class="text-danger py-1">* .png .jpg .jpeg</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary mx-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Assign To --}}
    <div class="modal fade" id="assignTo">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" id="assign-form"
                    action="{{ route('asset.physical.assignTo', ['id' => $asset->id]) }}" class="forms-control"
                    enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle">Add Assign and Proof Assign</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ditugaskan_ke">Assign To <span class="text-danger">*</span></label>
                            <select class="form-control select2bs4" id="ditugaskan_ke" name="ditugaskan_ke">
                                <option hidden disabled selected>Choose Staff</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="lampiran">Proof Assign <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="lampiran[]" id="documentInput"
                                accept="image/*;capture=camera" multiple="true" required>
                            <p class="text-danger py-1">* .png .jpg .jpeg</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary mx-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Return Asset --}}
    <div class="modal fade" id="returnAsset">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" id="return-asset"
                    action="{{ route('asset.physical.returnAsset', ['id' => $asset->id]) }}" class="forms-control"
                    enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle">Return and Proof Asset</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="lampiran">Proof Return <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="lampiran[]" id="documentInput"
                                accept="image/*;capture=camera" multiple="true" required>
                            <p class="text-danger py-1">* .png .jpg .jpeg</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary mx-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Maintence Asset --}}
    <div class="modal fade" id="maintence">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" id="maintence-form"
                    action="{{ route('asset.physical.maintence', ['id' => $asset->id]) }}" class="forms-control"
                    enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle">Tambah Perbaikan Aset</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="deskripsi">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date"
                                min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control select2bs4" id="status" name="status" required>
                                <option disabled hidden selected>Pilih Status</option>
                                <option value="1">
                                    Kondisi Bagus</option>
                                <option value="2"> Sedikit
                                </option>
                                <option value="3"> Banyak
                                </option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi <span class="text-danger">*</span></label>
                            <textarea cols="10" rows="3" name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="lampiran">Bukti Perbaikan <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="lampiran[]" id="documentInput"
                                accept="image/*;capture=camera" multiple="true" required>
                            <p class="text-danger py-1">* .png .jpg .jpeg</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary mx-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('javascript.asset.physical.script')
    @endpush


@endsection
