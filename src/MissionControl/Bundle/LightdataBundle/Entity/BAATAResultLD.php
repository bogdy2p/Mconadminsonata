<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * AllocationResultLD
 *
 * @ORM\Table(name = "lightdata_BAATA_result")
 * @ORM\Entity
 */
class BAATAResultLD {

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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BAATARIndividualPerformanceLD", mappedBy="result", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $individualperformances;

    /**
     * @var BAATAllocationLD
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BAATAllocationLD", inversedBy="result")
     * @ORM\JoinColumn(name="allocation_id", referencedColumnName="id")
     * 
     * 
     */
    protected $allocation;

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
     * @return BAATAResultLD
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
     * @return BAATAResultLD
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
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BAATARIndividualPerformanceLD $individualperformances
     * @return BAATAResultLD
     */
    public function addIndividualperformance(\MissionControl\Bundle\LightdataBundle\Entity\BAATARIndividualPerformanceLD $individualperformances) {
        $this->individualperformances[] = $individualperformances;

        return $this;
    }

    /**
     * Remove individualperformances
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BAATARIndividualPerformanceLD $individualperformances
     */
    public function removeIndividualperformance(\MissionControl\Bundle\LightdataBundle\Entity\BAATARIndividualPerformanceLD $individualperformances) {
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
     * Set allocation
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BAATAllocationLD $allocation
     * @return BAATAResultLD
     */
    public function setAllocation(\MissionControl\Bundle\LightdataBundle\Entity\BAATAllocationLD $allocation = null) {
        $this->allocation = $allocation;

        return $this;
    }

    /**
     * Get allocation
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\BAATAllocationLD 
     */
    public function getAllocation() {
        return $this->allocation;
    }

}
