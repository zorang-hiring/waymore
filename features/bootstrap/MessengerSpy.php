<?php
declare(strict_types=1);

namespace App\Features\bootstrap;

use App\Messenger;

class MessengerSpy implements Messenger
{
    /** @var array */
    protected $sentMessages = [];

    public function send($message, $to): void
    {
        $this->sentMessages[] = [
            'message' => $message,
            'phone' => $to
        ];
    }

    public function getSentMessages():array
    {
        return $this->sentMessages;
    }
}