<?php

namespace MissionControl\Bundle\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Taskmessage
 *
 * @ORM\Table(name = "task_message")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Taskmessage {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     * @Expose
     */
    private $message;

    /**
     * @ORM\Column(type="datetime" , name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime" , name="updated_at", nullable=false)
     */
    protected $updatedAt;

    /**
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="taskmessage")
     */
    protected $tasks;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Taskmessage
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tasks
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $tasks
     * @return Taskmessage
     */
    public function addTask(\MissionControl\Bundle\TaskBundle\Entity\Task $tasks) {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $tasks
     */
    public function removeTask(\MissionControl\Bundle\TaskBundle\Entity\Task $tasks) {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks() {
        return $this->tasks;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Taskmessage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Taskmessage
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
