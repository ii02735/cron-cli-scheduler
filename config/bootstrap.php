<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__."/../../../autoload.php";
require_once __DIR__."/config.php";

if(!is_null($entityManagerInstance) && is_file($entityManagerInstance))
{
    /** @var EntityManager $em */
    $em = null;
    $instance = include $entityManagerInstance;
    if($instance instanceof EntityManager)
        $em = $instance;
    else
    {
        $get_vars = get_defined_vars();
        $found = false;
        foreach($get_vars as $var)
        {
            if($found = $var instanceof EntityManager)
            {
                $em = $var;
                break;
            }
        }
        if(!$found)
            throw new Exception("EntityManager instance couldn't be found in ".$entityManagerInstance);
    }

}
else{
    $em = EntityManager::create($doctrine_parameters,Setup::createAnnotationMetadataConfiguration($entities_paths,$is_dev,$proxy_dir,$cache,$useSimpleannotationReader));
}
