<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Payment;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct() {
        Configuration::setXenditKey('xnd_development_GQlLQNtafKWWyAgEQWl9qFzOr5bgerPpHPaYkN9GhOjANWrCVhHC3doPQfoV4Z');
    }

    public function create(Request $request)
    {
        if ($price = $request->price) {
            
            $params = [
                'external_id' => (string) Str::uuid(),
                'description' => $request->description,
                'amount' => $price,
                'invoice_duration' => 19200,
                'currency' => 'IDR',
                'reminder_time' => 1,
            ];
    
            $apiInstance = new InvoiceApi();
            $createInvoice = $apiInstance->createInvoice($params);
    
            // Save to database        
            $invoice = new Payment();
            $invoice->checkout_link = $createInvoice['invoice_url'];
            $invoice->external_id = $createInvoice['external_id'];
            $invoice->status = Str::lower($createInvoice['status']);
            $invoice->save();
    
            return response()->json([
                'status' => 'success',
                'description' => 'Invoice berhasil dibuat',
                'data' => $createInvoice
            ]);
        }
        else {
            return response()->json([
                'message' => 'Paket tidak ditemukan. Mohon masukkan id paket yang tersedia'
            ], 404);
        }

    }

    public function createOld(Request $request) { 
        // dd($request->all());
        $paket = $request->id_paket;
        $description = Paket::find($paket)->nama_paket;
        $price = Paket::find($paket)->harga;
        // dd($description, $price);

        $params = [
            'external_id' => (string) Str::uuid(),
            'description' => $description,
            'amount' => $price,
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

    public function webhook(Request $request)
    {
        // When click button pay testing
        // Get all data from Xendit
        $apiInstance = new InvoiceApi();
        $requestData = $request->json()->all();
        // dd($requestData);

        // Get invoice from Xendit by invoice ID
        $invoiceId = $requestData['id'];
        $get_invoice = $apiInstance->getInvoiceById($invoiceId);

        // Check if the invoice is expired
        $current_time = date('Y-m-d H:i:s');
        $expired_date = $get_invoice['expiry_date']->format('Y-m-d H:i:s');
        // dd($current_time, $expired_date);

        $invoice = Payment::where('external_id', $get_invoice['external_id'])->first();
        if ($current_time > $expired_date) {
            // Update to database
            $invoice->status = Str::lower($get_invoice['status']);
            $invoice->save();

            return response()->json([
                'status' => $get_invoice['status'],
                'description' => 'Invoice sudah kadaluarsa. Silahkan membuat invoice baru',
                'data' => $get_invoice
            ]);
        }
        $invoice->status = Str::lower($get_invoice['status']);
        $invoice->save();

        return response()->json([
            'status' => $get_invoice['status'],
            'description' => 'Invoice berhasil dibayar',
            'data' => $get_invoice
        ]);
    }
}
