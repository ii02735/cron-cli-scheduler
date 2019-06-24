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
    /** @var XMLReader */
    private $reader;

    protected function configure()
    {
        $this->setDescription($this->reader->out("loadCommand","configure"));
        $this->addArgument("file",InputArgument::REQUIRED,$this->reader->out("loadCommand","argument"));
    }

    public function __construct($reader,EntityManager $em,$name = null)
    {
        $this->em = $em;
        $this->reader = $reader;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
            $file = Yaml::parseFile($input->getArgument("file"));

            foreach($file as $name => $task)
            {
                $taskPersist = new CRONTask();
                $taskPersist->setName($name);
                $taskPersist->setCommand($task["cmd"]);
                $taskPersist->setCreationdate(DateTime::createFromFormat("Y-m-d H:i",date("Y-m-d H:i")));
                $taskPersist->setPeriod(CronExpression::factory($task["schedule"]));

                if(isset($task["disabled"]) && $task["disabled"])
                    $taskPersist->setActive(false);
                else
                    $taskPersist->setNextexecution(CronExpression::factory($task["schedule"])->getNextRunDate());


                $output = new SymfonyStyle($input,$output);
                $output->getFormatter()->setStyle("important",new OutputFormatterStyle("yellow","default"));
                try {
                    $this->em->persist($taskPersist);
                    $this->em->flush();
                    $output->writeln("<info>".$this->reader->out("loadCommand","importTaskSuccess")."</> <important>$name</important>");

                }
                catch(\Exception $e)
                {
                    $output->writeln("<error>".$this->reader->out("loadCommand","importTaskFail")."</>");
                    echo $e->getMessage()."\n";
                    die();
                }



            }

        $output->newLine();


    }
}
