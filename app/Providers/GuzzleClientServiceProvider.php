<?php

namespace App\Providers;

use GuzzleHttp\HandlerStack;
use Illuminate\Support\ServiceProvider;

class GuzzleClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $stack = HandlerStack::create();
            $stack->push(GuzzleRetryMiddleware::factory([
                'retry_on_status' => [429, 500, 502, 503, 504],
                'default_retry_multiplier' => 1.5,
                'max_retry_attempts' => 5,
                'retry_delay_function' => function($retries, $response, $request) {
                    if ($response && $response->hasHeader('Retry-After')) {
                        $retryAfter = (int) $response->getHeader('Retry-After')[0];
                        return $retryAfter * 1000;
                    }
                    return (int) (1000 * pow(1.5, $retries));
                }
            ]));
            
            return new Client(['handler' => $stack]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
