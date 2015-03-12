<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * TATOABPRIndividualPerformance
 *
 * @ORM\Table(name = "lightdata_TATOABPR_individualperformance")
 * @ORM\Entity
 */
class TATOABPRIndividualPerformance {

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
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var TATOABPResult
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TATOABPResult", inversedBy="individualperformances")
     * @ORM\JoinColumn(name="result_id", referencedColumnName="id")
     * @Exclude
     */
    protected $result;


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
     * Set value
     *
     * @param float $value
     * @return TATOABPRIndividualPerformance
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set result
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATOABPResult $result
     * @return TATOABPRIndividualPerformance
     */
    public function setResult(\MissionControl\Bundle\LightdataBundle\Entity\TATOABPResult $result = null)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TATOABPResult 
     */
    public function getResult()
    {
        return $this->result;
    }
}
