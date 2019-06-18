<?php

namespace App;

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
    /** @var \PDO $pdo */
    private $pdo;
    protected function configure()
    {
        $this->setDescription("Liste les différentes tâches CRON")
             ->addOption("filtre","f",InputOption::VALUE_REQUIRED,"Trie les commandes selon leur critère");
        $this->pdo = include_once __DIR__."/connection.php";
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pdo = require __DIR__."/connection.php";
        $commands = [];

        $statement = null;


        if($input->getOption("filtre") == "on" || $input->getOption("filtre") == "off")
            $statement = $this->pdo->query("SELECT * FROM scheduler WHERE active='".($input->getOption("filtre") == "on")."'");
        else{
            $statement = $this->pdo->query("SELECT * FROM scheduler");
        }
        foreach($statement->fetchAll(\PDO::FETCH_ASSOC) as $row)
        {
            $attributes = [];
            foreach ($row as $attribute => $value)
            {
                $attributes[$attribute] = $value;
            }
            array_push($commands,$attributes);
        }


        for($i=0;$i<count($commands);$i++)
            $commands[$i]["active"] = ($commands[$i]["active"]==1)?"<success>Activé</>":"<danger>Désactivé</>";


        $io = new SymfonyStyle($input,$output);
        $io->newLine(2);
        $infoBlock = new OutputFormatterStyle("white","blue");
        $output->getFormatter()->setStyle("info",$infoBlock);
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper("formatter");

        $message = "Liste des tâches CRON";
        $message = $formatter->formatBlock($message,'info',true);

        $output->writeln($message);
        $default = new OutputFormatterStyle("white","default");
        $output->getFormatter()->setStyle("info",$default);
        $header = new OutputFormatterStyle("yellow","default");
        $output->getFormatter()->setStyle("header",$header);
        $success = new OutputFormatterStyle("green","default",["bold","blink"]);
        $output->getFormatter()->setStyle("success",$success);
        $danger = new OutputFormatterStyle("red","default",["bold"]);
        $output->getFormatter()->setStyle("danger",$danger);
        $io->newLine(2);
        $io->table(
            ["<header>Nom</>","<header>Commande</>","<header>Date de création</>","<header>Période</>","<header>Dernière exécution</header>","<header>Prochaine exécution</header>","<header>état</>"],
            $commands

        );
    }
}