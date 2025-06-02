<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    public function sendNotification($token, $title, $body, $data = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $token, // or use "registration_ids" => [] for multiple tokens
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
        ]);

        return $response->json();
    }
}
