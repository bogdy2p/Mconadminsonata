<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * WIRPSTMTotal
 *
 * @ORM\Table(name= "lightdata_WIRPSTM_total")
 * @ORM\Entity
 */
class WIRPSTMTotal {

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
     * @var WIRPSingleTouchpointMix
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPSingleTouchpointMix", inversedBy="total")
     * @ORM\JoinColumn(name="singletouchpointmix_id", referencedColumnName="id")
     * @Exclude
     */
    protected $singletouchpointmix;

    /**
     * @var string
     *
     * @ORM\Column(name="touchpointname", type="string", length=255)
     */
    private $touchpointname;

    /**
     * @var integer
     *
     * @ORM\Column(name="budget", type="integer")
     */
    private $budget;

    /**
     * @var float
     *
     * @ORM\Column(name="functionvalue", type="float")
     */
    private $functionvalue;


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
     * Set touchpointname
     *
     * @param string $touchpointname
     * @return WIRPSTMTotal
     */
    public function setTouchpointname($touchpointname)
    {
        $this->touchpointname = $touchpointname;

        return $this;
    }

    /**
     * Get touchpointname
     *
     * @return string 
     */
    public function getTouchpointname()
    {
        return $this->touchpointname;
    }

    /**
     * Set budget
     *
     * @param integer $budget
     * @return WIRPSTMTotal
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return integer 
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set functionvalue
     *
     * @param float $functionvalue
     * @return WIRPSTMTotal
     */
    public function setFunctionvalue($functionvalue)
    {
        $this->functionvalue = $functionvalue;

        return $this;
    }

    /**
     * Get functionvalue
     *
     * @return float 
     */
    public function getFunctionvalue()
    {
        return $this->functionvalue;
    }

    /**
     * Set singletouchpointmix
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPSingleTouchpointMix $singletouchpointmix
     * @return WIRPSTMTotal
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
