<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function buy_api() 
    {
        $pakets = Paket::where('hit', '!=', null)->get();
        // dd($pakets);

        return view('buy_api.index', compact('pakets'));
    }

    public function order_form()
    {

        return view('buy_api.form-pesan-api');
    }

    public function order_create()
    {
        return view('buy_api.form-pesan-api');
    }
}
