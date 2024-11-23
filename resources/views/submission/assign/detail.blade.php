@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail Pengajuan Penugasan Aset -
                                    #{{ $submission->id }}
                            </div>
                        </div>
                        <!-- form start -->
                        <div class="card-body">
                            <div class="col-md-12">
                                <!-- Date and Time -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pengajuan Tanggal dan Waktu</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ date('d F Y H:i:s', strtotime($submission->created_at)) }}
                                    </div>
                                </div>
                                <!-- Description -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Deskripsi</label>
                                    <div class="col-sm-9 col-form-label">
                                        {{ $submission->deskripsi ?? '-' }}
                                    </div>
                                </div>
                                <!-- Attachment -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Lampiran</label>
                                    <div class="col-sm-9 col-form-label">
                                        @if (!is_null($submission->lampiran))
                                            <a href="{{ asset($submission->lampiran) }}" target="_blank">
                                                <i class="fas fa-download mr-1"></i>
                                                Lampiran Dokumen
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <!-- Status -->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Approval Status</label>
                                    <div class="col-sm-9 col-form-label">
                                        @if (!is_null($submission->diterima_oleh) && !is_null($submission->diterima_pada))
                                            <span class="badge badge-success">Approved By
                                                {{ $submission->approvedBy->nama }}</span>
                                        @elseif(!is_null($submission->ditolak_oleh) && !is_null($submission->ditolak_pada))
                                            <span class="badge badge-danger">Rejected By
                                                {{ $submission->rejectedBy->nama }}</span>
                                        @else
                                            <span class="badge badge-warning">Process</span>
                                        @endif
                                    </div>
                                </div>
                                @if (!is_null($submission->ditolak_oleh) && !is_null($submission->ditolak_pada))
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Alasan Penolakan</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $submission->alasan ?? '-' }}
                                        </div>
                                    </div>
                                @endif
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered datatable" id="asset">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Aset
                                                </th>
                                                <th>
                                                    Barcode
                                                </th>
                                                <th>
                                                    Kategori
                                                </th>
                                                <th>
                                                    Status Aset
                                                </th>
                                                <th>
                                                    Status Penugasan
                                                </th>
                                                @role('admin')
                                                    @if (!is_null($submission->diterima_oleh) && !is_null($submission->diterima_pada))
                                                        <th width="10%">
                                                            Aksi
                                                        </th>
                                                    @endif
                                                @endrole
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">
                                            @foreach ($submission->submissionFormItemAsset as $item_asset)
                                                <tr>
                                                    <td>
                                                        {{ $item_asset->asset->nama }}
                                                    </td>
                                                    <td>
                                                        {{ $item_asset->asset->barcode_code }}
                                                    </td>
                                                    <td>
                                                        {{ $item_asset->asset->kategori->nama }}
                                                    </td>
                                                    <td>
                                                        @if ($item_asset->asset->status == 1)
                                                            <span class="badge badge-success">Kondisi Bagus</span>
                                                        @elseif($item_asset->asset->status == 2)
                                                            <span class="badge badge-warning">Kerusakan Ringan</span>
                                                        @elseif($item_asset->asset->status == 3)
                                                            <span class="badge badge-danger">Kerusakan Berat</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!is_null($submission->historyAssign->where('id_aset', $item_asset->asset->id)->first()))
                                                            <span class="badge badge-success">Ditugaskan</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    @role('admin')
                                                        @if (
                                                            !is_null($submission->diterima_oleh) &&
                                                                !is_null($submission->diterima_pada) &&
                                                                is_null($submission->historyAssign->where('id_aset', $item_asset->asset->id)->first()))
                                                            <td>
                                                                @if ($item_asset->asset->status != 4 && $item_asset->asset->status != 5)
                                                                    @if (is_null($item_asset->asset->ditugaskan_ke) &&
                                                                            is_null($item_asset->asset->ditugaskan_pada) &&
                                                                            (is_null($item_asset->asset->dipinjam_oleh) && is_null($item_asset->asset->check_out_at)))
                                                                        <button class="btn btn-sm btn-primary"
                                                                            data-toggle="modal"
                                                                            data-target="#assign_to_{{ $item_asset->asset->id }}">Assigning</button>
                                                                    @else
                                                                        <span class="badge badge-danger">Unavailable</span>
                                                                    @endif
                                                                @elseif($item_asset->asset->status == 4)
                                                                    <span class="badge badge-danger">On Maintence</span>
                                                                @elseif($item_asset->asset->status == 5)
                                                                    <span class="badge badge-danger">License Expired</span>
                                                                @endif
                                                            </td>
                                                        @elseif (!is_null($submission->historyAssign->where('id_aset', $item_asset->asset->id)->first()))
                                                            <td>-</td>
                                                        @endif
                                                    @endrole
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between py-3">
                                <a href="{{ route('submission.index') }}" class="btn btn-danger">Back</a>
                                @role('admin')
                                    <div class="d-flex">
                                        @if (is_null($submission->diterima_oleh) &&
                                                is_null($submission->diterima_pada) &&
                                                is_null($submission->ditolak_oleh) &&
                                                is_null($submission->ditolak_pada))
                                            <button class="btn btn-sm btn-danger ml-2"
                                                onclick="rejectedRecord({{ $submission->id }})"
                                                title="Rejected">Rejected</button>
                                            <button class="btn btn-sm btn-success ml-2"
                                                onclick="approvedRecord({{ $submission->id }})"
                                                title="Approve">Approve</button>
                                        @endif
                                    </div>
                                @endrole
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach ($submission->submissionFormItemAsset as $item_asset)
        {{-- Assign To --}}
        <div class="modal fade" id="assign_to_{{ $item_asset->asset->id }}">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="POST" id="assign-form-{{ $item_asset->asset->id }}"
                        action="{{ route('submission.assignTo', ['id' => $submission->id]) }}" class="forms-control"
                        enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="id_aset" value="{{ $item_asset->asset->id }}">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLongTitle">Add Assign and Proof Assign</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="attachment">Proof Assign <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="attachment[]" id="documentInput"
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
    @endforeach
    @push('javascript-bottom')
        @include('javascript.submission.script')
    @endpush
@endsection
