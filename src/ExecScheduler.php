<?php
declare(strict_types=1);

namespace App;

use App\ExecScheduler\ExecSchedulerLogger;
use App\ExecScheduler\ExecSchedulerStrategy;

/**
 * Generic loop callback executor
 *
 * It will execute callback using specified loop strategy.
 */
class ExecScheduler
{
    /** @var Container */
    protected $container;

    /** @var callable */
    protected $callableCommand;

    /** @var ExecSchedulerLogger */
    protected $logger;

    /**
     * @param Container $container
     * @param callable $execCommand Callback Command to be executed
     * @param ExecSchedulerLogger $logger
     */
    public function __construct(
        Container $container,
        callable $execCommand,
        ExecSchedulerLogger $logger
    ){
        $this->container = $container;
        $this->callableCommand = $execCommand;
        $this->logger = $logger;
    }

    public function execute()
    {
        /** @var ExecSchedulerStrategy $strategy */
        $strategy = $this->container->execSchedulerStrategy;
        while ($strategy->shouldLoop()) {

            if ($strategy->shouldPause()) {
                $this->logger->logPause();
                $strategy->pause();
            }

            $this->logger->logIterationExecuting();
            try {
                ($this->callableCommand)(); // execute callable
                $this->logger->logIterationDone();
            } catch (\Throwable $incident) {
                $this->logger->logIncident($incident);
            }

            $strategy->incExecutedLoopCount();
        }
    }
}