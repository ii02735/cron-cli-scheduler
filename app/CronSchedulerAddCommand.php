<?php


namespace App;


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
    /** @var \PDO $pdo */
    private $pdo;

    /** @var array $yamlFile*/
    private $commandsYaml;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $yamlFile = Yaml::parseFile(__DIR__."/commandes.yml");
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

        $infoBlock = new OutputFormatterStyle("white","black");
        $output->getFormatter()->setStyle("default",$infoBlock);

        $io->writeln("Commandes disponibles");
        $io->newLine();

        $listing = [];

        foreach ($this->commandsYaml as $key => $array)
            array_push($listing,$key."  -  ".$array["cmd"]);

        $io->listing($listing);

        $input = $io->ask("<default>Nom de la commande à enregistrer</>",null,function($name){

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
            '*/x => Toutes les x min/heures/jours...'
        ]);
        $freq = $io->ask("<default>Planification d'exécution</>",$this->commandsYaml[$input]['schedule'],function($command){

            return (string)$command;
        });
        $this->pdo = require __DIR__."/connection.php";
        $sql = "INSERT INTO scheduler (name,command,CreationDate,frequency) values (?,?,?,?)";
        $statement = $this->pdo->prepare($sql);
        $execution = $statement->execute([$input,$this->commandsYaml[$input]["cmd"],date('Y-m-d H:i'),$freq]);

        if(!$execution) {
            echo "ERROR\n";
            die(print_r($this->pdo->errorCode()));
        }

    }

}