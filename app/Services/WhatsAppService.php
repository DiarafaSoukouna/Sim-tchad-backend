<?php

namespace App\Services;

use GreenApi\RestApi\GreenApiClient;

class WhatsAppService
{
    protected $client;

    public function __construct()
    {
        $this->client = new GreenApiClient(
            env('services.green_api.id_instance'),
            env('services.green_api.api_token')
        );
    }

    public function sendMessage($phone, $message)
    {
        return $this->client->sending->sendMessage(
            $phone,
            $message
        );
    }
}