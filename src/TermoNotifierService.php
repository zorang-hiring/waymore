<?php
declare(strict_types=1);

namespace App;

use App\TermoNotifierRule\NotifierRule;
use App\TermoNotifierRule\TermoNotifierRulesCollection;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Service checks current temperature once and sends appropriate message
 */
class TermoNotifierService
{
    /** @var string E.g.: Phone number */
    protected $messageReceiver;

    /** @var Container */
    protected $container;

    /** @var NotifierRule[] */
    protected $notificationRules;

    protected $location;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param Container $container
     * @param string $location City Name
     * @param string $messageReceiver E.g.: Phone number
     */
    public function __construct(
        Container $container,
        string $location,
        string $messageReceiver,
        LoggerInterface $logger = null
    ){
        $this->container = $container;
        $this->location = $location;
        $this->messageReceiver = $messageReceiver;
        $this->notificationRules = (new TermoNotifierRulesCollection())->getRules();
        $this->logger = !$logger ? new NullLogger() : $logger;
    }

    public function __invoke()
    {
        $currentTemperature = $this->container->currentTemperatureGetter->getCurrentTemperature($this->location);

        $this->logger->debug(sprintf('Temperature is %sC', $currentTemperature));

        foreach ($this->notificationRules as $notifRule) {
            $notifRule->setCurrentTemperature($currentTemperature);
            if ($notifRule->isMatched()) {
                $this->container->messenger->send(
                    $message = $notifRule->getMessage(),
                    $receiver = $this->messageReceiver
                );
                $this->logger->debug(sprintf('Message "%s" sent to "%s"', $message, $receiver));
                break;
            }
        }
    }
}