<?php
declare(strict_types=1);

namespace App\TermoNotifierRule;

abstract class AbstractNotifierRule implements NotifierRule
{
    /** @var int */
    protected $currentTemperature;

    public function setCurrentTemperature(int $temperature): void
    {
        $this->currentTemperature = $temperature;
    }
}