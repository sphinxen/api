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


    public static function mostPopularProducts(int $nbrOfResults = 3)
    {
        $response = self::request("GET", "orders", [
            "query" => [
                "include_details" => "true",
            ]
        ]);

        $productCount = [];
        foreach(array_column($response, "products") as $poducts) {
            foreach($poducts as $product) {
                if (isset($productCount[$product->product_id])) {
                    $productCount[$product->product_id]["count"] += (int)$product->qty;
                } else {
                    $productCount[$product->product_id]["count"] = $product->qty;
                    $productCount[$product->product_id]["product"] = $product;

                }
            }
        }

        uasort($productCount, fn($p1, $p2) => $p2["count"] <=> $p1["count"]);

        if ($_GET["include_details"] ?? false) {
            return self::productDetails(array_keys($productCount));
        }
        return array_slice($productCount, 0 , $nbrOfResults);
    }

    private static function productDetails(array $product_ids)
    {
        $response = self::request("GET", "orders", [
            "query" => [
                "include_details" => "true",
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
