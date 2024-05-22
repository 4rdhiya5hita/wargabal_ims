<?php

use App\Http\Controllers\Api\AcaraAPI;
use App\Http\Controllers\Api\AutentikasiAPI;
use App\Http\Controllers\Api\CalendarAPI;
use App\Http\Controllers\Api\AlaAyuningDewasaAPI;
use App\Http\Controllers\Api\HariAPI;
use App\Http\Controllers\Api\HariRayaAPI;
use App\Http\Controllers\Api\KalenderBaliAPI;
use App\Http\Controllers\Api\KeteranganAPI;
use App\Http\Controllers\Api\OtonanAPI;
use App\Http\Controllers\Api\PiodalanAPI;
use App\Http\Controllers\Api\RerainanAPI;
use App\Http\Controllers\Api\searchHariRayaAPI;
use App\Http\Controllers\Api\TransactionAPI;
use App\Http\Controllers\Api\WewaranAPI;
use App\Http\Controllers\AlaAyuningDewasaController;
use App\Http\Controllers\Api\MengaturDewasaAPI;
use App\Http\Controllers\Api\RamalanSifatAPI;
use App\Http\Controllers\Api\WarigaPersonalAPI;
use App\Http\Controllers\OtonanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgressHasil;
use App\Http\Controllers\TasksController;
use App\Models\HariRaya;
use App\Models\Paket;
use Illuminate\Http\Request;
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
Route::get('/keteranganHariRaya', [KeteranganAPI::class, 'keteranganHariRaya']);
Route::get('/keteranganAlaAyuningDewasa', [KeteranganAPI::class, 'keteranganAlaAyuningDewasa']);

Route::get('/mengaturDewasa', [MengaturDewasaAPI::class, 'mengaturDewasa']);
Route::get('/ramalanSifat', [RamalanSifatAPI::class, 'ramalanSifat']);

Route::post('/register', [AutentikasiAPI::class, 'register']);
Route::post('/login', [AutentikasiAPI::class, 'login']);

// route lain
Route::get('/searchHariRayaAPI', [searchHariRayaAPI::class, 'searchHariRayaAPI']);

Route::get('/process_search_hari_raya', [ProgressHasil::class, 'process_search_hari_raya']);
Route::get('/process_search_dewasa_ayu', [ProgressHasil::class, 'process_search_dewasa_ayu']);
Route::get('/process_search_kalender', [ProgressHasil::class, 'process_search_kalender']);
Route::get('/process_search_otonan', [ProgressHasil::class, 'process_search_otonan']);

