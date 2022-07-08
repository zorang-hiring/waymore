<?php
declare(strict_types=1);

namespace App\Features\bootstrap;

use App\ExecScheduler\ExecSchedulerStrategyImpl;

class ExecSchedulerStrategyFake extends ExecSchedulerStrategyImpl
{
    public function setExecutedCycles(int $number)
    {
        $this->executedLoopsCount = $number;
    }

    public function incExecutedLoopCount(): void
    {
        // in test environment allow only one loop execution
        $this->executedLoopsCount = $this->maxLoops;
    }
}