<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
/**
 * GroupingCategoryLD
 *
 * @ORM\Table(name = "lightdata_grouping_category")
 * @ORM\Entity
 */
class GroupingCategoryLD {

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
     * @var string
     *
     * @ORM\Column(name="htmlcolor", type="string", length=255)
     */
    private $htmlcolor;

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
     * @return GroupingCategoryLD
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
     * Set htmlcolor
     *
     * @param string $htmlcolor
     * @return GroupingCategoryLD
     */
    public function setHtmlcolor($htmlcolor) {
        $this->htmlcolor = $htmlcolor;

        return $this;
    }

    /**
     * Get htmlcolor
     *
     * @return string 
     */
    public function getHtmlcolor() {
        return $this->htmlcolor;
    }


    /**
     * Set grouping_id
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $groupingId
     * @return GroupingCategoryLD
     */
    public function setGroupingId(\MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $groupingId = null)
    {
        $this->grouping_id = $groupingId;

        return $this;
    }

    /**
     * Get grouping_id
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD 
     */
    public function getGroupingId()
    {
        return $this->grouping_id;
    }

    /**
     * Set grouping
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $grouping
     * @return GroupingCategoryLD
     */
    public function setGrouping(\MissionControl\Bundle\LightdataBundle\Entity\GroupingLD $grouping = null)
    {
        $this->grouping = $grouping;

        return $this;
    }

    /**
     * Get grouping
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\GroupingLD 
     */
    public function getGrouping()
    {
        return $this->grouping;
    }
}
