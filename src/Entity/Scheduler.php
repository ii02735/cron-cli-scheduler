<?php

namespace CronScheduler\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Scheduler
 *
 * @ORM\Table(name="scheduler")
 * @ORM\Entity
 */
class Scheduler
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     * @ORM\Id
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string", length=255, nullable=true)
     */
    private $command;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CreationDate", type="datetime", nullable=false)
     */
    private $creationdate;

    /**
     * @var string
     *
     * @ORM\Column(name="period", type="string", length=21, nullable=false)
     */
    private $period;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="LastExecution", type="datetime", nullable=true)
     */
    private $lastexecution;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="NextExecution", type="datetime", nullable=true)
     */
    private $nextexecution;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active = '1';

    /**
     * set name
     *
     * @param string $name
     *
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set command
     *
     * @param string $command
     *
     * @return Scheduler
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Scheduler
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Set period
     *
     * @param string $period
     *
     * @return Scheduler
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set lastexecution
     *
     * @param \DateTime $lastexecution
     *
     * @return Scheduler
     */
    public function setLastexecution($lastexecution)
    {
        $this->lastexecution = $lastexecution;

        return $this;
    }

    /**
     * Get lastexecution
     *
     * @return \DateTime
     */
    public function getLastexecution()
    {
        return $this->lastexecution;
    }

    /**
     * Set nextexecution
     *
     * @param \DateTime $nextexecution
     *
     * @return Scheduler
     */
    public function setNextexecution($nextexecution)
    {
        $this->nextexecution = $nextexecution;

        return $this;
    }

    /**
     * Get nextexecution
     *
     * @return \DateTime
     */
    public function getNextexecution()
    {
        return $this->nextexecution;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Scheduler
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
