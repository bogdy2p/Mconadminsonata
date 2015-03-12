<?php

namespace MissionControl\Bundle\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Taskname
 *
 * @ORM\Table(name = "task_name")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Taskname {

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
     * @ORM\ManyToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Filetype", inversedBy="tasknames")
     * @ORM\JoinTable(name="tasknames_filetypes")
     * */
    private $filetypes;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="phaseid", type="integer")
     * @Expose
     */
    private $phaseid;

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
     * @ORM\OneToMany(targetEntity="Task", mappedBy="taskname")
     */
    protected $tasks;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->filetypes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Taskname
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Taskname
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set phaseid
     *
     * @param integer $phaseid
     * @return Taskname
     */
    public function setPhaseid($phaseid)
    {
        $this->phaseid = $phaseid;

        return $this;
    }

    /**
     * Get phaseid
     *
     * @return integer 
     */
    public function getPhaseid()
    {
        return $this->phaseid;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Taskname
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
     * @return Taskname
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

    /**
     * Add filetypes
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Filetype $filetypes
     * @return Taskname
     */
    public function addFiletype(\MissionControl\Bundle\CampaignBundle\Entity\Filetype $filetypes)
    {
        $this->filetypes[] = $filetypes;

        return $this;
    }

    /**
     * Remove filetypes
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Filetype $filetypes
     */
    public function removeFiletype(\MissionControl\Bundle\CampaignBundle\Entity\Filetype $filetypes)
    {
        $this->filetypes->removeElement($filetypes);
    }

    /**
     * Get filetypes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiletypes()
    {
        return $this->filetypes;
    }

    /**
     * Add tasks
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $tasks
     * @return Taskname
     */
    public function addTask(\MissionControl\Bundle\TaskBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $tasks
     */
    public function removeTask(\MissionControl\Bundle\TaskBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
