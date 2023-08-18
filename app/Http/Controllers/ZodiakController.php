<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZodiakController extends Controller
{
    public function Zodiak($tanggal)
    {
        $bln = date('n', strtotime($tanggal));
        $tgl = date('j', strtotime($tanggal));
        $zodiak = '';

        if ($bln == 1) {
            if ($tgl <= 20) {
                $zodiak = 'CAPRICORN';
            } else {
                $zodiak = 'AQUARIUS';
            }
        } elseif ($bln == 2) {
            if ($tgl <= 19) {
                $zodiak = 'AQUARIUS';
            } else {
                $zodiak = 'PISCES';
            }
        } elseif ($bln == 3) {
            if ($tgl <= 20) {
                $zodiak = 'PISCES';
            } else {
                $zodiak = 'ARIES';
            }
        } elseif ($bln == 4) {
            if ($tgl <= 19) {
                $zodiak = 'ARIES';
            } else {
                $zodiak = 'TAURUS';
            }
        } elseif ($bln == 5) {
            if ($tgl <= 20) {
                $zodiak = 'TAURUS';
            } else {
                $zodiak = 'GEMINI';
            }
        } elseif ($bln == 6) {
            if ($tgl <= 20) {
                $zodiak = 'GEMINI';
            } else {
                $zodiak = 'CANCER';
            }
        } elseif ($bln == 7) {
            if ($tgl <= 21) {
                $zodiak = 'CANCER';
            } else {
                $zodiak = 'LEO';
            }
        } elseif ($bln == 8) {
            if ($tgl <= 21) {
                $zodiak = 'LEO';
            } else {
                $zodiak = 'VIRGO';
            }
        } elseif ($bln == 9) {
            if ($tgl <= 21) {
                $zodiak = 'VIRGO';
            } else {
                $zodiak = 'LIBRA';
            }
        } elseif ($bln == 10) {
            if ($tgl <= 21) {
                $zodiak = 'LIBRA';
            } else {
                $zodiak = 'SCORPIO';
            }
        } elseif ($bln == 11) {
            if ($tgl <= 20) {
                $zodiak = 'SCORPIO';
            } else {
                $zodiak = 'SAGITARIUS';
            }
        } elseif ($bln == 12) {
            if ($tgl <= 21) {
                $zodiak = 'SAGITARIUS';
            } else {
                $zodiak = 'CAPRICORN';
            }
        }

        return $zodiak;
    }
}
