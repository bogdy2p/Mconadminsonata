<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * GroupingTouchpointCategoryMapLD
 *
 * @ORM\Table(name = "lightdata_grouping_touchpointcategorymap")
 * @ORM\Entity
 */
class GroupingTouchpointCategoryMapLD {

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
     * @var GroupingLD
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\GroupingLD", inversedBy="groupingcategories")
     * @ORM\JoinColumn(name="grouping_id", referencedColumnName="id")
     * @Exclude
     */
    protected $grouping;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return GroupingTouchpointCategoryMap
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set grouping
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $grouping
     * @return GroupingTouchpointCategoryMapLD
     */
    public function setGrouping(\MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $grouping = null) {
        $this->grouping = $grouping;

        return $this;
    }

    /**
     * Get grouping
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD 
     */
    public function getGrouping() {
        return $this->grouping;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return GroupingTouchpointCategoryMapLD
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }


    /**
     * Set value
     *
     * @param integer $value
     * @return GroupingTouchpointCategoryMapLD
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }
}
