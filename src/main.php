<?php
/**
 * Responsible to execute main app function
 */

declare(strict_types=1);

require_once 'vendor/autoload.php';

// load ENV variables
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// execute app
(require 'configure.php')->execute();