<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Lightxmlprojects
 *
 * @ORM\Table(name="lightdata_lightxmlprojects")
 * @ORM\Entity
 * @ExclusionPolicy("all");
 */
class Lightdata {

    public function __construct() {

        $this->objectives = new ArrayCollection();
        $this->groupings = new ArrayCollection();
        $this->touchpoints = new ArrayCollection();
        $this->cprattributes = new ArrayCollection();
    }

    /**
     * @var \MissionControl\Bundle\LightdataBundle\Entity\SetupLD
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\SetupLD", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     */
    private $setup;

    /**
     * @var string
     *
     * 
     * @ORM\Column(name="campaign_id", type="string", nullable=true)
     * @Expose
     */
    protected $campaign;

    /**
     * @var string
     *
     * 
     * @ORM\Column(name="version", type="integer", nullable=true)
     * @Expose
     */
    protected $version;

    /**
     * @var string
     *
     * 
     * @ORM\Column(name="inputstring", type="text", nullable=false)
     * @Expose
     */
    protected $inputstring;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\ObjectiveLD", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $objectives;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\GroupingLD", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $groupings;

    /**
     * @var integer
     * @ORM\Column(name="currentgroupingindex", type="integer", nullable=true)
     * @Expose
     */
    private $currentgroupingindex;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TouchpointLD", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $touchpoints;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\CPRAttributeLD", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $cprattributes;

    /**
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $budgetallocation;

    /**
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $timeallocation;

    /**
     * @var \MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult", mappedBy="lightdata", cascade={"persist","remove"})
     * @Expose
     */
    protected $whatifresult;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    private $id;

    /**
     * Set campaign
     *
     * @param string $campaign
     * @return Lightdata
     */
    public function setCampaign($campaign) {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return string 
     */
    public function getCampaign() {
        return $this->campaign;
    }

    /**
     * Set currentgroupingindex
     *
     * @param integer $currentgroupingindex
     * @return Lightdata
     */
    public function setCurrentgroupingindex($currentgroupingindex) {
        $this->currentgroupingindex = $currentgroupingindex;

        return $this;
    }

    /**
     * Get currentgroupingindex
     *
     * @return integer 
     */
    public function getCurrentgroupingindex() {
        return $this->currentgroupingindex;
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
     * Set setup
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\SetupLD $setup
     * @return Lightdata
     */
    public function setSetup(\MissionControl\Bundle\LightdataBundle\Entity\SetupLD $setup = null) {
        $this->setup = $setup;

        return $this;
    }

    /**
     * Get setup
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\SetupLD 
     */
    public function getSetup() {
        return $this->setup;
    }

    /**
     * Add objectives
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\ObjectiveLD $objectives
     * @return Lightdata
     */
    public function addObjective(\MissionControl\Bundle\LightdataBundle\Entity\ObjectiveLD $objectives) {
        $this->objectives[] = $objectives;

        return $this;
    }

    /**
     * Remove objectives
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\ObjectiveLD $objectives
     */
    public function removeObjective(\MissionControl\Bundle\LightdataBundle\Entity\ObjectiveLD $objectives) {
        $this->objectives->removeElement($objectives);
    }

    /**
     * Get objectives
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObjectives() {
        return $this->objectives;
    }

    /**
     * Add groupings
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $groupings
     * @return Lightdata
     */
    public function addGrouping(\MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $groupings) {
        $this->groupings[] = $groupings;

        return $this;
    }

    /**
     * Remove groupings
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $groupings
     */
    public function removeGrouping(\MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $groupings) {
        $this->groupings->removeElement($groupings);
    }

    /**
     * Get groupings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupings() {
        return $this->groupings;
    }

    /**
     * Add touchpoints
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TouchpointLD $touchpoints
     * @return Lightdata
     */
    public function addTouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\TouchpointLD $touchpoints) {
        $this->touchpoints[] = $touchpoints;

        return $this;
    }

    /**
     * Remove touchpoints
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TouchpointLD $touchpoints
     */
    public function removeTouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\TouchpointLD $touchpoints) {
        $this->touchpoints->removeElement($touchpoints);
    }

    /**
     * Get touchpoints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTouchpoints() {
        return $this->touchpoints;
    }

    /**
     * Add cprattributes
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\CPRAttributeLD $cprattributes
     * @return Lightdata
     */
    public function addCprattribute(\MissionControl\Bundle\LightdataBundle\Entity\CPRAttributeLD $cprattributes) {
        $this->cprattributes[] = $cprattributes;

        return $this;
    }

    /**
     * Remove cprattributes
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\CPRAttributeLD $cprattributes
     */
    public function removeCprattribute(\MissionControl\Bundle\LightdataBundle\Entity\CPRAttributeLD $cprattributes) {
        $this->cprattributes->removeElement($cprattributes);
    }

    /**
     * Get cprattributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCprattributes() {
        return $this->cprattributes;
    }

    /**
     * Set budgetallocation
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD $budgetallocation
     * @return Lightdata
     */
    public function setBudgetallocation(\MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD $budgetallocation = null) {
        $this->budgetallocation = $budgetallocation;

        return $this;
    }

    /**
     * Get budgetallocation
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD 
     */
    public function getBudgetallocation() {
        return $this->budgetallocation;
    }

    /**
     * Set timeallocation
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD $timeallocation
     * @return Lightdata
     */
    public function setTimeallocation(\MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD $timeallocation = null) {
        $this->timeallocation = $timeallocation;

        return $this;
    }

    /**
     * Get timeallocation
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD 
     */
    public function getTimeallocation() {
        return $this->timeallocation;
    }

    /**
     * Set whatifresult
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult $whatifresult
     * @return Lightdata
     */
    public function setWhatifresult(\MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult $whatifresult = null) {
        $this->whatifresult = $whatifresult;

        return $this;
    }

    /**
     * Get whatifresult
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult 
     */
    public function getWhatifresult() {
        return $this->whatifresult;
    }

    /**
     * Set version
     *
     * @param integer $version
     * @return Lightdata
     */
    public function setVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Set inputstring
     *
     * @param string $inputstring
     * @return Lightdata
     */
    public function setInputstring($inputstring) {
        $this->inputstring = $inputstring;

        return $this;
    }

    /**
     * Get inputstring
     *
     * @return string 
     */
    public function getInputstring() {
        return $this->inputstring;
    }

}
