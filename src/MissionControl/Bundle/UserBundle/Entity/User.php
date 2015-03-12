<?php

namespace MissionControl\Bundle\UserBundle\Entity;

// For extending FOSUserBundle:
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
// For setting validation constraints:
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;


/**
 * @ORM\Entity
 * @ORM\Table(name="control_user")
 * @ExclusionPolicy("all");
 */
class User extends BaseUser {

    public function __construct() {
        parent::__construct();

        $this->campaigns = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="api_key", nullable=true)
     */
    protected $apiKey;

    
    /**
     * @ORM\Column(type="string", name="honey_id", nullable=true)
     */
    protected $honeyid;

    
    /**
     * @ORM\Column(type="string", name="honey_uuid", nullable=true)
     */
    protected $honeyuuid;

    
    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", mappedBy="user")
     *
     */
    protected $campaigns;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\FileBundle\Entity\File", mappedBy="user")
     */
    protected $files;

    /**
     * @ORM\Column(type="string", name="firstname", nullable=true)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", name="lastname", nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", name="title", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", name="office", nullable=true)
     */
    protected $office;

    /**
     * @ORM\Column(type="string", name="phone", nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", name="profilepicture", nullable=true)
     */
    protected $profilepicture;

    /**
     * @ORM\Column(type="datetime" , name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime" , name="updated_at", nullable=false)
     */
    protected $updatedAt;

    /**
     * Set apiKey
     *
     * @param string $apiKey
     * @return Users
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string 
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * Confirm entered password.
     * @return boolean
     */
    public function getPasswordCheck($password, $confirm) {
        return $password == $confirm;
    }

    public function getCampaigns() {
        return $this->campaigns;
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
     * Add campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     * @return User
     */
    public function addCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns) {
        $this->campaigns[] = $campaigns;

        return $this;
    }

    /**
     * Remove campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     */
    public function removeCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns) {
        $this->campaigns->removeElement($campaigns);
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return User
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set office
     *
     * @param string $office
     * @return User
     */
    public function setOffice($office) {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return string 
     */
    public function getOffice() {
        return $this->office;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set profilepicture
     *
     * @param string $profilepicture
     * @return User
     */
    public function setProfilepicture($profilepicture) {
        $this->profilepicture = $profilepicture;

        return $this;
    }

    /**
     * Get profilepicture
     *
     * @return string 
     */
    public function getProfilepicture() {
        return $this->profilepicture;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return User
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
     * @return User
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
     * @return User
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
     * Set honeyid
     *
     * @param string $honeyid
     * @return User
     */
    public function setHoneyid($honeyid)
    {
        $this->honeyid = $honeyid;

        return $this;
    }

    /**
     * Get honeyid
     *
     * @return string 
     */
    public function getHoneyid()
    {
        return $this->honeyid;
    }

    /**
     * Set honeyuuid
     *
     * @param string $honeyuuid
     * @return User
     */
    public function setHoneyuuid($honeyuuid)
    {
        $this->honeyuuid = $honeyuuid;

        return $this;
    }

    /**
     * Get honeyuuid
     *
     * @return string 
     */
    public function getHoneyuuid()
    {
        return $this->honeyuuid;
    }
}
