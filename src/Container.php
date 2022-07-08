<?php
declare(strict_types=1);

namespace App;

use App\ExecScheduler\ExecPauser;
use App\ExecScheduler\ExecSchedulerStrategy;

/**
 * App container
 */
class Container
{
    private static $self;

    /** @var ExecSchedulerStrategy */
    public $execSchedulerStrategy;

    /** @var Messenger */
    public $messenger;

    /** @var CurrentTemperatureGetter */
    public $currentTemperatureGetter;

    /** @var ExecPauser */
    public $execPauser;

    private function __construct(){}

    public static function getInstance(): self
    {
        return !self::$self
            ? self::$self = new self()
            : self::$self;
    }
}