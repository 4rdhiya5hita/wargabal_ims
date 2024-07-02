<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $userId = $user->id;
        $urlLink = $request->fullUrl();
        $ipAddress = $request->ip();
        $statusResponse = $response->status(); // tipe data integer

        // Menyimpan log ke database
        ApiLog::create([
            'user_id' => $userId,
            'url_link' => $urlLink,
            'ip_address' => $ipAddress,
            'status_response' => $statusResponse,
        ]);

        return $response;
    }
}
