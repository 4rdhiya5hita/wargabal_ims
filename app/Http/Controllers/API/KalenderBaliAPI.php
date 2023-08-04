<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PengalantakaController;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\WukuController;
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
    //     $q = DB::table('kalender_hari_raya')->get();
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

        $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));
        $makna = $request->has('makna');
        $pura = $request->has('pura');
        // $tanggal_mulai = '2023-07-28';
        // $tanggal_selesai = '2023-07-29';
        // $pura = 'yes';
        // $makna = 'yes';
        $cacheKey = 'processed-data-' . $tanggal_mulai . '-' . $tanggal_selesai;

        $tanggal_mulai = Carbon::parse($tanggal_mulai);
        $tanggal_selesai = Carbon::parse($tanggal_selesai);

        // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $end = microtime(true);
            $executionTime = $end - $start;
            $executionTime = number_format($executionTime, 6);

            $response = [
                'message' => 'Sukses',
                $result,
                'waktu_eksekusi' => $executionTime,
            ];
        }

        $kalender = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $kalender[] = [
                'tanggal' => $tanggal_mulai->toDateString(),
                'hariRaya' => $this->getHariRaya($tanggal_mulai->toDateString(), $makna, $pura),
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
            $kalender,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
        // return view('dashboard.index', compact('kalender'));
    }

    private function getHariRaya($tanggal, $makna, $pura)
    {
        // dd($tanggal);
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
        $saptawaraWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();
        // $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
        $saptawara = $saptawaraWaraController->getSaptawara($tanggal);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

        // $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $namaSaptawara = $saptawaraWaraController->getNamaSaptaWara($saptawara);
        $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);
        $namaTriwara = $triWaraController->getNamatriwara($triwara);

        // $pengalantaka_dan_hariSasih = $pengalantakaController->getPengalantaka($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        // $hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        $pengalantaka_dan_hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);
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
        $piodalan = $namaSaptawara . ' ' . $namaPancawara . ' ' . $namaTriwara;

        $hariRayaLengkap = [];
        // Perjikaan kalau parameter di urlnya ada masukkin &makna / &pura
        if ($makna || $pura) {
            // Perjikaan kalau dalam satu hari, hari raya nya lebih dari satu, misal Kajeng Kliwon dan Sugian Bali
            if (is_array($hariRaya) && count($hariRaya) > 1) {
                foreach ($hariRaya as $value) {
                    $data_piodalan = Piodalan::where('piodalan', $value)->get();
                    foreach ($data_piodalan as $item) {
                        $ambil_makna = $item->arti;
                        $ambil_pura = $item->pura;
                    }
                    // Perjikaan kalau berdasarkan hari raya tidak ketemu makna/pura nya, maka dicari dengan piodalannya misalnya: Wraspati Umanis Dunggulan
                    if ($data_piodalan->isEmpty()) {
                        if ($piodalan !== '-') {
                            $data_piodalan = Piodalan::where('piodalan', $piodalan)->get();
                            foreach ($data_piodalan as $item) {
                                $ambil_makna = $item->arti;
                                $ambil_pura = $item->pura;
                            }
                        } else {
                            $ambil_makna = '-';
                            $ambil_pura = '-';
                        }
                    }
                    // Perjikaan sesuai parameter urlnya
                    if ($makna && $pura) {
                        array_push($hariRayaLengkap, [$value, $ambil_makna, $ambil_pura]);
                    } elseif (!$pura) {
                        array_push($hariRayaLengkap, [$value, $ambil_makna]);
                    } else {
                        array_push($hariRayaLengkap, [$value, $ambil_pura]);
                    }
                }
            }
            // Perjikaan kalau tidak ada hari raya apapun pada hari itu
            elseif ($hariRaya !== '-') {
                $data_piodalan = Piodalan::where('piodalan', $hariRaya)->get();
                foreach ($data_piodalan as $item) {
                    $ambil_makna = $item->arti;
                    $ambil_pura = $item->pura;
                }
                // Perjikaan kalau berdasarkan hari raya tidak ketemu makna/pura nya, maka dicari dengan piodalannya misalnya: Wraspati Umanis Dunggulan
                if ($data_piodalan->isEmpty()) {
                    if ($piodalan !== '-') {
                        $data_piodalan = Piodalan::where('piodalan', $piodalan)->get();
                        foreach ($data_piodalan as $item) {
                            $ambil_makna = $item->arti;
                            $ambil_pura = $item->pura;
                        }
                    } else {
                        $ambil_makna = '-';
                        $ambil_pura = '-';
                    }
                }
                // Perjikaan sesuai parameter urlnya
                if ($makna && $pura) {
                    array_push($hariRayaLengkap, [$hariRaya, $ambil_makna, $ambil_pura]);
                } elseif (!$pura) {
                    array_push($hariRayaLengkap, [$hariRaya, $ambil_makna]);
                } else {
                    array_push($hariRayaLengkap, [$hariRaya, $ambil_pura]);
                }    
            } 
            // Perjikaan kalau dalam satu hari, hari raya nya hanya satu saja misal Hari Raya Saraswati saja, Galungan saja
            else {
                $ambil_makna = '-';
                $ambil_pura = '-';
                // Perjikaan sesuai parameter urlnya
                if ($makna && $pura) {
                    array_push($hariRayaLengkap, [$hariRaya, $ambil_makna, $ambil_pura]);
                } elseif (!$pura) {
                    array_push($hariRayaLengkap, [$hariRaya, $ambil_makna]);
                } else {
                    array_push($hariRayaLengkap, [$hariRaya, $ambil_pura]);
                }
            }
        }
        // Perjikaan kalau parameter di urlnya TIDAK ADA masukkin &makna / &pura
        else {
            array_push($hariRayaLengkap, $hariRaya);
        }

        // dd($hariRayaLengkap);
        return $hariRayaLengkap;
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
        $saptawaraWaraController = new SaptaWaraController_07();
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

            $saptawara = $saptawaraWaraController->getSaptawara($tanggal[$i]);
            // $uripSaptawara = $saptawaraWaraController->getUripSaptaWara($saptawara);
            // $namaSaptawara = $saptawaraWaraController->getNamaSaptaWara($saptawara);


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
                'namaTriwara' => $namaTriwara,
                'saptawara' => $saptawara,
                'namaSaptawara' => $namaSaptawara,
                'pancawara' => $pancawara,
                'namaPancawara' => $namaPancawara,
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
        $saptawaraWaraController = new SaptaWaraController_07();
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

            $saptawara = $saptawaraWaraController->getSaptawara($tanggal[$i]);
            $uripSaptawara = $saptawaraWaraController->getUripSaptaWara($saptawara);
            $namaSaptawara = $saptawaraWaraController->getNamaSaptaWara($saptawara);


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
