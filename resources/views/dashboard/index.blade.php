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
                            <li class="breadcrumb-item"><a href="#!">Tabel Hari Raya</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ Hover-table ] start -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Tabel Hari Raya</h5>
                        <span class="d-block m-t-5">tabel yang menyajikan hasil perhitungan kalender bali untuk mencari piodalan tanggal 
                        <i>{{ $kalender[0]['tanggal'] }} sampai {{ $kalender[count($kalender) - 1]['tanggal'] }}</i> </span>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Hari Raya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kalender as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['tanggal'] }}</td>
                                        <td>
                                            @foreach($item['hariRaya'] as $hariRaya)
                                            {{ $hariRaya }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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