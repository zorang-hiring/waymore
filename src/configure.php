<?php
/**
 * Application configuration (preparation for execution)
 */

declare(strict_types=1);

namespace App;

use App\ExecScheduler\ExecPauserImpl;
use App\ExecScheduler\ExecSchedulerLogger;
use App\ExecScheduler\ExecSchedulerStrategyImpl;
use App\Gateway\OpenWeatherMap\OWMCurrentTemperatureGetterGateway;
use App\Gateway\Routee\RouteeAuthGatewayImpl;
use App\Gateway\Routee\RouteeSendSmsFacade;
use App\Gateway\Routee\RouteeSendSmsGatewayImpl;
use Guzzle\Http\Client as HttpClient;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

return (function () : ExecScheduler {

    // init logger
    $logger = new Logger('main');
    $loggerHandler = new StreamHandler(STDOUT);
    $loggerHandler->setFormatter(new LineFormatter(
        "\n%message% %context%"
    ));
    $logger->setHandlers([$loggerHandler]);

    // init container
    $container = Container::getInstance();
    $container->execSchedulerStrategy = new ExecSchedulerStrategyImpl($container);
    $container->messenger = new RouteeSendSmsFacade(
        new RouteeAuthGatewayImpl(
            new HttpClient(),
            $_ENV['APP_ROUTEE_API_ID'],
            $_ENV['APP_ROUTEE_API_SECRET']
        ),
        new RouteeSendSmsGatewayImpl(new HttpClient())
    );
    $container->currentTemperatureGetter = new OWMCurrentTemperatureGetterGateway(
        new HttpClient(),
        $_ENV['APP_WEATHER_API_KEY']
    );
    $container->execPauser = new ExecPauserImpl();

    // init execution
    $exec = new ExecScheduler(
        $container,
        new TermoNotifierService(
            $container,
            $_ENV['APP_TEMPERATURE_FOR_CITY'],
            $_ENV['APP_PHONE_TO_SEND_MESSAGE'],
            $logger
        ),
        new ExecSchedulerLogger($logger, $container)
    );

    return $exec;
})();