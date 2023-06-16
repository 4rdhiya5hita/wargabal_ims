<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengalantakaController extends Controller
{
    public function getPengalantaka($tanggal, $refTanggal, $refPenanggal, $refNgunaratri)
    {
        $selisih = intval(date_diff(date_create($tanggal), date_create($refTanggal))->format('%a'));
        $jumlahNgunaratri = floor(($selisih + $refNgunaratri) / 63);
        
        if (($selisih + $refNgunaratri) % 63 === 0) {
            $jumlahNgunaratri--;
        }
        
        $jumlah = $refPenanggal + $selisih + $jumlahNgunaratri;
        
        if (floor(($jumlah - 1) / 15) % 2 === 0) {
            return 'Pangelong';
        } else {
            return 'Penanggal';
        }
    }

    public function getPurnamaTilem($pengalantaka, $sasihDay1, $sasihDay2)
    {
        
        if (($sasihDay1 === 15 && $pengalantaka === 'Penanggal') || ($sasihDay2 === 15 && $pengalantaka === 'Penanggal')) {
            $purnama = 'Purnama';
            $tilem = '-';
        } elseif (($sasihDay1 === 15 && $pengalantaka === 'Pangelong') || ($sasihDay2 === 15 && $pengalantaka === 'Pangelong')) {
            $purnama = '-';
            $tilem = 'Tilem';
        }
        
        return [
            'purnama' => $purnama,
            'tilem' => $tilem,
        ];
    }
}
