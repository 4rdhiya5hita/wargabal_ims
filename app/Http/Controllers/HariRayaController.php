<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HariRayaController extends Controller
{
    public function getHariRaya($tanggal, $hari_sasih_1, $hari_sasih_2, $pengalantaka, $no_sasih, $triwara, $pancawara, $saptawara, $hasilWuku)
    // public function getHariRaya($pancawara,$saptawara,$hasilWuku)
    {

        // dd($pancawara, $saptawara, $hasilWuku);
        $pancawara = intval($pancawara);
        $saptawara = intval($saptawara);
        $wuku = intval($hasilWuku);
        

        $hari_raya = [];
        // wuku 1-3
        if ($saptawara == 1 && $pancawara == 2 && $wuku == 1) {
            $hari_raya[] = 'Banyu Pinaruh';
        } elseif ($saptawara == 2 && $pancawara == 3 && $wuku == 1) {
            $hari_raya[] = 'Soma Ribek';
        } elseif ($saptawara == 3 && $pancawara == 4 && $wuku == 1) {
            $hari_raya[] = 'Sabuh Mas';
        } elseif ($saptawara == 4 && $pancawara == 5 && $wuku == 1) {
            $hari_raya[] = 'Pagerwesi';
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 2) {
            $hari_raya[] = 'Tumpek Landep';
        } elseif ($saptawara == 1 && $pancawara == 1 && $wuku == 3) {
            $hari_raya[] = 'Persembahan Bhatara Guru';
        } elseif ($saptawara == 4 && $pancawara == 5 && $wuku == 3) {
            $hari_raya[] = 'Buda Cemeng Ukir';
        } elseif ($saptawara == 4 && $pancawara == 4 && $wuku == 3) {
            $hari_raya[] = 'Hari Bhatara Rambut Sedana';
            $hari_raya[] = 'Hari Bhatari Sri';
        } elseif ($saptawara == 3 && $pancawara == 5 && $wuku == 4) {
            $hari_raya[] = 'Anggara Kasih Kulantir';
        
        // wuku 7-9
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 7) {
            $hari_raya[] = 'Tumpek Wariga';
        // } elseif ($saptawara == 7 && $pancawara == 2 && $wuku == 8) {
        //     $hari_raya[] = 'Penyucian Bhatara Brahma';
        } elseif ($saptawara == 2 && $pancawara == 2 && $wuku == 8) {
            $hari_raya[] = 'Hari Bhatara Brahma';
        } elseif ($saptawara == 6 && $pancawara == 1 && $wuku == 8) {
            $hari_raya[] = 'Hari Bhatari Sri';
        } elseif ($saptawara == 3 && $pancawara == 5 && $wuku == 9) {
            $hari_raya[] = 'Anggara Kasih Julungwangi';

            // wuku 10
        } elseif ($saptawara == 5 && $pancawara == 4 && $wuku == 10) {
            $hari_raya[] = 'Hari Sugian Jawa';
        } elseif ($saptawara == 6 && $pancawara == 5 && $wuku == 10) {
            $hari_raya[] = 'Hari Sugian Bali';

            // wuku 11
        } elseif ($saptawara == 1 && $pancawara == 2 && $wuku == 11) {
            $hari_raya[] = 'Hari Penyekeban';
        } elseif ($saptawara == 2 && $pancawara == 3 && $wuku == 11) {
            $hari_raya[] = 'Hari Penyajaan';
        } elseif ($saptawara == 3 && $pancawara == 4 && $wuku == 11) {
            $hari_raya[] = 'Hari Penampahan Galungan';
        } elseif ($saptawara == 4 && $pancawara == 5 && $wuku == 11) {
            $hari_raya[] = 'Hari Raya Galungan';
        } elseif ($saptawara == 5 && $pancawara == 1 && $wuku == 11) {
            $hari_raya[] = 'Hari Umanis Galungan';
        } elseif ($saptawara == 7 && $pancawara == 3 && $wuku == 11) {
            $hari_raya[] = 'Hari Pamaridan Guru';

            // wuku 12
        } elseif ($saptawara == 1 && $pancawara == 4 && $wuku == 12) {
            $hari_raya[] = 'Hari Ulihan';
        } elseif ($saptawara == 2 && $pancawara == 5 && $wuku == 12) {
            $hari_raya[] = 'Hari Pemacekan Agung';
        } elseif ($saptawara == 6 && $pancawara == 4 && $wuku == 12) {
            $hari_raya[] = 'Hari Penampahan Kuningan';
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 12) {
            $hari_raya[] = 'Hari Raya Kuningan';
        } elseif ($saptawara == 4 && $pancawara == 2 && $wuku == 12) {
            $hari_raya[] = 'Pujawali Bhatara Wisnu';
        } elseif ($saptawara == 4 && $pancawara == 3 && $wuku == 13) {
            $hari_raya[] = 'Buda Cemeng Langkir';
        } elseif ($saptawara == 3 && $pancawara == 5 && $wuku == 14) {
            $hari_raya[] = 'Anggara Kasih Medangsia';
        
            // wuku 16-19
        } elseif ($saptawara == 4 && $pancawara == 5 && $wuku == 16) {
            $hari_raya[] = 'Hari Pegatwakan';
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 17) {
            $hari_raya[] = 'Tumpek Krulut';
        } elseif ($saptawara == 4 && $pancawara == 4 && $wuku == 18) {
            $hari_raya[] = 'Hari Bhatara Rambut Sedana';
        } elseif ($saptawara == 6 && $pancawara == 1 && $wuku == 18) {
            $hari_raya[] = 'Hari Bhatari Sri';
        } elseif ($saptawara == 4 && $pancawara == 3 && $wuku == 18) {
            $hari_raya[] = 'Buda Cemeng Merakih';
        } elseif ($saptawara == 3 && $pancawara == 5 && $wuku == 19) {
            $hari_raya[] = 'Anggara Kasih Tambir';

            // wuku 21-26
        } elseif ($saptawara == 4 && $pancawara == 5 && $wuku == 21) {
            $hari_raya[] = 'Buda Kliwon Matal';
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 22) {
            $hari_raya[] = 'Tumpek Kandang';
        } elseif ($saptawara == 4 && $pancawara == 3 && $wuku == 23) {
            $hari_raya[] = 'Buda Cemeng Menail';
        } elseif ($saptawara == 3 && $pancawara == 5 && $wuku == 24) {
            $hari_raya[] = 'Anggara Kasih Perangbakat';
        } elseif ($saptawara == 4 && $pancawara == 5 && $wuku == 26) {
            $hari_raya[] = 'Buda Kliwon Ugu';

            // wuku 27-29
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 27) {
            $hari_raya[] = 'Tumpek Wayang';
        } elseif ($saptawara == 4 && $pancawara == 4 && $wuku == 28) {
            $hari_raya[] = 'Hari Bhatara Rambut Sedana';
        } elseif ($saptawara == 4 && $pancawara == 3 && $wuku == 28) {
            $hari_raya[] = 'Buda Cemeng Klawu';
        } elseif ($saptawara == 3 && $pancawara == 5 && $wuku == 29) {
            $hari_raya[] = 'Anggara Kasih Dukut';

            // wuku 30
        } elseif ($saptawara == 3 && $pancawara == 2 && $wuku == 30) {
            $hari_raya[] = 'Hari Bhatari Sri';
        } elseif ($saptawara == 6 && $pancawara == 1 && $wuku == 30) {
            $hari_raya[] = 'Hari Paid-Paidan';
        } elseif ($saptawara == 4 && $pancawara == 3 && $wuku == 30) {
            $hari_raya[] = 'Hari Urip';
        } elseif ($saptawara == 5 && $pancawara == 4 && $wuku == 30) {
            $hari_raya[] = 'Hari Penegtegan';
        } elseif ($saptawara == 6 && $pancawara == 5 && $wuku == 30) {
            $hari_raya[] = 'Hari Pangredanan';
        } elseif ($saptawara == 7 && $pancawara == 1 && $wuku == 30) {
            $hari_raya[] = 'Hari Raya Saraswati';
        }

        if ($pancawara == 5 && $triwara == 3) {
            $hari_raya[] = 'Kajeng Kliwon';
        }

        if (($hari_sasih_1 == 1 || $hari_sasih_2 == 2) && $pengalantaka == 'Penanggal' && $no_sasih == 10) {
            $hari_raya[] = 'Nyepi';
        } elseif (($hari_sasih_1 == 14 || $hari_sasih_2 == 14) && $pengalantaka == 'Pangelong' && $no_sasih == 7) {
            $hari_raya[] = 'Siwalatri';
        }

        if (($hari_sasih_1 == 15 || $hari_sasih_2 == 15) && $pengalantaka == 'Penanggal') {
            $hari_raya[] = 'Purnama';
        } elseif (($hari_sasih_1 == 15 || $hari_sasih_2 == 15) && $pengalantaka == 'Pangelong') {
            $hari_raya[] = 'Tilem';
        }

        if (empty($hari_raya)) {
            $hari_raya[] = '-';
        }

        // dd($hari_raya);
        return $hari_raya;
    }
}
