<?php

use App\Http\Controllers\API\AcaraAPI;
use App\Http\Controllers\API\AutentikasiAPI;
use App\Http\Controllers\API\AlaAyuningDewasaAPI;
use App\Http\Controllers\API\HariRayaAPI;
use App\Http\Controllers\API\KalenderBaliAPI;
use App\Http\Controllers\API\KeteranganAPI;
use App\Http\Controllers\API\LogRequestAPI;
use App\Http\Controllers\API\OtonanAPI;
use App\Http\Controllers\API\PiodalanAPI;
use App\Http\Controllers\API\searchHariRayaAPI;
use App\Http\Controllers\API\TransactionAPI;
use App\Http\Controllers\API\MengaturDewasaAPI;
use App\Http\Controllers\API\RamalanSifatAPI;
use App\Http\Controllers\API\WarigaPersonalAPI;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgressHasil;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/tes', [KalenderBaliAPI::class, 'tesAPI']);
    Route::get('/kalenderAPI', [KalenderBaliAPI::class, 'getAPI']);
    Route::get('/searchTanggalHariRayaAPI', [KalenderBaliAPI::class, 'searchTanggalHariRayaAPI']);
    Route::get('/processDataAPI', [KalenderBaliAPI::class, 'processDataAPI']);

    
    Route::get('/logout', [AutentikasiAPI::class, 'logout']);
});
Route::get('/logRequestAPI', [LogRequestAPI::class, 'lihatLog']);
Route::get('/logRequestAPIByUser', [LogRequestAPI::class, 'lihatLogByUser']);

Route::post('/buatInvoice', [PaymentController::class, 'create']);
Route::post('/buatWebhook', [PaymentController::class, 'webhook']);

Route::get('/daftarServis', [TransactionAPI::class, 'daftarServis']);

Route::get('/cariElemenKalenderBali', [KalenderBaliAPI::class, 'cariElemenKalenderBali']);
Route::get('/cariAlaAyuningDewasa', [AlaAyuningDewasaAPI::class, 'cariAlaAyuningDewasa']);
Route::get('/cariOtonan', [OtonanAPI::class, 'cariOtonan']);
Route::get('/cariHariRaya', [HariRayaAPI::class, 'cariHariRaya']);
Route::get('/cariPiodalan', [PiodalanAPI::class, 'cariPiodalan']);
Route::get('/cariWarigaPersonal', [WarigaPersonalAPI::class, 'cariWarigaPersonal']);

Route::post('/buatAcaraPiodalan', [AcaraAPI::class, 'buatAcaraPiodalan']);
Route::get('/lihatAcaraPiodalan', [AcaraAPI::class, 'lihatAcaraPiodalan']);
Route::get('/lihatAcaraPiodalan/{id}', [AcaraAPI::class, 'lihatAcaraPiodalanById']);
Route::post('/ubahAcaraPiodalan', [AcaraAPI::class, 'ubahAcaraPiodalan']);
Route::post('/hapusAcaraPiodalan', [AcaraAPI::class, 'hapusAcaraPiodalan']);

Route::post('/buatAcaraDetail', [AcaraAPI::class, 'buatAcaraDetail']);
Route::get('/lihatAcaraDetail', [AcaraAPI::class, 'lihatAcaraDetail']);
Route::get('/lihatAcaraDetail/{id}', [AcaraAPI::class, 'lihatAcaraDetailById']);
Route::post('/ubahAcaraDetail', [AcaraAPI::class, 'ubahAcaraDetail']);
Route::post('/hapusAcaraDetail', [AcaraAPI::class, 'hapusAcaraDetail']);

Route::get('/lihatPura', [AcaraAPI::class, 'lihatPura']);
Route::get('/listKeterangan', [KeteranganAPI::class, 'listKeterangan']);
Route::get('/listPengajuanKeterangans', [KeteranganAPI::class, 'listPengajuanKeterangans']);
Route::post('/pengajuanKeterangans', [KeteranganAPI::class, 'pengajuanKeterangans']);

Route::get('/keteranganHariRaya', [KeteranganAPI::class, 'keteranganHariRaya']);
Route::get('/keteranganAlaAyuningDewasa', [KeteranganAPI::class, 'keteranganAlaAyuningDewasa']);

Route::get('/keteranganIngkel', [KeteranganAPI::class, 'keteranganIngkel']);
Route::get('/keteranganJejepan', [KeteranganAPI::class, 'keteranganJejepan']);
Route::get('/keteranganLintang', [KeteranganAPI::class, 'keteranganLintang']);
Route::get('/keteranganNeptu', [KeteranganAPI::class, 'keteranganNeptu']);
Route::get('/keteranganEkaJalaRsi', [KeteranganAPI::class, 'keteranganEkaJalaRsi']);
Route::get('/keteranganWatekMadya', [KeteranganAPI::class, 'keteranganWatekMadya']);
Route::get('/keteranganWatekAlit', [KeteranganAPI::class, 'keteranganWatekAlit']);
Route::get('/keteranganRakam', [KeteranganAPI::class, 'keteranganRakam']);
Route::get('/keteranganPratiti', [KeteranganAPI::class, 'keteranganPratiti']);
Route::get('/keteranganPancaSudha', [KeteranganAPI::class, 'keteranganPancaSudha']);
Route::get('/keteranganPangarasan', [KeteranganAPI::class, 'keteranganPangarasan']);
Route::get('/keteranganZodiak', [KeteranganAPI::class, 'keteranganZodiak']);
Route::get('/keteranganWuku', [KeteranganAPI::class, 'keteranganWuku']);

