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
<div class="container">
    <div class="row" id="kolomKeterangan">
        <div class="row landing-title1">Dewasa Ayu</div>
        <div class="row landing-description">
            Dalam Ala Ayuning Dewasa dikenal tiga jenis hari berdasarkan baik buruk- nya yaitu Dewasa Ayu adalah hari yang baik, Dewasa Ala Ayu adalah hari yang memiliki hari baik dan hari yang tidak baik dan Dewasa Ala yaitu hari tidak baik.
        </div>

        <br>
        <div class="row docs-text">Request Url</div>
        <div class="row docs-description">
            Berikut adalah contoh penggunaan url untuk mendapatkan data dewasa ayu
        </div>
        <div class="tableFitur">
            <div class="fiturListing">
                <table>
                    <tr>
                        <td class="px-2 landing-kiri fw-bold">Url Utama</td>
                        <td class="px-2 landing-kiri"> </td>
                        <td class="px-2 landing-kiri">https://kalenderbali.xyz/api/searchAlaAyuningDewasaAPI?tanggal_mulai={tahun}-{bulan}-{tanggal}&tanggal_selesai={tahun}-{bulan}-{tanggal}</td>
                    </tr>
                    <tr>
                        <td class="px-2 landing-kiri fw-bold">Contoh Penggunaan</td>
                        <td class="px-2 landing-kiri"> </td>
                        <td class="px-2 landing-kiri">https://kalenderbali.xyz/api/searchAlaAyuningDewasaAPI?tanggal_mulai=2023-11-01&tanggal_selesai=2023-11-30</td>
                    </tr>
                    <tr>
                        <td class="px-2 landing-kiri fw-bold">Parameter tambahan</td>
                        <td class="px-2 landing-kiri"> </td>
                        <td class="px-2 landing-kiri">&keterangan</td>
                    </tr>
                </table>
            </div>
        </div>

        <br>
        <div class="row docs-text mt-3">Response JSON</div>
        <div class="row docs-description">
            Berikut adalah contoh response yang akan didapatkan dari penggunaan url diatas
        </div>
        <div class="tableFitur">
            <div class="fiturListing">
                <table>
                    <tr>
                        <td class="px-2 landing-kiri fw-bold">Url</td>
                        <td class="px-2 landing-kiri"> </td>
                        <td class="px-2 landing-kiri fw-bold">http://localhost:8000/api/searchAlaAyuningDewasaAPI?tanggal_mulai=2023-11-01&tanggal_selesai=2023-11-02&keterangan</td>
                    </tr>
                    <tr>
                        <td class="px-2 landing-kiri fw-bold">Parameter</td>
                        <td class="px-2 landing-kiri"> </td>
                        <td class="px-2 landing-kiri">&keterangan</td>
                    </tr>
                    <tr>
                        <td class="px-2 landing-kiri">
                        </td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td class="px-2 landing-kiri">
                            <pre>
{
    "message": "Sukses",
    "result": [
        {
            "tanggal": "2023-11-01",
            "kalender": [
                {
                    "dewasa_ayu": "Banyu Urug",
                    "keterangan": "Bersifat panas, tidak baik membangun rumah, terutama tidak baik mengatapi rumah, karena mudah terbakar. Baik untuk mulai membakar bata, genteng, gerabah, keramik, tembikar, dan lain-lain."
                },
                {
                    "dewasa_ayu": "Carik Walangati",
                    "keterangan": "Baik untuk membuat bendungan. Tidak baik untuk membuat sumur."
                },
                {
                    "dewasa_ayu": "Geni Rawana",
                    "keterangan": "Tidak baik untuk melakukan pernikahan/wiwaha, atiwa-tiwa/ngaben dan membangun rumah."
                },
                {
                    "dewasa_ayu": "Kajeng Susunan",
                    "keterangan": "Baik untuk segala pekerjaan yang menggunakan api. Tidak baik untuk mengatapi rumah, melaspas, bercocok tanam."
                },
                {
                    "dewasa_ayu": "Kajeng Uwudan",
                    "keterangan": "Baik untuk membuat sok atau sejenisnya."
                },
                {
                    "dewasa_ayu": "Kala Bangkung, Kala Nanggung",
                    "keterangan": "Tidak baik untuk menanam dan memetik tanaman."
                },
                {
                    "dewasa_ayu": "Kala Lutung Megelut",
                    "keterangan": "Tidak baik untuk mulai memelihara ternak."
                },
                {
                    "dewasa_ayu": "Kala Pati",
                    "keterangan": null
                },
                {
                    "dewasa_ayu": "Kala Rumpuh",
                    "keterangan": "Baik untuk membuat jerat dan memasangnya, pembuat pengrusak. Tidak baik untuk semua upacara dan pekerjaan yang lainnya."
                },
                {
                    "dewasa_ayu": "Salah Wadi",
                    "keterangan": "Tidak baik untuk pindah rumah, memulai memelihara ayam, itik, sapi, kerbau, kambing, babi (ternak)."
                },
                {
                    "dewasa_ayu": "Sedana Yoga",
                    "keterangan": "Tidak baik untuk melakukan Manusa Yadnya (wiwaha, mapendes, potong rambut dll.) Pitra Yadnya (Penguburan, atiwa-tiwa/ngaben, nyekah, ngasti dll."
                },
                {
                    "dewasa_ayu": "Sedana Yoga",
                    "keterangan": "Baik untuk membuat alat berdagang, tempat berdagang, mulai berjualan karena akan murah rejeki."
                },
                {
                    "dewasa_ayu": "Srigati",
                    "keterangan": "Baik untuk membuat alat berdagang, tempat berdagang, mulai berjualan karena akan murah rejeki."
                },
                {
                    "dewasa_ayu": "Srigati Munggah",
                    "keterangan": "Baik untuk menyimpan padi di lumbung dan menurunkan padi dari lumbung"
                },
                {
                    "dewasa_ayu": "Titibuwuk",
                    "keterangan": "Baik untuk membibit/menanam padi, membuat alat-alat berjualan, membuat pahat, menyimpan padi atau upacara padi li lumbung. Tidak baik meminjam sesuatu, menjual beli beras."
                }
            ]
        },
        {
            "tanggal": "2023-11-02",
            "kalender": [
                {
                    "dewasa_ayu": "Amerta Yoga",
                    "keterangan": "Bersifat panas, tidak baik membangun rumah, terutama tidak baik mengatapi rumah, karena mudah terbakar. Baik untuk mulai membakar bata, genteng, gerabah, keramik, tembikar, dan lain-lain."
                },
                {
                    "dewasa_ayu": "Carik Walangati",
                    "keterangan": null
                },
                {
                    "dewasa_ayu": "Dauh Ayu",
                    "keterangan": "Tidak baik untuk melakukan pernikahan/wiwaha, atiwa-tiwa/ngaben dan membangun rumah."
                },
                {
                    "dewasa_ayu": "Geni Rawana",
                    "keterangan": "Baik untuk membuat awig-awig, peraturan-peraturan atau undang-undang, baik untuk membangun."
                },
                {
                    "dewasa_ayu": "Kala Temah",
                    "keterangan": "Baik untuk segala pekerjaan yang menggunakan api. Tidak baik untuk mengatapi rumah, melaspas, bercocok tanam."
                },
                {
                    "dewasa_ayu": "Kala Upa",
                    "keterangan": "Tidak baik untuk dewasa ayu."
                },
                {
                    "dewasa_ayu": "Panca Prawani",
                    "keterangan": "Baik untuk memulai mengambil/memelihara ternak (wewalungan)."
                },
                {
                    "dewasa_ayu": "Salah Wadi",
                    "keterangan": "Tidak baik dipakai dewasa ayu."
                },
                {
                    "dewasa_ayu": "Sedana Yoga",
                    "keterangan": "Tidak baik untuk melakukan Manusa Yadnya (wiwaha, mapendes, potong rambut dll.) Pitra Yadnya (Penguburan, atiwa-tiwa/ngaben, nyekah, ngasti dll."
                },
                {
                    "dewasa_ayu": "Sedana Yoga",
                    "keterangan": "Baik untuk membuat alat berdagang, tempat berdagang, mulai berjualan karena akan murah rejeki."
                },
                {
                    "dewasa_ayu": "Taliwangke",
                    "keterangan": "Baik untuk membuat alat berdagang, tempat berdagang, mulai berjualan karena akan murah rejeki."
                }
            ]
        }
    ],
    "execution_time": "0.060086"
}
                            </pre>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- </div> -->
@endsection




<!-- JAVASCRIPT -->
@section('script')
@endsection