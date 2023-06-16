<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaptaWaraController_07 extends Controller
{
    public function getSaptawara($tanggal)
    {
        $tanggal = date('N', strtotime($tanggal)) + 1;
        return $tanggal;
    }

    public function getUripSaptaWara($saptaWara)
    {
        $urip = '-';
        if ($saptaWara == 1) {
            $urip = 5;
        } elseif ($saptaWara == 2) {
            $urip = 4;
        } elseif ($saptaWara == 3) {
            $urip = 3;
        } elseif ($saptaWara == 4) {
            $urip = 7;
        } elseif ($saptaWara == 5) {
            $urip = 8;
        } elseif ($saptaWara == 6) {
            $urip = 6;
        } elseif ($saptaWara == 7) {
            $urip = 9;
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
