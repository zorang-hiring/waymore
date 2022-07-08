<?php
namespace App\Features\bootstrap;

use App\Container;
use App\ExecScheduler;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Dotenv\Dotenv;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var ExecScheduler */
    protected $exec;

    /** @var MessengerSpy */
    protected $messengerSpy;

    /** @var CurrentTemperatureGetterSpy */
    protected $currentTemperatureGetterSpy;

    /** @var ExecPauserSpy */
    protected $execPauserSpy;

    /** @var mixed */
    protected $execSchedulerStrategyFake;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $app = require __DIR__ . '/../../src/configure.php';

        $container = Container::getInstance();
        $container->execSchedulerStrategy = $this->execSchedulerStrategyFake = new ExecSchedulerStrategyFake($container);
        $container->messenger = $this->messengerSpy = new MessengerSpy();
        $container->currentTemperatureGetter = $this->currentTemperatureGetterSpy = new CurrentTemperatureGetterSpy();
        $container->execPauser = $this->execPauserSpy = new ExecPauserSpy();

        $this->exec = $app;
    }

    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        // load env variables
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }

    /**
     * @BeforeScenario
     */
    public static function reset(BeforeScenarioScope $scope)
    {
        Container::getInstance()->execPauser->pausedSec = null;
        Container::getInstance()->currentTemperatureGetter->resetLocation();
    }

    /**
     * @Given /^Temperature have been checked "([^"]*)" times$/
     */
    public function temperatureHaveBeenCheckedTimes($int)
    {
        $this->execSchedulerStrategyFake->setExecutedCycles((int) $int);
    }


    /**
     * @Then /^SMS "([^"]*)" have been sent to "([^"]*)"$/
     */
    public function smsHaveBeenSentTo($message, $phone)
    {
        $sentMessages = $this->messengerSpy->getSentMessages();

        assert(
            1 === count($sentMessages),
            sprintf('One message should be sent but %s was.', count($sentMessages))
        );
        assert($sentMessages[0]['message'] === $message,
            sprintf('Message "%s" have not been sent.', $message)
        );
        assert($sentMessages[0]['phone'] === $phone,
            sprintf('Message have not been sent to phone "%s"', $phone)
        );
    }

    /**
     * @Given /^Current temperature is "([^"]*)"$/
     */
    public function currentTemperatureIs($temperature)
    {
        $this->currentTemperatureGetterSpy->setCurrentTemperature((int) $temperature);
    }

    /**
     * @Then /^SMS have not been sent$/
     */
    public function smsHaveNotBeenSent()
    {
        assert(
            [] === $this->messengerSpy->getSentMessages(),
            'Message should not be sent'
        );
    }

    /**
     * @Then /^Execution pausing "([^"]*)" "([^"]*)"$/
     */
    public function executionPausing($executionHasBeenPaused, $expectedPausingSec)
    {
        switch ($executionHasBeenPaused) {
            case 'yes':
                assert(
                    $this->execPauserSpy->pausedSec === (int) $expectedPausingSec,
                    'Execution has not been paused'
                );
                break;
            case 'no':
                assert(
                    $this->execPauserSpy->pausedSec === null,
                    'Execution has been paused'
                );
                break;
        }
    }

    /**
     * @Then /^Temperature has been checked for city "([^"]*)"$/
     */
    public function temperatureHasBeenCheckedForCity($location)
    {
        $actual = $this->currentTemperatureGetterSpy->getCheckedForLocation();
        assert(
            $location === $actual,
            sprintf(
                'Temperature has not been checked for location "%s" but for "%s".',
                $location,
                $actual
            )
        );
    }

    /**
     * @Then /^Temperature has not been checked$/
     */
    public function temperatureHasNotBeenChecked()
    {
        $actual = $this->currentTemperatureGetterSpy->getCheckedForLocation();
        assert(
            null === $actual,
            sprintf(
                'Temperature should not been checked but it is checked for location "%s".',
                $actual
            )
        );
    }

    /**
     * @When /^Execute temperature check process$/
     */
    public function executeTemperatureCheckProcess()
    {
        $this->exec->execute();
    }
}
