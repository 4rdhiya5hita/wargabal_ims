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
        <div class="row justify-content-center">
            <!-- [ Hover-table ] start -->
            @foreach ($pakets as $paket)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>{{ $paket->harga }}<sub>
                                <h5>/{{ $paket->rentang }} bulan</h5>
                            </sub></h1>
                    </div>
                    <div class="card-body pl-5">
                        <div class="row">
                            <div class="ml-2 col-xs-2">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="col-md-10">
                                <p>
                                    @if( $paket->hit == "2147483647") 
                                    unlimited
                                    @else
                                    {{ $paket->hit }}
                                    @endif
                                    hit per hari</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ml-2 col-xs-2">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="col-md-10">
                                <p>{{ $paket->relation_akses->akses }} akses</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="ml-2 col-xs-2">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="col-md-10">
                                <p>API: 
                                    @if( $paket->relation_akses->akses == "full")
                                    hari raya hindu, dewasa ayu, kalender bali, otonan
                                    @elseif( $paket->relation_akses->akses == "hariRaya")
                                    hari raya hindu
                                    @elseif( $paket->relation_akses->akses == "dewasaAyu")
                                    dewasa ayu
                                    @elseif( $paket->relation_akses->akses == "kalenderBali")
                                    kalender bali
                                    @elseif( $paket->relation_akses->akses == "otonan")
                                    otonan
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card text-center">

                        @if (Auth::check())
                        <!-- <a class="btn btn-primary" href="{{ route('order_form', ['description'=> 'Beli API $variabel hit']) }}">Beli API ini</a> -->
                        <a class="btn btn-primary" href="api/payment/create?selected_item={{ $paket->id }}&description=Beli API {{ $paket->hit }} hit, rentang {{ $paket->rentang }} bulan &amount={{ $paket->harga }}">Beli API ini</a>
                        @else
                        <a class="btn btn-dark" href="{{ route('login') }}">Login untuk Melanjutkan</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            <!-- <div class="col-lg">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>95k<sub>
                                <h5>/12 bulan</h5>
                            </sub></h1>
                    </div>
                    <div class="card-body pl-5">
                        <div class="row">
                            <div class="ml-2 col-xs-2">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="col-md-10">
                                <p>10000 hit per hari</p>
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
                    <div class="card text-center">
                        @if (Auth::check())
                        <a class="btn btn-primary" href="{{ route('order_form') }}">Beli API ini</a>
                        @else
                        <a class="btn btn-dark" href="{{ route('login') }}">Login untuk Melanjutkan</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg">
                <div class="card">
                    <div class="card-header text-center">
                        <h1>165k<sub>
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
                    <div class="card text-center">

                        @if (Auth::check())
                        <a class="btn btn-primary" href="{{ route('order_form') }}">Beli API ini</a>
                        @else
                        <a class="btn btn-dark" href="{{ route('login') }}">Login untuk Melanjutkan</a>
                        @endif
                    </div>
                </div>
            </div> -->
            <!-- [ Hover-table ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>
@endsection