<?php


namespace CronScheduler;


use Cron\CronExpression;
use CronScheduler\Entity\CRONTask;
use DateTime;
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
    /** @var EntityManager $em */
    private $em;
    protected function configure()
    {
        $this->setDescription("Charge des tâches CRON");
        $this->addArgument("file",InputArgument::REQUIRED,"Chemin du fichier .yml");
    }

    public function __construct(EntityManager $em,$name = null)
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = Yaml::parseFile($input->getArgument("file"));
        $schedulerManager = $this->em->getRepository(CRONTask::class);
        foreach($file as $name => $task)
        {
            $task = new CRONTask();
            $task->setName($name);
            $task->setCommand($task["cmd"]);
            $task->setCreationdate(DateTime::createFromFormat("Y-m-d H:i",date("Y-m-d H:i")));
            $task->setPeriod(CronExpression::factory($task["schedule"]));

            if(isset($task["disabled"]) && $task["disabled"])
                $task->setActive(false);
            else
                $task->setNextexecution(CronExpression::factory($task["schedule"])->getNextRunDate());


            $output = new SymfonyStyle($input,$output);
            $output->getFormatter()->setStyle("important",new OutputFormatterStyle("yellow","default"));
            try {
                $this->em->persist($task);
                $this->em->flush();
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
