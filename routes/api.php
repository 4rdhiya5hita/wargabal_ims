<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CalendarAPI;
use App\Http\Controllers\API\KalenderBaliAPI;
use App\Http\Controllers\DewasaAyuController;
use App\Http\Controllers\OtonanController;
use App\Http\Controllers\ProgressHasil;
use App\Http\Controllers\TasksController;
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

    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::get('/searchHariRayaAPI', [KalenderBaliAPI::class, 'searchHariRayaAPI']);
Route::get('/searchDewasaAyuAPI', [DewasaAyuController::class, 'searchDewasaAyuAPI']);
Route::get('/searchKalenderAPI', [KalenderBaliAPI::class, 'searchHariRayaAPI']);
Route::get('/searchOtonanAPI', [OtonanController::class, 'searchOtonanAPI']);


Route::get('/process_search_hari_raya', [ProgressHasil::class, 'process_search_hari_raya']);
Route::get('/process_search_dewasa_ayu', [ProgressHasil::class, 'process_search_dewasa_ayu']);
Route::get('/process_search_kalender', [ProgressHasil::class, 'process_search_kalender']);
Route::get('/process_search_otonan', [ProgressHasil::class, 'process_search_otonan']);


Route::get('/payment/create', 'App\Http\Controllers\PaymentController@create');
Route::post('/payment/webhook', 'App\Http\Controllers\PaymentController@webhook');