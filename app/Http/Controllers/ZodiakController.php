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
                $zodiak = 'Capricorn';
            } else {
                $zodiak = 'Aquarius';
            }
        } elseif ($bln == 2) {
            if ($tgl <= 19) {
                $zodiak = 'Aquarius';
            } else {
                $zodiak = 'Pisces';
            }
        } elseif ($bln == 3) {
            if ($tgl <= 20) {
                $zodiak = 'Pisces';
            } else {
                $zodiak = 'Aries';
            }
        } elseif ($bln == 4) {
            if ($tgl <= 19) {
                $zodiak = 'Aries';
            } else {
                $zodiak = 'Taurus';
            }
        } elseif ($bln == 5) {
            if ($tgl <= 20) {
                $zodiak = 'Taurus';
            } else {
                $zodiak = 'Gemini';
            }
        } elseif ($bln == 6) {
            if ($tgl <= 20) {
                $zodiak = 'Gemini';
            } else {
                $zodiak = 'Cancer';
            }
        } elseif ($bln == 7) {
            if ($tgl <= 21) {
                $zodiak = 'Cancer';
            } else {
                $zodiak = 'Leo';
            }
        } elseif ($bln == 8) {
            if ($tgl <= 21) {
                $zodiak = 'Leo';
            } else {
                $zodiak = 'Virgo';
            }
        } elseif ($bln == 9) {
            if ($tgl <= 21) {
                $zodiak = 'Virgo';
            } else {
                $zodiak = 'Libra';
            }
        } elseif ($bln == 10) {
            if ($tgl <= 21) {
                $zodiak = 'Libra';
            } else {
                $zodiak = 'Scorpio';
            }
        } elseif ($bln == 11) {
            if ($tgl <= 20) {
                $zodiak = 'Scorpio';
            } else {
                $zodiak = 'Sagitarius';
            }
        } elseif ($bln == 12) {
            if ($tgl <= 21) {
                $zodiak = 'Sagitarius';
            } else {
                $zodiak = 'Capricorn';
            }
        }

        return $zodiak;
    }
}
