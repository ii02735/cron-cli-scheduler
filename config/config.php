<?php

use Symfony\Component\Dotenv\Dotenv;

$entityManagerInstance = null; #provide file path where entityManager is loaded

//else provide informations to create a new entityManager
//Here is declared a Dotenv instance in order to load informations from a .env file (the path in load() supposes that it is outside
//of the project folder
//In that case, instead of write your credentials in plain text, you load them from a file
$dotenv = new Dotenv();

$dotenv->load(isset($_ENV['CRON_SCHEDULER_ENV_FILE'])
	? getenv('CRON_SCHEDULER_ENV_FILE')
	: getcwd() . DIRECTORY_SEPARATOR . ".cron-scheduler-env")
;

if (!ini_get('date.timezone') && !empty(getenv('CRON_SCHEDULER_DATE_TIMEZONE'))) {
    
    date_default_timezone_set(getenv('CRON_SCHEDULER_DATE_TIMEZONE'));
}

$entities_paths = [
    __DIR__."/../src/Entity" //if you create a entityManager instance
                             //you should provide your own paths beside that one (which is the one where is located the
                             //entity for the scheduler table, you shouldn't touch it
];

$is_dev = false;

$proxy_dir = null;

$cache = null;

$useSimpleannotationReader = false;

//provide configuration for database access

$doctrine_parameters = [
  "driver"=> $_ENV["DB_DRIVER"], #database_driver
  "host" => $_ENV["DB_HOST"], #database_server_host
  "dbname" => $_ENV["DB_NAME"], #database_name
  "user" => $_ENV["DB_USER"], #database_username
  "password" => $_ENV["DB_PASSWORD"], #database_user_password
];


//Language of the interface (english => "en",french => "fr")

$lang = isset($_ENV["CRON_SCHEDULER_LANG"])?$_ENV["CRON_SCHEDULER_LANG"]:"en";

//File that must be loaded for user's choices (cron:scheduler:add)
$baseFile = isset($_ENV["CRON_SCHEDULER_BASE_FILE"]) 
	? getenv('CRON_SCHEDULER_BASE_FILE')
	: getcwd() . DIRECTORY_SEPARATOR . 'cron_scheduler_commands.yml'
;
