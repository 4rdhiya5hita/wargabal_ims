<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\ValidasiAPI;
use App\Http\Controllers\ValidasiTanggal;
use App\Http\Controllers\WukuController;
use App\Models\Piodalan;
use App\Models\Pura;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PiodalanAPI extends Controller
{
    public function cariPiodalan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 4;

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

        // Cache::forget('piodalan_' . $tanggal_mulai . '_' . $tanggal_selesai);
        $piodalan = Cache::remember('piodalan_' . $tanggal_mulai . '_' . $tanggal_selesai, now()->addDays(31), function () use ($tanggal_mulai, $tanggal_selesai) {
            $piodalan_cache = [];

            while ($tanggal_mulai <= $tanggal_selesai) {
                $hasil_piodalan = $this->getPiodalan($tanggal_mulai->toDateString());

                // Periksa apakah piodalan berisi informasi yang valid
                if ($hasil_piodalan[0] != '-') {
                    $piodalan_cache[] = [
                        'tanggal' => $tanggal_mulai->toDateString(),
                        'hari' => $hasil_piodalan[0]['hari'],
                        'hari_raya' => $hasil_piodalan[0]['hari_raya'],
                        'sasih' => $hasil_piodalan[0]['sasih'],
                        'pura' => $hasil_piodalan[0]['pura'],
                    ];
                } else {
                    $piodalan_cache[] = [
                        'tanggal' => $tanggal_mulai->toDateString(),
                        'hari' => '-',
                        'hari_raya' => '-',
                        'sasih' => '-',
                        'pura' => '-',
                    ];
                }

                $tanggal_mulai->addDay();
            }

            return $piodalan_cache;
        });

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'data' => $piodalan,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
        // return view('dashboard.index', compact('kalender'));
    }

    private function getPiodalan($tanggal)
    {
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
        // dd($no_sasih);
        $hariRaya = $hariRayaController->getHariRaya($tanggal, $pengalantaka_dan_hariSasih['penanggal_1'], $pengalantaka_dan_hariSasih['penanggal_2'], $pengalantaka_dan_hariSasih['pengalantaka'], $no_sasih['no_sasih'], $triwara, $pancawara, $saptawara, $hasilWuku);
        $piodalan = $namaSaptawara . ' ' . $namaPancawara . ' ' . $namaWuku;
        $namaSasih = $hariSasihController->getNamaSasih($no_sasih['no_sasih']);
        // dd($hariRaya, $piodalan, $namaSasih);

        $piodalanLengkap = [];
        if ($piodalan) {
            // dd('ok');
            $data_piodalan_by_wuku = Pura::where('wuku', $namaWuku)->where('saptawara', $namaSaptawara)->where('pancawara', $namaPancawara)->get();
            $data_piodalan_by_sasih = Pura::where('sasih', $namaSasih)->where('purnama_tilem', $hariRaya[0])->get();
            // dd($data_piodalan_by_sasih);

            if ($data_piodalan_by_wuku->isEmpty() && $data_piodalan_by_sasih->isEmpty()) {
                array_push($piodalanLengkap, '-');
            } else {
                $puraArray = [];
                foreach ($data_piodalan_by_wuku as $item) {
                    $ambil_pura = $item->name . ' di ' . $item->address;
                    $puraArray[] = [
                        'id_pura' => $item->id,
                        'nama_pura' => $ambil_pura
                    ];
                }
                $piodalanLengkap[] = ['hari' => $piodalan, 'hari_raya' => $hariRaya[0], 'sasih' => $namaSasih, 'pura' => $puraArray];

                // if (!$data_piodalan_by_wuku->isEmpty()) {
                //     $puraArray = [];
                //     foreach ($data_piodalan_by_wuku as $item) {
                //         $ambil_piodalan = $hariRaya[0];
                //         $ambil_sasih = $namaSasih;
                //         $ambil_pura = $item->name . ' di ' . $item->address;   
                //     }
                //     $piodalanLengkap[] = ['piodalan' => $ambil_piodalan, 'sasih' => $ambil_sasih, 'pura' => $ambil_pura];
                // }

                // if (!$data_piodalan_by_sasih->isEmpty() && $hariRaya[0] == 'Purnama' || $hariRaya[0] == 'Tilem') {
                //     $puraArray = [];
                //     foreach ($data_piodalan_by_sasih as $item) {
                //         $ambil_pura = $item->name . ' di ' . $item->address;
                //         $puraArray[] = $ambil_pura;
                //     }
                //     $piodalanLengkap[] = ['nama' => $hariRaya[0], 'sasih' => $namaSasih, 'pura' => $puraArray];
                // }
            }
        } else {
            // dd('no');
            array_push($piodalanLengkap, '-');
        }

        // dd($piodalanLengkap);
        return $piodalanLengkap;
    }
}
