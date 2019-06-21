<?php


namespace CronScheduler;


use Cron\CronExpression;
use Cron\Schedule\CrontabSchedule;
use CronScheduler\Entity\CRONTask;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use http\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class CronSchedulerAddCommand extends Command
{
    protected static $defaultName = "cron:scheduler:add";
    /** @var EntityManager $em */
    private $em;

    /** @var array $yamlFile*/
    private $commandsYaml;

    public function __construct(EntityManager $em,$name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $yamlFile = Yaml::parseFile(__DIR__ . "/commands.yml");
        foreach($yamlFile as $key => $value)
            $this->commandsYaml[$key] = $value;

    }

    protected function configure()
    {
        $this->setDescription("Ajoute une tâche CRON");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input,$output);
        $io->newLine(2);
        $infoBlock = new OutputFormatterStyle("white","blue");
        $output->getFormatter()->setStyle("info",$infoBlock);
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper("formatter");

        $message = "Création d'une tâche CRON";
        $message = $formatter->formatBlock($message,'info',true);

        $output->writeln($message);

        $io->newLine(2);

        $infoBlock = new OutputFormatterStyle("white","default");
        $output->getFormatter()->setStyle("default",$infoBlock);

        $header = new OutputFormatterStyle("yellow","default");
        $output->getFormatter()->setStyle("header",$header);

        $success = new OutputFormatterStyle("green","default");
        $output->getFormatter()->setStyle("success",$success);

        $io->writeln("Commandes disponibles");
        $io->newLine();

        $listing = [];

        foreach ($this->commandsYaml as $key => $array)
            array_push($listing,$key."  -  ".$array["cmd"]);

        $io->listing($listing);

        $name = $io->ask("<default>Nom de la tâche à enregistrer</>",null,function($name){

            if(!key_exists($name,$this->commandsYaml))
                throw new \RuntimeException("Saisie invalide");
            return $name;
        });

        $io->title("Format de planification" );
        $io->text([
            '*    *    *    *    *    *',
            '-    -    -    -    -    -',
            '|    |    |    |    |    |',
            '|    |    |    |    |    + année [facultatif]',
            '|    |    |    |    +----- jour de la semaine (0 - 7) (Dimanche=0 ou 7)',
            '|    |    |    +---------- mois (1 - 12)',
            '|    |    +--------------- jour du mois (1 - 31)',
            '|    +-------------------- heure (0 - 23)',
            '+------------------------- min (0 - 59)',
            '',
            '*/x => Toutes les xème min/heures/jours...'
        ]);
        $period = $io->ask("<default>Période d'exécution</>",$this->commandsYaml[$name]['schedule'],function($command){

            return (string)$command;
        });
        $active = (isset($this->commandsYaml[$name]["disabled"]) && $this->commandsYaml[$name]["disabled"] == "true")?0:1;
        $nextDate = ($active == 1)?CronExpression::factory($period)->getNextRunDate():null;

        /** @var CRONTask $scheduler */
        $scheduler = new CRONTask();
        $scheduler->setName($name);
        $scheduler->setCommand($this->commandsYaml[$name]["cmd"]);
        $scheduler->setCreationdate(DateTime::createFromFormat("Y-m-d H:i",date("Y-m-d H:i")));
        $scheduler->setNextexecution($nextDate);
        $scheduler->setPeriod($period);
        $scheduler->setActive($active);
        $this->em->persist($scheduler);
        $this->em->flush();
        /*$sql = "INSERT INTO scheduler (name,command,CreationDate,period,NextExecution,active) values (?,?,?,?,?,?)";
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$input,$this->commandsYaml[$input]["cmd"],date("Y-m-d H:i"),$freq,($nextDate),$active]);*/
        $io->writeln("<success>Ajout de la tâche <header>".$name ."</> avec succès !</>");


    }


}