<?php

require_once __DIR__."/vendor/autoload.php";

use App\CronSchedulerAddCommand;
use App\CronSchedulerListCommand;
use Symfony\Component\Console\Application;


$application = new Application();
$application->addCommands([new CronSchedulerListCommand(),
    new CronSchedulerAddCommand()]);
$application->run();