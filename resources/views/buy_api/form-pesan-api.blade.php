@extends('layout.app')

@section('content')
<section class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Tabel Dasar</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Tabel Pembelian API</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="card">
            <!-- [ Hover-table ] start -->
            <div class="card-body">
                <div class="row">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="{{ asset('../template/assets/images/chart.png') }}" alt="">
                        </div>
                        <div class="col">
                            <div class="card">
                                <div class="card-header text-center">
                                    <h1>35k<sub>
                                            <h5>/12 bulan</h5>
                                        </sub></h1>
                                </div>
                                <div class="card-body pl-5">
                                    <div class="row">
                                        <div class="ml-2 col-xs-2">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="col-md-10">
                                            <p>2500 hit per hari</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="ml-2 col-xs-2">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="col-md-10">
                                            <p>full akses</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="ml-2 col-xs-2">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="col-md-10">
                                            <p>API: hari raya hindu, dewasa ayu, kalender bali, otonan</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h5>User {{ Auth::user()->name }}</h5>
                                </div>
                            </div>
                            <div class="card latest-update-card">
                                <div class="card-header">
                                    <h5>Detail Transaksi</h5>
                                    <div class="card-header-right">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="latest-update-box">
                                        <div class="row p-t-30 p-b-30">
                                            <div class="col-auto text-right update-meta">
                                                <p class="text-muted m-b-0 d-inline-flex">08/09/23</p>
                                                <i class="fas fa-list bg-twitter update-icon"></i>
                                            </div>
                                            <div class="col">
                                                <a href="#!">
                                                    <h6>Buat Pesanan</h6>
                                                </a>
                                                <p class="text-muted m-b-0">Nama Paket: Paket Full Akses 12 Bulan</p>
                                                <p class="text-muted m-b-0">Harga: Rp. 35.000</p>
                                                <button class="btn btn-sm btn-success mt-2">
                                                    Buat Pesanan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Hover-table ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>
@endsection