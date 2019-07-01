<?php
use Cron\CronExpression;
use CronScheduler\Entity\CRONTask;
use GO\Scheduler;

require __DIR__."/config/bootstrap.php";

$scheduler = new Scheduler();

foreach($em->getRepository(CRONTask::class)->getTasks(true) as $task){

        $job = $scheduler->raw($task["command"])->at($task["period"]);
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

$scheduler->run();