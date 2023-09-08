<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\WukuController;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarAPI extends Controller
{
    public function tes()
    {
        $q = DB::select('CALL searchHariRaya(1, 2023)');
        return response()->json([
            'message' => 'Success',
            'data' => $q,
        ], 200);
    }
}
