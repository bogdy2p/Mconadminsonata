<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * BudgetAllocationLD
 *
 * @ORM\Table(name = "lightdata_budgetallocation")
 * @ORM\Entity
 */
class BudgetAllocationLD {

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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BAAllocatedTouchpointLD", mappedBy="budgetallocation", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $allocatedtouchpoints;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BATotalLD", mappedBy="budgetallocation", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $total;

    /**
     * @var Lightdata
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\Lightdata", inversedBy="budgetallocation")
     * @ORM\JoinColumn(name="lightdata", referencedColumnName="id")
     * 
     * 
     */
    protected $lightdata;

   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allocatedtouchpoints = new \Doctrine\Common\Collections\ArrayCollection();
        $this->total = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add allocatedtouchpoints
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BAAllocatedTouchpointLD $allocatedtouchpoints
     * @return BudgetAllocationLD
     */
    public function addAllocatedtouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\BAAllocatedTouchpointLD $allocatedtouchpoints)
    {
        $this->allocatedtouchpoints[] = $allocatedtouchpoints;

        return $this;
    }

    /**
     * Remove allocatedtouchpoints
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BAAllocatedTouchpointLD $allocatedtouchpoints
     */
    public function removeAllocatedtouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\BAAllocatedTouchpointLD $allocatedtouchpoints)
    {
        $this->allocatedtouchpoints->removeElement($allocatedtouchpoints);
    }

    /**
     * Get allocatedtouchpoints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllocatedtouchpoints()
    {
        return $this->allocatedtouchpoints;
    }

    /**
     * Add total
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BATotalLD $total
     * @return BudgetAllocationLD
     */
    public function addTotal(\MissionControl\Bundle\LightdataBundle\Entity\BATotalLD $total)
    {
        $this->total[] = $total;

        return $this;
    }

    /**
     * Remove total
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BATotalLD $total
     */
    public function removeTotal(\MissionControl\Bundle\LightdataBundle\Entity\BATotalLD $total)
    {
        $this->total->removeElement($total);
    }

    /**
     * Get total
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set lightdata
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata
     * @return BudgetAllocationLD
     */
    public function setLightdata(\MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata = null)
    {
        $this->lightdata = $lightdata;

        return $this;
    }

    /**
     * Get lightdata
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\Lightdata 
     */
    public function getLightdata()
    {
        return $this->lightdata;
    }
}
