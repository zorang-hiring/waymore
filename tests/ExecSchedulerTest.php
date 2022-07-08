<?php
declare(strict_types=1);

namespace App\Tests;

use App\Container;
use App\ExecScheduler;
use App\Features\bootstrap\ExecPauserSpy;
use App\Tests\ExecScheduler\ExecSchedulerStrategyImplSpy;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ExecSchedulerTest extends TestCase
{
    /** @var TestLogger */
    protected $logger;
    
    protected function setUp(): void
    {
        parent::setUp();

        $pauser = new ExecPauserSpy();
        $container = Container::getInstance();
        $container->execPauser = $pauser;
        $execStrategy = new ExecSchedulerStrategyImplSpy($container);
        $execStrategy->setLogger($this->logger = new TestLogger());
        $container->execSchedulerStrategy = $execStrategy;
    }

    public function testExecuteLoop10TimesAndPauseAfterEachExceptFirst()
    {
        // ARRANGE
        $sut = new ExecScheduler(
            $container = Container::getInstance(),
            function (){},
            new ExecScheduler\ExecSchedulerLogger(new NullLogger(), $container)
        );

        // ACT
        $sut->execute();

        // ASSERT
        self::assertSame(
            [
                ['debug', 'cycle 1 executed'],
                ['debug', 'cycle 2 paused'],
                ['debug', 'cycle 2 executed'],
                ['debug', 'cycle 3 paused'],
                ['debug', 'cycle 3 executed'],
                ['debug', 'cycle 4 paused'],
                ['debug', 'cycle 4 executed'],
                ['debug', 'cycle 5 paused'],
                ['debug', 'cycle 5 executed'],
                ['debug', 'cycle 6 paused'],
                ['debug', 'cycle 6 executed'],
                ['debug', 'cycle 7 paused'],
                ['debug', 'cycle 7 executed'],
                ['debug', 'cycle 8 paused'],
                ['debug', 'cycle 8 executed'],
                ['debug', 'cycle 9 paused'],
                ['debug', 'cycle 9 executed'],
                ['debug', 'cycle 10 paused'],
                ['debug', 'cycle 10 executed'],
            ],
            $this->logger->getLogs()
        );
    }

    public function testLogExceptionButDontStopLoop()
    {
        // ARRANGE
        $sut = new ExecScheduler(
            $container = Container::getInstance(),
            function (){ throw new \Exception('some error'); },
            new ExecScheduler\ExecSchedulerLogger(new NullLogger(), $container)
        );

        // ACT
        $sut->execute();

        // ASSERT
        self::assertSame(
            [
                ['debug', 'cycle 1 executed'],
                ['debug', 'cycle 2 paused'],
                ['debug', 'cycle 2 executed'],
                ['debug', 'cycle 3 paused'],
                ['debug', 'cycle 3 executed'],
                ['debug', 'cycle 4 paused'],
                ['debug', 'cycle 4 executed'],
                ['debug', 'cycle 5 paused'],
                ['debug', 'cycle 5 executed'],
                ['debug', 'cycle 6 paused'],
                ['debug', 'cycle 6 executed'],
                ['debug', 'cycle 7 paused'],
                ['debug', 'cycle 7 executed'],
                ['debug', 'cycle 8 paused'],
                ['debug', 'cycle 8 executed'],
                ['debug', 'cycle 9 paused'],
                ['debug', 'cycle 9 executed'],
                ['debug', 'cycle 10 paused'],
                ['debug', 'cycle 10 executed'],
            ],
            $this->logger->getLogs()
        );
    }
}
