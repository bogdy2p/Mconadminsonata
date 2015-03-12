<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * SetupLD
 *
 * @ORM\Table(name = "lightdata_setup")
 * @ORM\Entity
 */
class SetupLD {

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
     * @var \MissionControl\Bundle\LightdataBundle\Entity\SurveyLD
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\SurveyLD", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="survey", referencedColumnName="id")
     * })
     */
    private $survey;

    /**
     * @var \MissionControl\Bundle\LightdataBundle\Entity\TargetLD
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TargetLD", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="target", referencedColumnName="id")
     * })
     */
    private $target;

    /**
     * @var \MissionControl\Bundle\LightdataBundle\Entity\ClientLD
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\ClientLD", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client", referencedColumnName="id")
     * })
     */
    private $client;

    /**
     * @var Lightdata
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\Lightdata", inversedBy="setup")
     * @ORM\JoinColumn(name="lightdata", referencedColumnName="id")
     * 
     * 
     */
    protected $lightdata;

    /**
     * @var string
     *
     * @ORM\Column(name="projectname", type="string", length=255, nullable=true)
     */
    private $ProjectName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="datetime", nullable=true)
     */
    private $StartDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="periodtype", type="integer", nullable=true)
     */
    private $PeriodType;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbperiods", type="integer", nullable=true)
     */
    private $NbPeriods;

    /**
     * @var integer
     *
     * @ORM\Column(name="budget", type="bigint", nullable=true)
     */
    private $Budget;

    /**
     * @var string
     *
     * @ORM\Column(name="budgetcurrency", type="string", length=255, nullable=true)
     */
    private $BudgetCurrency;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set ProjectName
     *
     * @param string $projectName
     * @return SetupLD
     */
    public function setProjectName($projectName) {
        $this->ProjectName = $projectName;

        return $this;
    }

    /**
     * Get ProjectName
     *
     * @return string 
     */
    public function getProjectName() {
        return $this->ProjectName;
    }

    /**
     * Set StartDate
     *
     * @param \DateTime $startDate
     * @return SetupLD
     */
    public function setStartDate($startDate) {
        $this->StartDate = $startDate;

        return $this;
    }

    /**
     * Get StartDate
     *
     * @return \DateTime 
     */
    public function getStartDate() {
        return $this->StartDate;
    }

    /**
     * Set PeriodType
     *
     * @param integer $periodType
     * @return SetupLD
     */
    public function setPeriodType($periodType) {
        $this->PeriodType = $periodType;

        return $this;
    }

    /**
     * Get PeriodType
     *
     * @return integer 
     */
    public function getPeriodType() {
        return $this->PeriodType;
    }

    /**
     * Set NbPeriods
     *
     * @param integer $nbPeriods
     * @return SetupLD
     */
    public function setNbPeriods($nbPeriods) {
        $this->NbPeriods = $nbPeriods;

        return $this;
    }

    /**
     * Get NbPeriods
     *
     * @return integer 
     */
    public function getNbPeriods() {
        return $this->NbPeriods;
    }

    /**
     * Set Budget
     *
     * @param integer $budget
     * @return SetupLD
     */
    public function setBudget($budget) {
        $this->Budget = $budget;

        return $this;
    }

    /**
     * Get Budget
     *
     * @return integer 
     */
    public function getBudget() {
        return $this->Budget;
    }

    /**
     * Set BudgetCurrency
     *
     * @param string $budgetCurrency
     * @return SetupLD
     */
    public function setBudgetCurrency($budgetCurrency) {
        $this->BudgetCurrency = $budgetCurrency;

        return $this;
    }

    /**
     * Get BudgetCurrency
     *
     * @return string 
     */
    public function getBudgetCurrency() {
        return $this->BudgetCurrency;
    }

    /**
     * Set client
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\ClientLD $client
     * @return SetupLD
     */
    public function setClient(\MissionControl\Bundle\LightdataBundle\Entity\ClientLD $client = null) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\ClientLD 
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Set target
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TargetLD $target
     * @return SetupLD
     */
    public function setTarget(\MissionControl\Bundle\LightdataBundle\Entity\TargetLD $target = null) {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TargetLD 
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Set survey
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\SurveyLD $survey
     * @return SetupLD
     */
    public function setSurvey(\MissionControl\Bundle\LightdataBundle\Entity\SurveyLD $survey = null) {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get survey
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\SurveyLD 
     */
    public function getSurvey() {
        return $this->survey;
    }

    /**
     * Set lightdata
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata
     * @return SetupLD
     */
    public function setLightdata(\MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata = null) {
        $this->lightdata = $lightdata;

        return $this;
    }

    /**
     * Get lightdata
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\Lightdata 
     */
    public function getLightdata() {
        return $this->lightdata;
    }

}
