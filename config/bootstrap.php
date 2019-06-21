<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__."/../vendor/autoload.php";

$paths = [__DIR__."/../src/Entity"];

$dbParams = include __DIR__."/credentials.php";

$config = Setup::createAnnotationMetadataConfiguration($paths,true,null,null,false);
$em = EntityManager::create($dbParams,$config);
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');
$platform->registerDoctrineTypeMapping('bit', 'boolean');


