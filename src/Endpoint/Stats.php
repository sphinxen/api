<?php


namespace Api\Endpoint;

use GuzzleHttp\Client;

class Stats
{
    private static function request(string $method, string $endpoint, array $options = [])
    {
        $options = array_merge(
            $options,
            ["auth" => [$_ENV["API_KEY"], $_ENV["API_KEY"]]]
        );

         $clientData = [
             "base_uri" => "https://api.quickbutik.com/v1/",
             "timeout" => 2.0,
         ];

        $client = new Client($clientData);

        $response = $client->request($method, $endpoint, $options);

        return json_decode($response->getBody()->getContents());
    }

    public static function averageOrderValue()
    {
        $response = self::request("GET", "orders");

        $orderValues = array_map(fn($order) => $order->total_amount, $response);

        if (empty($orderValues)) {
            throw new \Exception("No orders were found", 404);
        }

        return ["averageOrderValue" => array_sum($orderValues) / count($orderValues)];
    }
}
