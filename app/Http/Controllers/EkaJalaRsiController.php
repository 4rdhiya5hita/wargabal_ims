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
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Suka';
        } elseif ($saptawara == 3) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Manggih Suka';
            }
        } elseif ($no_wuku == 2) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Kamaranan';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Werdhi Putra';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Suka Rahayu';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Sidha Kasobagian';
            }
        } elseif ($no_wuku == 3) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Rahayu';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Buat Astawa';
            }
        } elseif ($no_wuku == 4) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Langgeng Kayohanan';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Werdhi Putra';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Sidha Kasobagian';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Bagna Mapasah';
            }
        } elseif ($no_wuku == 5) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Kamertaan';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Suka Pinanggih';
            }
        } elseif ($no_wuku == 6) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Buat Merang';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Buat Astawa';
            }
        } elseif ($no_wuku == 7) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Buat Merang';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Langgeng Kayohanan';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Werdhi Putra';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Werdhi Putra';
            }
        } elseif ($no_wuku == 8) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Bahu Putra';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Buat Sebet';
            }
        } elseif ($no_wuku == 9) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Kamaranan';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Kasobagian';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Subagia';
            }
        } elseif ($no_wuku == 10) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Bagna Mapasah';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Bagna Mapasah';
            }
        } elseif ($no_wuku == 11) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Manggih Bagia';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Patining Amerta';
            }
        } elseif ($no_wuku == 12) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Suka Rahayu';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Werdhi Putra';
            }
        } elseif ($no_wuku == 13) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Dahat Kingking';
            }
        } elseif ($no_wuku == 14) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Langgeng Kayohanan';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Werdhi Putra';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Kinasihanin Amerta';
            }
        } elseif ($no_wuku == 15) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Sidha Kasobagian';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Kinasihanin Jana';
            }
        } elseif ($no_wuku == 16) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Werdhi Putra';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Sidha Kasobagian';
            }
        } elseif ($no_wuku == 17) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Manggih Suka';
            }
        } elseif ($no_wuku == 18) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Merang';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Werdhi Putra';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Buat Suka';
            }
        } elseif ($no_wuku == 19) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Kamaranan';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Patining Amerta';
            }
        } elseif ($no_wuku == 20) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Manggih Suka';
            }
        } elseif ($no_wuku == 21) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Sidha Kasobagian';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Langgeng Kayohanan';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Werdhi Sarwa Mule';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Dahat Kingking';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Tininggaling Suka';
            }
        } elseif ($no_wuku == 22) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Buat Kingking';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Patining Amerta';
            }
        } elseif ($no_wuku == 23) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Kinasihanin Jana';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Sidha Kasobagian';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Buat Suka';
            }
        } elseif ($no_wuku == 24) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Dahat Kingking';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Suka Rahayu';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Buat Astawa';
            }
        } elseif ($no_wuku == 25) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Kamaranan';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Suka Rahayu';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Suka Rahayu';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Suka Rahayu';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Sidha Kasobagian';
            }
        } elseif ($no_wuku == 26) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Langgeng Kayohanan';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Sidha Kasobagian';
            }
        } elseif ($no_wuku == 27) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Buat Merang';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Buat Merang';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Sidha Kasobagian';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Werdhi Putra';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Kinasihanin Amerta';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Suka Pinanggih';
            }
        } elseif ($no_wuku == 28) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Manggih Suka';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Lewih Bagia';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Suka Rahayu';
            }
        } elseif ($no_wuku == 29) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Buat Lara';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Suka Pinanggih';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Patining Amerta';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Sidha Kasobagian';
            }
        } elseif ($no_wuku == 30) {
            if ($saptawara == 1) {
                $ekajalarsi = 'Langgeng Kayohanan';
            } elseif ($saptawara == 2) {
                $ekajalarsi = 'Buat Sebet';
            } elseif ($saptawara == 3) {
                $ekajalarsi = 'Buat Astawa';
            } elseif ($saptawara == 4) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 5) {
                $ekajalarsi = 'Buat Suka';
            } elseif ($saptawara == 6) {
                $ekajalarsi = 'Tininggaling Suka';
            } elseif ($saptawara == 7) {
                $ekajalarsi = 'Manggih Suka';
            }
        }
        
        return $ekajalarsi;        
    }
}
