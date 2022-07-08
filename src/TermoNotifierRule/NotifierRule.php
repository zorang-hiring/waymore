<?php
declare(strict_types=1);

namespace App\TermoNotifierRule;

interface NotifierRule
{
    public function setCurrentTemperature(int $temperature): void;

    public function isMatched(): bool;

    public function getMessage(): string;
}