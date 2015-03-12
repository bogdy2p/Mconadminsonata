<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MissionControl\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
// For returning JSON content:
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Table(name = "lightdata_project")
 *
 * @ORM\Entity(repositoryClass="MissionControl\Bundle\LightdataBundle\Entity\ProjectRepository")
 *
 * @ExclusionPolicy("none");
 */
class Project {

    /**
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id
     *
     * @Groups({"projectInfo"})
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Groups({"projectInfo"})
     */
    private $name;

    /**
     * @ORM\Column(name="xml_file_path", type="string", nullable=true)
     *
     * @Groups({"projectInfo"})
     */
    public $xmlFilePath;

    /**
     * @Assert\File
     */
    private $xmlFile;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Exclude
     */
    protected $user;

    /*
     * Methods for handling the upload of a file:
     */

    public function getAbsolutePath() {

        return null === $this->xmlFilePath ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    /*
     * Can be used in a template to link to the uploaded file?
     */

    public function getWebPath() {

        return null === $this->xmlFilePath ? null : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir() {

        // The absolute directory path where uploaded documents should be saved:
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {

        // Get rid of the __DIR__ so it won't screw up when displaying uploaded doc/image in the view:
        return 'uploads/xml';
    }

    /*
     * Method will be used for moving the uploaded file into a directory.
     */

    public function upload() {

        // Use the original file name here but you should sanitize it at least to avoid any security issues:
        // move() method takes the target directory and then the target filename to move to:
        $this->getXmlFile()->move(
                $this->getUploadRootDir(), $this->getXmlFile()->getClientOriginalName()
        );

        // Set the path property to the filename where the file was saved.
        $originalName = $this->getXMLFile()->getClientOriginalName();
        $this->xmlFilePath = $this->getUploadDir();
        $this->xmlFilePath .= '/' . $originalName;

        // File property can be cleaned as it won't be needed anymore:
        $this->file = null;
    }

// End of upload() method.

    /**
     * Set id
     *
     * @param string $id
     * @return Project
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Project
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
     * Set xmlFilePath
     *
     * @param string $xmlFilePath
     * @return Project
     */
    public function setXmlFilePath($xmlFilePath)
    {
        $this->xmlFilePath = $xmlFilePath;

        return $this;
    }

    /**
     * Get xmlFilePath
     *
     * @return string 
     */
    public function getXmlFilePath()
    {
        return $this->xmlFilePath;
    }

    /**
     * Set user
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $user
     * @return Project
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
}
