<?php

use Cron\Cron;
use Cron\CronExpression;
use Cron\Executor\Executor;
use Cron\Job\ShellJob;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;

require __DIR__."/vendor/autoload.php";

/** @var PDO $pdo */
$pdo = include __DIR__."/app/connection.php";
$resolver = new ArrayResolver();
foreach($pdo->query("SELECT * FROM scheduler") as $task){
    if($task["active"] == 1){
        $job = new ShellJob();
        $job->setCommand($task["command"]);
        $job->setSchedule(new CrontabSchedule($task["frequency"]));
        $resolver->addJob($job);

        /** @var CronExpression $cronExpression */
        $cronExpression = CronExpression::factory($task["frequency"]);

        $pdo->prepare("UPDATE scheduler set scheduler.LastExecution=? WHERE command=?")->execute([date("Y-m-d H:i"),$task["command"]]);
        $pdo->prepare("UPDATE scheduler set scheduler.NextExecution=? WHERE command=?")->execute([$cronExpression->getNextRunDate()->format("Y-m-d H:i"),$task["command"]]);
    }
}

$cron = new Cron();
$cron->setExecutor(new Executor());
$cron->setResolver($resolver);
$cron->run();