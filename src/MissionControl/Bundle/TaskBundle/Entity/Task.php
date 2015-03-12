<?php

namespace MissionControl\Bundle\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MissionControl\Bundle\UserBundle\Entity\User;
use MissionControl\Bundle\CampaignBundle\Entity\Client;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\CampaignBundle\Entity\Filetype;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Task
 *
 * @ORM\Table(name = "task")
 * @ORM\Entity(repositoryClass="MissionControl\Bundle\TaskBundle\Entity\TaskRepository")
 * @ExclusionPolicy("none");
 */
class Task {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     *
     */
    private $owner;

   

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=false)
     *
     */
    private $createdby;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\FileBundle\Entity\File", mappedBy="task")
     */
    private $files;

    /**
     * @var Campaign
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", inversedBy="tasks")
     * @ORM\JoinColumn(name="campaign", referencedColumnName="id",nullable=false)
     *
     */
    private $campaign;

    /**
     * @var Taskname
     *
     * @ORM\ManyToOne(targetEntity="Taskname", inversedBy="task")
     * @ORM\JoinColumn(name="name", referencedColumnName="id")
     */
    private $taskname;

    /**
     * @var Taskstatus
     *
     * @ORM\ManyToOne(targetEntity="Taskstatus", inversedBy="task")
     * @ORM\JoinColumn(name="status", referencedColumnName="id",nullable = false)
     */
    private $taskstatus;

    /**
     * @var User
     *  
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="statuschangedby", referencedColumnName="id")
     */
    private $statuschangedby;

    /**
     * @var Taskmessage
     *
     * @ORM\ManyToOne(targetEntity="Taskmessage", inversedBy="task")
     * @ORM\JoinColumn(name="message", referencedColumnName="id")
     */
    private $taskmessage;

    /**
     * @var string
     *
     * @ORM\Column(name="phase", type="string", length=255)
     */
    private $phase;

    /**
     * @ORM\Column(type="datetime" , name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime" , name="updated_at", nullable=false)
     */
    protected $updatedAt;

    /**
     * @var integer
     * 
     * @ORM\Column(name="matrix_file_version", type="integer", nullable=true)
     */
    private $matrixfileversion;

    /**
     * @var Date
     * 
     * @ORM\Column(name="taskstatus_date", type="datetime", nullable=true)
     */
    private $taskstatusDate;

    /**
     * @var Date
     * 
     * @ORM\Column(name="matrix_version_date", type="datetime", nullable=true)
     */
    private $matrixVersionDate;

    /**
     * @var String
     * 
     * @ORM\Column(name="matrix_version_by", type="string", length=255, nullable=true)
     */
    private $matrixVersionBy;

    /**
     * Constructor
     */
    public function __construct() {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set phase
     *
     * @param string $phase
     * @return Task
     */
    public function setPhase($phase) {
        $this->phase = $phase;

        return $this;
    }

    /**
     * Get phase
     *
     * @return string 
     */
    public function getPhase() {
        return $this->phase;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Task
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Task
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set matrixfileversion
     *
     * @param integer $matrixfileversion
     * @return Task
     */
    public function setMatrixfileversion($matrixfileversion) {
        $this->matrixfileversion = $matrixfileversion;

        return $this;
    }

    /**
     * Get matrixfileversion
     *
     * @return integer 
     */
    public function getMatrixfileversion() {
        return $this->matrixfileversion;
    }

    /**
     * Set taskstatusDate
     *
     * @param \DateTime $taskstatusDate
     * @return Task
     */
    public function setTaskstatusDate($taskstatusDate) {
        $this->taskstatusDate = $taskstatusDate;

        return $this;
    }

    /**
     * Get taskstatusDate
     *
     * @return \DateTime 
     */
    public function getTaskstatusDate() {
        return $this->taskstatusDate;
    }

    /**
     * Set matrixVersionDate
     *
     * @param \DateTime $matrixVersionDate
     * @return Task
     */
    public function setMatrixVersionDate($matrixVersionDate) {
        $this->matrixVersionDate = $matrixVersionDate;

        return $this;
    }

    /**
     * Get matrixVersionDate
     *
     * @return \DateTime 
     */
    public function getMatrixVersionDate() {
        return $this->matrixVersionDate;
    }

    /**
     * Set matrixVersionBy
     *
     * @param string $matrixVersionBy
     * @return Task
     */
    public function setMatrixVersionBy($matrixVersionBy) {
        $this->matrixVersionBy = $matrixVersionBy;

        return $this;
    }

    /**
     * Get matrixVersionBy
     *
     * @return string 
     */
    public function getMatrixVersionBy() {
        return $this->matrixVersionBy;
    }

    /**
     * Set owner
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $owner
     * @return Task
     */
    public function setOwner(\MissionControl\Bundle\UserBundle\Entity\User $owner = null) {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \MissionControl\Bundle\UserBundle\Entity\User 
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * Set createdby
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $createdby
     * @return Task
     */
    public function setCreatedby(\MissionControl\Bundle\UserBundle\Entity\User $createdby) {
        $this->createdby = $createdby;

        return $this;
    }

    /**
     * Get createdby
     *
     * @return \MissionControl\Bundle\UserBundle\Entity\User 
     */
    public function getCreatedby() {
        return $this->createdby;
    }

    /**
     * Add files
     *
     * @param \MissionControl\Bundle\FileBundle\Entity\File $files
     * @return Task
     */
    public function addFile(\MissionControl\Bundle\FileBundle\Entity\File $files) {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \MissionControl\Bundle\FileBundle\Entity\File $files
     */
    public function removeFile(\MissionControl\Bundle\FileBundle\Entity\File $files) {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * Set campaign
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaign
     * @return Task
     */
    public function setCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaign) {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Campaign 
     */
    public function getCampaign() {
        return $this->campaign;
    }

    /**
     * Set taskname
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Taskname $taskname
     * @return Task
     */
    public function setTaskname(\MissionControl\Bundle\TaskBundle\Entity\Taskname $taskname = null) {
        $this->taskname = $taskname;

        return $this;
    }

    /**
     * Get taskname
     *
     * @return \MissionControl\Bundle\TaskBundle\Entity\Taskname 
     */
    public function getTaskname() {
        return $this->taskname;
    }

    /**
     * Set taskstatus
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Taskstatus $taskstatus
     * @return Task
     */
    public function setTaskstatus(\MissionControl\Bundle\TaskBundle\Entity\Taskstatus $taskstatus = null) {
        $this->taskstatus = $taskstatus;

        return $this;
    }

    /**
     * Get taskstatus
     *
     * @return \MissionControl\Bundle\TaskBundle\Entity\Taskstatus 
     */
    public function getTaskstatus() {
        return $this->taskstatus;
    }

    /**
     * Set statuschangedby
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $statuschangedby
     * @return Task
     */
    public function setStatuschangedby(\MissionControl\Bundle\UserBundle\Entity\User $statuschangedby = null) {
        $this->statuschangedby = $statuschangedby;

        return $this;
    }

    /**
     * Get statuschangedby
     *
     * @return \MissionControl\Bundle\UserBundle\Entity\User 
     */
    public function getStatuschangedby() {
        return $this->statuschangedby;
    }

    /**
     * Set taskmessage
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Taskmessage $taskmessage
     * @return Task
     */
    public function setTaskmessage(\MissionControl\Bundle\TaskBundle\Entity\Taskmessage $taskmessage = null) {
        $this->taskmessage = $taskmessage;

        return $this;
    }

    /**
     * Get taskmessage
     *
     * @return \MissionControl\Bundle\TaskBundle\Entity\Taskmessage 
     */
    public function getTaskmessage() {
        return $this->taskmessage;
    }


    /**
     * Add filetypes
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Filetype $filetypes
     * @return Task
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
}
