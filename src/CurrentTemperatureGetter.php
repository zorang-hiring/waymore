<?php
declare(strict_types=1);

namespace App;

use App\Exception\GetCurrentTemperatureException;

interface CurrentTemperatureGetter
{
    /**
     * @param string $location
     * @return int
     * @throws GetCurrentTemperatureException
     */
    public function getCurrentTemperature(string $location): int;
}