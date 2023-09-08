<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function order_form()
    {
        return view('buy_api.form-pesan-api');
    }

    public function order_create()
    {
        $pakets = Paket::all();

        return view('buy_api.form-pesan-api');
    }
}
