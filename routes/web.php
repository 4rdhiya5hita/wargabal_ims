<?php

use App\Http\Controllers\API\CalendarAPI;
use App\Http\Controllers\API\KalenderBaliAPI;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DewasaAyuController;
use App\Http\Controllers\ProgressHasil;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\WukuController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dash', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::resource('tasks', 'TasksController');
Route::get('/task', [TasksController::class, 'index'])->name('task');
Route::get('/get-data', [TasksController::class, 'getData'])->name('task-data');

// Kode Program Salah
Route::get('/searchHariRaya', [CalendarAPI::class, 'searchHariRaya'])->name('searchHariRaya');
Route::get('/searchTanggalHariRaya', [KalenderBaliAPI::class, 'searchTanggalHariRaya']);

// Uji Coba Lokal
Route::get('/searchHariRaya', [KalenderBaliAPI::class, 'searchHariRaya']);
Route::get('/searchHariRayaAPI', [KalenderBaliAPI::class, 'searchHariRayaAPI']);
Route::get('/searchDewasaAyuAPI', [DewasaAyuController::class, 'searchDewasaAyuAPI']);

Route::get('/processData', [KalenderBaliAPI::class, 'processData']);
// Route::get('/progress', [ProgressHasil::class, 'getProgress']);
// Route::get('/hasilProgress', [ProgressHasil::class, 'getHasilProgress']);

Route::group(['middleware' => 'auth'], function() {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/search_hari_raya', 'search_hari_raya')->name('search_hari_raya');
        Route::get('/search_dewasa_ayu', 'search_dewasa_ayu')->name('search_dewasa_ayu');
    });
    Route::controller(ProgressHasil::class)->group(function () {
        Route::get('/hasilProgress', [ProgressHasil::class, 'getHasilProgress']);
        Route::get('/progress', 'getProgress')->name('progress');
        Route::get('/process_search_hari_raya', 'process_search_hari_raya')->name('process_search_hari_raya');
        Route::get('/process_search_dewasa_ayu', 'process_search_dewasa_ayu')->name('process_search_dewasa_ayu');
    });
});

