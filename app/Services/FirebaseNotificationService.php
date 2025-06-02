<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $credentialsPath = storage_path('app/firebase/firebase_credentials.json');

         if (!file_exists($credentialsPath)) {
            throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
        }

        if (!is_readable($credentialsPath)) {
            throw new \Exception("Firebase credentials file is not readable at: {$credentialsPath}");
        }

        $factory = (new Factory)->withServiceAccount($credentialsPath);
        $this->messaging = $factory->createMessaging();

    }

    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = [])
    {

        $notification = Notification::create($title, $body);
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->send($message);
    }
}
