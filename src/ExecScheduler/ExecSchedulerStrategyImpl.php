<?php
declare(strict_types=1);

namespace App\ExecScheduler;

use App\Container;

class ExecSchedulerStrategyImpl implements ExecSchedulerStrategy
{
    /** @var Container */
    protected $container;

    protected $executedLoopsCount = 0;

    protected $maxLoops = 10;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function shouldLoop(): bool
    {
        return ($this->executedLoopsCount < $this->maxLoops);
    }

    public function shouldPause(): bool
    {
        return ($this->executedLoopsCount > 0);
    }

    public function pause(): void
    {
        $this->container->execPauser->pause(
            $this->getPauseSec()
        );
    }

    public function incExecutedLoopCount(): void
    {
        $this->executedLoopsCount += 1;
    }

    public function getLoopCount():int
    {
        return $this->executedLoopsCount;
    }

    public function getPauseSec(): int
    {
        return 60*10; // 10 minutes
    }
}