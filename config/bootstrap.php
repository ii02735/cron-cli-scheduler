<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__."/../vendor/autoload.php";

$paths = [__DIR__."/../src/Entity"];

$dbParams = [
    "driver" => "pdo_mysql",
    "user" => "ada_fr",
    "password" => "ada_db_pass",
    "host" => "localhost",
    "dbname" => "ada_fr_db_2"
];

$config = Setup::createAnnotationMetadataConfiguration($paths,true,null,null,false);
/** @var EntityManagerInterface $entityManager */
$entityManager = EntityManager::create($dbParams,$config);
$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');
$platform->registerDoctrineTypeMapping('bit', 'boolean');

