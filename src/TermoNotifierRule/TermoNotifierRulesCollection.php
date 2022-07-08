<?php
declare(strict_types=1);

namespace App\TermoNotifierRule;

class TermoNotifierRulesCollection
{
    public function getRules(): array
    {
        return [
            new TemperatureMoreThen20NotifierRule(),
            new TemperatureLessThen21NotifierRule(),
        ];
    }
}