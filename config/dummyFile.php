<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\Setup;

$a = 2;
$b = 3;
$c = 4;


$entity = EntityManager::create([
    "driver"=> "pdo_mysql", #database_driver
    "host" => "localhost", #database_server_host
    "dbname" => "ada_fr_db_2", #database_name
    "user" => "ada_fr", #database_username
    "password" => "ada_db_pass", #database_user_password
],Setup::createAnnotationMetadataConfiguration([__DIR__."/../src/Entity"],true,null,null,false));