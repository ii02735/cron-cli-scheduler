<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Reference;
require_once __DIR__."/../../config/bootstrap.php";

$container = new ContainerBuilder();
$container->register("entity.manager",EntityManager::class)
          ->setFactory([EntityManager::class,"create"])
          ->addArgument($dbParams)
          ->addArgument($config);

/** @var EntityManager $entityManager */
$entityManager = $container->get("entity.manager");

$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');
$platform->registerDoctrineTypeMapping('bit', 'boolean');
