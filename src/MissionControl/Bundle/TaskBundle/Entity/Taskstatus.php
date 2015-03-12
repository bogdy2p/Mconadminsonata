<?php

namespace MissionControl\Bundle\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Taskstatus
 *
 * @ORM\Table(name = "task_status")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Taskstatus {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Expose
     */
    private $name;

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
     * @ORM\OneToMany(targetEntity="Task", mappedBy="taskstatus")
     */
    protected $tasks;

    /**
     * Constructor
     */
    public function __construct() {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Taskstatus
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Taskstatus
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add tasks
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $tasks
     * @return Taskstatus
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
     * @return Taskstatus
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
     * @return Taskstatus
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
