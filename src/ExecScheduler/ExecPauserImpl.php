<?php
declare(strict_types=1);

namespace App\ExecScheduler;

class ExecPauserImpl implements ExecPauser
{
    public function pause(int $sec): void
    {
        sleep($sec);
    }
}