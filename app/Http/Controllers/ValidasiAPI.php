<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TransactionDetail;
use DateTime;
use Illuminate\Http\Request;

class ValidasiAPI extends Controller
{
    public function validasiAPI($user, $service_id)
    {
        $currentDate = (new DateTime())->format('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));

        if (!$user) {
            $response = [
                'pesan' => 'API Key tidak valid',
            ];
            return response()->json($response, 200);
        }
        // dd($user);

        $subscribed_user = TransactionDetail::where('user_id', $user->id)->first();
        // dd($service_id, $subscribed_user);
        if (!$subscribed_user) {
            $response = [
                'pesan' => 'Anda belum berlangganan',
            ];
            return response()->json($response, 200);
        } else {
            $apiStatus = TransactionDetail::where('service_id', $service_id)->where('api_status', 1)->first();
            // dd($apiStatus);
            if (!$apiStatus) {
                $response = [
                    'pesan' => 'Anda belum berlangganan servis ini',
                ];
                return response()->json($response, 200);
            } else {
                if ($apiStatus->end_date < $currentDate) {
                    $response = [
                        'pesan' => 'Masa berlangganan anda telah berakhir',
                    ];
                    return response()->json($response, 200);
                } else {
                    if ($apiStatus->hit_user >= $apiStatus->hit_limit) {
                        $response = [
                            'pesan' => 'Jumlah hit anda telah melebihi batas',
                        ];
                        return response()->json($response, 200);
                    } else {
                        $apiStatus->hit_user = $apiStatus->hit_user + 1;
                        $apiStatus->save();
                    }

                }
            }
        }
    }
}
