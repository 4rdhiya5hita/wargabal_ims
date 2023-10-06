@extends('layout.app')

@section('content')
<section class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Dashboard Analytics</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Dashboard Analytics</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <span class="pcoded-micon"><i class="feather icon-calendar"></i></span><span class="pcoded-mtext"> Website informasi Kalender Bali</span></i>
                    </div>
                    <div class="card-body">
                        <div style="text-align: center;" id='calendar'></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <span class="pcoded-micon"><i class="feather icon-info"></i></span><span class="pcoded-mtext" id='tanggal'> Informasi Tanggal <b> {{$tanggal_now}} Hari Ini </b> </span></i>
                    </div>
                    <div class="card-body">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header">
                                            Hari Raya
                                        </div>
                                        <div class="card-body" id='hari_raya'>
                                            {{ $hari_raya_now }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header">
                                            Penamaan Hari Bali
                                        </div>
                                        <div class="card-body" id='hari_bali'>
                                            {{ $penamaan_hari_bali_now }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col" id='keterangan'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
</section>

<script>
    function formatDateToYMD(dateString) {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Menggunakan padStart untuk memastikan 2 digit
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: function(fetchInfo, successCallback, failureCallback) {
                // Dapatkan tanggal awal dan akhir dari kalender
                var startDate = fetchInfo.startStr;
                var endDate = fetchInfo.endStr;
                var formattedStartDate = formatDateToYMD(startDate);
                var formattedEndDate = formatDateToYMD(endDate);
                // console.log(formattedStartDate);

                // Buat URL dengan tanggal awal dan akhir
                var apiUrl = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI' +
                    '?tanggal_mulai=' + formattedStartDate + '&tanggal_selesai=' + formattedEndDate + '&makna&pura';

                // console.log(apiUrl);

                // Ambil data dari API
                fetch(apiUrl)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        // Parsing data respons API ke dalam format yang dapat digunakan oleh FullCalendar
                        var events = [];
                        data.result.forEach(function(item) {
                            if (item.kalender[0] && item.kalender[0].hari_raya) {
                                // Tambahkan acara pertama
                                events.push({
                                    id: item.tanggal + '-1', // Gunakan id yang unik
                                    title: item.kalender[0].hari_raya,
                                    penamaan_hari_bali: item.kalender[0].penamaan_hari_bali,
                                    makna: item.kalender[0].makna,
                                    pura: item.kalender[0].pura,
                                    start: item.tanggal,
                                    end: item.tanggal,
                                });

                                // Tambahkan acara kedua (jika ada)
                                if (item.kalender[1] && item.kalender[1].hari_raya) {
                                    events.push({
                                        id: item.tanggal + '-2', // Gunakan id yang unik
                                        title: item.kalender[1].hari_raya,
                                        penamaan_hari_bali: item.kalender[1].penamaan_hari_bali,
                                        makna: item.kalender[1].makna,
                                        pura: item.kalender[1].pura,
                                        start: item.tanggal,
                                        end: item.tanggal,
                                    });
                                }

                                if (item.kalender[2] && item.kalender[2].hari_raya) {
                                    events.push({
                                        id: item.tanggal + '-3', // Gunakan id yang unik
                                        title: item.kalender[2].hari_raya,
                                        penamaan_hari_bali: item.kalender[2].penamaan_hari_bali,
                                        makna: item.kalender[2].makna,
                                        pura: item.kalender[2].pura,
                                        start: item.tanggal,
                                        end: item.tanggal,
                                    });
                                }

                                if (item.kalender[3] && item.kalender[3].hari_raya) {
                                    events.push({
                                        id: item.tanggal + '-4', // Gunakan id yang unik
                                        title: item.kalender[3].hari_raya,
                                        penamaan_hari_bali: item.kalender[3].penamaan_hari_bali,
                                        makna: item.kalender[3].makna,
                                        pura: item.kalender[3].pura,
                                        start: item.tanggal,
                                        end: item.tanggal,
                                    });
                                }
                            }
                        })
                        // console.log(events);
                        // Panggil callback sukses dan tambahkan event ke kalender
                        successCallback(events);
                    })
                    .catch(function(error) {
                        // Panggil callback gagal jika terjadi error
                        failureCallback(error);
                    });
            },

            eventClick: function(info) {
                var date = info.event.start;
                var formattedDate = formatDateToYMD(date);

                var title = info.event.title;
                var hari_raya = document.getElementById('hari_raya');
                var hari_bali = document.getElementById('hari_bali');
                var keterangan = document.getElementById('keterangan');
                var tanggal = document.getElementById('tanggal');

                var penamaan_hari_bali = info.event.extendedProps.penamaan_hari_bali;
                var makna = info.event.extendedProps.makna;
                var pura = info.event.extendedProps.pura;
                // console.log(info.event);

                hari_raya.innerHTML = title;
                hari_bali.innerHTML = penamaan_hari_bali;
                keterangan.innerHTML = '<div class="card"><div class="card-header">Makna</div><div class="card-body">' + makna + '</div></div><div class="card"><div class="card-header">Pura</div><div class="card-body">' + pura + '</div></div>';
                tanggal.innerHTML = '<span class="pcoded-micon"><i class="feather icon-info"></i></span><span class="pcoded-mtext"> Informasi Tanggal <b> ' + formattedDate + '</b></span></i>';

            }
        });

        calendar.render();
    });
</script>
@endsection