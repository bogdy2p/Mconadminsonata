<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * BAATAllocationLD
 *
 * @ORM\Table(name = "lightdata_BATOA_allocation")
 * @ORM\Entity
 */
class BATOAllocationLD {

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
     * @var BATotalLD
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BATotalLD", inversedBy="allocation")
     * @ORM\JoinColumn(name="allocatedtouchpoint_id", referencedColumnName="id")
     * 
     * 
     */
    protected $allocatedtouchpoint;

    /**
     * @var BATOAResultLD
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BATOAResultLD", mappedBy="allocation", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $result;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set budget
     *
     * @param float $budget
     * @return BATOAllocationLD
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return float 
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set costpergrp
     *
     * @param float $costpergrp
     * @return BATOAllocationLD
     */
    public function setCostpergrp($costpergrp)
    {
        $this->costpergrp = $costpergrp;

        return $this;
    }

    /**
     * Get costpergrp
     *
     * @return float 
     */
    public function getCostpergrp()
    {
        return $this->costpergrp;
    }

    /**
     * Set grp
     *
     * @param float $grp
     * @return BATOAllocationLD
     */
    public function setGrp($grp)
    {
        $this->grp = $grp;

        return $this;
    }

    /**
     * Get grp
     *
     * @return float 
     */
    public function getGrp()
    {
        return $this->grp;
    }

    /**
     * Set allocatedtouchpoint
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BATotalLD $allocatedtouchpoint
     * @return BATOAllocationLD
     */
    public function setAllocatedtouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\BATotalLD $allocatedtouchpoint = null)
    {
        $this->allocatedtouchpoint = $allocatedtouchpoint;

        return $this;
    }

    /**
     * Get allocatedtouchpoint
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\BATotalLD 
     */
    public function getAllocatedtouchpoint()
    {
        return $this->allocatedtouchpoint;
    }

    /**
     * Set result
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BATOAResultLD $result
     * @return BATOAllocationLD
     */
    public function setResult(\MissionControl\Bundle\LightdataBundle\Entity\BATOAResultLD $result = null)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\BATOAResultLD 
     */
    public function getResult()
    {
        return $this->result;
    }
}
