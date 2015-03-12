<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * TATOABPResult
 *
 * @ORM\Table(name = "lightdata_TATOABP_result")
 * @ORM\Entity
 */
class TATOABPResult {

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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TATOABPRIndividualPerformance", mappedBy="result", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $individualperformances;

    /**
     * @var float
     *
     * @ORM\Column(name="globalperformance", type="float")
     */
    private $globalperformance;

    /**
     * @var float
     *
     * @ORM\Column(name="reach", type="float")
     */
    private $reach;

    /**
     * @var TATOAllocationByPeriod
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod", inversedBy="result")
     * @ORM\JoinColumn(name="allocation_id", referencedColumnName="id")
     * 
     *  
     */
    protected $allocationbyperiod;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->individualperformances = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set globalperformance
     *
     * @param float $globalperformance
     * @return TATOABPResult
     */
    public function setGlobalperformance($globalperformance)
    {
        $this->globalperformance = $globalperformance;

        return $this;
    }

    /**
     * Get globalperformance
     *
     * @return float 
     */
    public function getGlobalperformance()
    {
        return $this->globalperformance;
    }

    /**
     * Set reach
     *
     * @param float $reach
     * @return TATOABPResult
     */
    public function setReach($reach)
    {
        $this->reach = $reach;

        return $this;
    }

    /**
     * Get reach
     *
     * @return float 
     */
    public function getReach()
    {
        return $this->reach;
    }

    /**
     * Add individualperformances
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATOABPRIndividualPerformance $individualperformances
     * @return TATOABPResult
     */
    public function addIndividualperformance(\MissionControl\Bundle\LightdataBundle\Entity\TATOABPRIndividualPerformance $individualperformances)
    {
        $this->individualperformances[] = $individualperformances;

        return $this;
    }

    /**
     * Remove individualperformances
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATOABPRIndividualPerformance $individualperformances
     */
    public function removeIndividualperformance(\MissionControl\Bundle\LightdataBundle\Entity\TATOABPRIndividualPerformance $individualperformances)
    {
        $this->individualperformances->removeElement($individualperformances);
    }

    /**
     * Get individualperformances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIndividualperformances()
    {
        return $this->individualperformances;
    }

    /**
     * Set allocationbyperiod
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod $allocationbyperiod
     * @return TATOABPResult
     */
    public function setAllocationbyperiod(\MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod $allocationbyperiod = null)
    {
        $this->allocationbyperiod = $allocationbyperiod;

        return $this;
    }

    /**
     * Get allocationbyperiod
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod 
     */
    public function getAllocationbyperiod()
    {
        return $this->allocationbyperiod;
    }
}
