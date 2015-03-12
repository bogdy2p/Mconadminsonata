<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * WIRPOMTotal
 *
 * @ORM\Table(name= "lightdata_WIRPOM_total")
 * @ORM\Entity
 */
class WIRPOMTotal {

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
     * @var WIRPOptimizedMix
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPOptimizedMix", inversedBy="total")
     * @ORM\JoinColumn(name="whatifresult_id", referencedColumnName="id")
     * @Exclude
     */
    protected $optimizedmix;

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
     * @return WIRPOMTotal
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
     * @return WIRPOMTotal
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
     * @return WIRPOMTotal
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
     * Set optimizedmix
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPOptimizedMix $optimizedmix
     * @return WIRPOMTotal
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
}
