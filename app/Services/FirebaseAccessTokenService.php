<?php

namespace App\Services;

use Google_Client;

class FirebaseAccessTokenService{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/firebase-service-account.json'));
        $this->client->addScope(["https://www.googleapis.com/auth/firebase.messaging"]);
    }

    public function getAccessToken()
    {
        $this->client->fetchAccessTokenWithAssertion();
        return $this->client->getAccessToken()['access_token'];
    }
}