<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * WIRConfig
 *
 * @ORM\Table(name = "lightdata_whatifresult_config")
 * @ORM\Entity
 */
class WIRConfig {

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
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult", inversedBy="config")
     * @ORM\JoinColumn(name="whatifresult_id", referencedColumnName="id")
     * 
     * 
     */
    protected $whatifresult;

    /**
     * @var integer
     *
     * @ORM\Column(name="firstperiod", type="integer")
     */
    private $firstperiod;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastperiod", type="integer")
     */
    private $lastperiod;

    /**
     * @var integer
     *
     * @ORM\Column(name="sourcebudget", type="integer")
     */
    private $sourcebudget;

    /**
     * @var integer
     *
     * @ORM\Column(name="budgetminpercent", type="integer")
     */
    private $budgetminpercent;

    /**
     * @var integer
     *
     * @ORM\Column(name="budgetmaxpercent", type="integer")
     */
    private $budgetmaxpercent;

    /**
     * @var integer
     *
     * @ORM\Column(name="budgetsteppercent", type="integer")
     */
    private $budgetsteppercent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hascurrentmix", type="boolean")
     */
    private $hascurrentmix;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hassingletouchpointmix", type="boolean")
     */
    private $hassingletouchpointmix;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasoptimizedmix", type="boolean")
     */
    private $hasoptimizedmix;

    /**
     * @var WIRCOptimizedFunction
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRCOptimizedFunction", mappedBy="config", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $optimizedfunction;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set firstperiod
     *
     * @param integer $firstperiod
     * @return WIRConfig
     */
    public function setFirstperiod($firstperiod) {
        $this->firstperiod = $firstperiod;

        return $this;
    }

    /**
     * Get firstperiod
     *
     * @return integer 
     */
    public function getFirstperiod() {
        return $this->firstperiod;
    }

    /**
     * Set lastperiod
     *
     * @param integer $lastperiod
     * @return WIRConfig
     */
    public function setLastperiod($lastperiod) {
        $this->lastperiod = $lastperiod;

        return $this;
    }

    /**
     * Get lastperiod
     *
     * @return integer 
     */
    public function getLastperiod() {
        return $this->lastperiod;
    }

    /**
     * Set sourcebudget
     *
     * @param integer $sourcebudget
     * @return WIRConfig
     */
    public function setSourcebudget($sourcebudget) {
        $this->sourcebudget = $sourcebudget;

        return $this;
    }

    /**
     * Get sourcebudget
     *
     * @return integer 
     */
    public function getSourcebudget() {
        return $this->sourcebudget;
    }

    /**
     * Set budgetminpercent
     *
     * @param integer $budgetminpercent
     * @return WIRConfig
     */
    public function setBudgetminpercent($budgetminpercent) {
        $this->budgetminpercent = $budgetminpercent;

        return $this;
    }

    /**
     * Get budgetminpercent
     *
     * @return integer 
     */
    public function getBudgetminpercent() {
        return $this->budgetminpercent;
    }

    /**
     * Set budgetmaxpercent
     *
     * @param integer $budgetmaxpercent
     * @return WIRConfig
     */
    public function setBudgetmaxpercent($budgetmaxpercent) {
        $this->budgetmaxpercent = $budgetmaxpercent;

        return $this;
    }

    /**
     * Get budgetmaxpercent
     *
     * @return integer 
     */
    public function getBudgetmaxpercent() {
        return $this->budgetmaxpercent;
    }

    /**
     * Set budgetsteppercent
     *
     * @param integer $budgetsteppercent
     * @return WIRConfig
     */
    public function setBudgetsteppercent($budgetsteppercent) {
        $this->budgetsteppercent = $budgetsteppercent;

        return $this;
    }

    /**
     * Get budgetsteppercent
     *
     * @return integer 
     */
    public function getBudgetsteppercent() {
        return $this->budgetsteppercent;
    }

    /**
     * Set hascurrentmix
     *
     * @param boolean $hascurrentmix
     * @return WIRConfig
     */
    public function setHascurrentmix($hascurrentmix) {
        $this->hascurrentmix = $hascurrentmix;

        return $this;
    }

    /**
     * Get hascurrentmix
     *
     * @return boolean 
     */
    public function getHascurrentmix() {
        return $this->hascurrentmix;
    }

    /**
     * Set hassingletouchpointmix
     *
     * @param boolean $hassingletouchpointmix
     * @return WIRConfig
     */
    public function setHassingletouchpointmix($hassingletouchpointmix) {
        $this->hassingletouchpointmix = $hassingletouchpointmix;

        return $this;
    }

    /**
     * Get hassingletouchpointmix
     *
     * @return boolean 
     */
    public function getHassingletouchpointmix() {
        return $this->hassingletouchpointmix;
    }

    /**
     * Set hasoptimizedmix
     *
     * @param boolean $hasoptimizedmix
     * @return WIRConfig
     */
    public function setHasoptimizedmix($hasoptimizedmix) {
        $this->hasoptimizedmix = $hasoptimizedmix;

        return $this;
    }

    /**
     * Get hasoptimizedmix
     *
     * @return boolean 
     */
    public function getHasoptimizedmix() {
        return $this->hasoptimizedmix;
    }

    /**
     * Set whatifresult
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult $whatifresult
     * @return WIRConfig
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
     * Set optimizedfunction
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRCOptimizedFunction $optimizedfunction
     * @return WIRConfig
     */
    public function setOptimizedfunction(\MissionControl\Bundle\LightdataBundle\Entity\WIRCOptimizedFunction $optimizedfunction = null) {
        $this->optimizedfunction = $optimizedfunction;

        return $this;
    }

    /**
     * Get optimizedfunction
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRCOptimizedFunction 
     */
    public function getOptimizedfunction() {
        return $this->optimizedfunction;
    }

}
