<?php

namespace App\Services;

use App\Exceptions\PolygonApiException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class PolygonService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('polygon.base_url'),
        ]);
    }

    public function getQuotes($ticker, $period, $from = "2023-01-01", $to = "2023-12-31")
    {
        $url = config('polygon.base_url') . "/v2/aggs/ticker/$ticker/range/1/$period/$from/$to";

        $response = Http::get($url, [
            'apiKey' => config('polygon.api_key')
        ]);

        if ($response->failed()) {
            throw new PolygonApiException(
                'Failed to fetch data from Polygon',
                $response->status(),
                $response->json()
            );
        }

        return [
            'error' => null,
            'data' => $response->json()['results'] ?? []
        ];
    }

    public function getPreviousQuote($ticker)
    {
        $url = config('polygon.base_url') . "/v2/aggs/ticker/{$ticker}/prev";

        $response = Http::get($url, [
            'apiKey' => config('polygon.api_key')
        ]);

        if ($response->failed()) {
            throw new PolygonApiException(
                'Failed to fetch previous quote from Polygon',
                $response->status(),
                $response->json()
            );
        }

        return [
            'error' => null,
            'data'  => $response->json()['results'] ?? []
        ];
    }
}