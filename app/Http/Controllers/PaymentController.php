<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct() {
        Configuration::setXenditKey('xnd_development_Ovbz2QoE1RVE5n5HWjBDj2I8u2sBhHovqNQySJt7094cXvttd0XtDYsYuj26gXh');
    }

    public function create(Request $request) { 
        // dd($request->all());
        $params = [
            'external_id' => (string) Str::uuid(),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'invoice_duration' => 172800,
            'currency' => 'IDR',
            'reminder_time' => 1,
        ];

        $apiInstance = new InvoiceApi();
        // $createInvoiceRequest = [
        //     'external_id' => 'test1234',
        //     'description' => 'Test Invoice',
        //     'amount' => 10000,
        //     'invoice_duration' => 172800,
        //     'currency' => 'IDR',
        //     'reminder_time' => 1,
        // ];
    
        $createInvoice = $apiInstance->createInvoice($params);    

        // Save to database
        $invoice = new Payment();
        $invoice->checkout_link = $createInvoice['invoice_url'];
        $invoice->external_id = $params['external_id'];
        $invoice->status = 'pending';
        $invoice->save();

        return redirect($createInvoice['invoice_url']);

        // return response()->json([
        //     'status' => 'success',
        //     'description' => 'Invoice has been created',
        //     'data' => $createInvoice
        // ]);
    }

    public function webhook(Request $request) {

        $apiInstance = new InvoiceApi();
        $get_invoice = $apiInstance->getInvoiceById($request->id);

        // Update status to database
        $invoice = Payment::where('external_id', $request->external_id)->first();
        $invoice->status = Str::lower($get_invoice['status']);

        $invoice->save();
        // dd($invoice);

        // return response()->json([
        //     'status' => $get_invoice['status'],
        //     'description' => 'Invoice has been paid',
        //     'data' => $get_invoice
        // ]);
    }
}
