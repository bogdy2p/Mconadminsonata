<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Region
 *
 * @ORM\Table(name="region")
 * @ORM\Entity
 * 
 * 
 */
class Region {

    /**
     * Constructor
     */
    public function __construct() {
        $this->countries = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     */
    private $id;

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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Country", mappedBy="region")
     */
    protected $countries;

    /**
     * Set id
     *
     * @param integer $id
     * @return Region
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
     * @return Region
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
     * Add countries
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Country $countries
     * @return Region
     */
    public function addCountry(\MissionControl\Bundle\CampaignBundle\Entity\Country $countries) {
        $this->countries[] = $countries;

        return $this;
    }

    /**
     * Remove countries
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Country $countries
     */
    public function removeCountry(\MissionControl\Bundle\CampaignBundle\Entity\Country $countries) {
        $this->countries->removeElement($countries);
    }

    /**
     * Get countries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCountries() {
        return $this->countries;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Region
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
     * @return Region
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
