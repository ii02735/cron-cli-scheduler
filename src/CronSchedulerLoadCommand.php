<?php


namespace CronScheduler;


use Cron\CronExpression;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class CronSchedulerLoadCommand extends Command
{
    protected static $defaultName = "cron:scheduler:load";
    private $pdo;
    /** @var EntityManager $pdo */
    //private $em;
    protected function configure()
    {
        $this->setDescription("Charge des tâches CRON");
        $this->addArgument("file",InputArgument::REQUIRED,"Chemin du fichier .yml");
    }

    public function __construct(/*EntityManagerInterface $em*/$name = null)
    {
        $this->pdo = require __DIR__."/connection.php";
        parent::__construct($name);
        //$this->em = $em;
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
            $statement->bindParam(":period",CronExpression::factory($task["schedule"]));
            if(isset($task["disabled"]) && $task["disabled"]){
                $enabled = 0;
                $null = null;
                $statement->bindParam(":active",$enabled);
                $statement->bindParam(":NextExecution",$null);

            }else{
                $enabled = 1;
		        $statement->bindParam(":active",$enabled);
                $statement->bindParam(":NextExecution",CronExpression::factory($task["schedule"])->getNextRunDate()->format("Y-m-d H:i"));

            }
            $output = new SymfonyStyle($input,$output);
            $output->getFormatter()->setStyle("important",new OutputFormatterStyle("yellow","default"));
            try {
                $statement->execute();
                $output->writeln("<info>Tâche <important>$name</> importée avec succès !</info>");

            }
            catch(\Exception $e)
            {
                $output->writeln("<error>Une erreur est survenue durant l'importation de la commande $name</>");
                echo $e->getMessage()."\n";
            }



        }

        $output->newLine();


    }
}
