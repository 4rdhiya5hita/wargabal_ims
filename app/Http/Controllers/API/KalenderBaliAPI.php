<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EkaJalaRsiController;
use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\IngkelController;
use App\Http\Controllers\JejepanController;
use App\Http\Controllers\LintangController;
use App\Http\Controllers\NeptuController;
use App\Http\Controllers\PancaSudhaController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PangarasanController;
use App\Http\Controllers\PengalantakaController;
use App\Http\Controllers\PratitiController;
use App\Http\Controllers\RakamController;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\WatekAlitController;
use App\Http\Controllers\WatekMadyaController;
use App\Http\Controllers\WukuController;
use App\Http\Controllers\ZodiakController;
use App\Jobs\PerhitunganKalender;
use App\Models\Piodalan;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KalenderBaliAPI extends Controller
{
    // public function get()
    // {
    //     $q = DB::table('result')->get();
    //     return response()->json([
    //         'message' => 'Success',
    //         'data' => $q,
    //     ], 200);
    // }

    public function tes()
    {
        $q = DB::select('CALL searchHariRaya(1, 2023)');
        return response()->json([
            'message' => 'Success',
            'data' => $q,
        ], 200);
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/


    public function searchHariRayaAPI(Request $request)
    {
        $start = microtime(true);
        $fullUrl = $request->fullUrl();
        $path = parse_url($fullUrl, PHP_URL_PATH);
        // dd($path);

        $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));
        $makna = $request->has('makna');
        $pura = $request->has('pura');
        // dd($jejepan_asli);
        $lengkap = $request->input('lengkap');
        $get_wuku = $request->input('wuku');
        $get_ingkel = $request->input('ingkel');
        $get_jejepan = $request->input('jejepan');
        $get_lintang = $request->input('lintang');
        $get_pancasudha = $request->input('pancasudha');
        $get_pangarasan = $request->input('pangarasan');
        $get_rakam = $request->input('rakam');
        $get_watek_madya = $request->input('watek_madya');
        $get_watek_alit = $request->input('watek_alit');
        $get_neptu = $request->input('neptu');
        $get_ekajalarsi = $request->input('ekajalarsi');
        $get_zodiak = $request->input('zodiak');
        $get_pratiti = $request->input('pratiti');

        $get_hari_raya = $request->input('hari_raya');
        if ($get_hari_raya) {
            $tanggal_mulai_obj = Carbon::now();
            $tanggal_mulai = $tanggal_mulai_obj->format('y-m-d');
            $tanggal_selesai = $tanggal_mulai_obj->addYear();
        }

        if ($lengkap) {
            if ($get_wuku || $get_ingkel || $get_jejepan || $get_lintang || $get_pancasudha || $get_pangarasan || $get_rakam || $get_watek_madya || $get_watek_alit || $get_neptu || $get_ekajalarsi || $get_zodiak || $get_pratiti) {
                return response()->json(['message' => 'Input parameter tidak valid. Parameter lengkap tidak bisa digabung dengan parameter lain']);
            }
        }

        // $tanggal_mulai = '1999-04-29';
        // $tanggal_selesai = '1999-04-30';
        // $pura = '';
        // $makna = '';
        // $lengkap = '';
        // $get_ingkel = '';
        // $get_jejepan = '';
        // $get_lintang = '';
        // $get_pancasudha = '';
        // $get_pangarasan = '';
        // $get_rakam = '';
        // $get_watek_madya = '';
        // $get_watek_alit = '';
        // $get_neptu = '';
        // $get_ekajalarsi = '';
        // $get_zodiak = '';
        // $get_pratiti = '';
        // dd($lengkap);

        $cacheKey = 'processed-data-' . $tanggal_mulai . '-' . $tanggal_selesai;

        $tanggal_mulai = Carbon::parse($tanggal_mulai);
        $tanggal_selesai = Carbon::parse($tanggal_selesai);

        if ($tanggal_selesai->lessThan($tanggal_mulai)) {
            return response()->json(['message' => 'Input tanggal_mulai dan tanggal_selesai tidak valid'], 400);
        }

        // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $end = microtime(true);
            $executionTime = $end - $start;
            $executionTime = number_format($executionTime, 6);

            $response = [
                'message' => 'Sukses',
                'result' => $result,
                'waktu_eksekusi' => $executionTime,
            ];
        }

        $kalender = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $kalender[] = [
                'tanggal' => $tanggal_mulai->toDateString(),
                'kalender' => $this->getHariRaya(
                    $tanggal_mulai->toDateString(),
                    $path,
                    $makna,
                    $pura,
                    $lengkap,
                    $get_wuku,
                    $get_ingkel,
                    $get_jejepan,
                    $get_lintang,
                    $get_pancasudha,
                    $get_pangarasan,
                    $get_rakam,
                    $get_watek_madya,
                    $get_watek_alit,
                    $get_neptu,
                    $get_ekajalarsi,
                    $get_zodiak,
                    $get_pratiti,
                ),
            ];
            $tanggal_mulai->addDay();
        }

        $minutes = 60; // Durasi penyimpanan cache dalam menit
        Cache::put($cacheKey, $kalender, $minutes); // Menyimpan hasil pemrosesan data dalam cache

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'message' => 'Sukses',
            'result' => $kalender,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
        // return view('dashboard.index', compact('kalender'));
    }

    private function getHariRaya(
        $tanggal,
        $path,
        $makna,
        $pura,
        $lengkap,
        $get_wuku,
        $get_ingkel,
        $get_jejepan,
        $get_lintang,
        $get_pancasudha,
        $get_pangarasan,
        $get_rakam,
        $get_watek_madya,
        $get_watek_alit,
        $get_neptu,
        $get_ekajalarsi,
        $get_zodiak,
        $get_pratiti
    ) {
        // dd($get_jejepan);
        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $isPangelong = true;
            $noNgunaratri = 46;
            $isNgunaratri = false;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $isPangelong = true;
            $noNgunaratri = 50;
            $isNgunaratri = false;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $isPangelong = true;
            $noNgunaratri = 22;
            $isNgunaratri = false;
        }

        // Panggil semua controller yang dibutuhkan
        $wukuController = new WukuController();
        $saptaWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();
        // $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
        $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

        // $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $namaSaptawara = $saptaWaraController->getNamaSaptaWara($saptawara);
        $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);
        $namaTriwara = $triWaraController->getNamatriwara($triwara);

        // $pengalantaka_dan_hariSasih = $pengalantakaController->getPengalantaka($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        // $hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);

        $pengalantaka_dan_hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        // HASIL DARI KODE DIATAS: 
        // return [
        //     'penanggal_1' => $penanggal,
        //     'penanggal_2' => $penanggal2,
        //     'pengalantaka' => $pengalantaka,
        // ];

        if ($tanggal > '2002-01-01' || $tanggal < '1992-01-01') {
            if (strtotime($tanggal) < strtotime($refTanggal)) {
                $no_sasih = $hariSasihController->getSasihBefore1992(
                    $tanggal,
                    $refTanggal,
                    $penanggal,
                    $noNgunaratri,
                    $noSasih,
                    $tahunSaka
                );
            } else {
                $no_sasih = $hariSasihController->getSasihAfter2002(
                    $tanggal,
                    $refTanggal,
                    $penanggal,
                    $noNgunaratri,
                    $noSasih,
                    $tahunSaka
                );
            }
        } else {
            $no_sasih = $hariSasihController->getSasihBetween(
                $tanggal,
                $refTanggal,
                $penanggal,
                $noNgunaratri,
                $noSasih,
                $tahunSaka
            );
        }
        // dd($namaWuku, $namaSaptawara, $namaPancawara, $namaTriwara);
        $hariRaya = $hariRayaController->getHariRaya($tanggal, $pengalantaka_dan_hariSasih['penanggal_1'], $pengalantaka_dan_hariSasih['penanggal_2'], $pengalantaka_dan_hariSasih['pengalantaka'], $no_sasih['no_sasih'], $triwara, $pancawara, $saptawara, $hasilWuku);
        $piodalan = $namaSaptawara . ' ' . $namaPancawara . ' ' . $namaWuku;
        // dd($hariRaya);

        // Mencari hari berdasarkan tanggal
        $hariController = new HariAPI();
        $hari = $hariController->getHari($tanggal);

        $kalenderLengkap = [];
        // Perjikaan kalau parameter di urlnya ada masukkin &makna / &pura
        if ($path == "/api/searchHariRayaAPI") {
            // Perjikaan kalau dalam satu hari, hari raya nya lebih dari satu, misal Kajeng Kliwon dan Sugian Bali
            if (is_array($hariRaya) && count($hariRaya) > 1) {
                // dd('full');
                foreach ($hariRaya as $value) {
                    $data_piodalan = Piodalan::where('piodalan', $value)->get();
                    if ($data_piodalan->isEmpty()) {
                        $data_piodalan = Piodalan::where('piodalan', $piodalan)->get();
                    }
                    foreach ($data_piodalan as $item) {
                        $ambil_makna = $item->arti;
                        $ambil_pura = $item->pura;
                    }
                    // Perjikaan sesuai parameter urlnya
                    if ($makna && $pura) {
                        array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $value, 'makna' => $ambil_makna, 'pura' => $ambil_pura]);
                    } elseif ($makna && !$pura) {
                        array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $value, 'makna' => $ambil_makna]);
                    } elseif ($pura && !$makna) {
                        array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $value, 'pura' => $ambil_pura]);
                    } else {
                        array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $value]);
                    }
                }
                // dd($kalenderLengkap);
            }
            // // Perjikaan kalau tidak ada hari raya apapun pada hari itu
            // elseif (is_array($hariRaya) && count($hariRaya) == 1) {
            //     $data_piodalan = Piodalan::where('piodalan', $hariRaya)->get();
                // dd($hariRaya);
                // dd($data_piodalan);
            //     foreach ($data_piodalan as $item) {
            //         $ambil_makna = $item->arti;
            //         $ambil_pura = $item->pura;
            //         // Perjikaan sesuai parameter urlnya
            //         if ($makna && $pura) {
            //             array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'makna' => $ambil_makna, 'pura' => $ambil_pura]);
            //         } elseif (!$pura) {
            //             array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'makna' => $ambil_makna]);
            //         } else {
            //             array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'pura' => $ambil_pura]);
            //         }
            //     }
            // }
            
            else {
                // Perjikaan kalau dalam satu hari, hari raya nya hanya satu saja misal Hari Raya Saraswati saja, Galungan saja
                if ($hariRaya[0] != '-') {
                    // dd('yes');
                    $data_piodalan = Piodalan::where('piodalan', $hariRaya[0])->get();
                    if ($data_piodalan->isEmpty()) {
                        $data_piodalan = Piodalan::where('piodalan', $piodalan)->get();
                    }
                    // dd($data_piodalan);
                    foreach ($data_piodalan as $item) {
                        $ambil_makna = $item->arti;
                        $ambil_pura = $item->pura;
                        // Perjikaan sesuai parameter urlnya
                        if ($makna && $pura) {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'makna' => $ambil_makna, 'pura' => $ambil_pura]);
                        } elseif ($makna && !$pura) {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'makna' => $ambil_makna]);
                        } elseif ($pura && !$makna) {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'pura' => $ambil_pura]);
                        } else {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0]]);
                        }
                    }
                }
                // Perjikaan kalau tidak ada hari raya besarnya, maka dicari dengan piodalannya misalnya: Wraspati Umanis Dunggulan
                elseif ($hariRaya[0] == '-' && $piodalan != '-') {
                    // dd('ok');
                    $data_piodalan = Piodalan::where('piodalan', $piodalan)->get();
                    // dd($data_piodalan);
                    if ($data_piodalan->isEmpty()) {
                        array_push($kalenderLengkap, '-');
                    } else {
                    // if (!$data_piodalan->isEmpty()) {
                        // dd($data_piodalan);
                        foreach ($data_piodalan as $item) {
                            $ambil_makna = $item->arti;
                            $ambil_pura = $item->pura;
                        }
                        // Perjikaan sesuai parameter urlnya
                        if ($makna && $pura) {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'makna' => $ambil_makna, 'pura' => $ambil_pura]);
                        } elseif ($makna && !$pura) {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'makna' => $ambil_makna]);
                        } elseif ($pura && !$makna) {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0], 'pura' => $ambil_pura]);
                        } else {
                            array_push($kalenderLengkap, ['penamaan_hari_bali' => $piodalan, 'hari_raya' => $hariRaya[0]]);
                        }
                    }
                } else {
                    // dd('no');
                    array_push($kalenderLengkap, '-');
                }
            }
            // $kalenderLengkap = array_reduce($kalenderLengkap, function ($carry, $item) { // Menggabungkan array multidimensi menjadi satu array
            //     return array_merge($carry, $item);
            // }, []);
        }
        // dd($kalenderLengkap);
        // Perjikaan kalau parameter di urlnya ada &lengkap
        // fungsi: mencari detail setiap tanggal pada kalender

        if ($path == '/api/searchKalenderAPI') {

            if ($lengkap) {
                $metode = ['wuku', 'ingkel', 'jejepan', 'lintang', 'pancasudha', 'pangarasan', 'rakam', 'watek_madya', 'watek_alit', 'neptu', 'ekajalarsi', 'zodiak', 'pratiti', $hari];
            } else {
                $metode = [$get_wuku, $get_ingkel, $get_jejepan, $get_lintang, $get_pancasudha, $get_pangarasan, $get_rakam, $get_watek_madya, $get_watek_alit, $get_neptu, $get_ekajalarsi, $get_zodiak, $get_pratiti];
            }
            
            $filteredArray = array_filter($metode);
            // Perjikaan kalau url tidak memiliki parameter lain (hanya url biasa), masukkan hari raya saja
            if (count($filteredArray) === 0) {
                array_push($kalenderLengkap, ['-']);
                
            // Perjikaan kalau url memiliki parameter lain
            } else {
                $kombinasi_array = [];
                $urip_pancawara = $pancaWaraController->getUripPancaWara($pancawara);
                $urip_saptawara = $saptaWaraController->getUripsaptawara($saptawara);

                $ingkelController = new IngkelController();
                $jejepanController = new JejepanController();
                $lintangController = new LintangController();
                $pancasudhaController = new PancaSudhaController();
                $pangarasanController = new PangarasanController();
                $rakamController = new RakamController();
                $watek_madyaController = new WatekMadyaController();
                $watek_alitController = new WatekAlitController();
                $neptuController = new NeptuController();
                $ekajalarsiController = new EkaJalaRsiController();
                $zodiakController = new ZodiakController();
                $pratitiController = new PratitiController();

                // Lakukan iterasi melalui pilihan metode yang dipilih
                foreach ($metode as $value) {
                    if ($value == 'wuku') {
                        array_push($kombinasi_array, ['wuku' => $namaWuku]);
                    }
                    if ($value == 'ingkel') {
                        $ingkel = $ingkelController->Ingkel($hasilWuku);
                        array_push($kombinasi_array, ['ingkel' => $ingkel]);
                    }
                    if ($value == 'jejepan') {
                        $jejepan = $jejepanController->Jejepan($hasilAngkaWuku);
                        array_push($kombinasi_array, ['jejepan' => $jejepan]);
                    }
                    if ($value == 'lintang') {
                        $lintang = $lintangController->Lintang($tanggal, $refTanggal);
                        array_push($kombinasi_array, ['lintang' => $lintang]);
                    }
                    if ($value == 'pancasudha') {
                        $pancasudha = $pancasudhaController->Pancasudha($pancawara, $saptawara);
                        array_push($kombinasi_array, ['pancasudha' => $pancasudha]);
                    }
                    if ($value == 'pangarasan') {
                        $pangarasan = $pangarasanController->Pangarasan($urip_pancawara, $urip_saptawara);
                        array_push($kombinasi_array, ['pangarasan' => $pangarasan]);
                    }
                    if ($value == 'rakam') {
                        $rakam = $rakamController->Rakam($pancawara, $saptawara);
                        array_push($kombinasi_array, ['rakam' => $rakam]);
                    }
                    if ($value == 'watek_madya') {
                        $watek_madya = $watek_madyaController->WatekMadya($urip_pancawara, $urip_saptawara);
                        array_push($kombinasi_array, ['watek_madya' => $watek_madya]);
                    }
                    if ($value == 'watek_alit') {
                        $watek_alit = $watek_alitController->WatekAlit($urip_pancawara, $urip_saptawara);
                        array_push($kombinasi_array, ['watek_alit' => $watek_alit]);
                    }
                    if ($value == 'neptu') {
                        $neptu = $neptuController->Neptu($urip_pancawara, $urip_saptawara);
                        array_push($kombinasi_array, ['neptu' => $neptu]);
                    }
                    if ($value == 'ekajalarsi') {
                        $ekajalarsi = $ekajalarsiController->EkaJalaRsi($hasilWuku, $saptawara);
                        array_push($kombinasi_array, ['ekajalarsi' => $ekajalarsi]);
                    }
                    if ($value == 'zodiak') {
                        $zodiak = $zodiakController->Zodiak($tanggal);
                        array_push($kombinasi_array, ['zodiak' => $zodiak]);
                    }
                    if ($value == 'pratiti') {
                        $pratiti = $pratitiController->Pratiti($pengalantaka_dan_hariSasih['pengalantaka'], $no_sasih['no_sasih'], $pengalantaka_dan_hariSasih['penanggal_1']);
                        array_push($kombinasi_array, ['pratiti' => $pratiti]);
                    }

                    // Perjikaan kalau parameter di urlnya ada &hari
                    if ($value == 'Sunday') {
                        array_push($kombinasi_array, ['hari' => 'Minggu']);
                    } elseif ($value == 'Saturday') {
                        array_push($kombinasi_array, ['hari' => 'Sabtu']);
                    } elseif ($value == 'Friday') {
                        array_push($kombinasi_array, ['hari' => 'Jumat']);
                    } elseif ($value == 'Thursday') {
                        array_push($kombinasi_array, ['hari' => 'Kamis']);
                    } elseif ($value == 'Wednesday') {
                        array_push($kombinasi_array, ['hari' => 'Rabu']);
                    } elseif ($value == 'Tuesday') {
                        array_push($kombinasi_array, ['hari' => 'Selasa']);
                    } elseif ($value == 'Monday') {
                        array_push($kombinasi_array, ['hari' => 'Senin']);
                    }
                }
                $kalenderLengkap = array_reduce($kombinasi_array, function ($carry, $item) { // Menggabungkan array multidimensi menjadi satu array
                    return array_merge($carry, $item);
                }, []);
                // dd($kalenderLengkap);
                // array_push($kalenderLengkap, [$ingkel, $jejepan, $lintang, $pancasudha, $pangarasan, $rakam, $watek_madya, $watek_alit, $neptu, $ekajalarsi, $zodiak, $pratiti]);
            }
        }

        // dd($kalenderLengkap);
        return $kalenderLengkap;
    }

    public function searchHariRayaAsli(Request $request)
    {
        // $tanggal = '2023-05-20';
        // $tanggal = Carbon::createFromFormat('Y-m-d', $request->input('tanggal'))->toDateString();
        // $tanggal = Carbon::createFromFormat('Y-m-d', $query)->toDateString();
        $tanggal = [];
        $tanggal_mulai = '2023-08-27';
        $tanggal_selesai = '2023-09-13';
        $tanggal_mulai = Carbon::parse($tanggal_mulai);
        $tanggal_selesai = Carbon::parse($tanggal_selesai);

        // $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        // $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));

        $tanggal = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $tanggal[] = $tanggal_mulai->toDateString();
            $tanggal_mulai->addDay();
        }

        if ($tanggal_mulai >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $isPangelong = true;
            $noNgunaratri = 46;
            $isNgunaratri = false;
        } elseif ($tanggal_mulai < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $isPangelong = true;
            $noNgunaratri = 50;
            $isNgunaratri = false;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $isPangelong = true;
            $noNgunaratri = 22;
            $isNgunaratri = false;
        }

        // dd($tanggal);
        // WUKU dan WEWARAN
        $wukuController = new WukuController();
        $saptaWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();

        // HARI RAYA
        $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        $kalender = [];

        for ($i = 0; $i < count($tanggal); $i++) {
            $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
            $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
            // $namaWuku = $wukuController->getNamaWuku($hasilWuku);

            $saptawara = $saptaWaraController->getSaptawara($tanggal[$i]);
            // $uripSaptawara = $saptaWaraController->getUripSaptaWara($saptawara);
            // $namaSaptawara = $saptaWaraController->getNamaSaptaWara($saptawara);


            $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
            // $uripPancawara = $pancaWaraController->getUripPancaWara($pancawara);
            // $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);


            $triwara = $triWaraController->gettriwara($hasilAngkaWuku);
            // $namaTriwara = $triWaraController->getNamatriwara($triwara);

            $pengalantaka = $pengalantakaController->getPengalantaka($tanggal[$i], $refTanggal, $penanggal, $noNgunaratri);
            $hariSasih = $hariSasihController->getHariSasih($tanggal[$i], $refTanggal, $penanggal, $noNgunaratri);

            if ($tanggal[$i] > '2002-01-01' || $tanggal[$i] < '1992-01-01') {
                if (strtotime($tanggal[$i]) < strtotime($refTanggal)) {
                    $no_sasih = $hariSasihController->getSasihBefore1992(
                        $tanggal[$i],
                        $refTanggal,
                        $penanggal,
                        $noNgunaratri,
                        $noSasih,
                        $tahunSaka
                    );
                } else {
                    $no_sasih = $hariSasihController->getSasihAfter2002(
                        $tanggal[$i],
                        $refTanggal,
                        $penanggal,
                        $noNgunaratri,
                        $noSasih,
                        $tahunSaka
                    );
                }
            } else {
                $no_sasih = $hariSasihController->getSasihBetween(
                    $tanggal[$i],
                    $refTanggal,
                    $penanggal,
                    $noNgunaratri,
                    $noSasih,
                    $tahunSaka
                );
            }

            // dd($hariSasih);            
            // dd($no_sasih['no_sasih']);
            $hariRaya = $hariRayaController->getHariRaya($tanggal[$i], $hariSasih['penanggal_1'], $hariSasih['penanggal_2'], $pengalantaka, $no_sasih['no_sasih'], $triwara, $pancawara, $saptawara, $hasilWuku);

            // $hariRaya = $hariRayaController->getHariRaya($pancawara,$saptawara,$hasilWuku);

            // // Menyimpan $tanggal[$i] dan $hariRaya ke dalam array $kalender
            // array_push($kalender, [
            //     'tanggal' => $tanggal[$i],
            //     'hariRaya' => $hariRaya,
            //     'namaWuku' => $namaWuku,
            //     'namaSaptawara' => $namaSaptawara,
            //     'namaPancawara' => $namaPancawara,
            // ]);

            array_push($kalender, [
                'tanggal' => $tanggal[$i],
                'hasilAngkaWuku' => $hasilAngkaWuku,
                'triwara' => $triwara,
                // 'namaTriwara' => $namaTriwara,
                'saptawara' => $saptawara,
                // 'namaSaptawara' => $namaSaptawara,
                'pancawara' => $pancawara,
                // 'namaPancawara' => $namaPancawara,
                'pengalantaka' => $pengalantaka,
                'hariRaya' => $hariRaya,
            ]);
        }

        $response = [
            'message' => 'Success',
            'data' => [
                'kalender' => $kalender,
                // 'tanggal' => $tanggal,
                // 'hariRaya' => $hariRaya,
                // 'namaWuku' => $namaWuku,
                // 'namaPancawara' => $namaPancawara,
                // 'namaSaptawara' => $namaSaptawara
            ]
        ];

        dd($response);

        return response()->json($response, 200);







        // return response()->json([
        //     'message' => 'Success',
        //     'data' => [
        //         'tanggal' => implode(', ', $tanggal),
        //     ]
        // ], 200);

    }

    public function searchTanggalHariRaya(Request $request)
    {
        // $tanggal = '2023-05-20';
        // $tanggal = Carbon::createFromFormat('Y-m-d', $request->input('tanggal'))->toDateString();
        // $tanggal = Carbon::createFromFormat('Y-m-d', $query)->toDateString();
        $tanggal = [];
        // $tanggal_mulai = '2023-01-15';
        // $tanggal_selesai = '2023-03-10';
        $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));

        $tanggal = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $tanggal[] = $tanggal_mulai->toDateString();
            $tanggal_mulai->addDay();
        }

        if ($tanggal_mulai >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $isPangelong = true;
            $noNgunaratri = 46;
            $isNgunaratri = false;
        } elseif ($tanggal_mulai < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $isPangelong = true;
            $noNgunaratri = 50;
            $isNgunaratri = false;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $isPangelong = true;
            $noNgunaratri = 22;
            $isNgunaratri = false;
        }

        // dd($tanggal);
        // WUKU dan WEWARAN
        $wukuController = new WukuController();
        $saptaWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();

        //PROSES KE HARI RAYA


        // HARI RAYA
        $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        $kalender = [];

        for ($i = 0; $i < count($tanggal); $i++) {
            $hasilAngkaWuku = $wukuController->getNoWuku($tanggal[$i], $angkaWuku, $refTanggal);
            $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
            $namaWuku = $wukuController->getNamaWuku($hasilWuku);

            $saptawara = $saptaWaraController->getSaptawara($tanggal[$i]);
            $uripSaptawara = $saptaWaraController->getUripSaptaWara($saptawara);
            $namaSaptawara = $saptaWaraController->getNamaSaptaWara($saptawara);


            $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
            $uripPancawara = $pancaWaraController->getUripPancaWara($pancawara);
            $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);


            $hariRaya = 1;

            array_push($kalender, [
                'tanggal' => $tanggal[$i],
                'hasilAngkaWuku' => $hasilAngkaWuku,
                // 'saptawara' => $saptawara,    
                'namaSaptawara' => $namaSaptawara,
                // 'pancawara' => $pancawara,    
                'namaPancawara' => $namaPancawara,
                'hariRaya' => $hariRaya,
            ]);
        }

        return response()->json([
            'message' => 'Success',
            'data' => [
                'kalender' => $kalender,
            ]
        ], 200);

        // return response()->json([
        //     'message' => 'Success',
        //     'data' => [
        //         'tanggal' => implode(', ', $tanggal),
        //     ]
        // ], 200);

    }

    public function processData(Request $request)
    {
        // $start = microtime(true);
        // $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        // $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));

        $tanggal_mulai = '2023-07-27';
        $tanggal_selesai = '2023-08-13';
        $tanggal_mulai = Carbon::parse($tanggal_mulai);
        $tanggal_selesai = Carbon::parse($tanggal_selesai);


        $cacheKey = 'processed-data-' . $tanggal_mulai->toDateString();
        $minutes = 60; // Durasi penyimpanan cache dalam menit (sesuaikan dengan kebutuhan)

        // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            // $end = microtime(true);
            // $executionTime = $end - $start;
            // $executionTime = number_format($executionTime, 6);

            return response()->json([
                'message' => 'Data has been retrieved from cache.',
                'hari_raya' => $result,
                // 'executionTime' => $executionTime
            ]);
        }

        $tanggal = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $tanggal[] = $tanggal_mulai->toDateString();
            $tanggal_mulai->addDay();
        }

        $chunks = array_chunk($tanggal, 4);

        // Inisialisasi array untuk menyimpan ID job
        $jobIds = [];
        $kalender = [];

        // Memasukkan job ke dalam antrian untuk setiap bagian data
        foreach ($chunks as $chunk) {
            $job = new PerhitunganKalender($chunk);
            $response = $job->handle();
            $jobIds[] = $this->dispatch($job);
            array_push($kalender, $response);
        }

        // $mergedJob = array_merge(...$kalender);
        // dd($kalender);

        // $end = microtime(true);
        // $executionTime = $end - $start;
        // $executionTime = number_format($executionTime, 6);

        // Menyimpan hasil pemrosesan data dalam cache
        Cache::put($cacheKey, $kalender, $minutes);

        // dd('hasil akhir: ', $kalender);

        // Mengembalikan response atau melakukan tindakan lainnya setelah memasukkan job ke dalam antrian
        return response()->json([
            'message' => 'Data processing job has been queued.',
            'hari_raya' => $kalender,
            // 'executionTime' => $executionTime
        ]);
    }

    // public function processData(Request $request)
    // {
    //     $start = microtime(true);
    //     $tanggal_mulai = '2023-08-27';
    //     $tanggal_selesai = '2023-09-23';
    //     $tanggal_mulai = Carbon::parse($tanggal_mulai);
    //     $tanggal_selesai = Carbon::parse($tanggal_selesai);

    //     // $cacheKey = 'processed-data-' . $tanggal_mulai->toDateString() . '-' . $tanggal_selesai->toDateString();
    //     // $minutes = 60; // Durasi penyimpanan cache dalam menit (sesuaikan dengan kebutuhan)

    //     // // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
    //     // if (Cache::has($cacheKey)) {
    //     //     $result = Cache::get($cacheKey);
    //     //     $end = microtime(true);
    //     //     $executionTime = $end - $start;
    //     //     $executionTime = number_format($executionTime, 6);

    //     //     return response()->json([
    //     //         'message' => 'Data has been retrieved from cache.',
    //     //         'hari_raya' => $result,
    //     //         'executionTime' => $executionTime
    //     //     ]);
    //     // }

    //     $tanggal = [];

    //     while ($tanggal_mulai <= $tanggal_selesai) {
    //         $tanggal[] = $tanggal_mulai->toDateString();
    //         $tanggal_mulai->addDay();
    //     }

    //     $chunks = array_chunk($tanggal, 4);

    //     // Inisialisasi array untuk menyimpan ID job
    //     $jobIds = [];
    //     $kalender = [];

    //     // Memasukkan job ke dalam antrian untuk setiap bagian data
    //     foreach ($chunks as $chunk) {
    //         $job = new PerhitunganKalender($chunk);
    //         $jobIds[] = $this->dispatch($job);
    //         array_push($kalender, $job);
    //     }

    //     $mergedJob = array_merge(...$kalender);
    //     dd($mergedJob);

    //     $end = microtime(true);
    //     $executionTime = $end - $start;
    //     $executionTime = number_format($executionTime, 6);

    //     // Menyimpan hasil pemrosesan data dalam cache
    //     // Cache::put($cacheKey, $kalender, $minutes);

    //     dd('hasil akhir: ', $kalender);

    //     // Mengembalikan response atau melakukan tindakan lainnya setelah memasukkan job ke dalam antrian
    //     return response()->json([
    //         'message' => 'Data processing job has been queued.',
    //         'job_ids' => $jobIds,
    //         'hari_raya' => $kalender,
    //         'executionTime' => $executionTime
    //     ]);
    // }
}
