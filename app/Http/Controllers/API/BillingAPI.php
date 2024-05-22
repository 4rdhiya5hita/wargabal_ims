<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingAPI extends Controller
{
    public function new_billing_store()
    {
        return view('buy_api.form-pesan-api');
    }
}
