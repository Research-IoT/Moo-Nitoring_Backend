<?php

namespace App\Helpers;


use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseFCM
{
    public static function withTopic($title = 'Alert Title', $body = 'Alert Body', $topic = null)
    {

        $firebase = (new Factory)
            ->withServiceAccount(__DIR__.'/../../firebase_credentials.json');
 
        $messaging = $firebase->createMessaging();

        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'topic' => $topic,
        ]);

        return $messaging->send($message);
    }
}