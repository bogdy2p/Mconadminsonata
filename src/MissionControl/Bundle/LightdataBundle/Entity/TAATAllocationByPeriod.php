<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * TAATAllocationByPeriod
 *
 * @ORM\Table(name = "lightdata_TAAT_allocationbyperiod")
 * @ORM\Entity
 */
class TAATAllocationByPeriod {

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
     * @var TAAllocatedTouchpointLD
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD", inversedBy="allocationbyperiod")
     * @ORM\JoinColumn(name="allocatedtouchpoint_id", referencedColumnName="id")
     * 
     * 
     */
    protected $allocatedtouchpoint;

    /**
     * @var float
     *
     * @ORM\Column(name="budget", type="float")
     */
    private $budget;

    /**
     * @var float
     *
     * @ORM\Column(name="costpergrp", type="float")
     */
    private $costpergrp;

    /**
     * @var float
     *
     * @ORM\Column(name="grp", type="float")
     */
    private $grp;

    /**
     * @var TAATABPResult
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TAATABPResult", mappedBy="allocationbyperiod", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $result;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set budget
     *
     * @param float $budget
     * @return TAATAllocationByPeriod
     */
    public function setBudget($budget) {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return float 
     */
    public function getBudget() {
        return $this->budget;
    }

    /**
     * Set costpergrp
     *
     * @param float $costpergrp
     * @return TAATAllocationByPeriod
     */
    public function setCostpergrp($costpergrp) {
        $this->costpergrp = $costpergrp;

        return $this;
    }

    /**
     * Get costpergrp
     *
     * @return float 
     */
    public function getCostpergrp() {
        return $this->costpergrp;
    }

    /**
     * Set grp
     *
     * @param float $grp
     * @return TAATAllocationByPeriod
     */
    public function setGrp($grp) {
        $this->grp = $grp;

        return $this;
    }

    /**
     * Get grp
     *
     * @return float 
     */
    public function getGrp() {
        return $this->grp;
    }

    /**
     * Set allocatedtouchpoint
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD $allocatedtouchpoint
     * @return TAATAllocationByPeriod
     */
    public function setAllocatedtouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD $allocatedtouchpoint = null) {
        $this->allocatedtouchpoint = $allocatedtouchpoint;

        return $this;
    }

    /**
     * Get allocatedtouchpoint
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD 
     */
    public function getAllocatedtouchpoint() {
        return $this->allocatedtouchpoint;
    }

    /**
     * Set result
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TAATABPResult $result
     * @return TAATAllocationByPeriod
     */
    public function setResult(\MissionControl\Bundle\LightdataBundle\Entity\TAATABPResult $result = null) {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TAATABPResult 
     */
    public function getResult() {
        return $this->result;
    }

}
