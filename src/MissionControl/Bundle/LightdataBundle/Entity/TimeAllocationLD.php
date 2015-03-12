<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * TimeAllocationLD
 *
 * @ORM\Table(name = "lightdata_timeallocation")
 * @ORM\Entity
 */
class TimeAllocationLD {

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
     * @var Lightdata
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\Lightdata", inversedBy="timeallocation")
     * @ORM\JoinColumn(name="lightdata", referencedColumnName="id")
     * 
     * 
     */
    protected $lightdata;

        /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD", mappedBy="timeallocation", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $allocatedtouchpoints;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TATotalLD", mappedBy="timeallocation", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $total;

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
     * Set lightdata
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata
     * @return TimeAllocationLD
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

    /**
     * Add allocatedtouchpoints
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD $allocatedtouchpoints
     * @return TimeAllocationLD
     */
    public function addAllocatedtouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD $allocatedtouchpoints)
    {
        $this->allocatedtouchpoints[] = $allocatedtouchpoints;

        return $this;
    }

    /**
     * Remove allocatedtouchpoints
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD $allocatedtouchpoints
     */
    public function removeAllocatedtouchpoint(\MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD $allocatedtouchpoints)
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
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATotalLD $total
     * @return TimeAllocationLD
     */
    public function addTotal(\MissionControl\Bundle\LightdataBundle\Entity\TATotalLD $total)
    {
        $this->total[] = $total;

        return $this;
    }

    /**
     * Remove total
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATotalLD $total
     */
    public function removeTotal(\MissionControl\Bundle\LightdataBundle\Entity\TATotalLD $total)
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
}
