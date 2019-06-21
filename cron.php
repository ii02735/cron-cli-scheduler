<?php
use Cron\Cron;
use Cron\CronExpression;
use Cron\Executor\Executor;
use Cron\Job\ShellJob;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;
use CronScheduler\Entity\CRONTask;

require __DIR__."/config/bootstrap.php";

$resolver = new ArrayResolver();

foreach($em->getRepository(CRONTask::class)->getTasks(true) as $task){

        $job = new ShellJob();
        $job->setCommand($task["command"]);
        $job->setSchedule(new CrontabSchedule($task["period"]));
        $resolver->addJob($job);
        /** @var CronExpression $cronExpression */
        $cronExpression = CronExpression::factory($task["period"]);

        if($cronExpression->isDue()){
            /** @var CRONTask $taskModified */
            $taskModified = $em->getRepository(CRONTask::class)->find($task["name"]);
            $taskModified->setLastexecution(DateTime::createFromFormat("Y-m-d H:i",date("Y-m-d H:i")));
            $taskModified->setNextexecution($cronExpression->getNextRunDate());
            $em->merge($taskModified);
            $em->flush();
        }

}
$cron = new Cron();
$cron->setExecutor(new Executor());
$cron->setResolver($resolver);
$cron->run();