<?php


namespace Api\Endpoint;

use Api\Request;

class Product
{
    public static function mostPopular(int $nbrOfResults = 3)
    {
        $response = Request::get("orders", [
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
            return self::details(array_keys($productCount));
        }
        return array_slice($productCount, 0 , $nbrOfResults);
    }

    public static function details(array $product_ids)
    {
        $response = Request::get("orders", [
            "query" => [
                "include_details" => "true",
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
