<?php
declare(strict_types=1);

namespace App\TermoNotifierRule;

class TemperatureMoreThen20NotifierRule extends AbstractNotifierRule
{
    public function isMatched(): bool
    {
        return ($this->currentTemperature > 20);
    }

    public function getMessage(): string
    {
        return sprintf(
            'Zoran, temperature is more than 20C. It is %sC.',
            $this->currentTemperature
        );
    }
}