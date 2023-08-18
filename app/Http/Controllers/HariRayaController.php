<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HariRayaController extends Controller
{
    public function getHariRaya($tanggal,$hari_sasih_1,$hari_sasih_2,$pengalantaka,$no_sasih,$triwara,$pancawara,$saptawara,$hasilWuku)
    // public function getHariRaya($pancawara,$saptawara,$hasilWuku)
    {

        // dd($pancawara, $saptawara, $hasilWuku);
        $pancawara = intval($pancawara);
        $saptawara = intval($saptawara);
        $wuku = intval($hasilWuku);
        // dd($pancawara, $saptawara, $wuku);

        $hari_raya = [];

        // wuku 3
        if ($saptawara == 4 && $pancawara == 4 && $wuku == 3) {
            $hari_raya[] = 'Hari Betara Sedana';
            $hari_raya[] = 'Hari Betari Sri';

        // wuku 7-9
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 7) {
            $hari_raya[] = 'Tumpek Uduh';
        } elseif ($saptawara == 2 && $pancawara == 2 && $wuku == 8) {
            $hari_raya[] = 'Hari Betara Brahma';
        } elseif ($saptawara == 6 && $pancawara == 1 && $wuku == 8) {
            $hari_raya[] = 'Hari Betari Sri';
        } elseif ($saptawara == 3 && $pancawara == 5 && $wuku == 9) {
            $hari_raya[] = 'Hari Anggara Kasih Julungwangi';

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
            $hari_raya[] = 'Hari Pemaridan Guru';

        // wuku 12
        } elseif ($saptawara == 1 && $pancawara == 4 && $wuku == 12) {
            $hari_raya[] = 'Hari Ulihan';
        } elseif ($saptawara == 2 && $pancawara == 5 && $wuku == 12) {
            $hari_raya[] = 'Hari Pemacekan Agung';
        } elseif ($saptawara == 6 && $pancawara == 4 && $wuku == 12) {
            $hari_raya[] = 'Hari Penampahan Kuningan';
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 12) {
            $hari_raya[] = 'Hari Raya Kuningan';

        // wuku 16-22
        } elseif ($saptawara == 4 && $pancawara == 5 && $wuku == 16) {
            $hari_raya[] = 'Hari Pegat Wakan';
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 17) {
            $hari_raya[] = 'Hari Tumpek Krulut';
        } elseif ($saptawara == 4 && $pancawara == 4 && $wuku == 18) {
            $hari_raya[] = 'Hari Betara Rambut Sedana';
        } elseif ($saptawara == 6 && $pancawara == 1 && $wuku == 18) {
            $hari_raya[] = 'Betara Sri';
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 22) {
            $hari_raya[] = 'Hari Tumpek Kandang';

        // wuku 27-28
        } elseif ($saptawara == 7 && $pancawara == 5 && $wuku == 27) {
            $hari_raya[] = 'Hari Tumpek Wayang';
        } elseif ($saptawara == 4 && $pancawara == 4 && $wuku == 28) {
            $hari_raya[] = 'Hari Betara Rambut Sedana';

        // wuku 30
        } elseif ($saptawara == 3 && $pancawara == 2 && $wuku == 30) {
            $hari_raya[] = 'Betara Sri';
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
            $hari_raya[] = 'Hari Raya Nyepi';
        } elseif (($hari_sasih_1 == 14 || $hari_sasih_2 == 14) && $pengalantaka == 'Pangelong' && $no_sasih == 7) {
            $hari_raya[] = 'Hari Raya Siwalatri';
        } 
        
        if (($hari_sasih_1 == 15 || $hari_sasih_2 == 15) && $pengalantaka == 'Penanggal') {
            $hari_raya[] = 'Hari Raya Purnama';
        } elseif (($hari_sasih_1 == 15 || $hari_sasih_2 == 15) && $pengalantaka == 'Pangelong') {
            $hari_raya[] = 'Hari Raya Tilem';
        } 
        
        if (empty($hari_raya)){
            $hari_raya[] = '-';
        }

        return $hari_raya;
    }
}
