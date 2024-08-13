<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class WukuController extends Controller
{
    public function getNoWuku($tanggal, $pivotAngkaWuku, $refTanggal)
    {
        $parsedRefTanggal = Carbon::createFromFormat('Y-m-d', $refTanggal);
        $parsedTanggal = Carbon::createFromFormat('Y-m-d', $tanggal);
        
        $bedaHari = $parsedTanggal->diffInDays($parsedRefTanggal);
        $angkaWuku = ($pivotAngkaWuku + $bedaHari) % 210;
        // dd($tanggal);

        if ($angkaWuku < 0) {
            $angkaWuku = 210 + $angkaWuku;
        }

        if ($angkaWuku == 0) {
            $angkaWuku = 210;
        }

        // dd($angkaWuku);
        return $angkaWuku;
    }

    public function getWuku($hasilAngkaWuku)
    {
        $noWuku = $hasilAngkaWuku;
        $wuku = ceil($noWuku / 7);

        return $wuku;
    }

    public function getNamaWuku($hasilWuku)
    {
        $i = $hasilWuku;

        if ($i == 1) {
            $nama = 'Sinta';
        } elseif ($i == 2) {
            $nama = 'Landep';
        } elseif ($i == 3) {
            $nama = 'Ukir';
        } elseif ($i == 4) {
            $nama = 'Kulantir';
        } elseif ($i == 5) {
            $nama = 'Tolu';
        } elseif ($i == 6) {
            $nama = 'Gumbreg';
        } elseif ($i == 7) {
            $nama = 'Wariga';
        } elseif ($i == 8) {
            $nama = 'Warigadean';
        } elseif ($i == 9) {
            $nama = 'Julungwangi';
        } elseif ($i == 10) {
            $nama = 'Sungsang';
        } elseif ($i == 11) {
            $nama = 'Dungulan';
        } elseif ($i == 12) {
            $nama = 'Kuningan';
        } elseif ($i == 13) {
            $nama = 'Langkir';
        } elseif ($i == 14) {
            $nama = 'Medangsia';
        } elseif ($i == 15) {
            $nama = 'Pujut';
        } elseif ($i == 16) {
            $nama = 'Pahang';
        } elseif ($i == 17) {
            $nama = 'Krulut';
        } elseif ($i == 18) {
            $nama = 'Merakih';
        } elseif ($i == 19) {
            $nama = 'Tambir';
        } elseif ($i == 20) {
            $nama = 'Medangkungan';
        } elseif ($i == 21) {
            $nama = 'Matal';
        } elseif ($i == 22) {
            $nama = 'Uye';
        } elseif ($i == 23) {
            $nama = 'Menail';
        } elseif ($i == 24) {
            $nama = 'Prangbakat';
        } elseif ($i == 25) {
            $nama = 'Bala';
        } elseif ($i == 26) {
            $nama = 'Ugu';
        } elseif ($i == 27) {
            $nama = 'Wayang';
        } elseif ($i == 28) {
            $nama = 'Klawu';
        } elseif ($i == 29) {
            $nama = 'Dukut';
        } elseif ($i == 30) {
            $nama = 'Watugunung';
        }

        return $nama;
    }

}
