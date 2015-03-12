<?php

namespace MissionControl\Bundle\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="project_file")
 * @ORM\Entity(repositoryClass="MissionControl\Bundle\FileBundle\Entity\FileRepository")
 */
class File {

	/**
	 * @ORM\Column(type="string", length=36, unique=true, nullable=false)
	 * @ORM\Id
	 */
	protected $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", inversedBy="files")
     * @ORM\JoinColumn(name="campaign_uuid", referencedColumnName="id")
     */
    protected $campaign;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="files")
     * @ORM\JoinColumn(name="user_creator_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @ORM\Column(type="integer", unique=false, nullable=true)
     */
    protected $version;

    /**
     * @ORM\Column(type="boolean", name="not_visible", nullable=true)
     */
    protected $notVisible;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\TaskBundle\Entity\Task", inversedBy="files")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    protected $task;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Filetype", inversedBy="files")
     * @ORM\JoinColumn(name="file_type_id", referencedColumnName="id", nullable=true)
     */
    protected $fileType;

	/**
	 * @ORM\Column(type="string", name="file_name")
	 */
	protected $fileName;

    /**
     * @ORM\Column(type="string", name="original_name")
     */
    protected $originalName;

	/**
	 * @ORM\Column(type="string", name="content_type")
	 */
	protected $contentType;

    /**
     * @ORM\Column(type="string", name="file_length")
     */
    protected $fileLength;

	/**
	 * @ORM\Column(type="datetime" , name="created_at")
	 */
	protected $createdAt;

	/**
	 * @ORM\Column(type="datetime" , name="updated_at", nullable=false)
	 */
	protected $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @Assert\File
     */
    private $file;

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return File
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string 
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set campaignUuid
     *
     * @param string $campaignUuid
     * @return File
     */
    public function setCampaignUuid($campaignUuid)
    {
        $this->campaignUuid = $campaignUuid;

        return $this;
    }

    /**
     * Get campaignUuid
     *
     * @return string 
     */
    public function getCampaignUuid()
    {
        return $this->campaignUuid;
    }

    /**
     * Set user
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $user
     * @return File
     */
    public function setUser(\MissionControl\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MissionControl\Bundle\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return File
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return File
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     * @return File
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string 
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Set contentType
     *
     * @param string $contentType
     * @return File
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return string 
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set fileLength
     *
     * @param string $fileLength
     * @return File
     */
    public function setFileLength($fileLength)
    {
        $this->fileLength = $fileLength;

        return $this;
    }

    /**
     * Get fileLength
     *
     * @return string 
     */
    public function getFileLength()
    {
        return $this->fileLength;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return File
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
     * @return File
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
     * Set campaign
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaign
     * @return File
     */
    public function setCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaign = null)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Sets file.
     *
     * @param File $file
     */
    public function setFile(\Symfony\Component\HttpFoundation\File\File $file = null) {

        $this->file = $file;

    }

    /**
     * Sets file as UploadedFile type. Used when setting the file from a HTTP request.
     *
     * @param UploadedFile
     */
    public function setCampaignFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file = null) {

        $this->file = $file;
        
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {

        return $this->file;

    }

    /**
     * Set task
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $task
     * @return File
     */
    public function setTask(\MissionControl\Bundle\TaskBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \MissionControl\Bundle\TaskBundle\Entity\Task 
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set fileType
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Filetype $fileType
     * @return File
     */
    public function setFileType(\MissionControl\Bundle\CampaignBundle\Entity\Filetype $fileType = null)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * Get fileType
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Filetype 
     */
    public function getFileType()
    {
        return $this->fileType;
    }
   
    /**
     * Set notVisible
     *
     * @param boolean $notVisible
     * @return File
     */
    public function setNotVisible($notVisible)
    {
        $this->notVisible = $notVisible;

        return $this;
    }

    /**
     * Get notVisible
     *
     * @return boolean 
     */
    public function getNotVisible()
    {
        return $this->notVisible;
    }



    /**
     * Method extracts extension from file name. 
     * 
     */
    public function getFileExtension ($fileName) {

        $lastDot = strrpos ($fileName, '.'); // Return the index position of the last '.'
        $extension = substr($fileName, $lastDot); // Return the substring starting from the last '.' to the end of the master string.

        return $extension;

    } // End of getFileExtension() method.



    #
    # Methods for handling file upload:
    #
    
    // Method returns the absolute path to the file:
    public function getAbsolutePath($targetDir) {

        return null === $this->path ? null : $this->getUploadRootDir($targetDir) . '/' . $this->path;

    }

    public function getWebPath($targetDir) {

        return null === $this->path ? null : $this->getUploadDir($targetDir) . '/' . $this->path;

    }

    protected function getUploadRootDir($targetDir) {

        // The absolute directory path where uploaded documents should be saved:
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir($targetDir);

    }

    protected function getUploadDir($targetDir) {

        // Get rid of the __DIR__ so it won't screw up when displaying uploaded doc/image in the view:
        return 'uploads/' . $targetDir;

    }
    
    // Method will be used for moving the uploaded file into a directory.
    public function upload($targetDir) {

        // move() method takes the target directory and then the target filename to move to:
        $this->getFile()->move(
            $this->getUploadRootDir($targetDir),
            $this->getFileName()
        );

        // Set the path property to the filename where the file was saved.
        $originalName = $this->getFileName();
        $this->path = $this->getUploadDir($targetDir);
        $this->path .= '/' . $originalName;

        // File property can be cleaned as it won't be needed anymore:
        $this->file = null;

    } // End of upload() method.
    
}
