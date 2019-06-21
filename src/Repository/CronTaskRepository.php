<?php


namespace CronScheduler\Repository;


use CronScheduler\Entity\Scheduler;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SchedulerRepository extends EntityRepository
{
    /**
     * Get saved tasks
     * @param boolean $active precise if we want
     * specific tasks according their status
     * @return array result
     */
    public function getTasks($active = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $result = null;
        if(is_null($active))
            $result = $qb->select("s")->from(Scheduler::class,"s")->getQuery();
        else
            $result = $qb->select("s")->from(Scheduler::class,"s")
                ->where("s.active = :active")->setParameter(":active",$active)
                ->getQuery();

        return $result->getArrayResult();
    }
}