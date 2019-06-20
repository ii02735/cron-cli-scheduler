<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\EntityManager;
// replace with file to your own project bootstrap
require_once __DIR__.'/../src/service/container.php';

// replace with mechanism to retrieve EntityManager in your app
/** @var EntityManager $container */
$container = $container->get("entity.manager");
return ConsoleRunner::createHelperSet($container);
