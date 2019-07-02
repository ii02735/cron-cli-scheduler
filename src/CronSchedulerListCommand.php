<?php

namespace CronScheduler;

use Cron\CronExpression;
use CronScheduler\Entity\CRONTask;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CronSchedulerListCommand extends Command
{
    protected static $defaultName = "cron:scheduler:list";
    /** @var EntityManager $em */
    private $em;
    /** @var XMLReader $reader */
    private $reader;
    protected function configure()
    {
        $this->setDescription($this->reader->out("listCommand","configure"))
             ->addOption("filter","f",InputOption::VALUE_REQUIRED,$this->reader->out("listCommand","option1"))
             ->addOption("del","d",InputOption::VALUE_REQUIRED,$this->reader->out("listCommand","option2"))
             ->addOption("toggle","t",InputOption::VALUE_REQUIRED,$this->reader->out("listCommand","option3"))
             ->addOption("set","s",InputOption::VALUE_REQUIRED,$this->reader->out("listCommand","option4"));
    }

    public function __construct($reader,EntityManager $em,$name = null)
    {
        $this->reader = $reader;
        $this->em = $em;
        parent::__construct($name);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input,$output);
        $schedulerManager = $this->em->getRepository(CRONTask::class);
        $tasks = [];
        $tasks_temp = [];

        $statement = null;
        $io = $this->addStyle($io,$output,"header","yellow");
        $io = $this->addStyle($io,$output,"success","green","default",["bold"]);
        $io = $this->addStyle($io,$output,"success2","green");
        $io = $this->addStyle($io,$output,"danger","red");

        if(!$input->getOption("filter"))
            $tasks_temp = $schedulerManager->getTasks();
        else
            $tasks_temp = $schedulerManager
                ->getTasks($input->getOption("filter")=="on");

        //die(var_dump($tasks_temp));

        for($i = 0; $i<count($tasks_temp);$i++)
        {
            foreach ($tasks_temp[$i] as $key => $value)
            {
                $tasks[$i][$key] = ($value instanceof \DateTime ? $value->format("Y-m-d H:i:s") : $value);
            }
        }

        if($input->getOption("del"))
        {
            $found = -1;
            //On va chercher si la commande existe depuis celles qu'on a récupéré depuis la BDD
            //Soite celles qui sont stockées dans le tableau, évitant de rappeler une nouvelle fois la BDD
            for($i=0;$i<count($tasks);$i++)
            {
                $found = ($tasks[$i]["name"] == $input->getOption("del"))?$i:-1;
                if($found >= 0)
                    goto delete;
            }

                throw new \RuntimeException($this->reader->out("listCommand","existenceException")." ".$input->getOption("del"));

            delete:
            $task = $schedulerManager->find($input->getOption("del"));
            $this->em->remove($task);
            $this->em->flush();
            $io->newLine();
            $io->writeln("<success2>".$this->reader->out("listCommand","deletionSuccess")."</>  <header>".$input->getOption("del")."</> !");
            array_splice($tasks,$found,1);

        }

        if($input->getOption("set"))
        {
            $found = -1;
            //On va chercher si la commande existe depuis celles qu'on a récupéré depuis la BDD
            //Soite celles qui sont stockées dans le tableau, évitant de rappeler une nouvelle fois la BDD
            for($i=0;$i<count($tasks);$i++)
            {
                $found = ($tasks[$i]["name"] == $input->getOption("set"))?$i:-1;
                if($found >= 0)
                    goto changePeriod;
            }

            throw new \RuntimeException($this->reader->out("listCommand","existenceException")." ".$input->getOption("set"));

            changePeriod:
            $io->title($this->reader->out("addCommand","CRONPattern"));
            $io->text($this->reader->out("addCommand","schemaCRON"));
            $newPeriod = $io->ask($this->reader->out("listCommand","setPeriod")." <header>".$input->getOption("set")."</>",null,function($period){
                return $period;
            });

            /** @var CRONTask $task */
            $task = $schedulerManager->find($input->getOption("set"));
            if($task->getActive())
                $task->setNextexecution(CronExpression::factory($newPeriod)->getNextRunDate());
            $task->setPeriod(CronExpression::factory($newPeriod));
            $this->em->merge($task); //Parce que $task a déjà été instancié avant on utilise merge
            $this->em->flush();
            $io->newLine();
            $io->writeln("<success2>".$this->reader->out("listCommand","setPeriodSuccess")."</> <header>".$input->getOption("set")."</>");
            $tasks[$found]["period"] = $newPeriod;
            $tasks[$found]["nextexecution"] = ($tasks[$found]["active"]?CronExpression::factory($newPeriod)->getNextRunDate()->format("Y-m-d H:i:s"):null);

        }

        if($input->getOption("toggle"))
        {
            $found = -1;
            //On va chercher si la commande existe depuis celles qu'on a récupéré depuis la BDD
            //Soite celles qui sont stockées dans le tableau, évitant de rappeler une nouvelle fois la BDD
            for($i=0;$i<count($tasks);$i++)
            {
                $found = ($tasks[$i]["name"] == $input->getOption("toggle"))?$i:-1;
                if($found >= 0)
                    goto toggle;
            }

            toggle:
            $tasks[$found]["active"] = ($tasks[$found]["active"] ? 0 : 1);
            $tasks[$found]["nextexecution"] = ($tasks[$found]["active"] ? CronExpression::factory($tasks[$found]["period"])->getNextRunDate()->format("Y-m-d H:i:s"): null);

            $task = $schedulerManager->find($input->getOption("toggle"));
            $task->setNextexecution(($tasks[$found]["active"]?CronExpression::factory($tasks[$found]["period"])->getNextRunDate():null))
                 ->setActive($tasks[$found]["active"]);
            $this->em->merge($task); //Parce que $task a déjà été instancié avant on utilise merge
            $this->em->flush();
            $io->newLine();
            $io->writeln("<header>".$input->getOption("toggle")."</>"." <success2>".($tasks[$found]["active"]?strtolower($this->reader->out("listCommand","enabled"))." ! </>":strtolower($this->reader->out("listCommand","disabled"))." ! </>"));


        }

        //Mise en forme des données pour l'affichage du tableau
        for ($i = 0; $i<count($tasks);$i++)
        {
           $tasks[$i]["active"] = ($tasks[$i]["active"])?"<success>".$this->reader->out("listCommand","enabled")."</>":"<danger>".$this->reader->out("listCommand","disabled")."</>";
        }
        $io->title($this->reader->out("listCommand","displayJobs"));
        $io->table(
            ["<header>".$this->reader->out("listCommand","name")."</>","<header>".$this->reader->out("listCommand","command")."</>","<header>".$this->reader->out("listCommand","creationDate")."</>",
                "<header>".$this->reader->out("listCommand","period")."</>","<header>".$this->reader->out("listCommand","lastExec")."</header>",
                "<header>".$this->reader->out("listCommand","nextExec")."</header>","<header>".$this->reader->out("listCommand","status")."</>"],
            $tasks

        );



    }

    /**
     * Rajoute un style pour l'écriture
     * sur la console
     * @param SymfonyStyle $io Objet pour customiser la console
     * @param OutputInterface $out Objet pour customiser la sortie sur console
     * @param $tagname string nom du tag / balise
     * @param string $foreground couleur du texte
     * @param string $background couleur arrière-plan
     * @param array $options Options supplémentaires de mise en forme
     * @return SymfonyStyle;
     */
    private function addStyle(SymfonyStyle $io,OutputInterface $out,$tagname,$foreground,$background="default",$options = [])
    {
        $block = new OutputFormatterStyle($foreground,$background,$options);
        $out->getFormatter()->setStyle($tagname,$block);
        return $io;
    }
}
