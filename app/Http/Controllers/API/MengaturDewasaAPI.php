<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AstaWaraController_08;
use App\Http\Controllers\CaturWaraController_04;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DasaWaraController;
use App\Http\Controllers\DwiWaraController_02;
use App\Http\Controllers\EkaWaraController_01;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PurnamaTilemController;
use App\Http\Controllers\SadWaraController_06;
use App\Http\Controllers\SangaWaraController_09;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\ValidasiAPI;
use App\Http\Controllers\ValidasiTanggal;
use App\Http\Controllers\WukuController;
use App\Models\AlaAyuningDewasa;
use App\Models\DewasaAyu;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MengaturDewasaAPI extends Controller
{
    public function mengaturDewasa(Request $request)
    {
        // dd($request->all());
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 6;

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


        $kriteria = $request->input('kriteria');

        // preg_match_all('/\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $kriteria, $matches);
        // $extractedVariables = $matches[0];
        // $extractedVariables = array_map(function ($var) {
        //     return substr($var, 1); // Menghapus karakter pertama (tanda $)
        // }, $extractedVariables);

        // preg_match_all('/==([0-9]+|\'[^\']*\'|"[^"]*")/', $kriteria, $matches);
        // $extractedValues = $matches[1];

        // $variables = array_combine($extractedVariables, $extractedValues);
        $makna = $request->has('beserta_keterangan');

        $ala_ayuning_dewasa = Cache::remember('ala_ayuning_dewasa_' . $tanggal_mulai . '_' . $tanggal_selesai . '_' . $kriteria, now()->addDays(31), function () use ($tanggal_mulai, $tanggal_selesai, $makna, $kriteria) {
            $ala_ayuning_dewasa_cache = [];

            while ($tanggal_mulai <= $tanggal_selesai) {
                $ala_ayuning_dewasa_cache[] = [
                    'tanggal' => $tanggal_mulai->toDateString(),
                    'ala_ayuning_dewasa' => $this->getAlaAyuningDewasa($tanggal_mulai->toDateString(), $makna, $kriteria),
                ];
                $tanggal_mulai->addDay();
            }

            return $ala_ayuning_dewasa_cache;
        });

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'data' => $ala_ayuning_dewasa,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
    }

    public function mengaturDewasaPOST (Request $request)
    {
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 6;

        $validasi_api = new ValidasiAPI();
        $result = $validasi_api->validasiAPI($user, $service_id);

        if ($result) {
            return $result;
        }

        $start = microtime(true);
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;

        $validasi_tanggal = new ValidasiTanggal();
        $response = $validasi_tanggal->validasiTanggal($tanggal_mulai, $tanggal_selesai);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }
        list($tanggal_mulai, $tanggal_selesai) = $response;

        $kriteria = $request->kriteria;
        $makna = $request->has('beserta_keterangan');

        $ala_ayuning_dewasa = Cache::remember('ala_ayuning_dewasa_' . $tanggal_mulai . '_' . $tanggal_selesai . '_' . $kriteria, now()->addDays(31), function () use ($tanggal_mulai, $tanggal_selesai, $makna, $kriteria) {
            $ala_ayuning_dewasa_cache = [];

            while ($tanggal_mulai <= $tanggal_selesai) {
                $ala_ayuning_dewasa_cache[] = [
                    'tanggal' => $tanggal_mulai->toDateString(),
                    'ala_ayuning_dewasa' => $this->getAlaAyuningDewasa($tanggal_mulai->toDateString(), $makna, $kriteria),
                ];
                $tanggal_mulai->addDay();
            }

            return $ala_ayuning_dewasa_cache;
        });

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'data' => $ala_ayuning_dewasa,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
    }

    public function getAlaAyuningDewasa($tanggal, $makna, $kriteria)
    {
        // dd($kriteria);
        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $noNgunaratri = 46;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $noNgunaratri = 50;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
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
        // $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController();
        $purnamaTilemController = new PurnamaTilemController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $wuku = $wukuController->getWuku($hasilAngkaWuku);

        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $urip_saptawara = $saptaWaraController->getUripSaptaWara($saptawara);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $urip_pancawara = $pancaWaraController->getUripPancaWara($pancawara);
        $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

        $ekawara = $ekaWaraController->getEkaWara($urip_pancawara, $urip_saptawara);
        // $namaEkawara = $triWaraController->getNamaEkaWara($ekawara);
        $dwiwara = $dwiWaraController->getDwiWara($urip_pancawara, $urip_saptawara);
        // $namaDwiwara = $dwiWaraController->getNamaDwiWara($dwiwara);
        $caturwara = $caturWaraController->getCaturWara($hasilAngkaWuku);
        // $namaCaturwara = $caturWaraController->getNamaCaturWara($caturwara);
        $sadwara = $sadWaraController->getSadWara($hasilAngkaWuku);
        // $namaSadwara = $sadWaraController->getNamaSadWara($sadwara);
        $astawara = $astaWaraController->getAstaWara($hasilAngkaWuku);
        // $namaAstawara = $astaWaraController->getNamaAstaWara($astawara);
        $sangawara = $sangaWaraController->getSangaWara($hasilAngkaWuku);
        // $namaSangawara = $sangaWaraController->getNamaSangaWara($sangawara);
        $dasawara = $dasaWaraController->getDasawara($urip_pancawara, $urip_saptawara);
        // $namaDasawara = $dasaWaraController->getNamaDasaWara($dasawara);

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
        $purnama_tilem = $purnamaTilemController->getPurnamaTilem($pengalantaka_dan_hariSasih['pengalantaka'], $pengalantaka_dan_hariSasih['penanggal_1'], $pengalantaka_dan_hariSasih['penanggal_2']);
        // dd($purnama_tilem);
        // dd($no_sasih['no_sasih']);
        $pengalantaka = $pengalantaka_dan_hariSasih['pengalantaka'];
        $sasihDay1 = $pengalantaka_dan_hariSasih['penanggal_1'];
        $sasihDay2 = $pengalantaka_dan_hariSasih['penanggal_2'];
        $sasih = $no_sasih['no_sasih'];
        $dewasaAyu = [];
        // $description = [];

        // $variabel = [
        //     'ekawara' => $ekawara,
        //     'dwiwara' => $dwiwara,
        //     'triwara' => $triwara,
        //     'caturwara' => $caturwara,
        //     'pancawara' => $pancawara,
        //     'sadwara' => $sadwara,
        //     'saptawara' => $saptawara,
        //     'astawara' => $astawara,
        //     'sangawara' => $sangawara,
        //     'dasawara' => $dasawara,
        //     'wuku' => $wuku,
        //     'purnamatilem' => $purnama_tilem,
        //     'sasihDay1' => $sasihDay1,
        //     'sasihDay2' => $sasihDay2,
        //     'pengalantaka' => $pengalantaka,
        //     'sasih' => $no_sasih['no_sasih']
        // ];

        // dd($kriteria);
        // dd($pengalantaka);
        // Evaluasi ekspresi kondisional menggunakan eval()
        $result = eval("return ($kriteria);");
        // dd($result);

        if ($result == true) {

            if ($purnama_tilem == 0) {
                $purnama_tilem = "Purnama";
            } elseif ($purnama_tilem == 1) {
                $purnama_tilem = "Tilem";
            }

            // // if (array_key_exists('ekawara', $variables)) {
            // //     $ekawara = $variables['ekawara'];
            // // } else {
            // //     $ekawara = "";
            // // }

            // if (array_key_exists('dwiwara', $variables)) {
            //     $dwiwara = $variables['dwiwara'];
            // } else {
            //     $dwiwara = "";
            // }

            // if (array_key_exists('triwara', $variables)) {
            //     $triwara = $variables['triwara'];
            // } else {
            //     $triwara = "";
            // }

            // // if (array_key_exists('caturwara', $variables)) {
            // //     $caturwara = $variables['caturwara'];
            // // } else {
            // //     $caturwara = "";
            // // }

            // if (array_key_exists('pancawara', $variables)) {
            //     $pancawara = $variables['pancawara'];
            // } else {
            //     $pancawara = "";
            // }

            // if (array_key_exists('sadwara', $variables)) {
            //     $sadwara = $variables['sadwara'];
            // } else {
            //     $sadwara = "";
            // }

            // if (array_key_exists('saptawara', $variables)) {
            //     $saptawara = $variables['saptawara'];
            // } else {
            //     $saptawara = "";
            // }

            // if (array_key_exists('astawara', $variables)) {
            //     $astawara = $variables['astawara'];
            // } else {
            //     $astawara = "";
            // }

            // if (array_key_exists('sangawara', $variables)) {
            //     $sangawara = $variables['sangawara'];
            // } else {
            //     $sangawara = "";
            // }

            // if (array_key_exists('dasawara', $variables)) {
            //     $dasawara = $variables['dasawara'];
            // } else {
            //     $dasawara = "";
            // }

            // if (array_key_exists('wuku', $variables)) {
            //     $wuku = $variables['wuku'];
            // } else {
            //     $wuku = "";
            // }

            // if (array_key_exists('purnamatilem', $variables)) {
            //     $purnama_tilem = $variables['purnamatilem'];
            // } else {
            //     $purnama_tilem = "";
            // }

            // if (array_key_exists('sasihDay1', $variables)) {
            //     $sasihDay1 = $variables['sasihDay1'];
            // } else {
            //     $sasihDay1 = "";
            // }

            // if (array_key_exists('sasihDay2', $variables)) {
            //     $sasihDay2 = $variables['sasihDay2'];
            // } else {
            //     $sasihDay2 = "";
            // }

            // if (array_key_exists('pengalantaka', $variables)) {
            //     $pengalantaka = $variables['pengalantaka'];
            // } else {
            //     $pengalantaka = "";
            // }

            // if (array_key_exists('sasih', $variables)) {
            //     $no_sasih = $variables['sasih'];
            // } else {
            //     $no_sasih = "";
            // }
            // dd($dwiwara, $triwara, $pancawara, $sadwara, $saptawara, $astawara, $sangawara, $dasawara, $wuku, $purnama_tilem, $sasihDay1, $sasihDay2, $pengalantaka, $no_sasih);
            // // dd($saptawara == 1);

            // 1. AgniAgungDoyanBasmi: Selasa Purnama dengan Asta Wara Brahma
            if (($saptawara == 3 && ($astawara == 6 || $purnama_tilem == 'Purnama'))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Agni Agung Doyan Basmi', 'keterangan' => $description]);

                // $description[] = $isi_description[0]->first();
            }

            // 2. Agni Agung Patra Limutan: Minggu dengan Asta Wara Brahma
            if ($saptawara == 1 && $astawara == 6) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Agni Agung Patra Limutan', 'keterangan' => $description]);
            }

            // 3. Amerta Akasa: Anggara Purnama
            if ($saptawara == 3 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Akasa', 'keterangan' => $description]);
            }

            // 4. Amerta Bumi: Soma Wage Penanggal 1. Buda Pon Penanggal 10.
            if (($saptawara == 2 && $pancawara == 4 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay2 == 1)) ||
                ($saptawara == 4 && $pancawara == 3 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 10 || $sasihDay2 == 10))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Bumi', 'keterangan' => $description]);
            }

            // 5. Amerta Bhuwana: Redite Purnama, Soma Purnama, dan Anggara Purnama
            if (($saptawara == 1 || $saptawara == 2 || $saptawara == 3) && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Bhuwana', 'keterangan' => $description]);
            }

            // 6. Amerta Dadi: Soma Beteng atau Purnama Kajeng
            if (($saptawara == 2 && $triwara == 2) || ($triwara == 3 && $purnama_tilem == 'Purnama')) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Dadi', 'keterangan' => $description]);
            }

            // 7. Amerta Danta
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6))) ||
                ($saptawara == 2 && (($purnama_tilem == 'Purnama') || (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5)))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay1 == 10)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 3 || $sasihDay1 == 10)))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 2) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 2))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 1) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 1))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Danta', 'keterangan' => $description]);
            }

            // 8. Amerta Dewa
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6) || ($pengalantaka == 'Pangelong' && $sasihDay1 == 6) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 6))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7) || ($pengalantaka == 'Pangelong' && $sasihDay1 == 7) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 7))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 3) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 3) || ($pengalantaka == 'Pangelong' && $sasihDay1 == 3) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 3))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 2) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 2) || ($pengalantaka == 'Pangelong' && $sasihDay1 == 2) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 2))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5) || ($pengalantaka == 'Pangelong' && $sasihDay1 == 5) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 5))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 1) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 1) || ($pengalantaka == 'Pangelong' && $sasihDay1 == 1) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 1))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4) || ($pengalantaka == 'Pangelong' && $sasihDay1 == 4) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 4)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Dewa', 'keterangan' => $description]);
            }

            // 9. Amerta Dewa Jaya
            if ($saptawara == 2 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay2 == 3)) || ($pengalantaka == 'Penanggal' && ($sasihDay1 == 12 || $sasihDay2 == 12)))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Dewa Jaya', 'keterangan' => $description]);
            }

            // 10. Amerta Dewata
            if ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 12) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 12))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Dewata', 'keterangan' => $description]);
            }

            // 11. Amerta Gati
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 6 || $sasihDay1 == 3 || $sasihDay2 == 6 || $sasihDay2 == 3)))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 7 || $sasihDay2 == 7)))) ||
                ($saptawara == 3 && ($pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay2 == 3))) ||
                ($saptawara == 4 && ((($pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay1 == 3)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 2 || $sasihDay2 == 3)))) ||
                    ($saptawara == 5 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 5 || $sasihDay2 == 5)))) ||
                    ($saptawara == 6 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay2 == 1)))) ||
                    ($saptawara == 7 && ($pengalantaka == 'Penanggal' && ($sasihDay1 == 7 || $sasihDay2 == 7 || $sasihDay1 == 4 || $sasihDay2 == 4)))
                )
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Gati', 'keterangan' => $description]);
            }

            // 12. Amerta Kundalini
            if (
                ($saptawara == 2 && $wuku == 24 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay2 == 1 || $sasihDay1 == 8 || $sasihDay2 == 8)) ||
                ($saptawara == 2 && $wuku == 29 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay2 == 1 || $sasihDay1 == 7 || $sasihDay2 == 7 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 2 && $wuku == 8 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 5 || $sasihDay2 == 5)) ||
                ($saptawara == 4 && $wuku == 2 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 12 || $sasihDay2 == 12)) ||
                ($saptawara == 4 && $wuku == 5 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 10 || $sasihDay2 == 10)) ||
                ($saptawara == 4 && $wuku == 8 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($saptawara == 4 && $wuku == 9 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 13 || $sasihDay2 == 13)) ||
                ($saptawara == 4 && $wuku == 15 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 12 || $sasihDay2 == 12)) ||
                ($saptawara == 5 && $wuku == 2 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 10 || $sasihDay2 == 10)) ||
                ($saptawara == 5 && $wuku == 20 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 5 && $wuku == 13 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay2 == 1 || $sasihDay1 == 11 || $sasihDay2 == 11))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Kundalini', 'keterangan' => $description]);
            }

            // 13. Amerta Masa
            if ($saptawara == 6 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Masa', 'keterangan' => $description]);
            }

            // 14. Amerta Murti
            if ($saptawara == 4 && $pancawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 12) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 12))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Murti', 'keterangan' => $description]);
            }

            // 15. Amerta Pageh
            if ($saptawara == 7 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Pageh', 'keterangan' => $description]);
            }

            // 16. Amerta Pepageran
            if ($saptawara == 7 && ($purnama_tilem == 'Purnama' || $astawara == 4)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Pepageran', 'keterangan' => $description]);
            }

            // 17. Amerta Sari
            if ($saptawara == 4 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Sari', 'keterangan' => $description]);
            }

            // 18. Amerta Wija
            if ($saptawara == 5 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Wija', 'keterangan' => $description]);
            }

            // 19. Amerta Yoga
            if (
                ($saptawara == 2 && ($wuku == 2 || $wuku == 5 || $wuku == 14 || $wuku == 17 || $wuku == 20 || $wuku == 23 || $wuku == 26 || $wuku == 29)) ||
                ($saptawara == 5 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 4) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 4))) ||
                ($saptawara == 7 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 5) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 5))) ||
                (($no_sasih == 10) && (($pengalantaka == 'Pangelong' && $sasihDay1 == 4) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 4))) ||
                (($no_sasih == 12) && (($pengalantaka == 'Pangelong' && $sasihDay1 == 1) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 1)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Amerta Yoga', 'keterangan' => $description]);
            }

            // 20. Asuajag Munggah
            if (
                ($saptawara == 1 && $wuku == 6) ||
                ($saptawara == 2 && $wuku == 23) ||
                ($saptawara == 3 && $wuku == 10) ||
                ($saptawara == 4 && $wuku == 27) ||
                ($saptawara == 5 && $wuku == 14) ||
                ($saptawara == 6 && $wuku == 1) ||
                ($saptawara == 7 && $wuku == 18)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Asuajag Munggah', 'keterangan' => $description]);
            }

            // 21. Asuajag Turun
            if (
                ($saptawara == 1 && $wuku == 21) ||
                ($saptawara == 2 && $wuku == 8) ||
                ($saptawara == 3 && $wuku == 25) ||
                ($saptawara == 4 && $wuku == 12) ||
                ($saptawara == 5 && $wuku == 29) ||
                ($saptawara == 6 && $wuku == 16) ||
                ($saptawara == 7 && $wuku == 3)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Asuajag Turun', 'keterangan' => $description]);
            }

            // 22. Asuasa
            if (
                ($saptawara == 1 && $wuku == 3) ||
                ($saptawara == 1 && $wuku == 15) ||
                ($saptawara == 2 && $wuku == 14) ||
                ($saptawara == 3 && $wuku == 7) ||
                ($saptawara == 4 && $wuku == 24) ||
                ($saptawara == 5 && $wuku == 11) ||
                ($saptawara == 6 && $wuku == 28)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Asuasa', 'keterangan' => $description]);
            }

            // 23. Ayu Bhadra
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 3) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 3))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 7 || $sasihDay1 == 10)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 7 || $sasihDay2 == 10)))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 3) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 3))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 12) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 12))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 11) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 11)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ayu Bhadra', 'keterangan' => $description]);
            }

            // 24. Ayu Dana
            if ($saptawara == 6 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ayu Dana', 'keterangan' => $description]);
            }

            // 25. Ayu Nulus
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 3) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 3))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 12 || $sasihDay1 == 13)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 12 || $sasihDay2 == 13)))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 1) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 1))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ayu Nulus', 'keterangan' => $description]);
            }

            // 26. Babi Munggah
            if ($pancawara == 4 && $sadwara == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Babi Munggah', 'keterangan' => $description]);
            }

            // 27. Babi Turun
            if ($pancawara == 4 && $sadwara == 4) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Babi Turun', 'keterangan' => $description]);
            }

            // 28. Banyu Milir
            if (
                ($saptawara == 1 && $wuku == 4) ||
                ($saptawara == 2 && $wuku == 27) ||
                ($saptawara == 4 && $wuku == 1) ||
                ($saptawara == 6 && $wuku == 13)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Banyu Milir', 'keterangan' => $description]);
            }

            // 29. Banyu Urung
            if (
                ($saptawara == 1 && $wuku == 1) ||
                ($saptawara == 2 && ($wuku == 1 || $wuku == 2 || $wuku == 8 || $wuku == 10 || $wuku == 17 || $wuku == 18 || $wuku == 20 || $wuku == 22)) ||
                ($saptawara == 3 && ($wuku == 1 || $wuku == 5 || $wuku == 14 || $wuku == 16 || $wuku == 17 || $wuku == 18 || $wuku == 23 || $wuku == 21)) ||
                ($saptawara == 4 && ($wuku == 28 || $wuku == 5 || $wuku == 10 || $wuku == 19 || $wuku == 21)) ||
                ($saptawara == 5 && ($wuku == 5 || $wuku == 6 || $wuku == 15 || $wuku == 19 || $wuku == 20 || $wuku == 22 || $wuku == 24)) ||
                ($saptawara == 6 && ($wuku == 28 || $wuku == 29 || $wuku == 6 || $wuku == 11 || $wuku == 15 || $wuku == 17)) ||
                ($saptawara == 7 && ($wuku == 4 || $wuku == 8 || $wuku == 19))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Banyu Urug', 'keterangan' => $description]);
            }

            // 30. Bojog Munggah
            if ($pancawara == 5 && $sadwara == 5) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Bojog Munggah', 'keterangan' => $description]);
            }

            // 31. Bojog Turun
            if ($pancawara == 5 && $sadwara == 2) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Bojog Turun', 'keterangan' => $description]);
            }

            // 32. Buda Gajah
            if ($saptawara == 4 && $pancawara == 4 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Buda Gajah', 'keterangan' => $description]);
            }

            // 33. Buda Ireng
            if ($saptawara == 4 && $pancawara == 4 && $purnama_tilem == 'Tilem') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Buda Ireng', 'keterangan' => $description]);
            }

            // 34. Buda Suka
            if ($saptawara == 4 && $pancawara == 5 && $purnama_tilem == 'Tilem') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Buda Suka', 'keterangan' => $description]);
            }

            // 35. Carik Walangati
            if (
                $wuku == 1 || $wuku == 6 || $wuku == 10 || $wuku == 12 || $wuku == 24 ||
                $wuku == 25 || $wuku == 27 || $wuku == 28 || $wuku == 30 || $wuku == 7
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Carik Walangati', 'keterangan' => $description]);
            }

            // 36. Catur Laba
            if (
                ($saptawara == 1 && $pancawara == 1) ||
                ($saptawara == 2 && $pancawara == 4) ||
                ($saptawara == 4 && $pancawara == 3) ||
                ($saptawara == 7 && $pancawara == 2)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Catur Laba', 'keterangan' => $description]);
            }

            // 37. Cintamanik
            if ($saptawara == 4 && ($wuku % 2 == 1)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Cintamanik', 'keterangan' => $description]);
            }

            // 38. Corok Kodong
            if ($saptawara == 5 && $pancawara == 5 && $wuku == 13) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Corok Kodong', 'keterangan' => $description]);
            }

            // 39. DagDig Karana
            if (
                ($saptawara == 1 && ($sasihDay1 == 2 || $sasihDay2 == 2)) ||
                ($saptawara == 2 && ($sasihDay1 == 1 || $sasihDay2 == 1)) ||
                ($saptawara == 3 && ($sasihDay1 == 10 || $sasihDay2 == 10)) ||
                ($saptawara == 4 && ($sasihDay1 == 7 || $sasihDay2 == 7)) ||
                ($saptawara == 5 && ($sasihDay1 == 3 || $sasihDay2 == 3)) ||
                ($saptawara == 7 && ($sasihDay1 == 6 || $sasihDay2 == 6))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'DagDig Karana', 'keterangan' => $description]);
            }

            // 40. Dasa Amertha
            if ($saptawara == 6 && $pancawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dasa Amertha', 'keterangan' => $description]);
            }

            // 41. Dasa Guna
            if ($saptawara == 4 && ($purnama_tilem == 'Purnama' || $purnama_tilem == 'Tilem')) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dasa Guna', 'keterangan' => $description]);
            }

            // 42. Dauh Ayu
            if (
                ($saptawara == 1 && ($sasihDay1 == 4 || $sasihDay2 == 4 || $sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($saptawara == 2 && ($sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 3 || $sasihDay2 == 3 || $sasihDay1 == 5 || $sasihDay2 == 5)) ||
                ($saptawara == 3 && ($sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 7 || $sasihDay2 == 7 || $sasihDay1 == 8 || $sasihDay2 == 8)) ||
                ($saptawara == 4 && ($sasihDay1 == 4 || $sasihDay2 == 4)) ||
                ($saptawara == 5 && ($sasihDay1 == 1 || $sasihDay2 == 1 || $sasihDay1 == 4 || $sasihDay2 == 4)) ||
                ($saptawara == 6 && ($sasihDay1 == 1 || $sasihDay2 == 1 || $sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($saptawara == 7 && ($sasihDay1 == 5 || $sasihDay2 == 5))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dauh Ayu', 'keterangan' => $description]);
            }

            // 43. Derman Bagia
            if ($saptawara == 2 && ($sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 3 || $sasihDay2 == 3 || $sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 12 || $sasihDay2 == 12)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Derman Bagia', 'keterangan' => $description]);
            }

            // 44. Dewa Ngelayang
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 3) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 3))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay1 == 7)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 3 || $sasihDay2 == 7)))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay1 == 13 || $sasihDay1 == 15)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 3 || $sasihDay2 == 13 || $sasihDay2 == 15)))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 1) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 1))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dewa Ngelayang', 'keterangan' => $description]);
            }

            // 45. Dewa Satata
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dewa Satata', 'keterangan' => $description]);
            }

            // 46. Dewa Werdhi
            if ($saptawara == 6 && $pancawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dewa Werdhi', 'keterangan' => $description]);
            }

            // 47. Dewa Mentas
            if ($saptawara == 5 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 15 || $sasihDay2 == 15)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dewa Mentas', 'keterangan' => $description]);
            }

            // 48. Dewasa Ngelayang
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay1 == 8)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 1 || $sasihDay2 == 8)))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay1 == 3)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 2 || $sasihDay2 == 3)))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dewasa Ngelayang', 'keterangan' => $description]);
            }

            // 49. Dewasa Tanian
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dewasa Tanian', 'keterangan' => $description]);
            }

            // 50. Dina Carik
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 12) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 12))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 11) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 11))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dina Carik', 'keterangan' => $description]);
            }

            // 51. Dina Jaya
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 3) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 3))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 2) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 2))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 1) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 1))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dina Jaya', 'keterangan' => $description]);
            }

            // 52. Dina Mandi
            if (
                ($saptawara == 3 && $purnama_tilem == 'Purnama') ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 2) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 2))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 14) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 14))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 3) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 3)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dina Mandi', 'keterangan' => $description]);
            }

            // 53. Dirgahayu
            if ($saptawara == 3 && $pancawara == 3 && $dasawara == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dirgahayu', 'keterangan' => $description]);
            }

            // 54. DirghaYusa
            if ($saptawara == 4 && $pancawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Dirgha Yusa', 'keterangan' => $description]);
            }

            // 55. Gagak Anungsung Pati
            if (
                ($pengalantaka == 'Penanggal' && ($sasihDay1 == 9 || $sasihDay2 == 9)) ||
                ($pengalantaka == 'Pangelong' && ($sasihDay1 == 1 || $sasihDay2 == 1)) ||
                ($pengalantaka == 'Pangelong' && ($sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($pengalantaka == 'Pangelong' && ($sasihDay1 == 14 || $sasihDay2 == 14))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Gagak Anungsung Pati', 'keterangan' => $description]);
            }

            // 56. Geheng Manyinget
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 14) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 14))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 1) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 1))) ||
                ($saptawara == 2 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 7) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 7))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay1 == 10)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 2 || $sasihDay2 == 10)))) ||
                ($saptawara == 4 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 10) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 10))) ||
                ($saptawara == 5 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 5) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 5))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 14) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 14))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay1 == 9)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 1 || $sasihDay2 == 9))))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Geheng Manyinget', 'keterangan' => $description]);
            }

            // 57. Geni Agung
            if (
                ($saptawara == 1 && $pancawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 3 && $pancawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 4 && $pancawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 14) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 14)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Geni Agung', 'keterangan' => $description]);
            }

            // 58. Geni Murub
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 12) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 12))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 11) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 11))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Geni Murub', 'keterangan' => $description]);
            }

            // 59. Geni Rawana
            if (
                (($pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay1 == 4 || $sasihDay1 == 8 || $sasihDay1 == 11)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 2 || $sasihDay2 == 4 || $sasihDay2 == 8 || $sasihDay2 == 11))) ||
                (($pengalantaka == 'Pangelong' && ($sasihDay1 == 3 || $sasihDay1 == 4 || $sasihDay1 == 9 || $sasihDay1 == 13)) || ($pengalantaka == 'Pangelong' && ($sasihDay2 == 3 || $sasihDay2 == 4 || $sasihDay2 == 9 || $sasihDay2 == 13)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Geni Rawana', 'keterangan' => $description]);
            }

            // 60. Geni Rawana Jejepan
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 12) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 12))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 11) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 11))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Geni Rawana Jejepan', 'keterangan' => $description]);
            }

            // 61. Geni Rawana Rangkep
            if (
                (($saptawara == 3 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay1 == 4 || $sasihDay1 == 8 || $sasihDay1 == 11 || $sasihDay2 == 2 || $sasihDay2 == 4 || $sasihDay2 == 8 || $sasihDay2 == 11)) || ($saptawara == 3 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay1 == 4 || $sasihDay1 == 9 || $sasihDay1 == 13 || $sasihDay2 == 3 || $sasihDay2 == 4 || $sasihDay2 == 9 || $sasihDay2 == 13)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Geni Rawana Rangkep', 'keterangan' => $description]);
            }

            // 62. Guntur Graha
            if (
                ($saptawara == 4 && $wuku == 2) ||
                ($saptawara == 4 && $wuku == 5) ||
                ($saptawara == 5 && $wuku == 14) ||
                ($saptawara == 5 && $wuku == 18) ||
                ($saptawara == 7 && $wuku == 20) ||
                ($saptawara == 7 && $wuku == 26)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Guntur Graha', 'keterangan' => $description]);
            }

            // 63. Ingkel Macan
            if ($saptawara == 5 && $pancawara == 3 && $wuku == 7) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ingkel Macan', 'keterangan' => $description]);
            }

            // 64. Istri Payasan
            if ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Istri Payasan', 'keterangan' => $description]);
            }

            // 65. Jiwa Manganti
            if (($saptawara == 2 && $wuku == 19) || ($saptawara == 5 && ($wuku == 2 || $wuku == 20)) || ($saptawara == 6 && ($wuku == 25 || $wuku == 7)) || ($saptawara == 7 && $wuku == 30)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Jiwa Manganti', 'keterangan' => $description]);
            }

            // 66. Kajeng Kipkipan
            if ($saptawara == 4 && ($wuku == 6 || $wuku == 30)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kajeng Kipkipan', 'keterangan' => $description]);
            }

            // 67. Kajeng Kliwon Enyitan
            if ($triwara == 3 && $pancawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 < 15 && $sasihDay1 > 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 < 15 && $sasihDay2 > 7))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kajeng Kliwon Enyitan', 'keterangan' => $description]);
            }

            // 68. Kajeng Lulunan
            if ($triwara == 3 && $astawara == 5 && $sangawara == 9) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kajeng Lulunan', 'keterangan' => $description]);
            }

            // 69. Kajeng Rendetan
            if ($triwara == 3 && $pengalantaka == 'Penanggal' && ($saptawara == 1 || $saptawara == 4 || $saptawara == 7)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kajeng Rendetan', 'keterangan' => $description]);
            }

            // 70. Kajeng Susunan
            if ($triwara == 3 && $astawara == 3 && $sangawara == 9) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kajeng Susunan', 'keterangan' => $description]);
            }

            // 71. Kajeng Uwudan
            if ($triwara == 3 && $pengalantaka == 'Pangelong' && ($saptawara == 1 || $saptawara == 4 || $saptawara == 7)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kajeng Uwudan', 'keterangan' => $description]);
            }

            // 72. Kala Alap
            if ($saptawara == 2 && $wuku == 22) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Alap', 'keterangan' => $description]);
            }

            // 73. Kala Angin
            if ($saptawara == 1 && ($wuku == 17 || $wuku == 25 || $wuku == 28)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Angin', 'keterangan' => $description]);
            }

            // 74. Kala Atat
            if (($saptawara == 1 && $wuku == 22) || ($saptawara == 3 && $wuku == 30) || ($saptawara == 4 && $wuku == 19)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Atat', 'keterangan' => $description]);
            }

            // 75. Kala Awus
            if ($saptawara == 4 && $wuku == 28) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Awus', 'keterangan' => $description]);
            }

            // 76. Kala Bancaran
            if (
                $saptawara == 1 && $wuku == 11 ||
                $saptawara == 2 && $wuku == 1 ||
                $saptawara == 3 && ($wuku == 5 || $wuku == 11 || $wuku == 19) ||
                $saptawara == 5 && $wuku == 21 ||
                $saptawara == 6 && $wuku == 12 ||
                $saptawara == 7 && $wuku == 7
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Bancaran', 'keterangan' => $description]);
            }

            // 77. Kala Bangkung, Kala Nanggung
            if (
                $saptawara == 1 && $pancawara == 3 ||
                $saptawara == 2 && $pancawara == 2 ||
                $saptawara == 4 && $pancawara == 1 ||
                $saptawara == 7 && $pancawara == 4
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Bangkung, Kala Nanggung', 'keterangan' => $description]);
            }

            // 78. Kala Beser
            if ($sadwara == 1 && $astawara == 7) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Beser', 'keterangan' => $description]);
            }

            // 79. Kala Brahma
            if (
                $saptawara == 1 && $wuku == 23 ||
                $saptawara == 3 && $wuku == 14 ||
                $saptawara == 4 && $wuku == 1 ||
                $saptawara == 6 && ($wuku == 4 || $wuku == 25 || $wuku == 30) ||
                $saptawara == 7 && $wuku == 13
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Brahma', 'keterangan' => $description]);
            }

            // 80. Kala Bregala
            if ($saptawara == 2 && $wuku == 2) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Bregala', 'keterangan' => $description]);
            }

            // 81. Kala Buingrau
            if (($saptawara == 1 && $astawara == 2) ||
                ($saptawara == 2 && $astawara == 8) ||
                ($saptawara == 3 && $astawara == 5) ||
                ($saptawara == 4 && $astawara == 6) ||
                ($saptawara == 5 && $astawara == 3) ||
                ($saptawara == 6 && $astawara == 1) ||
                ($saptawara == 7 && $astawara == 4)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Buingrau', 'keterangan' => $description]);
            }

            // 82. Kala Cakra
            if ($saptawara == 7 && $wuku == 23) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Cakra', 'keterangan' => $description]);
            }

            // 83. Kala Capika
            if ($saptawara == 1 && $wuku == 18 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay2 == 3)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Capika', 'keterangan' => $description]);
            }

            // 84. Kala Caplokan
            if (($saptawara == 2 && ($wuku == 18 || $wuku == 9)) ||
                ($saptawara == 3 && $wuku == 19) ||
                ($saptawara == 4 && $wuku == 24) ||
                ($saptawara == 6 && $wuku == 12) ||
                ($saptawara == 7 && ($wuku == 9 || $wuku == 15 || $wuku == 1))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Caplokan', 'keterangan' => $description]);
            }

            // 85. Kala Cepitan
            if ($saptawara == 2 && $pancawara == 2 && $wuku == 18) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Cepitan', 'keterangan' => $description]);
            }

            // 86. Kala Dangastra
            if (($saptawara == 1 && ($wuku == 4 || $wuku == 23)) ||
                ($saptawara == 2 && ($wuku == 10 || $wuku == 29)) ||
                ($saptawara == 3 && ($wuku == 14 || $wuku == 16 || $wuku == 18)) ||
                ($saptawara == 4 && ($wuku == 1 || $wuku == 20)) ||
                ($saptawara == 5 && $wuku == 11) ||
                ($saptawara == 6 && ($wuku == 4 || $wuku == 11 || $wuku == 25 || $wuku == 30)) ||
                ($saptawara == 7 && ($wuku == 13 || $wuku == 15 || $wuku == 17))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Dangastra', 'keterangan' => $description]);
            }

            // 87. Kala Dangu
            if (($saptawara == 1 && ($wuku == 5 || $wuku == 13 || $wuku == 22 || $wuku == 27)) ||
                ($saptawara == 2 && $wuku == 18) ||
                ($saptawara == 3 && ($wuku == 3 || $wuku == 6 || $wuku == 11 || $wuku == 17)) ||
                ($saptawara == 4 && ($wuku == 1 || $wuku == 9 || $wuku == 19 || $wuku == 28)) ||
                ($saptawara == 5 && ($wuku == 7 || $wuku == 15 || $wuku == 24)) ||
                ($saptawara == 6 && ($wuku == 11 || $wuku == 21 || $wuku == 23 || $wuku == 26)) ||
                ($saptawara == 7 && ($wuku == 8 || $wuku == 10 || $wuku == 11 ||
                    $wuku == 14 || $wuku == 16 || $wuku == 20 || $wuku == 25 ||
                    $wuku == 29 || $wuku == 30))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Dangu', 'keterangan' => $description]);
            }

            // 88. Kala Demit
            if ($saptawara == 7 && $wuku == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Demit', 'keterangan' => $description]);
            }

            // 89. Kala Empas Munggah
            if ($pancawara == 4 && $sadwara == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Empas Munggah', 'keterangan' => $description]);
            }

            // 90. Kala Empas Turun
            if ($pancawara == 4 && $sadwara == 6) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Empas Turun', 'keterangan' => $description]);
            }

            // 91. Kala Gacokan
            if ($saptawara == 3 && $wuku == 19) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Gacokan', 'keterangan' => $description]);
            }

            // 92. Kala Garuda
            if ($saptawara == 3 && $wuku == 2) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Garuda', 'keterangan' => $description]);
            }

            // 93. Kala Geger
            if (($saptawara == 5 || $saptawara == 7) && $wuku == 7) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Geger', 'keterangan' => $description]);
            }

            // 94. Kala Gotongan
            if (($saptawara == 6 && $pancawara == 5) ||
                ($saptawara == 7 && $pancawara == 1) ||
                ($saptawara == 1 && $pancawara == 2)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Gotongan', 'keterangan' => $description]);
            }

            // 95. Kala Graha
            if (($saptawara == 2 && $wuku == 2) ||
                ($saptawara == 7 && $wuku == 5)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Graha', 'keterangan' => $description]);
            }

            // 96. Kala Gumarang Munggah
            if ($pancawara == 3 && $sadwara == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Gumarang Munggah', 'keterangan' => $description]);
            }

            // 97. Kala Gumarang Turun
            if ($pancawara == 3 && $sadwara == 6) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Gumarang Turun', 'keterangan' => $description]);
            }

            // 98. Kala Guru
            if ($saptawara == 4 && $wuku == 2) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Guru', 'keterangan' => $description]);
            }

            // 99. Kala Ingsor
            if ($wuku == 4 || $wuku == 14 || $wuku == 24) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Ingsor', 'keterangan' => $description]);
            }

            // 100. Kala Isinan
            if (($saptawara == 2 && ($wuku == 11 || $wuku == 17)) ||
                ($saptawara == 4 && $wuku == 30)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Isinan', 'keterangan' => $description]);
            }

            // 101. Kala Jangkut
            if ($triwara == 3 && $dwiwara == 2) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Jangkut', 'keterangan' => $description]);
            }

            // 102. Kala Jengkang
            if ($saptawara == 1 && $pancawara == 1 && $wuku == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Jengkang', 'keterangan' => $description]);
            }

            // 103. Kala Jengking
            if ($sadwara == 3 && $astawara == 7) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Jengking', 'keterangan' => $description]);
            }

            // 104. Kala Katemu
            if (($saptawara == 1 && ($wuku == 1 || $wuku == 9 || $wuku == 15)) ||
                ($saptawara == 2 && ($wuku == 3 || $wuku == 5 || $wuku == 17)) ||
                ($saptawara == 3 && ($wuku == 11 || $wuku == 16 || $wuku == 19 || $wuku == 30)) ||
                ($saptawara == 4 && ($wuku == 13 || $wuku == 29 || $wuku == 5 || $wuku == 7)) ||
                ($saptawara == 5 && ($wuku == 15 || $wuku == 1 || $wuku == 9)) ||
                ($saptawara == 6 && ($wuku == 17 || $wuku == 3)) ||
                ($saptawara == 7 && ($wuku == 16 || $wuku == 19 || $wuku == 27 || $wuku == 5 || $wuku == 11))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Katemu', 'keterangan' => $description]);
            }

            // 105. Kala Keciran
            if ($saptawara == 4 && $wuku == 6) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Keciran', 'keterangan' => $description]);
            }

            // 106. Kala Kilang-Kilung
            if (($saptawara == 2 && $wuku == 17) ||
                ($saptawara == 5 && $wuku == 19)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Kilang-Kilung', 'keterangan' => $description]);
            }

            // 107. Kala Kingkingan
            if ($saptawara == 5 && $wuku == 17) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Kingkingan', 'keterangan' => $description]);
            }

            // 108. Kala Klingkung
            if ($saptawara == 3 && $wuku == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Klingkung', 'keterangan' => $description]);
            }

            // 109. Kala Kutila Manik
            if ($triwara == 3 && $pancawara == 5) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Kutila Manik', 'keterangan' => $description]);
            }

            // 110. Kala Kutila
            if ($sadwara == 2 && $astawara == 6) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Kutila', 'keterangan' => $description]);
            }

            // 111. Kala Luang
            if (($saptawara == 1 && ($wuku == 11 || $wuku == 12 || $wuku == 13)) ||
                ($saptawara == 2 && ($wuku == 27)) ||
                ($saptawara == 3 && ($wuku == 1 || $wuku == 10 || $wuku == 8 || $wuku == 19 || $wuku == 23 || $wuku == 30)) ||
                ($saptawara == 4 && ($wuku == 2 || $wuku == 5 || $wuku == 6 || $wuku == 16 || $wuku == 18)) ||
                ($saptawara == 5 && ($wuku == 28 || $wuku == 29))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Luang', 'keterangan' => $description]);
            }

            // 112. Kala Lutung Megelut
            if (($saptawara == 1 && $wuku == 3) || ($saptawara == 4 && $wuku == 10)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Lutung Megelut', 'keterangan' => $description]);
            }

            // 113. Kala Lutung Megandong
            if ($saptawara == 5 && $pancawara == 5) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Lutung Megandong', 'keterangan' => $description]);
            }

            // 114. Kala Macan
            if ($saptawara == 5 && $wuku == 19) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Macan', 'keterangan' => $description]);
            }

            // 115. Kala Mangap
            if ($saptawara == 1 && $pancawara == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Mangap', 'keterangan' => $description]);
            }

            // 116. Kala Manguneb
            if ($saptawara == 5 && $pancawara == 14) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Manguneb', 'keterangan' => $description]);
            }

            // 117. Kala Matampak
            if (($saptawara == 4 && $wuku == 3) ||
                ($saptawara == 5 && $wuku == 28) ||
                ($saptawara == 6 && $wuku == 3) ||
                ($saptawara == 7 && ($wuku == 7 || $wuku == 24))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Matampak', 'keterangan' => $description]);
            }

            // 118. Kala Mereng
            if (($saptawara == 1 && ($wuku == 9 || $wuku == 24)) ||
                ($saptawara == 2 && ($wuku == 11 || $wuku == 26)) ||
                ($saptawara == 3 && ($wuku == 13)) ||
                ($saptawara == 4 && ($wuku == 15 || $wuku == 30)) ||
                ($saptawara == 5 && ($wuku == 2 || $wuku == 17 || $wuku == 19)) ||
                ($saptawara == 7 && ($wuku == 21))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Mereng', 'keterangan' => $description]);
            }

            // 119. Kala Miled
            if ($saptawara == 2 && $pancawara == 16) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Miled', 'keterangan' => $description]);
            }

            // 120. Kala Mina
            if ($saptawara == 6 && ($wuku == 8 || $wuku == 14)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Mina', 'keterangan' => $description]);
            }

            // 121. Kala Mretyu
            if (($saptawara == 1 && ($wuku == 1 || $wuku == 18)) ||
                ($saptawara == 2 && ($wuku == 23)) ||
                ($saptawara == 3 && ($wuku == 14 || $wuku == 27)) ||
                ($saptawara == 4 && ($wuku == 1)) ||
                ($saptawara == 5 && ($wuku == 5)) ||
                ($saptawara == 6 && ($wuku == 9)) ||
                ($saptawara == 7 && ($wuku == 14))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Mretyu', 'keterangan' => $description]);
            }

            // 122. Kala Muas
            if (($saptawara == 1 && ($wuku == 4)) ||
                ($saptawara == 2 && ($wuku == 27)) ||
                ($saptawara == 7 && ($wuku == 16))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Muas', 'keterangan' => $description]);
            }

            // 123. Kala Muncar
            if ($saptawara == 4 && ($wuku == 11)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Muncar', 'keterangan' => $description]);
            }

            // 124. Kala Muncrat
            if ($saptawara == 2 && $pancawara == 3 && $wuku == 18) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Muncrat', 'keterangan' => $description]);
            }

            // 125. Kala Ngadeg
            if (($saptawara == 1 && ($wuku == 15 || $wuku == 17)) ||
                ($saptawara == 2 && ($wuku == 19 || $wuku == 28)) ||
                ($saptawara == 6 && ($wuku == 12 || $wuku == 30))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Ngadeg', 'keterangan' => $description]);
            }

            // 118. Kala Mereng
            if (($saptawara == 1 && ($wuku == 9 || $wuku == 24)) ||
                ($saptawara == 2 && ($wuku == 11 || $wuku == 26)) ||
                ($saptawara == 3 && ($wuku == 13)) ||
                ($saptawara == 4 && ($wuku == 15 || $wuku == 30)) ||
                ($saptawara == 5 && ($wuku == 2 || $wuku == 17 || $wuku == 19)) ||
                ($saptawara == 7 && ($wuku == 21))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Mereng', 'keterangan' => $description]);
            }

            // 119. Kala Miled
            if ($saptawara == 2 && $pancawara == 16) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Miled', 'keterangan' => $description]);
            }

            // 120. Kala Mina
            if ($saptawara == 6 && ($wuku == 8 || $wuku == 14)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Mina', 'keterangan' => $description]);
            }

            // 121. Kala Mretyu
            if (($saptawara == 1 && ($wuku == 1 || $wuku == 18)) ||
                ($saptawara == 2 && ($wuku == 23)) ||
                ($saptawara == 3 && ($wuku == 14 || $wuku == 27)) ||
                ($saptawara == 4 && ($wuku == 1)) ||
                ($saptawara == 5 && ($wuku == 5)) ||
                ($saptawara == 6 && ($wuku == 9)) ||
                ($saptawara == 7 && ($wuku == 14))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Mretyu', 'keterangan' => $description]);
            }

            // 122. Kala Muas
            if (($saptawara == 1 && ($wuku == 4)) ||
                ($saptawara == 2 && ($wuku == 27)) ||
                ($saptawara == 7 && ($wuku == 16))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Muas', 'keterangan' => $description]);
            }

            // 123. Kala Muncar
            if ($saptawara == 4 && ($wuku == 11)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Muncar', 'keterangan' => $description]);
            }

            // 124. Kala Muncrat
            if ($saptawara == 2 && $pancawara == 3 && $wuku == 18) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Muncrat', 'keterangan' => $description]);
            }

            // 125. Kala Ngadeg
            if (($saptawara == 1 && ($wuku == 15 || $wuku == 17)) ||
                ($saptawara == 2 && ($wuku == 19 || $wuku == 28)) ||
                ($saptawara == 6 && ($wuku == 12 || $wuku == 30))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Ngadeg', 'keterangan' => $description]);
            }

            // 126. Kala Ngamut
            if ($saptawara == 2 && $wuku == 18) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Ngamut', 'keterangan' => $description]);
            }

            // 127. Kala Ngruda
            if (($saptawara == 1 && ($wuku == 29)) ||
                ($saptawara == 2 && ($wuku == 23 || $wuku == 10)) ||
                ($saptawara == 7 && ($wuku == 10))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Ngruda', 'keterangan' => $description]);
            }

            // 128. Kala Ngunya
            if ($saptawara == 1 && $wuku == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Ngunya', 'keterangan' => $description]);
            }

            // 129. Kala Olih
            if ($saptawara == 4 && $wuku == 24) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Olih', 'keterangan' => $description]);
            }

            // 130. Kala Pacekan
            if ($saptawara == 3 && $wuku == 5) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Pacekan', 'keterangan' => $description]);
            }

            // 131. Kala Pager
            if ($saptawara == 5 && $wuku == 7) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Pager', 'keterangan' => $description]);
            }

            // 132. Kala Panyeneng
            if (($saptawara == 1 && $wuku == 7) ||
                ($saptawara == 6 && $wuku == 30)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Panyeneng', 'keterangan' => $description]);
            }

            // 133. Kala Pati
            if (($saptawara == 1 && ($wuku == 10 || $wuku == 2)) ||
                ($saptawara == 3 && ($wuku == 6 || $wuku == 14 || $wuku == 27)) ||
                ($saptawara == 4 && ($wuku == 2 || $wuku == 10 || $wuku == 26)) ||
                ($saptawara == 7 && ($wuku == 17))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Pati', 'keterangan' => $description]);
            }

            // 134. Kala Pati Jengkang
            if ($saptawara == 5 && $sadwara == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Pati Jengkang', 'keterangan' => $description]);
            }

            // 135. Kala Pegat
            if (
                $saptawara == 4 && $wuku == 12 ||
                $saptawara == 7 && ($wuku == 3 || $wuku == 18)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Pegat', 'keterangan' => $description]);
            }

            // 136. Kala Prawani
            if (($saptawara == 1 && $wuku == 1) ||
                ($saptawara == 3 && $wuku == 24) ||
                ($saptawara == 4 && $wuku == 2) ||
                ($saptawara == 5 && $wuku == 19)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Prawani', 'keterangan' => $description]);
            }

            // 137. Kala Raja
            if ($saptawara == 5 && $wuku == 29) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Raja', 'keterangan' => $description]);
            }

            // 138. Kala Rau
            if (($saptawara == 1 && $wuku == 1) ||
                ($saptawara == 7 && ($wuku == 3 || $wuku == 4 || $wuku == 18)) ||
                ($saptawara == 6 && $wuku == 6)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Rau', 'keterangan' => $description]);
            }

            // 139. Kala Rebutan
            if ($saptawara == 2 && $wuku == 26) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Rebutan', 'keterangan' => $description]);
            }

            // 140. Kala Rumpuh
            if (($saptawara == 1 && ($wuku == 18 || $wuku == 30)) ||
                ($saptawara == 2 && ($wuku == 9 || $wuku == 20)) ||
                ($saptawara == 4 && ($wuku == 10 || $wuku == 19 || $wuku == 25 || $wuku == 26 || $wuku == 27)) ||
                ($saptawara == 5 && ($wuku == 13 || $wuku == 14 || $wuku == 17 || $wuku == 22 || $wuku == 24)) ||
                ($saptawara == 6 && ($wuku == 11 || $wuku == 12)) ||
                ($saptawara == 7 && ($wuku == 21 || $wuku == 23 || $wuku == 28 || $wuku == 29))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Rumpuh', 'keterangan' => $description]);
            }

            // 141. Kala Sapuhau
            if (($saptawara == 2 && $wuku == 3) ||
                ($saptawara == 3 && $wuku == 27) ||
                ($saptawara == 4 && $wuku == 28) ||
                ($saptawara == 6 && $wuku == 30)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Sapuhau', 'keterangan' => $description]);
            }

            // 142. Kala Sarang
            if ($wuku == 7 || $wuku == 17) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Sarang', 'keterangan' => $description]);
            }

            // 143. Kala Siyung
            if (($saptawara == 1 && ($wuku == 2 || $wuku == 21)) ||
                ($saptawara == 2 && ($wuku == 1 || $wuku == 10 || $wuku == 25)) ||
                ($saptawara == 4 && ($wuku == 1 || $wuku == 20)) ||
                ($saptawara == 5 && ($wuku == 24 || $wuku == 26)) ||
                ($saptawara == 6 && $wuku == 28) ||
                ($saptawara == 7 && ($wuku == 15 || $wuku == 17))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Siyung', 'keterangan' => $description]);
            }

            // 144. Kala Sor
            if (($saptawara == 1 && ($wuku == 3 || $wuku == 9 || $wuku == 15 || $wuku == 21 || $wuku == 27)) ||
                ($saptawara == 2 && ($wuku == 1 || $wuku == 2 || $wuku == 8 || $wuku == 6 ||
                    $wuku == 11 || $wuku == 14 || $wuku == 16 || $wuku == 20 ||
                    $wuku == 21 || $wuku == 26)) ||
                ($saptawara == 3 && ($wuku == 9 || $wuku == 1 || $wuku == 4 || $wuku == 7 || $wuku == 13 ||
                    $wuku == 14 || $wuku == 24 || $wuku == 25 || $wuku == 29)) ||
                ($saptawara == 4 && ($wuku == 3 || $wuku == 8 || $wuku == 12 || $wuku == 13 ||
                    $wuku == 18 || $wuku == 23 || $wuku == 24 || $wuku == 28 || $wuku == 30)) ||
                ($saptawara == 5 && ($wuku == 5 || $wuku == 11 || $wuku == 17 || $wuku == 23 || $wuku == 29)) ||
                ($saptawara == 6 && ($wuku == 10 || $wuku == 8 || $wuku == 3 || $wuku == 4 || $wuku == 13 ||
                    $wuku == 16 || $wuku == 18 || $wuku == 22 || $wuku == 23 || $wuku == 28)) ||
                ($saptawara == 7 && ($wuku == 9 || $wuku == 3 || $wuku == 15 || $wuku == 21 || $wuku == 27))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Sor', 'keterangan' => $description]);
            }

            // 145. Kala Sudangastra
            if (($saptawara == 1 && $wuku == 24) ||
                ($saptawara == 3 && $wuku == 28) ||
                ($saptawara == 4 && ($wuku == 2 || $wuku == 12)) ||
                ($saptawara == 5 && $wuku == 19) ||
                ($saptawara == 7 && $wuku == 6)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Sudangastra', 'keterangan' => $description]);
            }

            // 146. Kala Sudukan
            if (($saptawara == 1 && $wuku == 12) ||
                ($saptawara == 2 && ($wuku == 2 || $wuku == 3 || $wuku == 22 || $wuku == 25)) ||
                ($saptawara == 3 && ($wuku == 6 || $wuku == 8 || $wuku == 27)) ||
                ($saptawara == 4 && ($wuku == 1 || $wuku == 20)) ||
                ($saptawara == 5 && $wuku == 21) ||
                ($saptawara == 6 && ($wuku == 5 || $wuku == 24 || $wuku == 26)) ||
                ($saptawara == 7 && ($wuku == 14 || $wuku == 15 || $wuku == 16 || $wuku == 17))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Sudukan', 'keterangan' => $description]);
            }

            // 147. Kala Sungsang
            if ($wuku == 27) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Sungsang', 'keterangan' => $description]);
            }

            // 148. Kala Susulan
            if ($saptawara == 2 && $wuku == 11) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Susulan', 'keterangan' => $description]);
            }

            // 149. Kala Suwung
            if (($saptawara == 2 && $wuku == 2) ||
                ($saptawara == 3 && ($wuku == 8 || $wuku == 10)) ||
                ($saptawara == 4 && ($wuku == 5 || $wuku == 6 || $wuku == 16 || $wuku == 19)) ||
                ($saptawara == 7 && ($wuku == 11 || $wuku == 13 || $wuku == 14))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Suwung', 'keterangan' => $description]);
            }

            // 150. Kala Tampak
            if (($saptawara == 1 && ($wuku == 5 || $wuku == 13 || $wuku == 21 || $wuku == 29)) ||
                ($saptawara == 2 && ($wuku == 3 || $wuku == 11 || $wuku == 19 || $wuku == 27)) ||
                ($saptawara == 3 && ($wuku == 8 || $wuku == 16 || $wuku == 24)) ||
                ($saptawara == 4 && ($wuku == 1 || $wuku == 9 || $wuku == 17 || $wuku == 25)) ||
                ($saptawara == 5 && ($wuku == 14 || $wuku == 22 || $wuku == 30)) ||
                ($saptawara == 6 && ($wuku == 4 || $wuku == 12 || $wuku == 20 || $wuku == 28)) ||
                ($saptawara == 7 && ($wuku == 7 || $wuku == 15 || $wuku == 23))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Tampak', 'keterangan' => $description]);
            }

            // 151. Kala Temah
            if (($saptawara == 1 && ($wuku == 14 || $wuku == 15 || $wuku == 28 || $wuku == 29)) ||
                ($saptawara == 2 && ($wuku == 1 || $wuku == 2 || $wuku == 5 || $wuku == 7 || $wuku == 8 || $wuku == 9 ||
                    $wuku == 13 || $wuku == 16 || $wuku == 20 || $wuku == 23 || $wuku == 30)) ||
                ($saptawara == 3 && ($wuku == 3 || $wuku == 10 || $wuku == 12 || $wuku == 17 || $wuku == 19)) ||
                ($saptawara == 4 && ($wuku == 4 || $wuku == 11)) ||
                ($saptawara == 5 && ($wuku == 3 || $wuku == 5 || $wuku == 10 || $wuku == 12 || $wuku == 17 || $wuku == 19)) ||
                ($saptawara == 6 && ($wuku == 3 || $wuku == 5 || $wuku == 9 || $wuku == 13 ||
                    $wuku == 16 || $wuku == 20 || $wuku == 23 || $wuku == 30)) ||
                ($saptawara == 7 && ($wuku == 3 || $wuku == 14 || $wuku == 15 || $wuku == 29))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Temah', 'keterangan' => $description]);
            }

            // 152. Kala Timpang
            if (($saptawara == 3 && $wuku == 1) ||
                ($saptawara == 6 && $wuku == 14) ||
                ($saptawara == 7 && $wuku == 2)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Timpang', 'keterangan' => $description]);
            }

            // 153. Kala Tukaran
            if ($saptawara == 3 && ($wuku == 3 || $wuku == 8)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Tukaran', 'keterangan' => $description]);
            }

            // 154. Kala Tumapel
            if ($wuku == 12 && ($saptawara == 3 || $saptawara == 4)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Tumapel', 'keterangan' => $description]);
            }

            // 155. Kala Tumpar
            if (($saptawara == 3 && $wuku == 13) || ($saptawara == 4 && $wuku == 8)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Tumpar', 'keterangan' => $description]);
            }

            // 156. Kala Upa
            if ($sadwara == 4 && $triwara == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Upa', 'keterangan' => $description]);
            }

            // 157. Kala Was
            if ($saptawara == 2 && $wuku == 17) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Was', 'keterangan' => $description]);
            }

            // 158. Kala Wikalpa
            if (($saptawara == 2 && ($wuku == 22 || $wuku == 25)) || ($saptawara == 6 && ($wuku == 27 || $wuku == 30))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Wikalpa', 'keterangan' => $description]);
            }

            // 159. Kala Wisesa
            if ($sadwara == 5 && $astawara == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Wisesa', 'keterangan' => $description]);
            }

            // 160. Kala Wong
            if ($saptawara == 4 && $wuku == 20) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kala Wong', 'keterangan' => $description]);
            }

            // 161. Kaleburau
            if (
                ($saptawara == 1 && ($wuku == 2 || $wuku == 3 || $wuku == 8 || $wuku == 14 || $wuku == 27 || $wuku == 30)) ||
                ($saptawara == 2 && ($triwara == 2 || $purnama_tilem == 'Tilem')) ||
                ($saptawara == 3 && ($wuku == 7 || $wuku == 13 || $wuku == 22 || $wuku == 25 || $wuku == 21)) ||
                ($saptawara == 4 && ($wuku == 17 || $wuku == 29 || $wuku == 21)) ||
                ($saptawara == 5 && $wuku == 20) ||
                ($saptawara == 6 && ($wuku == 6 || $wuku == 28)) ||
                ($saptawara == 7 && ($wuku == 18 || $wuku == 26))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kaleburau', 'keterangan' => $description]);
            }

            // 162. Kamajaya
            if ($saptawara == 4 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay1 == 3 || $sasihDay1 == 7)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 2 || $sasihDay2 == 3 || $sasihDay2 == 7)))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Kamajaya', 'keterangan' => $description]);
            }

            // 163. Karna Sula
            if (
                ($saptawara == 1 && ($sasihDay1 == 2 || $sasihDay2 == 2)) ||
                ($saptawara == 3 && ($sasihDay1 == 9 || $sasihDay2 == 9)) ||
                ($saptawara == 7 && ($purnama_tilem == 'Purnama' || $purnama_tilem == 'Tilem'))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Karna Sula', 'keterangan' => $description]);
            }

            // 164. Karnasula
            if (
                ($saptawara == 2 && ($wuku == 1 || $wuku == 4 || $wuku == 7 || $wuku == 9)) ||
                ($saptawara == 3 && $wuku == 13) ||
                ($saptawara == 4 && $wuku == 11) ||
                ($saptawara == 5 && ($wuku == 8 || $wuku == 11)) ||
                ($saptawara == 6 && $wuku == 3) ||
                ($saptawara == 7 && ($wuku == 5 || $wuku == 10))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Karnasula', 'keterangan' => $description]);
            }

            // 165. Lebur Awu
            if (
                ($saptawara == 1 && $astawara == 2) ||
                ($saptawara == 2 && $astawara == 8) ||
                ($saptawara == 3 && $astawara == 5) ||
                ($saptawara == 4 && $astawara == 6) ||
                ($saptawara == 5 && $astawara == 3) ||
                ($saptawara == 6 && $astawara == 1) ||
                ($saptawara == 7 && $astawara == 4)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Lebur Awu', 'keterangan' => $description]);
            }

            // 166. Lutung Magandong
            if ($saptawara == 5 && ($wuku == 3 || $wuku == 8 || $wuku == 13 || $wuku == 18 || $wuku == 23 || $wuku == 28)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Lutung Magandong', 'keterangan' => $description]);
            }

            // 167. Macekan Agung
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 12) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 12))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 11) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 11))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 5 || $sasihDay1 == 7)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 5 || $sasihDay2 == 7)))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Macekan Agung', 'keterangan' => $description]);
            }

            // 168. Macekan Lanang
            if (
                ($saptawara == 1 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay1 == 12)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 2 || $sasihDay2 == 12)))) ||
                ($saptawara == 2 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay1 == 11)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 1 || $sasihDay2 == 11)))) ||
                ($saptawara == 3 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 11 || $sasihDay1 == 9)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 11 || $sasihDay2 == 9)))) ||
                ($saptawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 9) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 9))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 8) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 8))) ||
                ($saptawara == 6 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 5 || $sasihDay1 == 7)) || ($pengalantaka == 'Penanggal' && ($sasihDay2 == 5 || $sasihDay2 == 7)))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 6) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 6)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Macekan Lanang', 'keterangan' => $description]);
            }

            // 169. Macekan Wadon
            if (
                ($saptawara == 1 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 5) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 5))) ||
                ($saptawara == 2 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 11) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 11))) ||
                ($saptawara == 3 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 10) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 10))) ||
                ($saptawara == 4 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 9) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 9))) ||
                ($saptawara == 5 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 8) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 8))) ||
                ($saptawara == 7 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 13) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 13)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Macekan Wadon', 'keterangan' => $description]);
            }

            // 170. Merta Sula
            if ($saptawara == 5 && ($sasihDay1 == 7 || $sasihDay2 == 7)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Merta Sula', 'keterangan' => $description]);
            }

            // 171. Naga Naut
            if ($sasihDay1 == 'no_sasih' || $sasihDay2 == 'no_sasih') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Naga Naut', 'keterangan' => $description]);
            }

            // 172. Pemacekan
            if (
                ($saptawara == 1 && ($sasihDay1 == 12 || $sasihDay2 == 12 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 2 && ($sasihDay1 == 11 || $sasihDay2 == 11 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 3 && ($sasihDay1 == 10 || $sasihDay2 == 10 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 4 && ($sasihDay1 == 9 || $sasihDay2 == 9 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 5 && ($sasihDay1 == 8 || $sasihDay2 == 8 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 6 && ($sasihDay1 == 7 || $sasihDay2 == 7 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 7 && ($sasihDay1 == 6 || $sasihDay2 == 6 || $sasihDay1 == 15 || $sasihDay2 == 15))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Pamacekan', 'keterangan' => $description]);
            }

            // 173. Panca Amerta
            if ($saptawara == 4 && $pancawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Panca Amerta', 'keterangan' => $description]);
            }

            // 174. Panca Prawani
            if ($sasihDay1 == 4 || $sasihDay1 == 8 || $sasihDay1 == 12 || $sasihDay2 == 4 || $sasihDay2 == 8 || $sasihDay2 == 12) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Panca Prawani', 'keterangan' => $description]);
            }

            // 175. Panca Wedhi
            if ($saptawara == 2 && $pancawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Panca Werdhi', 'keterangan' => $description]);
            }

            // 176. Pati Paten
            if ($saptawara == 6 && (($sasihDay1 == 10 || $sasihDay2 == 10) || $purnama_tilem == 'Tilem')) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Pati Paten', 'keterangan' => $description]);
            }

            // 177. Patra Limutan
            if ($triwara == 3 && $purnama_tilem == 'Tilem') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Patra Limutan', 'keterangan' => $description]);
            }

            // 178. Pepedan
            if (
                ($saptawara == 1 && ($wuku == 5 || $wuku == 9 || $wuku == 10 || $wuku == 11 || $wuku == 15 || $wuku == 20 ||
                    $wuku == 21 || $wuku == 23 || $wuku == 25 || $wuku == 26 || $wuku == 27 || $wuku == 28 ||
                    $wuku == 30 || $wuku == 22
                )) ||
                ($saptawara == 2 && ($wuku == 8 || $wuku == 14 || $wuku == 17 || $wuku == 18 || $wuku == 21 || $wuku == 22 ||
                    $wuku == 24 || $wuku == 25 || $wuku == 26 || $wuku == 27 || $wuku == 29
                )) ||
                ($saptawara == 3 && ($wuku == 1 || $wuku == 3 || $wuku == 5 || $wuku == 7 || $wuku == 10 || $wuku == 11 ||
                    $wuku == 13 || $wuku == 14 || $wuku == 17 || $wuku == 18 || $wuku == 19 || $wuku == 20 ||
                    $wuku == 22 || $wuku == 23 || $wuku == 24 || $wuku == 25 || $wuku == 26 || $wuku == 27 ||
                    $wuku == 29 || $wuku == 30
                )) ||
                ($saptawara == 4 && ($wuku == 4 || $wuku == 5 || $wuku == 6 || $wuku == 7 || $wuku == 8 || $wuku == 9 ||
                    $wuku == 11 || $wuku == 12 || $wuku == 15 || $wuku == 16 || $wuku == 18 || $wuku == 23 ||
                    $wuku == 24 || $wuku == 27 || $wuku == 28 || $wuku == 30
                )) ||
                ($saptawara == 5 && ($wuku == 1 || $wuku == 3 || $wuku == 4 || $wuku == 7 || $wuku == 8 || $wuku == 9 ||
                    $wuku == 11 || $wuku == 14 || $wuku == 19 || $wuku == 21 || $wuku == 23 || $wuku == 24 ||
                    $wuku == 29
                )) ||
                ($saptawara == 6 && ($wuku == 2 || $wuku == 4 || $wuku == 14 || $wuku == 16 || $wuku == 19 || $wuku == 20 ||
                    $wuku == 21 || $wuku == 23 || $wuku == 24 || $wuku == 25 || $wuku == 27 || $wuku == 29
                )) ||
                ($saptawara == 7 && ($wuku == 2 || $wuku == 3 || $wuku == 7 || $wuku == 9 || $wuku == 10 || $wuku == 11 ||
                    $wuku == 13 || $wuku == 23 || $wuku == 24 || $wuku == 25 || $wuku == 27 || $wuku == 29 ||
                    $wuku == 30
                ))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Pepedan', 'keterangan' => $description]);
            }

            // 179. Prabu Pendah
            if ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 14) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 14))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Prabu Pendah', 'keterangan' => $description]);
            }

            // 180. Prangewa
            if ($saptawara == 3 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 1) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 1))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Prangewa', 'keterangan' => $description]);
            }

            // 181. Purnama Danta
            if ($saptawara == 4 && $pancawara == 5 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Purnama Danta', 'keterangan' => $description]);
            }

            // 182. Purna Suka
            if ($saptawara == 6 && $pancawara == 1 && $purnama_tilem == 'Purnama') {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Purna Suka', 'keterangan' => $description]);
            }

            // 183. Purwani
            if ($sasihDay1 == 14 || $sasihDay2 == 14) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Purwani', 'keterangan' => $description]);
            }

            // 184. Purwanin Dina
            if (
                ($saptawara == 2 && $pancawara == 4) ||
                ($saptawara == 3 && $pancawara == 5) ||
                ($saptawara == 4 && $pancawara == 5) ||
                ($saptawara == 6 && $pancawara == 4) ||
                ($saptawara == 7 && $pancawara == 5)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Purwanin Dina', 'keterangan' => $description]);
            }

            // 185. Rangda Tiga
            if ($wuku == 7 || $wuku == 8 || $wuku == 15 || $wuku == 16 || $wuku == 23 || $wuku == 24) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Rangda Tiga', 'keterangan' => $description]);
            }

            // 186. Rarung Pagelangan
            if ($saptawara == 5 && ($sasihDay1 == 6 || $sasihDay2 == 6)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Rarung Pagelangan', 'keterangan' => $description]);
            }

            // 187. Ratu Magelung
            if ($saptawara == 4 && $wuku == 23) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ratu Magelung', 'keterangan' => $description]);
            }

            // 188. Ratu Mangure
            if ($saptawara == 5 && $wuku == 20) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ratu Mangure', 'keterangan' => $description]);
            }

            // 189. Ratu Megambahan
            if ($saptawara == 7 && (($pengalantaka == 'Pangelong' && $sasihDay1 == 6) || ($pengalantaka == 'Pangelong' && $sasihDay2 == 6))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ratu Megambahan', 'keterangan' => $description]);
            }

            // 190. Ratu Nanyingal
            if ($saptawara == 5 && $wuku == 21) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ratu Nanyingal', 'keterangan' => $description]);
            }

            // 191. Ratu Ngemban Putra
            if ($saptawara == 6 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Ratu Ngemban Putra', 'keterangan' => $description]);
            }

            // 192. Rekatadala Ayudana
            if ($saptawara == 1 && ($sasihDay1 == 1 || $sasihDay1 == 6 || $sasihDay1 == 11 || $sasihDay1 == 2 || $sasihDay2 == 6 || $sasihDay2 == 11)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Rekatadala Ayudana', 'keterangan' => $description]);
            }

            // 193. Salah Wadi
            if ($wuku == 1 || $wuku == 2 || $wuku == 6 || $wuku == 10 || $wuku == 11 || $wuku == 16 || $wuku == 19 || $wuku == 20 || $wuku == 24 || $wuku == 25 || $wuku == 27 || $wuku == 30) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Salah Wadi', 'keterangan' => $description]);

                // dd($description);
            }

            // 194. Sampar Wangke
            if ($saptawara == 2 && $sadwara == 2) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sampar Wangke', 'keterangan' => $description]);
            }

            // 195. Sampi Gumarang Munggah
            if ($pancawara == 3 && $sadwara == 4) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sampi Gumarang Munggah', 'keterangan' => $description]);
            }

            // 196. Sampi Gumarang Turun
            if ($pancawara == 3 && $sadwara == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sampi Gumarang Turun', 'keterangan' => $description]);
            }

            // 197. Sarik Agung
            if ($saptawara == 4 && ($wuku == 25 || $wuku == 4 || $wuku == 11 || $wuku == 18)) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sarik Agung', 'keterangan' => $description]);
            }

            // 198. Sarik Ketah
            if (($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sarik Ketah', 'keterangan' => $description]);
            }

            // 199. Sedana Tiba
            if ($saptawara == 5 && $pancawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 7) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 7))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sedana Tiba', 'keterangan' => $description]);
            }

            // 200. Sedana Yoga
            if (($saptawara == 1 && ($sasihDay1 == 8 || $sasihDay2 == 8 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 2 && ($sasihDay1 == 3 || $sasihDay2 == 3)) ||
                ($saptawara == 3 && ($sasihDay1 == 7 || $sasihDay2 == 7)) ||
                ($saptawara == 4 && ($sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 3 || $sasihDay2 == 3)) ||
                ($saptawara == 5 && ($sasihDay1 == 4 || $sasihDay2 == 4 || $sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 6 && ($sasihDay1 == 1 || $sasihDay2 == 1 || $sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($saptawara == 7 && ($sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 15 || $sasihDay2 == 15))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sedana Yoga', 'keterangan' => $description]);
            }

            // 201. Semut Sadulur
            if (($saptawara == 6 && $pancawara == 3) ||
                ($saptawara == 7 && $pancawara == 4) ||
                ($saptawara == 1 && $pancawara == 5)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Semut Sadulur', 'keterangan' => $description]);
            }

            // 202. Siwa Sampurna
            if (($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Siwa Sampurna', 'keterangan' => $description]);
            }

            // 203. Sri Bagia
            if (($saptawara == 2 && ($wuku == 6 || $wuku == 15 || $wuku == 21)) ||
                ($saptawara == 4 && $wuku == 4) ||
                ($saptawara == 7 && ($wuku == 1 || $wuku == 25))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sri Bagia', 'keterangan' => $description]);
            }

            // 200. Sedana Yoga
            if (($saptawara == 1 && ($sasihDay1 == 8 || $sasihDay2 == 8 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 2 && ($sasihDay1 == 3 || $sasihDay2 == 3)) ||
                ($saptawara == 3 && ($sasihDay1 == 7 || $sasihDay2 == 7)) ||
                ($saptawara == 4 && ($sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 3 || $sasihDay2 == 3)) ||
                ($saptawara == 5 && ($sasihDay1 == 4 || $sasihDay2 == 4 || $sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 15 || $sasihDay2 == 15)) ||
                ($saptawara == 6 && ($sasihDay1 == 1 || $sasihDay2 == 1 || $sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($saptawara == 7 && ($sasihDay1 == 5 || $sasihDay2 == 5 || $sasihDay1 == 15 || $sasihDay2 == 15))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sedana Yoga', 'keterangan' => $description]);
            }

            // 201. Semut Sadulur
            if (($saptawara == 6 && $pancawara == 3) ||
                ($saptawara == 7 && $pancawara == 4) ||
                ($saptawara == 1 && $pancawara == 5)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Semut Sadulur', 'keterangan' => $description]);
            }

            // 202. Siwa Sampurna
            if (($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5))) ||
                ($saptawara == 5 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Siwa Sampurna', 'keterangan' => $description]);
            }

            // 203. Sri Bagia
            if (($saptawara == 2 && ($wuku == 6 || $wuku == 15 || $wuku == 21)) ||
                ($saptawara == 4 && $wuku == 4) ||
                ($saptawara == 7 && ($wuku == 1 || $wuku == 25))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sri Bagia', 'keterangan' => $description]);
            }

            // 204. Sri Murti
            if ($sadwara == 5 && $astawara == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sri Murti', 'keterangan' => $description]);
            }

            // 205. Sri Tumpuk
            if ($astawara == 1) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Sri Tumpuk', 'keterangan' => $description]);
            }

            // 206. Srigati
            if (($triwara == 3 && $pancawara == 1 && $sadwara == 3) ||
                ($triwara == 3 && $pancawara == 1 && $sadwara == 6)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Srigati', 'keterangan' => $description]);
            }

            // 207. Srigati Jenek
            if ($pancawara == 5 && $sadwara == 6) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Srigati Jenek', 'keterangan' => $description]);
            }

            // 208. Srigati Munggah
            if ($pancawara == 1 && $sadwara == 3) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Srigati Munggah', 'keterangan' => $description]);
            }

            // 209. Srigati Turun
            if ($pancawara == 1 && $sadwara == 6) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Srigati Turun', 'keterangan' => $description]);
            }

            // 210. Subhacara
            if (($saptawara == 1 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay2 == 3)) ||
                    ($pengalantaka == 'Penanggal' && ($sasihDay1 == 15 || $sasihDay2 == 15)))) ||
                ($saptawara == 2 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 3 || $sasihDay2 == 3)) ||
                ($saptawara == 3 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay2 == 2 ||
                    $sasihDay1 == 7 || $sasihDay2 == 7 || $sasihDay1 == 8 || $sasihDay2 == 8)) ||
                ($saptawara == 4 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 2 || $sasihDay2 == 2 ||
                    $sasihDay1 == 3 || $sasihDay2 == 3 || $sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($saptawara == 5 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 5 || $sasihDay2 == 5 ||
                    $sasihDay1 == 6 || $sasihDay2 == 6)) ||
                ($saptawara == 6 && $pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay2 == 1 ||
                    $sasihDay1 == 2 || $sasihDay2 == 2 || $sasihDay1 == 3 || $sasihDay2 == 3)) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 4) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 4))) ||
                ($saptawara == 7 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Subhacara', 'keterangan' => $description]);
            }

            // 211. Swarga Menga
            if (($saptawara == 3 && $pancawara == 3 && $wuku == 3 &&
                    (($pengalantaka == 'Penanggal' && $sasihDay1 == 11) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 11))) ||
                ($saptawara == 5 && $pancawara == 2 && $wuku == 4)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Swarga Menga', 'keterangan' => $description]);
            }

            // 212. Taliwangke
            if (($saptawara == 2 && ($wuku == 22 || $wuku == 23 || $wuku == 24 || $wuku == 25 || $wuku == 26)) ||
                ($saptawara == 3 && ($wuku == 1 || $wuku == 27 || $wuku == 28 || $wuku == 29 || $wuku == 30)) ||
                ($saptawara == 4 && ($wuku == 2 || $wuku == 3 || $wuku == 4 || $wuku == 6)) ||
                ($saptawara == 5 && ($wuku == 7 || $wuku == 8 || $wuku == 9 || $wuku == 10 || $wuku == 11 || $wuku == 17 || $wuku == 18 || $wuku == 20 || $wuku == 21)) ||
                ($saptawara == 6 && ($wuku == 12 || $wuku == 13 || $wuku == 14 || $wuku == 15 || $wuku == 16))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Taliwangke', 'keterangan' => $description]);
            }

            // 213. Titibuwuk
            if (($saptawara == 1 && ($wuku == 18 || $wuku == 26 || $wuku == 27 || $wuku == 28 || $wuku == 30)) ||
                ($saptawara == 2 && ($wuku == 8 || $wuku == 9 || $wuku == 20)) ||
                ($saptawara == 3 && ($wuku == 7 || $wuku == 21 || $wuku == 1)) ||
                ($saptawara == 4 && ($wuku == 4 || $wuku == 5 || $wuku == 10 || $wuku == 15 || $wuku == 19 || $wuku == 25 || $wuku == 2)) ||
                ($saptawara == 5 && ($wuku == 6 || $wuku == 13 || $wuku == 17 || $wuku == 22 || $wuku == 24)) ||
                ($saptawara == 6 && ($wuku == 3 || $wuku == 12)) ||
                ($saptawara == 7 && ($wuku == 16 || $wuku == 21 || $wuku == 23 || $wuku == 29))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Titibuwuk', 'keterangan' => $description]);
            }

            // 214. Tunut Masih
            if (($saptawara == 1 && $wuku == 18) ||
                ($saptawara == 2 && ($wuku == 12 || $wuku == 13 || $wuku == 27)) ||
                ($saptawara == 3 && ($wuku == 17 || $wuku == 24)) ||
                ($saptawara == 5 && $wuku == 1) ||
                ($saptawara == 6 && ($wuku == 19 || $wuku == 22))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Tunut Masih', 'keterangan' => $description]);
            }

            // 215. Tutur Mandi
            if (($saptawara == 1 && $wuku == 26) ||
                ($saptawara == 5 && ($wuku == 3 || $wuku == 9 || $wuku == 15 || $wuku == 20 || $wuku == 21 || $wuku == 24)) ||
                ($saptawara == 6 && $wuku == 2) ||
                ($saptawara == 7 && $wuku == 24)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Tutur Mandi', 'keterangan' => $description]);
            }

            // 216. Uncal Balung
            if ($wuku == 12 || $wuku == 13 || (($wuku == 14 && $saptawara == 1) || ($wuku == 16 && $saptawara < 5))) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Uncal Balung', 'keterangan' => $description]);
            }

            // 217. Upadana Merta
            if (
                $saptawara == 1 && (($pengalantaka == 'Penanggal' && ($sasihDay1 == 1 || $sasihDay1 == 8 || $sasihDay1 == 6 || $sasihDay1 == 10)) ||
                    ($pengalantaka == 'Penanggal' && ($sasihDay2 == 1 || $sasihDay2 == 8 || $sasihDay2 == 6 || $sasihDay2 == 10)))
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Upadana Merta', 'keterangan' => $description]);
            }

            // 218. Werdi Suka
            if (
                $saptawara == 4 && $pancawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 10) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 10)) &&
                ($no_sasih == 1)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Werdi Suka', 'keterangan' => $description]);
            }

            // 219. Wisesa
            if (
                $saptawara == 4 && $pancawara == 2 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 13) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 13)) &&
                ($no_sasih == 1)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Wisesa', 'keterangan' => $description]);
            }

            // 220. Wredhi Guna
            if (
                $saptawara == 4 && $pancawara == 4 && (($pengalantaka == 'Penanggal' && $sasihDay1 == 5) || ($pengalantaka == 'Penanggal' && $sasihDay2 == 5)) &&
                ($no_sasih == 1)
            ) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['nama' => 'Wredhi Guna', 'keterangan' => $description]);
            }
        }

        // Remove leading comma and space
        // $dewasaAyu = ltrim($dewasaAyu, ', ');

        //     return response()->json([
        // if ($makna) {
        //         'keterangan' => $description,

        //         'dewasaAyu' => $dewasaAyu,
        //     ], 200);
        // }
        return $dewasaAyu;
    }
}
