<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__."/../vendor/autoload.php";

$paths = [__DIR__."/../src/Entity"];

$dbParams = [
    "driver" => "pdo_mysql",
    "user" => "yadallee",
    "password" => "bilaal",
    "host" => "localhost",
    "dbname" => "test"
];

$config = Setup::createAnnotationMetadataConfiguration($paths,true,null,null,false);


