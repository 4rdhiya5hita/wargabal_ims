<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EkaJalaRsiController extends Controller
{
    public function EkaJalaRsi($no_wuku, $saptawara)
    {
        $ekajalarsi = '';

        if ($no_wuku == 1) {
            if ($saptawara == 1) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SUKA';
        } elseif ($saptawara == 3) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'MANGGIH SUKA';
            }
        } elseif ($no_wuku == 2) {
            if ($saptawara == 1) {
                $ekajalarsi = 'KAMARANAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'WERDHI PUTRA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'SUKA RAHAYU';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            }
        } elseif ($no_wuku == 3) {
            if ($saptawara == 1) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'RAHAYU';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BUAT ASTAWA';
            }
        } elseif ($no_wuku == 4) {
            if ($saptawara == 1) {
                $ekajalarsi = 'LANGGENG KAYOHANAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'WERDHI PUTRA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BAGNA MAPASAH';
            }
        } elseif ($no_wuku == 5) {
            if ($saptawara == 1) {
                $ekajalarsi = 'KAMERTAAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SUKA PINANGGIH';
            }
        } elseif ($no_wuku == 6) {
            if ($saptawara == 1) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'BUAT MERANG';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BUAT ASTAWA';
            }
        } elseif ($no_wuku == 7) {
            if ($saptawara == 1) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'BUAT MERANG';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'LANGGENG KAYOHANAN';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'WERDHI PUTRA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'WERDHI PUTRA';
            }
        } elseif ($no_wuku == 8) {
            if ($saptawara == 1) {
                $ekajalarsi = 'BAHU PUTRA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BUAT SEBET';
            }
        } elseif ($no_wuku == 9) {
            if ($saptawara == 1) {
                $ekajalarsi = 'KAMARANAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'KASOBAGIAN';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SUBAGIA';
            }
        } elseif ($no_wuku == 10) {
            if ($saptawara == 1) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BAGNA MAPASAH';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BAGNA MAPASAH';
            }
        } elseif ($no_wuku == 11) {
            if ($saptawara == 1) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'MANGGIH BAGIA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'PATINING AMERTA';
            }
        } elseif ($no_wuku == 12) {
            if ($saptawara == 1) {
                $ekajalarsi = 'SUKA RAHAYU';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'WERDHI PUTRA';
            }
        } elseif ($no_wuku == 13) {
            if ($saptawara == 1) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'DAHAT KINGKING';
            }
        } elseif ($no_wuku == 14) {
            if ($saptawara == 1) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'LANGGENG KAYOHANAN';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'WERDHI PUTRA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            }
        } elseif ($no_wuku == 15) {
            if ($saptawara == 1) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'KINASIHANIN JANA';
            }
        } elseif ($no_wuku == 16) {
            if ($saptawara == 1) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'WERDHI PUTRA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            }
        } elseif ($no_wuku == 17) {
            if ($saptawara == 1) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'MANGGIH SUKA';
            }
        } elseif ($no_wuku == 18) {
            if ($saptawara == 1) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT MERANG';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'WERDHI PUTRA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BUAT SUKA';
            }
        } elseif ($no_wuku == 19) {
            if ($saptawara == 1) {
                $ekajalarsi = 'KAMARANAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'PATINING AMERTA';
            }
        } elseif ($no_wuku == 20) {
            if ($saptawara == 1) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'MANGGIH SUKA';
            }
        } elseif ($no_wuku == 21) {
            if ($saptawara == 1) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'LANGGENG KAYOHANAN';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'WERDHI SARWA MULE';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'DAHAT KINGKING';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'TININGGALING SUKA';
            }
        } elseif ($no_wuku == 22) {
            if ($saptawara == 1) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'BUAT KINGKING';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'PATINING AMERTA';
            }
        } elseif ($no_wuku == 23) {
            if ($saptawara == 1) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'KINASIHANIN JANA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BUAT SUKA';
            }
        } elseif ($no_wuku == 24) {
            if ($saptawara == 1) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'DAHAT KINGKING';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'SUKA RAHAYU';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'BUAT ASTAWA';
            }
        } elseif ($no_wuku == 25) {
            if ($saptawara == 1) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'KAMARANAN';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'SUKA RAHAYU';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'SUKA RAHAYU';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'SUKA RAHAYU';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            }
        } elseif ($no_wuku == 26) {
            if ($saptawara == 1) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'LANGGENG KAYOHANAN';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            }
        } elseif ($no_wuku == 27) {
            if ($saptawara == 1) {
                $ekajalarsi = 'BUAT MERANG';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'BUAT MERANG';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'WERDHI PUTRA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'KINASIHANIN AMERTA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SUKA PINANGGIH';
            }
        } elseif ($no_wuku == 28) {
            if ($saptawara == 1) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'MANGGIH SUKA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'LEWIH BAGIA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SUKA RAHAYU';
            }
        } elseif ($no_wuku == 29) {
            if ($saptawara == 1) {
                $ekajalarsi = 'BUAT LARA';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'SUKA PINANGGIH';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'PATINING AMERTA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'SIDHA KASOBAGIAN';
            }
        } elseif ($no_wuku == 30) {
            if ($saptawara == 1) {
                $ekajalarsi = 'LANGGENG KAYOHANAN';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'BUAT SEBET';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'BUAT ASTAWA';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'BUAT SUKA';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'TININGGALING SUKA';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'MANGGIH SUKA';
            }
        }
        
        return $ekajalarsi;        
    }
}
