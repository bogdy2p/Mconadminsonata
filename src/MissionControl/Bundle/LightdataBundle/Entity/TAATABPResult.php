<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * TAATABPResult
 *
 * @ORM\Table(name = "lightdata_TAATABP_result")
 * @ORM\Entity
 */
class TAATABPResult {

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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TAATABPRIndividualPerformance", mappedBy="result", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $individualperformances;

    /**
     * @var TAATAllocationByPeriod
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TAATAllocationByPeriod", inversedBy="result")
     * @ORM\JoinColumn(name="allocationbyperiod_id", referencedColumnName="id")
     * 
     * 
     */
    protected $allocationbyperiod;

    /**
     * Constructor
     */
    public function __construct() {
        $this->individualperformances = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set globalperformance
     *
     * @param float $globalperformance
     * @return TAATABPResult
     */
    public function setGlobalperformance($globalperformance) {
        $this->globalperformance = $globalperformance;

        return $this;
    }

    /**
     * Get globalperformance
     *
     * @return float 
     */
    public function getGlobalperformance() {
        return $this->globalperformance;
    }

    /**
     * Set reach
     *
     * @param float $reach
     * @return TAATABPResult
     */
    public function setReach($reach) {
        $this->reach = $reach;

        return $this;
    }

    /**
     * Get reach
     *
     * @return float 
     */
    public function getReach() {
        return $this->reach;
    }

    /**
     * Add individualperformances
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TAATABPRIndividualPerformance $individualperformances
     * @return TAATABPResult
     */
    public function addIndividualperformance(\MissionControl\Bundle\LightdataBundle\Entity\TAATABPRIndividualPerformance $individualperformances) {
        $this->individualperformances[] = $individualperformances;

        return $this;
    }

    /**
     * Remove individualperformances
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TAATABPRIndividualPerformance $individualperformances
     */
    public function removeIndividualperformance(\MissionControl\Bundle\LightdataBundle\Entity\TAATABPRIndividualPerformance $individualperformances) {
        $this->individualperformances->removeElement($individualperformances);
    }

    /**
     * Get individualperformances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIndividualperformances() {
        return $this->individualperformances;
    }

    /**
     * Set allocationbyperiod
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TAATAllocationByPeriod $allocationbyperiod
     * @return TAATABPResult
     */
    public function setAllocationbyperiod(\MissionControl\Bundle\LightdataBundle\Entity\TAATAllocationByPeriod $allocationbyperiod = null) {
        $this->allocationbyperiod = $allocationbyperiod;

        return $this;
    }

    /**
     * Get allocationbyperiod
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TAATAllocationByPeriod 
     */
    public function getAllocationbyperiod() {
        return $this->allocationbyperiod;
    }

}
