<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * WIRPoint
 *
 * @ORM\Table(name = "lightdata_whatifresult_point")
 * @ORM\Entity
 */
class WIRPoint {

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
     * @var WhatIfResult
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult", inversedBy="points")
     * @ORM\JoinColumn(name="whatifresult_id", referencedColumnName="id")
     * @Exclude
     */
    protected $whatifresult;

    /**
     * @var integer
     *
     * @ORM\Column(name="stepposition", type="integer")
     */
    private $stepposition;

    /**
     * @var integer
     *
     * @ORM\Column(name="actualpercent", type="integer")
     */
    private $actualpercent;

    /**
     * @var WIRPCurrentMix
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPCurrentMix", mappedBy="point", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $currentmix;

    /**
     * @var WIRPOptimizedMix
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPOptimizedMix", mappedBy="point", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $optimizedmix;

    /**
     * @var WIRPSingleTouchpointMix
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPSingleTouchpointMix", mappedBy="point", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $singletouchpointmix;


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
     * Set stepposition
     *
     * @param integer $stepposition
     * @return WIRPoint
     */
    public function setStepposition($stepposition)
    {
        $this->stepposition = $stepposition;

        return $this;
    }

    /**
     * Get stepposition
     *
     * @return integer 
     */
    public function getStepposition()
    {
        return $this->stepposition;
    }

    /**
     * Set actualpercent
     *
     * @param integer $actualpercent
     * @return WIRPoint
     */
    public function setActualpercent($actualpercent)
    {
        $this->actualpercent = $actualpercent;

        return $this;
    }

    /**
     * Get actualpercent
     *
     * @return integer 
     */
    public function getActualpercent()
    {
        return $this->actualpercent;
    }

    /**
     * Set whatifresult
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult $whatifresult
     * @return WIRPoint
     */
    public function setWhatifresult(\MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult $whatifresult = null)
    {
        $this->whatifresult = $whatifresult;

        return $this;
    }

    /**
     * Get whatifresult
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult 
     */
    public function getWhatifresult()
    {
        return $this->whatifresult;
    }

    /**
     * Set currentmix
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPCurrentMix $currentmix
     * @return WIRPoint
     */
    public function setCurrentmix(\MissionControl\Bundle\LightdataBundle\Entity\WIRPCurrentMix $currentmix = null)
    {
        $this->currentmix = $currentmix;

        return $this;
    }

    /**
     * Get currentmix
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRPCurrentMix 
     */
    public function getCurrentmix()
    {
        return $this->currentmix;
    }

    /**
     * Set optimizedmix
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPOptimizedMix $optimizedmix
     * @return WIRPoint
     */
    public function setOptimizedmix(\MissionControl\Bundle\LightdataBundle\Entity\WIRPOptimizedMix $optimizedmix = null)
    {
        $this->optimizedmix = $optimizedmix;

        return $this;
    }

    /**
     * Get optimizedmix
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRPOptimizedMix 
     */
    public function getOptimizedmix()
    {
        return $this->optimizedmix;
    }

    /**
     * Set singletouchpointmix
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPSingleTouchpointMix $singletouchpointmix
     * @return WIRPoint
     */
    public function setSingletouchpointmix(\MissionControl\Bundle\LightdataBundle\Entity\WIRPSingleTouchpointMix $singletouchpointmix = null)
    {
        $this->singletouchpointmix = $singletouchpointmix;

        return $this;
    }

    /**
     * Get singletouchpointmix
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRPSingleTouchpointMix 
     */
    public function getSingletouchpointmix()
    {
        return $this->singletouchpointmix;
    }
}
