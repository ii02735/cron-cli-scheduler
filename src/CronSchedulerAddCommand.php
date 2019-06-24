<?php


namespace CronScheduler;


use Cron\CronExpression;
use CronScheduler\Entity\CRONTask;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class CronSchedulerAddCommand extends Command
{
    protected static $defaultName = "cron:scheduler:add";

    /** @var XMLReader $reader */
    private $reader;
    /** @var EntityManager $em */
    private $em;

    /** @var array $yamlFile*/
    private $commandsYaml;

    public function __construct($XMLReader,EntityManager $em,$basePath,$name = null)
    {

        $this->reader = $XMLReader;
        $this->em = $em;
        $yamlFile = Yaml::parseFile($basePath);
        foreach($yamlFile as $key => $value)
            $this->commandsYaml[$key] = $value;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription($this->reader->out("addCommand","configure"));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input,$output);
        $io->newLine(1);
        $infoBlock = new OutputFormatterStyle("white","blue");
        $output->getFormatter()->setStyle("info",$infoBlock);
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper("formatter");

        $message = $this->reader->out("addCommand","message");
        $message = $formatter->formatBlock($message,'info',true);

        $output->writeln($message);

        $io->newLine(2);

        $infoBlock = new OutputFormatterStyle("white","default");
        $output->getFormatter()->setStyle("default",$infoBlock);

        $header = new OutputFormatterStyle("yellow","default");
        $output->getFormatter()->setStyle("header",$header);

        $success = new OutputFormatterStyle("green","default");
        $output->getFormatter()->setStyle("success",$success);
        $message = $this->reader->out("addCommand","listCommands");
        $io->writeln($message);
        $io->newLine();

        $listing = [];

        foreach ($this->commandsYaml as $key => $array)
            array_push($listing,$key."  -  ".$array["cmd"]);

        $io->listing($listing);

        $name = $io->ask("<default>".$this->reader->out("addCommand","inputCRON")."</>",null,function($name){

            if(!key_exists($name,$this->commandsYaml))
                throw new \RuntimeException($this->reader->out("addCommand","inputException"));
            return $name;
        });

        $io->title($this->reader->out("addCommand","CRONPattern"));
        $io->text($this->reader->out("addCommand","schemaCRON"));
        $period = $io->ask("<default>".$this->reader->out("addCommand","executionPeriod")."</>",$this->commandsYaml[$name]['schedule'],function($command){

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
        $io->writeln("<success>".$this->reader->out("addCommand","successAdd")."</><header> ".$name."</>");


    }


}