<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaptaWaraController_07 extends Controller
{
public function getSaptawara($tanggal)
    {
        // dd($tanggal);
        $tanggal = date('N', strtotime($tanggal)) + 1;
        if($tanggal == 8){
            $tanggal = 1;
        }
        
        return $tanggal;
    }

    public function getUripSaptaWara($saptaWara)
    {
        $urip = '-';
        switch ($saptaWara) {
            case 1:
                $urip = 5;
                break;
            case 2:
                $urip = 4;
                break;
            case 3:
                $urip = 3;
                break;
            case 4:
                $urip = 7;
                break;
            case 5:
                $urip = 8;
                break;
            case 6:
                $urip = 6;
                break;
            case 7:
                $urip = 9;
                break;
            case 8:
                $urip = 5;
                break;
        }

        return $urip;
    }

    public function getNamaSaptaWara($i)
    {        
        if ($i == 8 or $i == 1) {
            $nama = 'Redite';
        } elseif ($i == 2) {
            $nama = 'Soma';
        } elseif ($i == 3) {
            $nama = 'Anggara';
        } elseif ($i == 4) {
            $nama = 'Budha';
        } elseif ($i == 5) {
            $nama = 'Wrespati';
        } elseif ($i == 6) {
            $nama = 'Sukra';
        } elseif ($i == 7) {
            $nama = 'Saniscara';
        }

        return $nama;
    }
}
