<?php
declare(strict_types=1);

namespace App\Features\bootstrap;

use App\ExecScheduler\ExecPauser;

class ExecPauserSpy implements ExecPauser
{
    /** @var null|int Null if not paused */
    public $pausedSec = null;

    public function pause(int $sec): void
    {
        $this->pausedSec = $sec;
    }
}