<?php


namespace Api;

use GuzzleHttp\Client;

class Request
{
    private $client;
    private static $instance = null;

    private function __construct() {
        $clientData = [
            "base_uri" => "https://api.quickbutik.com/v1/",
            "timeout" => 2.0,
        ];

        $this->client = new Client($clientData);
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get(string $endpoint, array $options = []) {
        return self::getInstance()->request("get", $endpoint, $options);
    }

    private function request(string $method, string $endpoint, array $options)
    {
        $options = array_merge(
            $options,
            ["auth" => [$_ENV["API_KEY"], $_ENV["API_KEY"]]]
        );

        try {
            $response = $this->client->request($method, $endpoint, $options);

            $body = $response->getBody();
            $content = $body->getContents();

            $data = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            return json_decode(["error" => "Invalid external response"]);
        }

        return $data;
    }
}
