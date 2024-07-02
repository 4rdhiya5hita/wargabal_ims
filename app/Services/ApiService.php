<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Melakukan permintaan GET ke API dengan opsi caching.
     *
     * @param string $url
     * @param array $query
     * @param string|null $cacheKey
     * @param int $cacheDuration
     * @return array
     */
    public function get($url, $query = [], $cacheKey = null, $cacheDuration = 3600)
    {
        if ($cacheKey) {
            return Cache::remember($cacheKey, $cacheDuration, function () use ($url, $query) {
                return $this->makeRequest('GET', $url, $query);
            });
        } else {
            return $this->makeRequest('GET', $url, $query);
        }
    }

    /**
     * Membuat permintaan HTTP menggunakan Guzzle.
     *
     * @param string $method
     * @param string $url
     * @param array $query
     * @return array
     */
    protected function makeRequest($method, $url, $query = [])
    {
        try {
            $response = $this->client->request($method, $url, [
                'query' => $query,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            Log::error('API Request failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

