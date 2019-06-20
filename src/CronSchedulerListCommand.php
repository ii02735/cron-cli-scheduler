<?php

namespace CronScheduler;

use Cron\CronExpression;
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
    private $pdo;
    /** @var EntityManager $pdo */
    //private $em;
    protected function configure()
    {
        $this->setDescription("Liste les différentes tâches CRON")
             ->addOption("filtre","f",InputOption::VALUE_REQUIRED,"Trie les tâches selon leur état (on|off)")
             ->addOption("del","d",InputOption::VALUE_REQUIRED,"Supprimer une tâche")
             ->addOption("toggle","t",InputOption::VALUE_REQUIRED,"Activer/désaciver une tâche")
             ->addOption("set","s",InputOption::VALUE_REQUIRED,"Modifier la période d'une tâche");
        $this->pdo = include __DIR__."/connection.php";
    }

    public function __construct(/*EntityManagerInterface $em,*/$name = null)
    {
        //$this->em = $em;
        $this->pdo = require __DIR__."/connection.php";
        parent::__construct($name);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input,$output);
        $tasks = [];
        $statement = null;
        $io = $this->addStyle($io,$output,"header","yellow");
        $io = $this->addStyle($io,$output,"success","green","default",["bold"]);
        $io = $this->addStyle($io,$output,"success2","green");
        $io = $this->addStyle($io,$output,"danger","red");
        if(!$input->getOption("filtre")){
            $statement = $this->pdo->query("SELECT * FROM scheduler");
            $statement->execute();
        }else{
            $statement = $this->pdo->prepare("SELECT * FROM scheduler WHERE active=?");
            $statement->execute([$input->getOption("filtre")=="on"]);
        }

        if($array = $statement->fetchAll(\PDO::FETCH_ASSOC)){
            foreach ($array as $row){
                $attributes = [];
                foreach ($row as $key => $value)
                    $attributes[$key] = $value;
                array_push($tasks,$attributes);
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

                throw new \RuntimeException("La tâche ".$input->getOption("del")." n'existe pas");

            delete:
            $statement = $this->pdo->prepare("DELETE FROM scheduler WHERE name=?");
            $statement->execute([$input->getOption("del")]);
            if($this->pdo->errorCode() == "00000")
            {
                $io->newLine();
                $io->writeln("<success2>Suppression de la tâche <header>".$input->getOption("del")."</> avec succès !");
                array_splice($tasks,$found,1);
            }
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

            throw new \RuntimeException("La tâche ".$input->getOption("set")." n'existe pas");

            changePeriod:
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
            $newPeriod = $io->ask("Entrer la nouvelle période de la tâche <header>".$input->getOption("set")."</>",null,function($period){
                return $period;
            });

            $statement = $this->pdo->prepare("UPDATE scheduler SET NextExecution=? WHERE name=?");
            $statement->execute([CronExpression::factory($newPeriod)->getNextRunDate()->format("Y-m-d H:i:s"),$input->getOption("set")]);
            $statement = $this->pdo->prepare("UPDATE scheduler SET period=? WHERE name=?");
            $statement->execute([CronExpression::factory($newPeriod),$input->getOption("set")]);

            if($this->pdo->errorCode() == "00000")
            {
                $io->newLine();
                $io->writeln("<success2>Modification de la tâche <header>".$input->getOption("set")."</> avec succès !");
                $tasks[$i]["period"] = $newPeriod;
                $tasks[$i]["NextExecution"] = CronExpression::factory($newPeriod)->getNextRunDate()->format("Y-m-d H:i:s");
            }
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
            $tasks[$found]["active"] = ($tasks[$found]["active"]==1 ? 0 : 1);
            $tasks[$found]["NextExecution"] = ($tasks[$found]["active"]==1 ? CronExpression::factory($tasks[$found]["period"])->getNextRunDate()->format("Y-m-d H:i:s"): null);
            $statement = $this->pdo->prepare("UPDATE scheduler SET NextExecution=? WHERE name=?");
            $statement->execute([($tasks[$found]["active"]==1?CronExpression::factory($tasks[$found]["period"])->getNextRunDate()->format("Y-m-d H:i:s"):null),$input->getOption("toggle")]);
            $statement = $this->pdo->prepare("UPDATE scheduler SET active=? WHERE name=?");
            $statement->execute([$tasks[$found]["active"],$input->getOption("toggle")]);

            if($this->pdo->errorCode() == "00000")
            {
                $io->newLine();
                $io->writeln("<success2>Tâche <header>".$input->getOption("toggle")."</> ".($tasks[$i]["active"]==1 ?"activée":"désactivée")." avec succès !");
            }

        }

        //Mise en forme des données pour l'affichage du tableau
        for ($i = 0; $i<count($tasks);$i++)
        {
           $tasks[$i]["active"] = ($tasks[$i]["active"]==1)?"<success>Activé</>":"<danger>Désactivé</>";
        }
        $io->title("Liste des tâches CRON");
        $io->table(
            ["<header>Nom</>","<header>Commande</>","<header>Date de création</>","<header>Période</>","<header>Dernière exécution</header>","<header>Prochaine exécution</header>","<header>état</>"],
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