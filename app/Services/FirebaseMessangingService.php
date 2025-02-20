<?php

namespace App\Services;

use GuzzleHttp\Client;

class FirebaseMessangingService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendData($accesstoken,$title,$body)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/milanjodi-b3e84/messages:send';
        $headers = [
            'Authorization' => 'Bearer ' . $accesstoken,
            'Content-Type' => 'application/json',
        ];
        $payload = [
            "message" => [
                "topic" => "brownfish",
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                    // "title" => "Hello",
                    // "body" => "Hello, This is a test notification"
                ]
            ]
        ];

        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $payload,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
