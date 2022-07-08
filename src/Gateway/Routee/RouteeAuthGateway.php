<?php
declare(strict_types=1);

namespace App\Gateway\Routee;

interface RouteeAuthGateway
{
    public function auth(): RouteeAuthTokenType;
}