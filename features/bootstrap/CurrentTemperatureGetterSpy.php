<?php
declare(strict_types=1);

namespace App\Features\bootstrap;

use App\CurrentTemperatureGetter;

class CurrentTemperatureGetterSpy implements CurrentTemperatureGetter
{
    /** @var int */
    protected $currentTemperature;

    /** @var string|null */
    protected $location;

    public function getCurrentTemperature(string $location): int
    {
        $this->location = $location;
        return $this->currentTemperature;
    }

    public function setCurrentTemperature(int $temperature): void
    {
        $this->currentTemperature = $temperature;
    }

    public function getCheckedForLocation(): ?string
    {
        return $this->location;
    }

    public function resetLocation(): void
    {
        $this->location = null;
    }
}