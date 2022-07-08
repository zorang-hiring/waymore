<?php
declare(strict_types=1);

namespace App\ExecScheduler;

interface ExecPauser
{
    public function pause(int $sec): void;
}