Route::get('/keteranganEkawara', [KeteranganAPI::class, 'keteranganEkawara']);
Route::get('/keteranganDwiwara', [KeteranganAPI::class, 'keteranganDwiwara']);
Route::get('/keteranganTriwara', [KeteranganAPI::class, 'keteranganTriwara']);
Route::get('/keteranganCaturwara', [KeteranganAPI::class, 'keteranganCaturwara']);
Route::get('/keteranganPancawara', [KeteranganAPI::class, 'keteranganPancawara']);
Route::get('/keteranganSadwara', [KeteranganAPI::class, 'keteranganSadwara']);
Route::get('/keteranganSaptawara', [KeteranganAPI::class, 'keteranganSaptawara']);
Route::get('/keteranganAstawara', [KeteranganAPI::class, 'keteranganAstawara']);
Route::get('/keteranganSangawara', [KeteranganAPI::class, 'keteranganSangawara']);
Route::get('/keteranganDasawara', [KeteranganAPI::class, 'keteranganDasawara']);

Route::post('/editAlaAyuningDewasa', [KeteranganAPI::class, 'editAlaAyuningDewasa']);
Route::post('/editHariRaya', [KeteranganAPI::class, 'editHariRaya']);

Route::post('/editIngkel', [KeteranganAPI::class, 'editIngkel']);
Route::post('/editJejepan', [KeteranganAPI::class, 'editJejepan']);
Route::post('/editLintang', [KeteranganAPI::class, 'editLintang']);
Route::post('/editNeptu', [KeteranganAPI::class, 'editNeptu']);
Route::post('/editEkaJalaRsi', [KeteranganAPI::class, 'editEkaJalaRsi']);
Route::post('/editWatekMadya', [KeteranganAPI::class, 'editWatekMadya']);
Route::post('/editWatekAlit', [KeteranganAPI::class, 'editWatekAlit']);
Route::post('/editRakam', [KeteranganAPI::class, 'editRakam']);
Route::post('/editPratiti', [KeteranganAPI::class, 'editPratiti']);
Route::post('/editPancaSudha', [KeteranganAPI::class, 'editPancaSudha']);
Route::post('/editPangarasan', [KeteranganAPI::class, 'editPangarasan']);
Route::post('/editZodiak', [KeteranganAPI::class, 'editZodiak']);
Route::post('/editWuku', [KeteranganAPI::class, 'editWuku']);

Route::post('/editEkawara', [KeteranganAPI::class, 'editEkawara']);
Route::post('/editDwiwara', [KeteranganAPI::class, 'editDwiwara']);
Route::post('/editTriwara', [KeteranganAPI::class, 'editTriwara']);
Route::post('/editCaturwara', [KeteranganAPI::class, 'editCaturwara']);
Route::post('/editPancawara', [KeteranganAPI::class, 'editPancawara']);
Route::post('/editSadwara', [KeteranganAPI::class, 'editSadwara']);
Route::post('/editSaptawara', [KeteranganAPI::class, 'editSaptawara']);
Route::post('/editAstawara', [KeteranganAPI::class, 'editAstawara']);
Route::post('/editSangawara', [KeteranganAPI::class, 'editSangawara']);
Route::post('/editDasawara', [KeteranganAPI::class, 'editDasawara']);

Route::get('/mengaturDewasa', [MengaturDewasaAPI::class, 'mengaturDewasa']);
Route::post('/mengaturDewasaPOST', [MengaturDewasaAPI::class, 'mengaturDewasaPOST']);
Route::get('/ramalanSifat', [RamalanSifatAPI::class, 'ramalanSifat']);

Route::post('/register', [AutentikasiAPI::class, 'register']);
Route::post('/login', [AutentikasiAPI::class, 'login']);

// route lain
Route::get('/searchHariRayaAPI', [searchHariRayaAPI::class, 'searchHariRayaAPI']);

Route::get('/process_search_hari_raya', [ProgressHasil::class, 'process_search_hari_raya']);
Route::get('/process_search_dewasa_ayu', [ProgressHasil::class, 'process_search_dewasa_ayu']);
Route::get('/process_search_kalender', [ProgressHasil::class, 'process_search_kalender']);
Route::get('/process_search_otonan', [ProgressHasil::class, 'process_search_otonan']);

