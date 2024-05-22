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

use function PHPUnit\Framework\isNull;

class KalenderBaliAPI extends Controller
{
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
        
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        if ($tanggal_mulai === null && $tanggal_selesai === null) {
            return response()->json([
                'message' => 'Data tanggal mulai dan tanggal selesai tidak boleh kosong'
            ], 400);
        } else if ($tanggal_mulai === null) {
            return response()->json([
                'message' => 'Data tanggal mulai tidak boleh kosong'
            ], 400);
        } else if ($tanggal_selesai === null) {
            return response()->json([
                'message' => 'Data tanggal selesai tidak boleh kosong'
            ], 400);
        } 

        // Validasi format tanggal
        if (!strtotime($tanggal_mulai) || ctype_digit($tanggal_mulai) || !strtotime($tanggal_selesai) || ctype_digit($tanggal_selesai)) {
            return response()->json([
                'message' => 'Data tanggal harus berupa data tanggal yang valid'
            ], 400);
        }

        $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));

        if ($tanggal_selesai->lessThan($tanggal_mulai)) {
            return response()->json([
                'message' => 'Data tanggal_mulai tidak boleh lebih dari tanggal_selesai '
            ], 400);
        }

        $makna = $request->has('makna');
        $pura = $request->has('pura');
        $filter = $request->has('filter');
        if ($filter) {
            $filterValue = $request->input('filter');

            // Membersihkan filter dari spasi, emoji, bilangan, atau simbol lainnya kecuali koma
            $cleanFilter = preg_replace('/[^\w\s,]|[\d]|[\p{So}]/u', '', $filterValue);

            // Memisahkan string menjadi array dengan koma sebagai delimiter
            $filterArray = explode(',', $cleanFilter);

            // Membersihkan nilai-nilai array dari spasi kosong
            $filterArray = array_map('trim', $filterArray);

            // Hapus nilai yang duplikat dalam array filter
            $filterArray = array_unique($filterArray);

            if ($filterArray) {
                $allowed_values = ['wuku', 'ingkel', 'jejepan', 'lintang', 'pancasudha', 'pangarasan', 'rakam', 'watek_madya', 'watek_alit', 'neptu', 'ekajalarsi', 'zodiak', 'pratiti'];
            
                $filter = array_unique($filterArray);
                // cek apakah terdapat nilai filter yang sama dengan daftar nilai yang diperbolehkan
                $filter = array_intersect($filter, $allowed_values);
    
                // Filter nilai yang tidak terdapat di dalam daftar nilai yang diperbolehkan
                if (count($filter) < count($filterArray)) {
                    return response()->json([
                        'message' => 'Filter memiliki nilai yang tidak sesuai. Mohon periksa kembali inputan filter Anda.'
                    ], 400);
                }
            }
        }
        

        // $get_wuku = $request->input('wuku');
        // $get_ingkel = $request->input('ingkel');
        // $get_jejepan = $request->input('jejepan');
        // $get_lintang = $request->input('lintang');
        // $get_pancasudha = $request->input('pancasudha');
        // $get_pangarasan = $request->input('pangarasan');
        // $get_rakam = $request->input('rakam');
        // $get_watek_madya = $request->input('watek_madya');
        // $get_watek_alit = $request->input('watek_alit');
        // $get_neptu = $request->input('neptu');
        // $get_ekajalarsi = $request->input('ekajalarsi');
        // $get_zodiak = $request->input('zodiak');
        // $get_pratiti = $request->input('pratiti');

        $get_hari_raya = $request->input('hari_raya');
        if ($get_hari_raya) {
            $tanggal_mulai_obj = Carbon::now();
            $tanggal_mulai = $tanggal_mulai_obj->format('y-m-d');
            $tanggal_selesai = $tanggal_mulai_obj->addYear();
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

        // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $end = microtime(true);
            $executionTime = $end - $start;
            $executionTime = number_format($executionTime, 6);

            $response = [
                'pesan' => 'Sukses',
                'data' => $result,
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
                    $filter
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
            'pesan' => 'Sukses',
            'data' => $kalender,
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
        $filter
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
        // dd($hasilWuku);
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
        if ($path == "/api/cariHariRayaHindu") {
            // Perjikaan kalau dalam satu hari, hari raya nya lebih dari satu, misal Kajeng Kliwon dan Sugian Bali
            if (is_array($hariRaya) && count($hariRaya) > 1) {
                // dd('full');
                foreach ($hariRaya as $value) {
                    $data_piodalan = Piodalan::where('piodalan', $value)->get();
                    dd($data_piodalan);
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

        if ($path == '/api/cariElemenKalenderBali') {

            if ($filter) {
                $metode = array_values($filter);
                // dd($metode);
            } else {
                $metode = ['wuku', 'ingkel', 'jejepan', 'lintang', 'pancasudha', 'pangarasan', 'rakam', 'watek_madya', 'watek_alit', 'neptu', 'ekajalarsi', 'zodiak', 'pratiti', $hari];
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
}