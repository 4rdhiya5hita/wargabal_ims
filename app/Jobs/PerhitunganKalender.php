<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PengalantakaController;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\WukuController;
use Illuminate\Support\Facades\Cache;

class PerhitunganKalender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result1 = []; // Menyimpan hasil pemrosesan

        foreach ($this->data as $item) {
            // Lakukan pemrosesan pada setiap item data
            $processedData = $this->processItemFirst($item);

            // Tambahkan hasil pemrosesan ke dalam $result1
            $result1[] = $processedData;
        }

        $response = [
            'message' => 'Success',
            'data' => $result1,
        ];
        // dd($response);

        return response()->json($response);
    }

    /**
     * Proses item data.
     *
     * @param mixed $item
     * @return mixed
     */
    private function processItemFirst($tanggal)
    {
        // dd($tanggal);
        $cacheKey = 'processed-data-first-' . $tanggal;
        $minutes = 60; // Durasi penyimpanan cache dalam menit (sesuaikan dengan kebutuhan)

        // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            return $cachedData;
        }

        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $angkaWuku = 70;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $angkaWuku = 33;
        } else {
            $refTanggal = '1992-01-01';
            $angkaWuku = 88;
        }

        $wukuController = new WukuController();
        $saptawaraWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03;

        // $tanggal = $tanggal[0]['tanggal'];
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
        $saptawara = $saptawaraWaraController->getSaptawara($tanggal);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $noNgunaratri = 46;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $noNgunaratri = 50;
        } else {
            $refTanggal = '1992-01-01';
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $noNgunaratri = 22;
        }

        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        $pengalantaka_dan_hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        if ($tanggal > '2002-01-01' || $tanggal < '1992-01-01') {
            if (strtotime($tanggal) < strtotime($refTanggal)) {
                $no_sasih = $hariSasihController->getSasihBefore1992($tanggal, $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka);
            } else {
                $no_sasih = $hariSasihController->getSasihAfter2002($tanggal, $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka);
            }
        } else {
            $no_sasih = $hariSasihController->getSasihBetween($tanggal, $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka);
        }
        $hariRaya = $hariRayaController->getHariRaya($tanggal, $pengalantaka_dan_hariSasih['penanggal_1'], $pengalantaka_dan_hariSasih['penanggal_2'], $pengalantaka_dan_hariSasih['pengalantaka'], $no_sasih['no_sasih'], $triwara, $pancawara, $saptawara, $hasilWuku);

        // dd($hariRaya);
        // Cache::put($cacheKey, $hariRaya, $minutes);

        $response = [
            'tanggal' => $tanggal,
            'hariRaya' => $hariRaya,
        ];

        Cache::put($cacheKey, $response, $minutes);
        return $response;
       
    }

    // public function handle()
    // {
    //     $result1 = []; // Menyimpan hasil pemrosesan

    //     foreach ($this->data as $item) {
    //         // Lakukan pemrosesan pada setiap item data
    //         $processedData = $this->processItemFirst($item);

    //         // Tambahkan hasil pemrosesan ke dalam $result1
    //         $result1[] = $processedData;
    //     }

    //     $chunks = array_chunk($result1, 2);

    //     // Inisialisasi array untuk menyimpan ID job
    //     $jobIds = [];
    //     $kalender = [];

    //     // Memasukkan job ke dalam antrian untuk setiap bagian data
    //     foreach ($chunks as $chunk) {
    //         $job = $this->other_handle($chunk);
    //         $jobIds[] = $this->dispatch($job);
    //         array_push($kalender, $job);
    //     }
    //     $mergedJob = array_merge(...$kalender);
    //     // dd($job);
    //     // Menggabungkan semua elemen dalam $job menjadi satu array tunggal

    //     // Lakukan tindakan selanjutnya, seperti mengirim data kembali ke API atau menyimpannya di tempat lain
    //     // Misalnya, Anda dapat menggunakan HTTP Client untuk mengirim data kembali ke API

    //     $response = [
    //         'message' => 'Success',
    //         'data' => $mergedJob,
    //     ];
    //     // dd($response);

    //     return $response;
    // }

    // /**
    //  * Proses item data.
    //  *
    //  * @param mixed $item
    //  * @return mixed
    //  */
    // private function processItemFirst($tanggal)
    // {
    //     $tanggal = [];
    //     if (is_array($tanggal)) {
    //         // Mengambil data tanggal dari setiap elemen array dalam $job
    //         foreach ($tanggal as $item) {
    //             $tanggal[] = $tanggal;
    //         }
    //     }
    //     // dd($tanggal);
    //     // $cacheKey = 'processed-data-first-' . $tanggal;
    //     // $minutes = 60; // Durasi penyimpanan cache dalam menit (sesuaikan dengan kebutuhan)

    //     // // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
    //     // if (Cache::has($cacheKey)) {
    //     //     $cachedData = Cache::get($cacheKey);
    //     //     return $cachedData;
    //     // }

    //     if ($tanggal >= '2000-01-01') {
    //         $refTanggal = '2000-01-01';
    //         $angkaWuku = 70;
    //     } elseif ($tanggal < '1992-01-01') {
    //         $refTanggal = '1970-01-01';
    //         $angkaWuku = 33;
    //     } else {
    //         $refTanggal = '1992-01-01';
    //         $angkaWuku = 88;
    //     }

    //     // dd($tanggal);
    //     // Lakukan pemrosesan pada $item
    //     // Misalnya, lakukan operasi matematika atau transformasi data pada $item
    //     // Panggil semua controller yang dibutuhkan
    //     $wukuController = new WukuController();
    //     $saptawaraWaraController = new SaptaWaraController_07();
    //     $pancaWaraController = new PancaWaraController_05();
    //     $triWaraController = new TriWaraController_03;

    //     // $tanggal = $tanggal[0]['tanggal'];
    //     $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
    //     $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
    //     $saptawara = $saptawaraWaraController->getSaptawara($tanggal);
    //     $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
    //     $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

    //     $data = [
    //         'tanggal' => $tanggal,
    //         'hasilWuku' => $hasilWuku,
    //         'saptawara' => $saptawara,
    //         'pancawara' => $pancawara,
    //         'triwara' => $triwara,
    //     ];

    //     // dd($data);

    //     // Cache::put($cacheKey, $data, $minutes);

    //     return $data;
    // }

    // /**
    //  * Proses item data.
    //  *
    //  * @param mixed $item
    //  * @return mixed
    //  */
    // private function other_handle($chunk)
    // {
    //     $result2 = [];

    //     foreach ($chunk as $item) {
    //         // Lakukan pemrosesan pada setiap item data
    //         $processedData = $this->processItemSecond($item);

    //         // Tambahkan hasil pemrosesan ke dalam $result2
    //         array_push($result2, $processedData);
    //     }
    //     // dd($result2);
    //     // dd($processedData);

    //     return $result2;
    // }

    // private function processItemSecond($item)
    // {
    //     // dd($item);
    //     // $cacheKey = 'processed-data-second-' . serialize($item);
    //     // $minutes = 60; // Durasi penyimpanan cache dalam menit (sesuaikan dengan kebutuhan)

    //     // // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
    //     // if (Cache::has($cacheKey)) {
    //     //     $cachedData = Cache::get($cacheKey);
    //     //     return $cachedData;
    //     // }

    //     if ($item['tanggal'] >= '2000-01-01') {
    //         $refTanggal = '2000-01-01';
    //         $tahunSaka = 1921;
    //         $noSasih = 7;
    //         $penanggal = 10;
    //         $noNgunaratri = 46;
    //     } elseif ($item['tanggal'] < '1992-01-01') {
    //         $refTanggal = '1970-01-01';
    //         $tahunSaka = 1891;
    //         $noSasih = 7;
    //         $penanggal = 8;
    //         $noNgunaratri = 50;
    //     } else {
    //         $refTanggal = '1992-01-01';
    //         $tahunSaka = 1913;
    //         $noSasih = 7;
    //         $penanggal = 11;
    //         $noNgunaratri = 22;
    //     }

    //     $pengalantakaController = new PengalantakaController;
    //     $hariSasihController = new HariSasihController;
    //     $hariRayaController = new HariRayaController();

    //     $pengalantaka = $pengalantakaController->getPengalantaka($item['tanggal'], $refTanggal, $penanggal, $noNgunaratri);
    //     $hariSasih = $hariSasihController->getHariSasih($item['tanggal'], $refTanggal, $penanggal, $noNgunaratri);
    //     if ($item['tanggal'] > '2002-01-01' || $item['tanggal'] < '1992-01-01') {
    //         if (strtotime($item['tanggal']) < strtotime($refTanggal)) {
    //             $no_sasih = $hariSasihController->getSasihBefore1992($item['tanggal'], $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka);
    //         } else {
    //             $no_sasih = $hariSasihController->getSasihAfter2002($item['tanggal'], $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka);
    //         }
    //     } else {
    //         $no_sasih = $hariSasihController->getSasihBetween($item['tanggal'], $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka);
    //     }
    //     $hariRaya = $hariRayaController->getHariRaya($item['tanggal'], $hariSasih['penanggal_1'], $hariSasih['penanggal_2'], $pengalantaka, $no_sasih['no_sasih'], $item['triwara'], $item['pancawara'], $item['saptawara'], $item['hasilWuku']);

    //     // dd($hariRaya);
    //     // Cache::put($cacheKey, $hariRaya, $minutes);

    //     $response = [
    //         'tanggal' => $item['tanggal'],
    //         'hariRaya' => $hariRaya,
    //     ];

    //     return $response;
    // }
}
