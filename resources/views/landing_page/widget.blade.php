@extends('layout_landing_page.app')

@section('style')
<link href="{{ asset('css/widget/widget.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/widget/popup_widget.css') }}" media="screen" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/widget/jquery.ui.css') }}" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{{ asset('js/widget/jquery-1.8.0.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/widget/app.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/widget/jquery.ui.core.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/widget/jquery.cycle.all.js') }}"></script>
@endsection

@section('content')
<!-- <div id="middleOnly"> -->
<div class="col-md-5" style="padding-left: 6.5em;">
    <div class="container">
        <div class="row" id="kolomKeterangan">
            <div class="row landing-title1">Kalender Bali</div>
            <div class="row landing-description">
                Kalender Bali adalah sistem penanggalan tradisional yang digunakan oleh masyarakat Bali di Indonesia.
                dimana Kalender Bali mencerminkan kekayaan budaya dan tradisi Hindu di Bali, dan banyak acara keagamaan dan budaya diatur berdasarkan perhitungan waktu dalam kalender ini.
            </div>
        </div>
        <!-- <div class="row">
        <div class="btn btn-primary rounded-pill" style="font-size: 12px;" id="ClickButton">Coba Klik Disini!</div>
    </div> -->

        <div id="fitur" style="display: none;">
            <div class="landing-title2 mt-3">Pilih Fitur</div>
            <div class="row landing-description mt-2">
                <form>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fiturKalender" id="fiturKalender" value="option1">
                        <label class="form-check-label" for="fiturKalender">
                            Kalender Bali
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fiturHariRaya" id="fiturHariRaya" value="option2">
                        <label class="form-check-label" for="fiturHariRaya">
                            Hari Raya
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fiturAlaAyuningDewasa" id="fiturAlaAyuningDewasa" value="option3">
                        <label class="form-check-label" for="fiturAlaAyuningDewasa">
                            Dewasa Ayu
                        </label>
                    </div>
                </form>
            </div>
            <div class="landing-text fst-italic">Silahkan tekan tanggal yang diinginkan!</div>
        </div>
    </div>
</div>

<div class="col-md-6 my-5">
    <div class="table">
        <div class="atas mb-1 py-2">
            <table width="248" class="kalender1">
                <tr align="center">
                    <td width="15%"><a href="#" id="mundur"><img src="https://www.kalenderbali.info/asset/img/widget/prev.png" alt="mundur" border="0" /></a></td>
                    <td>
                        <div class="judul1" id="kolomBulan"></div>
                        <div class="judul2" id="kolomSasih"></div>
                    </td>
                    <td width="15%"><a href="#" id="maju"><img src="https://www.kalenderbali.info/asset/img/widget/next.png" alt="maju" border="0" /></a></td>
                </tr>
            </table>
        </div>


        <table class="listing" cellpadding="0" cellspacing="0" id=kolomWuku>
        </table>

        <table class="listing" cellpadding="0" cellspacing="0" id=kolomHari>
        </table>

        <table class="listing" cellpadding="0" cellspacing="0" id=kolomIngkel>
        </table>


        <div class="bawah mt-1">
            <table class="kalender1">
                <tr>
                    <td>
                        <div class="footerCal putih">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div>
    <div class="row mx-5" style="align-content:center">
        <div class="col-md-4" id="fiturKalender">
            <div class="tableFitur">
                <div class="fiturAtas">
                    <table class="kalender1">
                        <tr align="center">
                            <td>
                                <div class="judul1">Elemen Kalender Bali</div>
                                <div class="judul2" id="judul2Kalender"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="tableFitur">
                <div class="fiturListing" cellpadding="0" cellspacing="0" id="hasilKalenderBali">
                </div>
            </div>
        </div>
        <div class="col-md-4" id="fiturHariRaya">
            <div class="tableFitur">
                <div class="fiturAtas">
                    <table class="kalender1">
                        <tr align="center">
                            <td>
                                <div class="judul1">Hari Raya</div>
                                <div class="judul2" id="judul2HariRaya"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="tableFitur">
                <div class="fiturListing" cellpadding="0" cellspacing="0" id="hasilHariRaya">
                </div>
            </div>
        </div>
        <div class="col-md-4" id="fiturAlaAyuningDewasa">
            <div class="tableFitur">
                <div class="fiturAtas">
                    <table class="kalender1">
                        <tr align="center">
                            <td>
                                <div class="judul1">Dewasa Ayu</div>
                                <div class="judul2" id="judul2AlaAyuningDewasa"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="tableFitur">
                <div class="fiturListing" cellpadding="0" cellspacing="0" id="hasilAlaAyuningDewasa">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- </div> -->
