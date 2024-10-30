<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AstaWaraController_08;
use App\Http\Controllers\CaturWaraController_04;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DasaWaraController;
use App\Http\Controllers\DwiWaraController_02;
use App\Http\Controllers\EkaJalaRsiController;
use App\Http\Controllers\EkaWaraController_01;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\IngkelController;
use App\Http\Controllers\JejepanController;
use App\Http\Controllers\LintangController;
use App\Http\Controllers\NeptuController;
use App\Http\Controllers\PancaSudhaController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PangarasanController;
use App\Http\Controllers\PratitiController;
use App\Http\Controllers\RakamController;
use App\Http\Controllers\SadWaraController_06;
use App\Http\Controllers\SangaWaraController_09;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\ValidasiAPI;
use App\Http\Controllers\ValidasiTanggal;
use App\Http\Controllers\WatekAlitController;
use App\Http\Controllers\WatekMadyaController;
use App\Http\Controllers\WukuController;
use App\Http\Controllers\ZodiakController;
use App\Models\User;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KalenderBaliAPI extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function cariElemenKalenderBali(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 3;

        $validasi_api = new ValidasiAPI();
        $result = $validasi_api->validasiAPI($user, $service_id);
        
        if ($result) {
            return $result;
        }

        $start = microtime(true);
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        $validasi_tanggal = new ValidasiTanggal();
        $response = $validasi_tanggal->validasiTanggal($tanggal_mulai, $tanggal_selesai);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }
        list($tanggal_mulai, $tanggal_selesai) = $response;

        $filterCache = '';
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
                $allowed_values = ['wewaran', 'wuku', 'ingkel', 'jejepan', 'lintang', 'panca_sudha', 'pangarasan', 'rakam', 'watek_madya', 'watek_alit', 'neptu', 'ekajalarsi', 'zodiak', 'pratiti', 'tahun_saka', 'sasih', 'pengalantaka', 'angka_pengalantaka'];

                $filter = array_unique($filterArray);
                // cek apakah terdapat nilai filter yang sama dengan daftar nilai yang diperbolehkan
                $filter = array_intersect($filter, $allowed_values);

                $filterCache = implode(',', $filter);

                // Filter nilai yang tidak terdapat di dalam daftar nilai yang diperbolehkan
                if (count($filter) < count($filterArray)) {
                    return response()->json([
                        'message' => 'Filter memiliki nilai yang tidak sesuai. Mohon periksa kembali inputan filter Anda.'
                    ], 400);
                }
            }
        }

        // cache data
        // Cache::forget('kalender_' . $tanggal_mulai . '_' . $tanggal_selesai . '_' . $filterCache);
        $kalender = Cache::remember('kalender_' . $tanggal_mulai . '_' . $tanggal_selesai . '_' . $filterCache, now()->addDays(31), function () use ($tanggal_mulai, $tanggal_selesai, $filter) {
            $kalender_cache = [];

            while ($tanggal_mulai <= $tanggal_selesai) {
                $kalender_cache[] = [
                    'tanggal' => $tanggal_mulai->toDateString(),
                    'kalender' => $this->getHariRaya(
                        $tanggal_mulai->toDateString(),
                        $filter
                    ),
                ];
                $tanggal_mulai->addDay();
            }

            return $kalender_cache;
        });

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
        $filter
    ) {
        // dd($get_jejepan);
        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $noNgunaratri = 46;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $noNgunaratri = 50;
        } else {
            $refTanggal = '1992-01-01';
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $noNgunaratri = 22;
        }

        // Panggil semua controller yang dibutuhkan
        $wukuController = new WukuController();
        $ekaWaraController = new EkaWaraController_01();
        $dwiWaraController = new DwiWaraController_02();
        $triWaraController = new TriWaraController_03();
        $caturWaraController = new CaturWaraController_04();
        $pancaWaraController = new PancaWaraController_05();
        $sadWaraController = new SadWaraController_06();
        $saptaWaraController = new SaptaWaraController_07();
        $astaWaraController = new AstaWaraController_08();
        $sangaWaraController = new SangaWaraController_09();
        $dasaWaraController = new DasaWaraController();
        $hariSasihController = new HariSasihController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
        
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $namaPancawara = $pancaWaraController->getNamaPancawara($pancawara);
        $urip_pancawara = $pancaWaraController->getUripPancaWara($pancawara);

        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $namaSaptawara = $saptaWaraController->getNamaSaptawara($saptawara);
        $urip_saptawara = $saptaWaraController->getUripSaptaWara($saptawara);

        $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $ekawara = $ekaWaraController->getEkaWara($urip_pancawara, $urip_saptawara);
        $namaEkawara = $ekaWaraController->getNamaEkaWara($ekawara);
        $dwiwara = $dwiWaraController->getDwiWara($urip_pancawara, $urip_saptawara);
        $namaDwiwara = $dwiWaraController->getNamaDwiWara($dwiwara);
        $triwara = $triWaraController->getTriWara($hasilAngkaWuku);
        $namaTriwara = $triWaraController->getNamaTriWara($triwara);
        $caturwara = $caturWaraController->getCaturWara($hasilAngkaWuku);
        $namaCaturwara = $caturWaraController->getNamaCaturWara($caturwara);
        $sadwara = $sadWaraController->getSadWara($hasilAngkaWuku);
        $namaSadwara = $sadWaraController->getNamaSadWara($sadwara);
        $astawara = $astaWaraController->getAstaWara($hasilAngkaWuku);
        $namaAstawara = $astaWaraController->getNamaAstaWara($astawara);
        $sangawara = $sangaWaraController->getSangaWara($hasilAngkaWuku);
        $namaSangawara = $sangaWaraController->getNamaSangaWara($sangawara);
        $dasawara = $dasaWaraController->getDasawara($urip_pancawara, $urip_saptawara);
        $namaDasawara = $dasaWaraController->getNamaDasaWara($dasawara);

        $pengalantaka_dan_hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        $pengalantaka = $pengalantaka_dan_hariSasih['pengalantaka'];
        $penanggal1 = $pengalantaka_dan_hariSasih['penanggal_1'];
        $penanggal2 = $pengalantaka_dan_hariSasih['penanggal_2'];

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

        // Mencari tahun saka dan sasih
        $tahunSaka = $no_sasih['hasil_tahun'];
        $namaSasih = $hariSasihController->getNamaSasih($no_sasih['no_sasih']);

        // Mencari hari berdasarkan tanggal
        $hariController = new HariAPI();
        $hari = $hariController->getHari($tanggal);
        $kalenderLengkap = [];

        if ($filter) {
            $metode = array_values($filter);
        } else {
            $metode = ['wewaran', 'wuku', 'ingkel', 'jejepan', 'lintang', 'panca_sudha', 'pangarasan', 'rakam', 'watek_madya', 'watek_alit', 'neptu', 'ekajalarsi', 'zodiak', 'pratiti', 'tahun_saka', 'sasih', 'pengalantaka', 'angka_pengalantaka'];
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
            $panca_sudhaController = new PancaSudhaController();
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
                if ($value == 'tahun_saka') {
                    array_push($kombinasi_array, ['tahun_saka' => $tahunSaka]);
                }
                if ($value == 'sasih') {
                    array_push($kombinasi_array, ['sasih' => $namaSasih]);
                }
                if ($value == 'pengalantaka') {
                    array_push($kombinasi_array, ['pengalantaka' => $pengalantaka]);
                }
                if ($value == 'angka_pengalantaka') {
                    array_push($kombinasi_array, ['angka_pengalantaka' => $penanggal1]);
                }

                if ($value == 'wuku') {
                    array_push($kombinasi_array, ['wuku' => $namaWuku]);
                }
                if ($value == 'wewaran') {
                    array_push($kombinasi_array, ['ekawara' => $namaEkawara]);
                    array_push($kombinasi_array, ['dwiwara' => $namaDwiwara]);
                    array_push($kombinasi_array, ['triwara' => $namaTriwara]);
                    array_push($kombinasi_array, ['caturwara' => $namaCaturwara]);
                    array_push($kombinasi_array, ['pancawara' => $namaPancawara]);
                    array_push($kombinasi_array, ['sadwara' => $namaSadwara]);
                    array_push($kombinasi_array, ['saptawara' => $namaSaptawara]);
                    array_push($kombinasi_array, ['astawara' => $namaAstawara]);
                    array_push($kombinasi_array, ['sangawara' => $namaSangawara]);
                    array_push($kombinasi_array, ['dasawara' => $namaDasawara]);
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
                if ($value == 'panca_sudha') {
                    $panca_sudha = $panca_sudhaController->PancaSudha($pancawara, $saptawara);
                    array_push($kombinasi_array, ['panca_sudha' => $panca_sudha]);
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

                // // Perjikaan kalau parameter di urlnya ada &hari
                // if ($value == 'Sunday') {
                //     array_push($kombinasi_array, ['hari' => 'Minggu']);
                // } elseif ($value == 'Saturday') {
                //     array_push($kombinasi_array, ['hari' => 'Sabtu']);
                // } elseif ($value == 'Friday') {
                //     array_push($kombinasi_array, ['hari' => 'Jumat']);
                // } elseif ($value == 'Thursday') {
                //     array_push($kombinasi_array, ['hari' => 'Kamis']);
                // } elseif ($value == 'Wednesday') {
                //     array_push($kombinasi_array, ['hari' => 'Rabu']);
                // } elseif ($value == 'Tuesday') {
                //     array_push($kombinasi_array, ['hari' => 'Selasa']);
                // } elseif ($value == 'Monday') {
                //     array_push($kombinasi_array, ['hari' => 'Senin']);
                // }

                // array_push($kombinasi_array, ['angka_wuku' => $hasilAngkaWuku]);
            }
            $kalenderLengkap = array_reduce($kombinasi_array, function ($carry, $item) { // Menggabungkan array multidimensi menjadi satu array
                return array_merge($carry, $item);
            }, []);
        }

        return $kalenderLengkap;
    }
}
