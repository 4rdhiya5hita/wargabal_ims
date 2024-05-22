<?php

use App\Http\Controllers\api\AcaraAPI;
use App\Http\Controllers\API\AutentikasiAPI;
use App\Http\Controllers\API\CalendarAPI;
use App\Http\Controllers\api\AlaAyuningDewasaAPI;
use App\Http\Controllers\API\HariAPI;
use App\Http\Controllers\api\HariRayaAPI;
use App\Http\Controllers\API\KalenderBaliAPI;
use App\Http\Controllers\api\KeteranganAPI;
use App\Http\Controllers\api\OtonanAPI;
use App\Http\Controllers\api\PiodalanAPI;
use App\Http\Controllers\api\RerainanAPI;
use App\Http\Controllers\api\searchHariRayaAPI;
use App\Http\Controllers\api\TransactionAPI;
use App\Http\Controllers\API\WewaranAPI;
use App\Http\Controllers\AlaAyuningDewasaController;
use App\Http\Controllers\api\MengaturDewasaAPI;
use App\Http\Controllers\API\RamalanSifatAPI;
use App\Http\Controllers\API\WarigaPersonalAPI;
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
Route::post('/buatInvoice', 'PaymentController@create');
Route::post('/buatWebhook', 'PaymentController@webhook');

Route::get('/daftarServis', 'TransactionAPI@daftarServis');

Route::get('/cariElemenKalenderBali', 'KalenderBaliAPI@cariElemenKalenderBali');
Route::get('/cariAlaAyuningDewasa', 'AlaAyuningDewasaAPI@cariAlaAyuningDewasa');
Route::get('/cariOtonan', 'OtonanAPI@cariOtonan');
Route::get('/cariHariRaya', 'HariRayaAPI@cariHariRaya');
Route::get('/cariPiodalan', 'PiodalanAPI@cariPiodalan');
Route::get('/cariWarigaPersonal', 'WarigaPersonalAPI@cariWarigaPersonal');

Route::post('/buatAcaraPiodalan', 'AcaraAPI@buatAcaraPiodalan');
Route::get('/lihatAcaraPiodalan', 'AcaraAPI@lihatAcaraPiodalan');
Route::get('/lihatAcaraPiodalan/{id}', 'AcaraAPI@lihatAcaraPiodalanById');
Route::post('/ubahAcaraPiodalan', 'AcaraAPI@ubahAcaraPiodalan');
Route::post('/hapusAcaraPiodalan', 'AcaraAPI@hapusAcaraPiodalan');

Route::post('/buatAcaraDetail', 'AcaraAPI@buatAcaraDetail');
Route::get('/lihatAcaraDetail', 'AcaraAPI@lihatAcaraDetail');
Route::get('/lihatAcaraDetail/{id}', 'AcaraAPI@lihatAcaraDetailById');
Route::post('/ubahAcaraDetail', 'AcaraAPI@ubahAcaraDetail');
Route::post('/hapusAcaraDetail', 'AcaraAPI@hapusAcaraDetail');

Route::get('/lihatPura', 'AcaraAPI@lihatPura');
Route::get('/keteranganHariRaya', 'KeteranganAPI@keteranganHariRaya');
Route::get('/keteranganAlaAyuningDewasa', 'KeteranganAPI@keteranganAlaAyuningDewasa');

Route::get('/mengaturDewasa', 'MengaturDewasaAPI@mengaturDewasa');
Route::get('/ramalanSifat', 'RamalanSifatAPI@ramalanSifat');

Route::post('/register', [AutentikasiAPI::class, 'register']);
Route::post('/login', [AutentikasiAPI::class, 'login']);

// route lain
Route::get('/searchHariRayaAPI', [searchHariRayaAPI::class, 'searchHariRayaAPI']);

Route::get('/process_search_hari_raya', [ProgressHasil::class, 'process_search_hari_raya']);
Route::get('/process_search_dewasa_ayu', [ProgressHasil::class, 'process_search_dewasa_ayu']);
Route::get('/process_search_kalender', [ProgressHasil::class, 'process_search_kalender']);
Route::get('/process_search_otonan', [ProgressHasil::class, 'process_search_otonan']);

