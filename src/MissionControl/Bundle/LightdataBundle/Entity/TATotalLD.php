<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * TATotalLD
 *
 * @ORM\Table(name = "lightdata_timeallocation_total")
 * @ORM\Entity
 */
class TATotalLD {

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
     * @var TATOAllocationByPeriod
     * 
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod", mappedBy="allocatedtouchpoint", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $allocationbyperiod;

    /**
     * @var string
     *
     * @ORM\Column(name="touchpointname", type="string", length=255)
     */
    private $touchpointname;

    /**
     * @var float
     *
     * @ORM\Column(name="reachfrequency", type="float")
     */
    private $reachfrequency;

    /**
     * @var TimeAllocationLD
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD", inversedBy="allocatedtouchpoints")
     * @ORM\JoinColumn(name="timeallocation_id", referencedColumnName="id")
     * @Exclude
     */
    protected $timeallocation;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allocationbyperiod = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set touchpointname
     *
     * @param string $touchpointname
     * @return TATotalLD
     */
    public function setTouchpointname($touchpointname)
    {
        $this->touchpointname = $touchpointname;

        return $this;
    }

    /**
     * Get touchpointname
     *
     * @return string 
     */
    public function getTouchpointname()
    {
        return $this->touchpointname;
    }

    /**
     * Set reachfrequency
     *
     * @param float $reachfrequency
     * @return TATotalLD
     */
    public function setReachfrequency($reachfrequency)
    {
        $this->reachfrequency = $reachfrequency;

        return $this;
    }

    /**
     * Get reachfrequency
     *
     * @return float 
     */
    public function getReachfrequency()
    {
        return $this->reachfrequency;
    }

    /**
     * Add allocationbyperiod
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod $allocationbyperiod
     * @return TATotalLD
     */
    public function addAllocationbyperiod(\MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod $allocationbyperiod)
    {
        $this->allocationbyperiod[] = $allocationbyperiod;

        return $this;
    }

    /**
     * Remove allocationbyperiod
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod $allocationbyperiod
     */
    public function removeAllocationbyperiod(\MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod $allocationbyperiod)
    {
        $this->allocationbyperiod->removeElement($allocationbyperiod);
    }

    /**
     * Get allocationbyperiod
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAllocationbyperiod()
    {
        return $this->allocationbyperiod;
    }

    /**
     * Set timeallocation
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD $timeallocation
     * @return TATotalLD
     */
    public function setTimeallocation(\MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD $timeallocation = null)
    {
        $this->timeallocation = $timeallocation;

        return $this;
    }

    /**
     * Get timeallocation
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD 
     */
    public function getTimeallocation()
    {
        return $this->timeallocation;
    }
}
