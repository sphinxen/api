<?php


namespace Api\Endpoint;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class Stats
{
    private static function client(?string $mock): Client
    {
         $clientData = [
             "base_uri" => "https://api.quickbutik.com/v1/",
             "timeout" => 2.0,
         ];

         if (!is_null($mock)) {
//             $mock = new MockHandler([
//                 new Response(200, ['X-Foo' => 'Bar'], self::mock($mock)),
//                 new Response(202, ['Content-Length' => 0]),
//                 new RequestException('Error Communicating with Server', new Request('GET', 'invalid'))
//             ]);

//             $handlerStack = HandlerStack::create($mock);

             $clientData["handler"] = function(Request $request) {
                 $path = $request->getUri()->getPath();

                 if($path == "/v1/orders") {
                     return new Response(200, ['X-Foo' => 'Bar'], self::mock("orders"));
                 }

                 if (preg_match("%^/v1/metadata/order/(\d+)%", $path, $matches)) {
                     return new Response(200, ['X-Foo' => 'Bar'], self::mock("meta", $matches));
                 }
//                 $mock = new MockHandler([
//                     new Response(200, ['X-Foo' => 'Bar'], $response200 ),
//                     new Response(202, ['Content-Length' => 0]),
//                     new RequestException('Error Communicating with Server', new Request('GET', 'invalid'))
//                 ]);

//                 return HandlerStack::create($mock);
             };
         }

        return new Client($clientData);
    }

    public static function averageOrderValue()
    {
        $client = self::client("orders");
        $options = ["auth" => $_ENV["API_KEY"], $_ENV["API_KEY"]];

        $response = $client->request("GET", "orders", $options);

        if (!$response) {
            // Errorhandler
        }

        $bodyResponse = json_decode($response->getBody()->getContents());

        $orderValues = array_map(fn($order) => $order->total_amount, $bodyResponse->orders);

        return ["averageOrderValue" => array_sum($orderValues) / count($orderValues)];
    }

    public static function mostPopularProducts()
    {
        $client = self::client("orders");
        $options = ["auth" => $_ENV["API_KEY"], $_ENV["API_KEY"]];

        $response = $client->request("GET", "orders", $options);

        $bodyResponse = json_decode($response->getBody()->getContents());

        $products = [];
        foreach($bodyResponse->orders as &$order) {
            $client = self::client("metadata/order");
            $metaResponse = $client->request("GET", "metadata/order/{$order->order_id}", $options);

            $order->products = json_decode($metaResponse->getBody()->getContents());

            foreach($order->products as $product) {
                $products[$product->product_id] = ($products[$product->product_id] ?? 0) + $product->qty;
            }
        }

        uasort($products, fn($p1, $p2) => $p2 <=> $p1);

        return ["products" => array_slice($products, 0,3, true)];
    }

     private static function mock($endpoint, $meta = null)
     {
         $mockData = '{
              "orders": [
                {
                  "order_id": "45678",
                  "date_created": "2022-03-25 16:30:55",
                  "products": [
                    {
                      "product_id": 34,
                      "title": "T-shirt",
                      "price": 199.00,
                      "qty": 2,
                      "variant": null,
                      "sku": "34"
                    }
                  ],
                  "total_amount": "398.00",
                  "status": "3"
                },
                {
                  "order_id": "11223",
                  "date_created": "2022-09-01 13:55:29",
                  "products": [
                    {
                      "product_id": 20,
                      "title": "Black Shoes",
                      "price": 59.99,
                      "qty": 1,
                      "variant": "Classic",
                      "sku": "SHOES-123"
                    },
                    {
                      "product_id": 10,
                      "title": "Blue Shirt",
                      "price": 29.99,
                      "qty": 3,
                      "variant": null,
                      "sku": "SHIRT-789"
                    }
                  ],
                  "total_amount": "149.96",
                  "status": "2"
                },
                {
                  "order_id": "24680",
                  "date_created": "2021-08-05 18:10:22",
                  "products": [
                    {
                      "product_id": 45,
                      "title": "Jeans",
                      "price": 89.99,
                      "qty": 1,
                      "variant": "Slim-fit",
                      "sku": "JEANS-456"
                    },
                    {
                      "product_id": 30,
                      "title": "Sneakers",
                      "price": 149.99,
                      "qty": 2,
                      "variant": "Red",
                      "sku": "SNEAKERS-987"
                    }
                  ],
                  "total_amount": "329.97",
                  "status": "1"
                },
                {
                  "order_id": "97531",
                  "date_created": "2022-01-12 10:05:47",
                  "products": [
                    {
                      "product_id": 20,
                      "title": "Black Shoes",
                      "price": 59.99,
                      "qty": 2,
                      "variant": null,
                      "sku": "SHOES-123"
                    },
                    {
                      "product_id": 30,
                      "title": "Silver Necklace",
                      "price": 89.99,
                      "qty": 1,
                      "variant": null,
                      "sku": "NECKLACE-456"
                    },
                    {
                      "product_id": 40,
                      "title": "Leather Belt",
                      "price": 39.99,
                      "qty": 1,
                      "variant": "Brown",
                      "sku": "BELT-789"
                    }
                  ],
                  "total_amount": "289.96",
                  "status": "2"
                },
                {
                  "order_id": "78901",
                  "date_created": "2022-06-14 09:20:11",
                  "products": [
                    {
                      "product_id": 10,
                      "title": "Blue Shirt",
                      "price": 29.99,
                      "qty": 1,
                      "variant": "V-neck",
                      "sku": "SHIRT-789"
                    }
                  ],
                  "total_amount": "29.99",
                  "status": "1"
                }
              ]
            }';

         switch ($endpoint) {

             case "orders":
                 $orders = json_decode($mockData);
                 foreach($orders->orders as &$order) {
                     unset($order->products);
                 }
                 return json_encode($orders);
             case "meta":
                $orders = json_decode($mockData, true);

                $orders = array_filter($orders["orders"], fn($order) => $order["order_id"] == $meta[1]);
                return json_encode(...array_column($orders,"products"));
             default:
                 return "{}";
         }
     }
}