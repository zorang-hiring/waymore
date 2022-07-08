<?php
declare(strict_types=1);

namespace App\ExecScheduler;

use App\Container;
use Psr\Log\LoggerInterface;

class ExecSchedulerLogger
{
    /** @var LoggerInterface  */
    protected $logger;
    /** @var ExecSchedulerStrategy */
    protected $execInfo;

    public function __construct(LoggerInterface $logger, Container $container)
    {
        $this->logger = $logger;
        if ($container->execSchedulerStrategy instanceof ExecSchedulerStrategy) {
            $this->execInfo = $container->execSchedulerStrategy;
        }
    }

    public function logPause(): void
    {
        $this->logger->debug(sprintf(
            'Pausing %s sec from now - %s ...',
            $this->execInfo->getPauseSec(),
            $this->getNowString()
        ));
    }

    public function logIterationExecuting(): void
    {
        $this->logger->debug(sprintf(
            'Processing No%s started at %s ..',
            $this->execInfo->getLoopCount() + 1,
            $this->getNowString()
        ));
    }

    public function logIterationDone(): void
    {
        $this->logger->debug(sprintf(
            'Processing No%s DONE',
            $this->execInfo->getLoopCount() + 1
        ));
    }

    public function logIncident(\Throwable $throwable): void
    {
        $this->logger->error(
            sprintf(
                'Processing No%s ERROR',
                $this->execInfo->getLoopCount() + 1
            ),
            ['exception' => $throwable]
        );
    }

    private function getNowString(): string
    {
        return (new \DateTime())->format('H:m:s');
    }
}