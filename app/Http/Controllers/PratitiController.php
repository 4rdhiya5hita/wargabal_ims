<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PratitiController extends Controller
{
    public function Pratiti($pengalantaka, $sasih, $hari_sasih)
    {
        // dd($pengalantaka, $sasih, $hari_sasih);
        // $pengalantaka -> pengalantaka (penanggal / pangelong)
        // $sasih -> no_sasih
        // $hari_sasih -> hari_sasih_1

        $pratiti = '';

        if ($pengalantaka === 'Penanggal') {
            if ($sasih === 6) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Jaramarana';
                }
            } elseif ($sasih === 7) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Widnyana';
                }
            } elseif ($sasih === 8) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Namarupa';
                }
            } elseif ($sasih === 9) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Sadayatana';
                }
            } elseif ($sasih === 10) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Separsa';
                }
            } elseif ($sasih === 11) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Wedana';
                }
            } elseif ($sasih === 12) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Tresna';
                }
            } elseif ($sasih === 1) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Upadana';
                }
            } elseif ($sasih === 2) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Bhawa';
                }
            } elseif ($sasih === 3) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Jati';
                }
            } elseif ($sasih === 4) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Jaramarana';
                }
            } elseif ($sasih === 5) {
                if ($hari_sasih === 1 || $hari_sasih === 15) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 3) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 4) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 5) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 8 || $hari_sasih === 9) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 13 || $hari_sasih === 14) {
                    $pratiti = 'Awidya';
                }
            }

        } elseif ($pengalantaka === 'Pangelong') {
            // PANGELONG
            if ($sasih === 6) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Jaramarana';
                }
            } elseif ($sasih === 7) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Widnyana';
                }
            } elseif ($sasih === 8) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Namarupa';
                }
            } elseif ($sasih === 9) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Sadayatana';
                }
            } elseif ($sasih === 10) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Separsa';
                }
            } elseif ($sasih === 11) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Wedana';
                }
            } elseif ($sasih === 12) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Tresna';
                }
            } elseif ($sasih === 1) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Upadana';
                }
            } elseif ($sasih === 2) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Bhawa';
                }
            } elseif ($sasih === 3) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Jati';
                }
            } elseif ($sasih === 4) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Awidya';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Jaramarana';
                }
            } elseif ($sasih === 5) {
                if ($hari_sasih === 1) {
                    $pratiti = 'Jaramarana';
                } elseif ($hari_sasih === 2) {
                    $pratiti = 'Jati';
                } elseif ($hari_sasih === 3 || $hari_sasih === 13) {
                    $pratiti = 'Bhawa';
                } elseif ($hari_sasih === 4 || $hari_sasih === 14) {
                    $pratiti = 'Upadana';
                } elseif ($hari_sasih === 5 || $hari_sasih === 15) {
                    $pratiti = 'Tresna';
                } elseif ($hari_sasih === 6) {
                    $pratiti = 'Wedana';
                } elseif ($hari_sasih === 7) {
                    $pratiti = 'Separsa';
                } elseif ($hari_sasih === 8) {
                    $pratiti = 'Sadayatana';
                } elseif ($hari_sasih === 9) {
                    $pratiti = 'Namarupa';
                } elseif ($hari_sasih === 10) {
                    $pratiti = 'Widnyana';
                } elseif ($hari_sasih === 11) {
                    $pratiti = 'Saskara';
                } elseif ($hari_sasih === 12) {
                    $pratiti = 'Awidya';
                }
            }
        }

        return $pratiti;
    }
}
