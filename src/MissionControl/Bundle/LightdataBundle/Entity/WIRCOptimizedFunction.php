<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
/**
 * WIRCOptimizedFunction
 *
 * @ORM\Table(name= "lightdata_WIRC_optimizedfunction")
 * @ORM\Entity
 */
class WIRCOptimizedFunction
{
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
     * @var WIRConfig
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRConfig", inversedBy="optimizedfunction")
     * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
     * 
     * 
     */
    protected $config;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="calculationtype", type="integer")
     */
    private $calculationtype;

    /**
     * @var integer
     *
     * @ORM\Column(name="attributeindex", type="integer")
     */
    private $attributeindex;


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
     * Set calculationtype
     *
     * @param integer $calculationtype
     * @return WIRCOptimizedFunction
     */
    public function setCalculationtype($calculationtype)
    {
        $this->calculationtype = $calculationtype;

        return $this;
    }

    /**
     * Get calculationtype
     *
     * @return integer 
     */
    public function getCalculationtype()
    {
        return $this->calculationtype;
    }

    /**
     * Set attributeindex
     *
     * @param integer $attributeindex
     * @return WIRCOptimizedFunction
     */
    public function setAttributeindex($attributeindex)
    {
        $this->attributeindex = $attributeindex;

        return $this;
    }

    /**
     * Get attributeindex
     *
     * @return integer 
     */
    public function getAttributeindex()
    {
        return $this->attributeindex;
    }

    /**
     * Set config
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRConfig $config
     * @return WIRCOptimizedFunction
     */
    public function setConfig(\MissionControl\Bundle\LightdataBundle\Entity\WIRConfig $config = null)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRConfig 
     */
    public function getConfig()
    {
        return $this->config;
    }
}
