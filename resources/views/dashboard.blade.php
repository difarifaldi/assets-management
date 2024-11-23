@extends('layouts.section')
@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info py-2">
                    <div class="inner">
                        <h5>
                            <sup style="font-size: 10px">Rp </sup>{{ number_format($totalValue, 0, ',', '.') }}
                        </h5>
                        <p><b>Total Nilai</b></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-cash"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success py-2">
                    <div class="inner">
                        <h5>{{ $allAsset }}
                        </h5>
                        <p><b>Jumlah Semua Aset</b></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-archive"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning py-2">
                    <div class="inner">
                        <h5>{{ $physical }}</h5>
                        <p><b>Aset Fisik</b></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-cube"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger py-2">
                    <div class="inner">
                        <h5>{{ $license }}</h5>
                        <p><b>Aset Lisensi</b></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-document"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
        </div>

    </div>
@endsection