@endsection

@section('content2')
@include('layout_landing_page.carousel')
@endsection




<!-- JAVASCRIPT -->
@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() { // wait until the document is loaded
        // tampilkan kalender saat halaman dimuat
        var currentDate = new Date(); // Tanggal saat ini
        updateURL(0, currentDate); // Kode untuk menambah bulan pada URL

        // Ambil elemen-elemen yang diberi ID
        var mundurButton = document.getElementById("mundur");
        var majuButton = document.getElementById("maju");
        var kolomHari = document.getElementById("kolomHari");
        var kolomWuku = document.getElementById("kolomWuku");
        var namaHari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        var uniqueWukuArray = [];
        var uniqueIngkelArray = [];


        // Tambahkan event listener untuk tombol "mundur"
        mundurButton.addEventListener("click", function(event) {
            // reset upadetURL sebelum menambahkan bulan
            kolomBulan.innerHTML = "";
            kolomSasih.innerHTML = "";
            kolomHari.innerHTML = "";
            kolomWuku.innerHTML = "";
            kolomIngkel.innerHTML = "";
            uniqueWukuArray = [];
            uniqueIngkelArray = [];
            currentDate.setMonth(currentDate.getMonth());
            updateURL(-1, currentDate); // Kode untuk menambah bulan pada URL
        });

        // Tambahkan event listener untuk tombol "maju"        
        majuButton.addEventListener("click", function(event) {
            event.preventDefault();
            // reset upadetURL sebelum menambahkan bulan
            kolomBulan.innerHTML = "";
            kolomSasih.innerHTML = "";
            kolomHari.innerHTML = "";
            kolomWuku.innerHTML = "";
            kolomIngkel.innerHTML = "";
            uniqueWukuArray = [];
            uniqueIngkelArray = [];
            currentDate.setMonth(currentDate.getMonth());
            updateURL(1, currentDate); // Kode untuk menambah bulan pada URL
        });

        // Fungsi untuk mengambil data Hari Raya dengan callback
        function updateHariRaya(newURL2, callback) {

            // fetch api
            fetch(newURL2)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    var total_hari_raya = data.result[0].kalender.length;
                    if (total_hari_raya <= 1 && data.result[0].kalender[0] === "-" || data.result[0].kalender[0] === null) {
                        total_hari_raya = 0;
                    }

                    // tambahkan * sesuai dengan total hari raya
                    for (var k = 0; k < total_hari_raya; k++) {
                        htmlUpdateTanggal += "*";
                    }
                    console.log('htmlUpdateTanggal:', htmlUpdateTanggal);

                    // Panggil callback dengan hasil htmlUpdateTanggal
                    // callback(htmlUpdateTanggal);
                });
        }

        // Fungsi yang memeriksa kondisi dan mengembalikan teks yang sesuai
        var j = 0; // Variabel j agar dapat diakses oleh fungsi cekDanTambahkanTeks
        function updateTanggal(i, hari, uniqueWukuArray, data) {
            htmlUpdateTanggal = "";

            // Iterasi melalui data untuk mencari tanggal yang sesuai
            for (var j = 0; j < data.result.length; j++) {
                if (data.result[j].kalender.wuku === uniqueWukuArray[i] && data.result[j].kalender.hari === hari) {
                    var tanda_hari = 0;
                    htmlUpdateTanggal += '<a href="#" id="tanggal_id">' + data.result[j].tanggal.split('-')[2] + '</a>';
                }
            }

            // Kembalikan string kosong jika tidak ada tanggal yang sesuai
            return htmlUpdateTanggal;
        }



        // Fungsi untuk memperbarui URL
        function updateURL(monthOffset, currentDate) {
            currentDate.setMonth(currentDate.getMonth() + monthOffset);
            var year = currentDate.getFullYear();
            var month = currentDate.getMonth() + 1; // Tambah 1 karena bulan dimulai dari 0

            // Menghitung tanggal akhir berdasarkan bulan dan tahun
            var lastDay = new Date(year, month, 0).getDate();

            // Buat URL baru dengan tanggal awal dan tanggal akhir yang sesuai
            var newURL = "http://localhost:8000/api/searchKalenderAPI?tanggal_mulai=" + year + "-" + month + "-01&tanggal_selesai=" + year + "-" + month + "-" + lastDay + "&lengkap=lengkap";
            // console.log(newURL);

            // update html bulan dan sasih
            var htmlBulan = "";
            var htmlSasih = "";
            var tahunSaka = year - 78;

            switch (month) {
                case 1:
                    htmlBulan += `Januari ${year}`;
                    htmlSasih += `Kapitu ${tahunSaka-1}`;
                    break;
                case 2:
                    htmlBulan += `Februari ${year}`;
                    htmlSasih += `Kaulu ${tahunSaka-1}`;
                    break;
                case 3:
                    htmlBulan += `Maret ${year}`;
                    htmlSasih += `Kasanga ${tahunSaka}`;
                    break;
                case 4:
                    htmlBulan += `April ${year}`;
                    htmlSasih += `Kadasa ${tahunSaka}`;
                    break;
                case 5:
                    htmlBulan += `Mei ${year}`;
                    htmlSasih += `Jyestha ${tahunSaka}`;
                    break;
                case 6:
                    htmlBulan += `Juni ${year}`;
                    htmlSasih += `Sadha ${tahunSaka}`;
                    break;
                case 7:
                    htmlBulan += `Juli ${year}`;
                    htmlSasih += `Kasa ${tahunSaka}`;
                    break;
                case 8:
                    htmlBulan += `Agustus ${year}`;
                    htmlSasih += `Karo ${tahunSaka}`;
                    break;
                case 9:
                    htmlBulan += `September ${year}`;
                    htmlSasih += `Katiga ${tahunSaka}`;
                    break;
                case 10:
                    htmlBulan += `Oktober ${year}`;
                    htmlSasih += `Kapat ${tahunSaka}`;
                    break;
                case 11:
                    htmlBulan += `November ${year}`;
                    htmlSasih += `Kalima ${tahunSaka}`;
                    break;
                case 12:
                    htmlBulan += `Desember ${year}`;
                    htmlSasih += `Kanam ${tahunSaka}`;
                    break;
            }

            kolomBulan.innerHTML += htmlBulan;
            kolomSasih.innerHTML += htmlSasih;

            // fetch api
            fetch(newURL)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {

                    var htmlWuku = "";
                    htmlWuku += `
                                <tr height="50">
                                    <td class="first model1" width="80">
                                        <table class="kalender1">
                                            <tr>
                                                <td class="judulSamping hitam">
                                                    WUKU
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                `;

                    var htmlIngkel = "";
                    htmlIngkel += `
                                <tr height="50">
                                    <td class="first model1" width="80">
                                        <table class="kalender1">
                                            <tr>
                                                <td class="judulSamping merah">
                                                    INGKEL
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                `;

                    data.result.forEach(function(item) {
                        const wuku = item.kalender['wuku'];
                        const ingkel = item.kalender['ingkel'];

                        // Cek apakah nilai wuku sudah ada dalam uniqueWukuArray
                        if (!uniqueWukuArray.includes(wuku)) {
                            uniqueWukuArray.push(wuku);
                        }

                        // Cek apakah nilai ingkel sudah ada dalam uniqueIngkelArray
                        if (!uniqueIngkelArray.includes(ingkel)) {
                            uniqueIngkelArray.push(ingkel);
                        }

                    });

                    // Looping untuk membuat kolom wuku
                    uniqueWukuArray.forEach(function(wuku) {
                        htmlWuku += `
                                    <td width="80">
                                        <table class="kalender1">
                                            <tr>
                                                <td class="judulAtas hitam">
                                                    ${wuku}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    `;
                    });
                    htmlWuku += `</tr>`;
                    kolomWuku.innerHTML += htmlWuku;


                    uniqueIngkelArray.forEach(function(ingkel) {
                        htmlIngkel += `
                            <td class="first model5" width="80">${ingkel}</td>
                        `;
                    });
                    htmlIngkel += `</tr>`;
                    kolomIngkel.innerHTML += htmlIngkel;


                    // Parsing data respons API ke dalam format yang dapat digunakan oleh FullCalendar
                    namaHari.forEach(function(hari) {
                        var html = ""; // Inisialisasi variabel html di dalam loop
                        html += `
                            <tr height="50">
                                <td class="first model1" width="80">
                                    <table class="kalender1">
                                        <tr>
                                            <td class="judulSamping ${hari === 'Minggu' ? 'merah' : 'hitam'}">
                                                ${hari}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="isi merah">
                                                <img src="https://www.kalenderbali.info/asset/img/widget/hari/day0.png" alt="nama hari" />
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            `;


                        for (var i = 0; i < uniqueWukuArray.length; i++) {
                            var j = 0;
                            // console.log('hari', hari);
                            // console.log('wuku:', uniqueWukuArray[i]);
                            // console.log('i:', i);
                            html += `
                                    <td class="first model2" width="80">
                                        <table class="kalenderCell">
                                            <tr>
                                                <td colspan="2" class="isitanggal ${hari === 'Minggu' ? 'merah' : 'hitam'} tengah">
                                                    <span class="${hari === 'Minggu' ? 'merah' : 'hitam'}" >
                                                    ${updateTanggal(i, hari, uniqueWukuArray, data)}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                `;
                        }
                        html += `</tr>`;

                        kolomHari.innerHTML += html; // Tambahkan html yang telah dibangun di dalam loop ini

                    });

                    // Tambahkan tanggal ke samping nama harinya
                });

        }
    });

    $(document).on('click', '#ClickButton', function() {
        // munculkan fitur
        if ($('#fitur').is(':visible')) {
            $('#fitur').hide();
            $('#ClickButton').text("Aktifkan fitur kembali!");

            buttonChecked = [];
            $('input[type="checkbox"]').prop('checked', false);
            // console.log('seluruh false:', buttonChecked);

        } else if ($('#fitur').is(':hidden')) {
            $('#fitur').show();
            $('#ClickButton').text("Matikan fungsi fitur ini!");

            var buttonChecked = [];
            $('input[type="checkbox"]').change(function() {
                var checkboxId = $(this).attr('id');

                // cek apakah checkbox tersebut dicentang atau tidak
                if ($(this).prop('checked')) {
                    // cek apakah ada string buttonChecked yang sama dengan checkboxId
                    if (!buttonChecked.includes(checkboxId)) {
                        // jika tidak ada tambahkan string tersebut ke array buttonChecked
                        buttonChecked.push(checkboxId);
                    }
                } else {
                    // jika ada hapus string tersebut dari array buttonChecked
                    buttonChecked.splice(buttonChecked.indexOf(checkboxId), 1);
                }
                // console.log(buttonChecked);
            });
        }
    });


    $(document).on('click', '#tanggal_id', function() {
        // ubah warna ketika hover jika tidak kembali ke warna semula
        $('a').css('color', 'black');
        $(this).css('color', 'blue');

        // hapus html elemen kalender yang lama
        $('#hasilKalenderBali').empty();
        $('#hasilHariRaya').empty();
        $('#hasilAlaAyuningDewasa').empty();
        $('#judul2Kalender').empty();
        $('#judul2HariRaya').empty();
        $('#judul2AlaAyuningDewasa').empty();

        // buat html elemen kalender yang baru
        var hasilKalenderBali = document.getElementById("hasilKalenderBali");
        var hasilHariRaya = document.getElementById("hasilHariRaya");
        var hasilAlaAyuningDewasa = document.getElementById("hasilAlaAyuningDewasa");
        var tanggalKalender = document.getElementById("judul2Kalender");
        var tanggalHariRaya = document.getElementById("judul2HariRaya");
        var tanggalAlaAyuningDewasa = document.getElementById("judul2AlaAyuningDewasa");
        var htmlElemenKalender = "";
        var htmlAlaAyuningDewasa = "";
        var htmlHariRaya = "";
        var htmlTanggalKalender = "";

        // ambil tanggal
        var tanggal = $(this).text();
        // ambil bulan
        var data_bulan = $('#kolomBulan').text();
        var bulan = data_bulan.split(' ')[0];
        // jadikan bulan menjadi angka
        switch (bulan) {
            case 'Januari':
                bulan = '01';
                break;
            case 'Februari':
                bulan = '02';
                break;
            case 'Maret':
                bulan = '03';
                break;
            case 'April':
                bulan = '04';
                break;
            case 'Mei':
                bulan = '05';
                break;
            case 'Juni':
                bulan = '06';
                break;
            case 'Juli':
                bulan = '07';
                break;
            case 'Agustus':
                bulan = '08';
                break;
            case 'September':
                bulan = '09';
                break;
            case 'Oktober':
                bulan = '10';
                break;
            case 'November':
                bulan = '11';
                break;
            case 'Desember':
                bulan = '12';
                break;
        }
        // ambil tahun
        var tahun = data_bulan.split(' ')[1];

        // Buat URL baru dengan tanggal awal dan tanggal akhir yang sesuai
        var URLkalender = "http://localhost:8000/api/searchKalenderAPI?tanggal_mulai=" + tahun + "-" + bulan + "-" + tanggal + "&tanggal_selesai=" + tahun + "-" + bulan + "-" + tanggal + "&lengkap=lengkap";
        var URLhariRaya = "http://localhost:8000/api/searchHariRayaAPI?tanggal_mulai=" + tahun + "-" + bulan + "-" + tanggal + "&tanggal_selesai=" + tahun + "-" + bulan + "-" + tanggal + "&makna&pura";
        var URLdewasaAyu = "http://localhost:8000/api/searchAlaAyuningDewasaAPI?tanggal_mulai=" + tahun + "-" + bulan + "-" + tanggal + "&tanggal_selesai=" + tahun + "-" + bulan + "-" + tanggal + "&keterangan";
        // console.log(URLhariRaya);
        // console.log(URLkalender);
        // console.log(URLdewasaAyu);

        htmlTanggalKalender += `${tanggal} ${data_bulan}`;
        tanggalKalender.innerHTML += htmlTanggalKalender;
        tanggalHariRaya.innerHTML += htmlTanggalKalender;
        tanggalAlaAyuningDewasa.innerHTML += htmlTanggalKalender;

        // fetch api
        fetch(URLkalender)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // Mendapatkan array kunci (keys) dari objek
                var keysArray = Object.keys(data.result[0].kalender);
                // console.log(keysArray);

                // Mendapatkan panjang array kunci
                var panjangObjek = keysArray.length;
                // console.log(panjangObjek);

                htmlElemenKalender += `
                    <table class="kalenderCellFitur" style="padding: 5px">
                    `;

                // menampilkan data
                for (var i = 1; i < panjangObjek; i++) {
                    htmlElemenKalender += `
                        <tr>
                            <td class="landing-tengah">${keysArray[i]}</td>
                            <td class="landing-tengah">:</td>
                            <td class="landing-tengah">${data.result[0].kalender[keysArray[i]]}</td>
                        </tr>
                    `;
                }

                htmlElemenKalender += `
                    </table>
                    `;
                hasilKalenderBali.innerHTML += htmlElemenKalender;
            });

        // fetch api
        fetch(URLhariRaya)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // Mendapatkan array kunci (keys) dari objek
                var keysArray = Object.keys(data.result[0].kalender);
                // console.log(keysArray);

                // Mendapatkan panjang array kunci
                var panjangObjek = keysArray.length;
                // console.log(panjangObjek);

                htmlHariRaya += `
                    <table class="kalenderCellFitur" style="padding: 5px">
                    `;

                // menampilkan data

                if (panjangObjek <= 1) {
                    htmlHariRaya += `
                    <tr>
                    <td class="landing-tengah">Tidak ada hari raya</td>
                    </tr>
                    `;
                } else {
                    for (var i = 1; i < panjangObjek; i++) {
                        htmlHariRaya += `
                            <tr style="border-width: 0.5px">
                                <td class="px-2 landing-kiri">${keysArray[i]}</td>
                                <td class="px-2 landing-kiri">:</td>
                                <td class="px-2 landing-kiri">${data.result[0].kalender[keysArray[i]]}</td>
                            </tr>
                        `;
                    }
                }


                htmlHariRaya += `
                    </table>
                    `;
                hasilHariRaya.innerHTML += htmlHariRaya;
            });

        // fetch api
        fetch(URLdewasaAyu)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // console.log(data);
                // Mendapatkan array kunci (keys) dari objek
                // var keysArray = Object.keys(data.result[0].kalender[0]);
                // console.log(keysArray);

                // Mendapatkan panjang array kunci
                var panjangObjek = data.result[0].kalender.length;
                // console.log(panjangObjek);

                htmlAlaAyuningDewasa += `
                    <table class="kalenderCellFitur" style="padding: 5px">
                    <tr style="border-width: 0.5px">
                        <td class="px-2 landing-kiri">Dewasa Ayu</td>
                        <td class="px-2 landing-kiri"></td>
                        <td class="px-2 landing-kiri">Keterangan</td>
                    </tr>
                    `;

                // menampilkan data
                for (var i = 0; i < panjangObjek; i++) {
                    htmlAlaAyuningDewasa += `
                        <tr style="border-width: 0.5px">
                            <td class="px-2 landing-kiri">${data.result[0].kalender[i].dewasa_ayu}</td>
                            <td class="px-2 landing-kiri">:</td>
                            <td class="px-2 landing-kiri">${data.result[0].kalender[i].keterangan}</td>
                        </tr>
                    `;
                }



                htmlAlaAyuningDewasa += `
                    </table>
                    `;
                hasilAlaAyuningDewasa.innerHTML += htmlAlaAyuningDewasa;
            });



    });
</script>
@endsection