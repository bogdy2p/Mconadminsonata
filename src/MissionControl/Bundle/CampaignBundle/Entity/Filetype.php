<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use Doctrine\Common\Collections\ArrayCollection;
use MissionControl\Bundle\TaskBundle\Entity\Task;

/**
 * Filetype
 *
 * @ORM\Table(name="filetype")
 * @ORM\Entity
 */
class Filetype {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\FileBundle\Entity\File", mappedBy="fileType")
     */
    private $files;

    /**
     * @ORM\ManyToMany(targetEntity="MissionControl\Bundle\TaskBundle\Entity\Taskname", mappedBy="filetypes")
     *
     **/
    private $tasknames;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
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
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasknames = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Filetype
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
     * @return Filetype
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Filetype
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
     * @return Filetype
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
     * Add files
     *
     * @param \MissionControl\Bundle\FileBundle\Entity\File $files
     * @return Filetype
     */
    public function addFile(\MissionControl\Bundle\FileBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \MissionControl\Bundle\FileBundle\Entity\File $files
     */
    public function removeFile(\MissionControl\Bundle\FileBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add tasknames
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Taskname $tasknames
     * @return Filetype
     */
    public function addTaskname(\MissionControl\Bundle\TaskBundle\Entity\Taskname $tasknames)
    {
        $this->tasknames[] = $tasknames;

        return $this;
    }

    /**
     * Remove tasknames
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Taskname $tasknames
     */
    public function removeTaskname(\MissionControl\Bundle\TaskBundle\Entity\Taskname $tasknames)
    {
        $this->tasknames->removeElement($tasknames);
    }

    /**
     * Get tasknames
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasknames()
    {
        return $this->tasknames;
    }
}
