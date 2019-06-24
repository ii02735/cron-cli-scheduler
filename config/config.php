<?php


$entityManagerInstance =  null; //provide file path where your EntityManager instance is initialized

//if none is provided you must fill the fields below in order to initialize it

// provide doctrine configuration (array of Entities paths, dev env, proxy directory, cache, use simple annotation reader

$entities_paths  = [
    __DIR__."/../src/Entity"
];

$is_dev = false;

$proxy_dir = null;

$cache = null;

$useSimpleannotationReader = false;

//provide configuration for database access

$doctrine_parameters = [
  "driver"=> "pdo_mysql", #database_driver
  "host" => "localhost", #database_server_host
  "dbname" => "ada_fr_db_2", #database_name
  "user" => "ada_fr", #database_username
  "password" => "ada_db_pass", #database_user_password
];

//Provide base path where your yml is located (inside you should have written your commands that you want to add as cron jobs)
//If null provided, "commands.yml" will be loaded by default
$basePath = null;

//Language of the interface (english,french)

$lang = "en";

//File that must be loaded for user's choices (cron:scheduler:add)
$baseFile = __DIR__."/../src/dummy.yml";