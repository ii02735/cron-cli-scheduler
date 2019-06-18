<?php

require_once __DIR__."/../vendor/autoload.php";

$cron = Cron\CronExpression::factory("* * * * *");
echo $cron->getNextRunDate()->format("Y-m-d H:i:s");
date_default_timezone_set("Europe/Paris");
echo date('Y-m-d h:i:s');