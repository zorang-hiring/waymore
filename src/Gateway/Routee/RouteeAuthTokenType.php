<?php
declare(strict_types=1);

namespace App\Gateway\Routee;

class RouteeAuthTokenType
{
    /** @var string */
    public $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function __toString()
    {
        return $this->token;
    }
}