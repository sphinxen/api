<?php


namespace Api\Endpoint;


use Api\Request;

class Order
{
    public static function averageValue()
    {
        $response = Request::get("orders");

        $orderValues = array_map(fn($order) => $order->total_amount, $response);

        if (empty($orderValues)) {
            throw new \Exception("No orders were found", 404);
        }

        return ["averageOrderValue" => array_sum($orderValues) / count($orderValues)];
    }
}
