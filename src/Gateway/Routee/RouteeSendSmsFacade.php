<?php
declare(strict_types=1);

namespace App\Gateway\Routee;

use App\Messenger;

class RouteeSendSmsFacade implements Messenger
{
    /** @var RouteeAuthGateway */
    protected $authAdapter;

    /** @var RouteeSendSmsGateway */
    protected $sendSmsAdapter;

    public function __construct(RouteeAuthGateway $authAdapter, RouteeSendSmsGateway $sendSmsAdapter)
    {
        $this->authAdapter = $authAdapter;
        $this->sendSmsAdapter = $sendSmsAdapter;
    }

    public function send(string $message, $to): void
    {
        $authToken = $this->authAdapter->auth();
        $this->sendSmsAdapter->send($authToken, $message, $to);
    }
}