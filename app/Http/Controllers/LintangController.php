<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LintangController extends Controller
{
    public function Lintang($tanggal, $refTanggal)
    {
        $selisihHari_lintang = strtotime($tanggal) - strtotime($refTanggal);
        $selisihHari_lintang = floor($selisihHari_lintang / (60 * 60 * 24)); // Convert to days

        $lintang = $selisihHari_lintang % 35;

        $h_lintang = '';

        switch ($lintang) {
            case 0:
                $h_lintang = 'Begong';
                break;
            case 1:
                $h_lintang = 'Gajah';
                break;
            case 2:
                $h_lintang = 'Kiriman';
                break;
            case 3:
                $h_lintang = 'Perahu Sarat';
                break;
            case 4:
                $h_lintang = 'Tiwa-tiwa';
                break;
            case 5:
                $h_lintang = 'Sangkatikel';
                break;
            case 6:
                $h_lintang = 'Bubu Bolong';
                break;
            case 7:
                $h_lintang = 'Sungenge';
                break;
            case 8:
                $h_lintang = 'Uluku';
                break;
            case 9:
                $h_lintang = 'Pedati';
                break;
            case 10:
                $h_lintang = 'Kuda';
                break;
            case 11:
                $h_lintang = 'Gajah Mina';
                break;
            case 12:
                $h_lintang = 'Bade';
                break;
            case 13:
                $h_lintang = 'Magelut';
                break;
            case 14:
                $h_lintang = 'Rarung Pegelangan';
                break;
            case 15:
                $h_lintang = 'Kala Sungsang';
                break;
            case 16:
                $h_lintang = 'Dupa';
                break;
            case 17:
                $h_lintang = 'Asu';
                break;
            case 18:
                $h_lintang = 'Kartika';
                break;
            case 19:
                $h_lintang = 'Naga';
                break;
            case 20:
                $h_lintang = 'Angsa Angrem';
                break;
            case 21:
                $h_lintang = 'Panah';
                break;
            case 22:
                $h_lintang = 'Patrem';
                break;
            case 23:
                $h_lintang = 'Lembu';
                break;
            case 24:
                $h_lintang = 'Depat';
                break;
            case 25:
                $h_lintang = 'Tangis';
                break;
            case 26:
                $h_lintang = 'Salah Ukur';
                break;
            case 27:
                $h_lintang = 'Prahu Pegat';
                break;
            case 28:
                $h_lintang = 'Puwuh Atarung';
                break;
            case 29:
                $h_lintang = 'Lawean';
                break;
            case 30:
                $h_lintang = 'Kelapa';
                break;
            case 31:
                $h_lintang = 'Yuyu';
                break;
            case 32:
                $h_lintang = 'Lumbung';
                break;
            case 33:
                $h_lintang = 'Kumba';
                break;
            case 34:
                $h_lintang = 'Udang';
                break;
        }

        return $h_lintang;
    }
}
