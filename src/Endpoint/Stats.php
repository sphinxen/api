<?php


namespace Api\Endpoint;

use GuzzleHttp\Client;

class Stats
{
    private static function client(): Client
    {
       return new Client([
           "base_uri" => "https://api.quickbutik.com/v1/",
            "timeout" => 2.0
        ]);
    }

    public static function averageOrderValue()
    {
        $client = self::client();
        $options = ["auth" => $_ENV["API_KEY"], $_ENV["API_KEY"]];

        $response = $client->request("GET", "orders", $options);

        if (!$response) {
            // Errorhandler
        }

        $body = $response->getBody()->getContents();

        $orderValues = array_map(fn($order) => $order->total_amount, json_decode($body));

        return array_sum($orderValues) / count($orderValues);
    }

    public static function mostPopularProducts()
    {
        $client = self::client();
        $options = ["auth" => $_ENV["API_KEY"], $_ENV["API_KEY"]];

        $response = $client->request("GET", "orders", $options);

        return $response->getBody();
    }
}