<?php


namespace Api\Endpoint;

use Api\Request;

class Product
{
    /**
     * Get the most puchased products based on data from previous orders
     *
     * @param int $nbrOfResults
     * @return mixed
     */
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
                }
            }
        }

        uasort($productCount, fn($p1, $p2) => $p2["count"] <=> $p1["count"]);

        return self::fetchData(array_keys(array_slice($productCount, 0, $nbrOfResults, true)));
    }

    /**
     * Fetches product information
     *
     * @param array $product_ids
     * @return mixed
     */
    public static function fetchData(array $product_ids)
    {
        return Request::get("products", [
            "query" => [
                "product_id" => implode(",", $product_ids),
            ]
        ]);
    }
}
