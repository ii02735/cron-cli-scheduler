<?php

use Cron\Cron;
use Cron\CronExpression;
use Cron\Executor\Executor;
use Cron\Job\ShellJob;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;

require __DIR__."/vendor/autoload.php";

/** @var PDO $pdo */
$pdo = include __DIR__ . "/src/connection.php";
$resolver = new ArrayResolver();
foreach($pdo->query("SELECT * FROM scheduler") as $task){
    if($task["active"] == 1){
        $job = new ShellJob();
        $job->setCommand($task["command"]);
        $job->setSchedule(new CrontabSchedule($task["period"]));
        $resolver->addJob($job);

        /** @var CronExpression $cronExpression */
        $cronExpression = CronExpression::factory($task["period"]);

        if($cronExpression->isDue()){
        $pdo->prepare("UPDATE scheduler set scheduler.LastExecution=? WHERE name=?")->execute([date("Y-m-d H:i"),$task["name"]]);
        $pdo->prepare("UPDATE scheduler set scheduler.NextExecution=? WHERE name=?")->execute([$cronExpression->getNextRunDate()->format("Y-m-d H:i"),$task["name"]]);
        }
    }
}

$cron = new Cron();
$cron->setExecutor(new Executor());
$cron->setResolver($resolver);
$cron->run();