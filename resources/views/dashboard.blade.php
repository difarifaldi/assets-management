@extends('layouts.section')
@section('content')
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h5>
                            <sup style="font-size: 10px">Rp </sup>{{ number_format($totalValue, 0, ',', '.') }}
                        </h5>


                        <p>Total Value</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-cash"></i>
                    </div>

                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h5>{{ $allAsset }}
                        </h5>

                        <p>All Asset</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-archive"></i>
                    </div>

                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h5>{{ $physical }}</h5>

                        <p>Physical Asset</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-cube"></i>
                    </div>

                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h5>{{ $license }}</h5>

                        <p>License Asset</p>
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
