<?php
declare(strict_types=1);

namespace App\Tests\ExecScheduler;

use App\ExecScheduler\ExecSchedulerStrategyImpl;
use Psr\Log\LoggerInterface;

class ExecSchedulerStrategyImplSpy extends ExecSchedulerStrategyImpl
{
    /** @var LoggerInterface */
    protected $logger;

    /** @param LoggerInterface $logger */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function pause(): void
    {
        parent::pause();

        $this->logger->debug(sprintf('cycle %s paused', $this->executedLoopsCount + 1));
    }

    public function incExecutedLoopCount(): void
    {
        parent::incExecutedLoopCount();

        $this->logger->debug(sprintf('cycle %s executed', $this->executedLoopsCount));
    }
}