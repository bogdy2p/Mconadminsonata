<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * ARIndividualPerformanceLD
 *
 * @ORM\Table(name = "lightdata_BAATAR_individualperformance")
 * @ORM\Entity
 */
class BAATARIndividualPerformanceLD {

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
     * @var BAATAResultLD
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BAATAResultLD", inversedBy="individualperformances")
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
     * @return BAATARIndividualPerformanceLD
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
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BAATAResultLD $result
     * @return BAATARIndividualPerformanceLD
     */
    public function setResult(\MissionControl\Bundle\LightdataBundle\Entity\BAATAResultLD $result = null)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\BAATAResultLD 
     */
    public function getResult()
    {
        return $this->result;
    }
}
