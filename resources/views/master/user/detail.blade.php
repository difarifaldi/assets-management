@extends('layouts.section')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">Detail User - {{ $user->name }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" data-toggle="tab" data-target="#nav-detail"
                                        type="button" role="tab" aria-controls="nav-detail"
                                        aria-selected="true">Detail</button>
                                    <button class="nav-link" data-toggle="tab" data-target="#nav-assets" type="button"
                                        role="tab" aria-controls="nav-maintence" aria-selected="false">List of
                                        Assets</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane pt-3 fade show active" id="nav-detail" role="tabpanel">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="nik">NIK </label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $user->nik }}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="name">Name </label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="email">Email </label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Username</label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $user->username }}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="phone">Phone </label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $user->phone }}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="division">Division </label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $user->division->name ?? '-' }}
                                        </div>
                                    </div>
                                    @role('admin')
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="roles">Role </label>
                                            <div class="col-sm-9 col-form-label">
                                                {{ $user_role }}
                                            </div>
                                        </div>
                                    @endrole
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label" for="address">Address </label>
                                        <div class="col-sm-9 col-form-label">
                                            {{ $user->address ? $user->address : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane pt-3 assets" id="nav-assets" role="tabpanel">
                                    <div class="table-responsive py-3">
                                        <table class="table table-bordered datatable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        #
                                                    </th>
                                                    <th>
                                                        Asset
                                                    </th>
                                                    <th>
                                                        Barcode
                                                    </th>
                                                    <th>
                                                        Category
                                                    </th>
                                                    <th>
                                                        Status
                                                    </th>
                                                    <th>
                                                        Value
                                                    </th>
                                                    <th>
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            @php
                                                $total_value = 0;
                                            @endphp
                                            <tbody>
                                                @foreach ($user->asset as $index => $asset)
                                                    <tr>
                                                        <td>
                                                            {{ $index + 1 }}
                                                        </td>
                                                        <td>
                                                            {{ $asset->name }}
                                                        </td>
                                                        <td>
                                                            {{ $asset->barcode_code }}
                                                        </td>
                                                        <td>
                                                            {{ $asset->category->name }}
                                                        </td>
                                                        <td>
                                                            @if ($asset->status == 1)
                                                                <span class="badge badge-success">Good Condition</span>
                                                            @elseif($asset->status == 2)
                                                                <span class="badge badge-warning">Minor Damage</span>
                                                            @elseif($asset->status == 3)
                                                                <span class="badge badge-danger">Major Damage</span>
                                                            @endif
                                                        </td>
                                                        <td align="right">
                                                            @php
                                                                $total_value += intval($asset->value);
                                                            @endphp
                                                            {{ 'Rp.' . number_format($asset->value, 0, ',', '.') . ',00' }}
                                                        </td>
                                                        <td>
                                                            @if ($asset->type == 1)
                                                                <a href="{{ route('asset.physical.show', ['id' => $asset->id]) }}"
                                                                    class="btn btn-sm btn-primary">Detail</a>
                                                            @else
                                                                <a href="{{ route('asset.license.show', ['id' => $asset->id]) }}"
                                                                    class="btn btn-sm btn-primary">Detail</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" align="right">
                                                        <label>Total Value</label>
                                                    </td>
                                                    <td align="right">
                                                        {{ 'Rp.' . number_format($total_value, 0, ',', '.') . ',00' }}
                                                    </td>
                                                    <td>
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-3">
                                <a href="{{ route('master.user.index') }}" class="btn btn-danger">Back</a>
                            </div>
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
