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
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">

                    </div>
                    <div class="card-body">
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
                    '?tanggal_mulai=' + formattedStartDate + '&tanggal_selesai=' + formattedEndDate;

                console.log(apiUrl);

                // Ambil data dari API
                fetch(apiUrl)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        // Parsing data respons API ke dalam format yang dapat digunakan oleh FullCalendar
                        var events = data.result.map(function(item) {
                            console.log(item);
                            if (!item.kalender[0]) {
                                return {
                                    null: null,
                                };
                            } else {
                                console.log('Ada data');
                                return {
                                    id: item.tanggal,
                                    title: item.kalender[0].penamaan_hari_bali,
                                    start: item.tanggal,
                                    end: item.tanggal,
                                };
                            }
                        });

                        // Panggil callback sukses dan tambahkan event ke kalender
                        successCallback(events);
                    })
                    .catch(function(error) {
                        // Panggil callback gagal jika terjadi error
                        failureCallback(error);
                    });
            },
        });

        calendar.render();
    });
</script>
@endsection