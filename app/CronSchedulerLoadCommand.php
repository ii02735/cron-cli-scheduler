<?php


namespace App;


use Cron\CronExpression;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class CronSchedulerLoadCommand extends Command
{
    protected static $defaultName = "cron:scheduler:load";
    /** @var \PDO $pdo */
    private $pdo;
    protected function configure()
    {
        $this->setDescription("Charge des tâches CRON");
        $this->addArgument("file",InputArgument::REQUIRED,"Chemin du fichier .yml");
        $this->pdo = require __DIR__."/connection.php";
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $file = Yaml::parseFile($input->getArgument("file"));

        foreach($file as $name => $task)
        {
            $statement = $this->pdo->prepare("INSERT INTO scheduler (name,command,CreationDate,NextExecution,period,active)
                                          VALUES (:name,:command,:CreationDate,:NextExecution,:period,:active)");

            $statement->bindParam(":name",$name);
            $statement->bindParam(":command",$task["cmd"]);
            $statement->bindParam(":CreationDate",date("Y-m-d H:i"));
            $statement->bindParam(":NextExecution",CronExpression::factory($task["schedule"])->getNextRunDate()->format("Y-m-d H:i"));
            $statement->bindParam(":period",CronExpression::factory($task["schedule"]));
            if(isset($task["disabled"]) && $task["disabled"]){
                $enabled = 0;
                $statement->bindParam(":active",$enabled);
            }else{
                $enabled = 1;
                $statement->bindParam(":active",$enabled);
            }
            $statement->execute();


        }


    }
}