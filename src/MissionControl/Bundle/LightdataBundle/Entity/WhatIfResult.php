<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * WhatIfResult
 *
 * @ORM\Table(name = "lightdata_whatifresult")
 * @ORM\Entity
 */
class WhatIfResult {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Exclude
     */
    private $id;

     /**
     * @var WIRConfig
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRConfig", mappedBy="whatifresult", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $config;
    
    /**
     * @var WIRPoint
     * 
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPoint", mappedBy="whatifresult", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $points;

   

    /**
     * @var Lightdata
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\Lightdata", inversedBy="whatifresult")
     * @ORM\JoinColumn(name="lightdata", referencedColumnName="id")
     * 
     * 
     */
    protected $lightdata;

  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->points = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set config
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRConfig $config
     * @return WhatIfResult
     */
    public function setConfig(\MissionControl\Bundle\LightdataBundle\Entity\WIRConfig $config = null)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRConfig 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Add points
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPoint $points
     * @return WhatIfResult
     */
    public function addPoint(\MissionControl\Bundle\LightdataBundle\Entity\WIRPoint $points)
    {
        $this->points[] = $points;

        return $this;
    }

    /**
     * Remove points
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPoint $points
     */
    public function removePoint(\MissionControl\Bundle\LightdataBundle\Entity\WIRPoint $points)
    {
        $this->points->removeElement($points);
    }

    /**
     * Get points
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set lightdata
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata
     * @return WhatIfResult
     */
    public function setLightdata(\MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata = null)
    {
        $this->lightdata = $lightdata;

        return $this;
    }

    /**
     * Get lightdata
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\Lightdata 
     */
    public function getLightdata()
    {
        return $this->lightdata;
    }
}
