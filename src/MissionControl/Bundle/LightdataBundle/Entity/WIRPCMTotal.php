<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * WIRTotal
 *
 * @ORM\Table(name= "lightdata_WIRPCM_total")
 * @ORM\Entity
 */
class WIRPCMTotal {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var WIRPCurrentMix
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPCurrentMix", inversedBy="total")
     * @ORM\JoinColumn(name="whatifresult_id", referencedColumnName="id")
     * @Exclude
     */
    protected $currentmix;

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
     * @return WIRPCMTotal
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
     * @return WIRPCMTotal
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
     * @return WIRPCMTotal
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
     * Set currentmix
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPCurrentMix $currentmix
     * @return WIRPCMTotal
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
}
