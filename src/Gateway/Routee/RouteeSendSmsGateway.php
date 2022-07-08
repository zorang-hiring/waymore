<?php
declare(strict_types=1);

namespace App\Gateway\Routee;

interface RouteeSendSmsGateway
{
    public function send(RouteeAuthTokenType $token, string $message, string $toPhone): void;
}