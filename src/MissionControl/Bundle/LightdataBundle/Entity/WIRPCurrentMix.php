<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * WIRPCurrentMix
 *
 * @ORM\Table(name= "lightdata_WIRP_currentmix")
 * @ORM\Entity
 */
class WIRPCurrentMix {

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
     * @var WIRPCMDetail
     * 
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPCMDetail", mappedBy="currentmix", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $details;

    /**
     * @var WIRPCMTotal
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPCMTotal", mappedBy="currentmix", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $total;

    /**
     * @var WIRPoint
     *
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\WIRPoint", inversedBy="currentmix")
     * @ORM\JoinColumn(name="point_id", referencedColumnName="id")
     * 
     * 
     */
    protected $point;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->details = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add details
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPCMDetail $details
     * @return WIRPCurrentMix
     */
    public function addDetail(\MissionControl\Bundle\LightdataBundle\Entity\WIRPCMDetail $details)
    {
        $this->details[] = $details;

        return $this;
    }

    /**
     * Remove details
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPCMDetail $details
     */
    public function removeDetail(\MissionControl\Bundle\LightdataBundle\Entity\WIRPCMDetail $details)
    {
        $this->details->removeElement($details);
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set total
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPCMTotal $total
     * @return WIRPCurrentMix
     */
    public function setTotal(\MissionControl\Bundle\LightdataBundle\Entity\WIRPCMTotal $total = null)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRPCMTotal 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set point
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\WIRPoint $point
     * @return WIRPCurrentMix
     */
    public function setPoint(\MissionControl\Bundle\LightdataBundle\Entity\WIRPoint $point = null)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\WIRPoint 
     */
    public function getPoint()
    {
        return $this->point;
    }
}
