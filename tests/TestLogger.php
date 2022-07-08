<?php
declare(strict_types=1);

namespace App\Tests;

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    protected $logs = [];

    public function log($level, $message, array $context = array())
    {
        $this->logs[] = [$level, $message];
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}