<?php

namespace App\Services;

use GreenApi\RestApi\GreenApiClient;

class WhatsAppService
{
    protected $client;

    public function __construct()
    {
        $this->client = new GreenApiClient(
            env('GREEN_API_ID_INSTANCE'),
            env('GREEN_API_TOKEN')
        );
    }

    public function sendMessage($phone, $message)
    {
        return $this->client->sending->sendMessage(
            $phone . '@c.us',
            $message
        );
    }
}