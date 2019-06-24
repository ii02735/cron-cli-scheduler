<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\EntityManager;
use CronScheduler\XMLReader;
// replace with mechanism to retrieve EntityManager in your app
require __DIR__."/bootstrap.php";


return ConsoleRunner::createHelperSet($em);
