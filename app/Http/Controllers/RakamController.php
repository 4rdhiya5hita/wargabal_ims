<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RakamController extends Controller
{
    public function Rakam($pancawara, $saptawara)
    {
        $urip_panca_rakam = 0;
        $urip_sapta_rakam = 0;

        if ($pancawara == 1) {
            $urip_panca_rakam = 2;
        } elseif ($pancawara == 2) {
            $urip_panca_rakam = 3;
        } elseif ($pancawara == 3) {
            $urip_panca_rakam = 4;
        } elseif ($pancawara == 4) {
            $urip_panca_rakam = 5;
        } elseif ($pancawara == 5) {
            $urip_panca_rakam = 1;
        }

        if ($saptawara == 1) {
            $urip_sapta_rakam = 3;
        } elseif ($saptawara == 2) {
            $urip_sapta_rakam = 4;
        } elseif ($saptawara == 3) {
            $urip_sapta_rakam = 5;
        } elseif ($saptawara == 4) {
            $urip_sapta_rakam = 6;
        } elseif ($saptawara == 5) {
            $urip_sapta_rakam = 7;
        } elseif ($saptawara == 6) {
            $urip_sapta_rakam = 1;
        } elseif ($saptawara == 7) {
            $urip_sapta_rakam = 2;
        }

        $rakam = ($urip_panca_rakam + $urip_sapta_rakam) % 6;

        $h_rakam = '';

        switch ($rakam) {
            case 0:
                $h_rakam = 'Pati';
                break;
            case 1:
                $h_rakam = 'Kala Tinantang';
                break;
            case 2:
                $h_rakam = 'Demang Kandhuruwan';
                break;
            case 3:
                $h_rakam = 'Sanggar Waringin';
                break;
            case 4:
                $h_rakam = 'Mantri Sinaroja';
                break;
            case 5:
                $h_rakam = 'Macan Katawan';
                break;
        }

        return $h_rakam;
    }
}
