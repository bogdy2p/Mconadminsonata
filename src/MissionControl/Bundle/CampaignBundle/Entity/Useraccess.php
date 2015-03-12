<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Useraccess
 *
 * @ORM\Table(name= "control_user_access")
 * @ORM\Entity
 * @ExclusionPolicy("all");
 */
class Useraccess {

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
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)   
     * @Expose  
     */
    private $user;

    /**
     * @var Client
     * 
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Client", inversedBy="clients")
     * @ORM\JoinColumn(name="client", referencedColumnName="id", nullable=false)     
     */
    private $client;

    /**
     * @var Region
     * 
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Region", inversedBy="regions")
     * @ORM\JoinColumn(name="region", referencedColumnName="id", nullable=false)     
     */
    private $region;

    /**
     * @var Country
     * 
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Country", inversedBy="countries")
     * @ORM\JoinColumn(name="country", referencedColumnName="id", nullable=true)     
     */
    private $country;

    /**
     * @var bool
     * 
     * @ORM\Column(name="all_countries_in_region", type="boolean", nullable=false)
     * @Expose
     */
    private $all_countries;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $user
     * @return Useraccess
     */
    public function setUser(\MissionControl\Bundle\UserBundle\Entity\User $user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MissionControl\Bundle\UserBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set client
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Client $client
     * @return Useraccess
     */
    public function setClient(\MissionControl\Bundle\CampaignBundle\Entity\Client $client) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Client 
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Set region
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Region $region
     * @return Useraccess
     */
    public function setRegion(\MissionControl\Bundle\CampaignBundle\Entity\Region $region) {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Region 
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * Set country
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Country $country
     * @return Useraccess
     */
    public function setCountry(\MissionControl\Bundle\CampaignBundle\Entity\Country $country) {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Country 
     */
    public function getCountry() {
        return $this->country;
    }


    /**
     * Set all_countries
     *
     * @param boolean $allCountries
     * @return Useraccess
     */
    public function setAllCountries($allCountries)
    {
        $this->all_countries = $allCountries;

        return $this;
    }

    /**
     * Get all_countries
     *
     * @return boolean 
     */
    public function getAllCountries()
    {
        return $this->all_countries;
    }
}
