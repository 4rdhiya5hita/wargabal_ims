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
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mt-5">Pencarian Tanggal</h5>
                        <hr>
                        
                        <form action="{{ route('process_search_hari_raya') }}" method="get" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="validationTooltip03">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="validationTooltip03" placeholder="Tanggal Mulai" required>
                                    <div class="invalid-tooltip">
                                        Sertakan Tanggal Mulai yang benar.
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationTooltip03">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="validationTooltip03" placeholder="Tanggal Selesai" required>
                                    <div class="invalid-tooltip">
                                        Sertakan Tanggal Selesai yang benar.
                                    </div>
                                </div>
                            </div>
                            <button class="btn  btn-primary" type="submit">Submit form</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ Hover-table ] start -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Tabel Hari Raya</h5>
                        <span class="d-block m-t-5">Contoh Hasil Perhitungan: <br>
                            Berikut hasil perhitungan kalender bali untuk mencari piodalan dari tanggal
                            <i> 2023-07-15 sampai 2023-07-17</i> </span>
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
                                    <tr>
                                        <td>1.</td>
                                        <td>2023-07-15</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td>2023-07-16</td>
                                        <td>-</td>
                                    <tr>
                                    </tr>
                                    <td>3.</td>
                                    <td>2023-07-17</td>
                                    <td>Hari Raya Tilem</td>
                                    </tr>
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