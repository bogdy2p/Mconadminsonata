<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * BATotalLD
 *
 * @ORM\Table(name= "lightdata_budgetallocation_total")
 * @ORM\Entity
 */
class BATotalLD {

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
     * @var string
     *
     * @ORM\Column(name="touchpointname", type="string", length=255)
     */
    private $touchpointname;

    /**
     * @var BATOAllocationLD
     * 
     * @ORM\OneToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BATOAllocationLD", mappedBy="allocatedtouchpoint", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $allocation;

    /**
     * @var BudgetAllocationLD
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD", inversedBy="total")
     * @ORM\JoinColumn(name="total_id", referencedColumnName="id")
     * @Exclude
     */
    protected $budgetallocation;


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
     * @return BATotalLD
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
     * Set allocation
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BATOAllocationLD $allocation
     * @return BATotalLD
     */
    public function setAllocation(\MissionControl\Bundle\LightdataBundle\Entity\BATOAllocationLD $allocation = null)
    {
        $this->allocation = $allocation;

        return $this;
    }

    /**
     * Get allocation
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\BATOAllocationLD 
     */
    public function getAllocation()
    {
        return $this->allocation;
    }

    /**
     * Set budgetallocation
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD $budgetallocation
     * @return BATotalLD
     */
    public function setBudgetallocation(\MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD $budgetallocation = null)
    {
        $this->budgetallocation = $budgetallocation;

        return $this;
    }

    /**
     * Get budgetallocation
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD 
     */
    public function getBudgetallocation()
    {
        return $this->budgetallocation;
    }
}
