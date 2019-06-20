<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;

require_once __DIR__."/../../config/bootstrap.php";

$container = new ContainerBuilder();
$container->register("entity.manager",$entityManager);