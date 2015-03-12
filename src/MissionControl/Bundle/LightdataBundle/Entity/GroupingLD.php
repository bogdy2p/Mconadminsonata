<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * GroupingLD
 *
 * @ORM\Table(name = "lightdata_grouping")
 * @ORM\Entity
 */
class GroupingLD {

    public function __construct() {

        $this->groupingcategories = new ArrayCollection();
        $this->groupingtouchpointcategorymap = new ArrayCollection();
    }

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\GroupingCategoryLD", mappedBy="grouping", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $groupingcategories;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\GroupingTouchpointCategoryMapLD", mappedBy="grouping", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $groupingtouchpointcategorymap;

    /**
     * @var Lightdata
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\Lightdata", inversedBy="groupings")
     * @ORM\JoinColumn(name="lightdata", referencedColumnName="id")
     *
     */
    protected $lightdata;

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
     * @return GroupingLD
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
     * Add groupingcategories
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingCategoryLD $groupingcategories
     * @return GroupingLD
     */
    public function addGroupingcategory(\MissionControl\Bundle\LightdataBundle\Entity\GroupingCategoryLD $groupingcategories) {
        $this->groupingcategories[] = $groupingcategories;

        return $this;
    }

    /**
     * Remove groupingcategories
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingCategoryLD $groupingcategories
     */
    public function removeGroupingcategory(\MissionControl\Bundle\LightdataBundle\Entity\GroupingCategoryLD $groupingcategories) {
        $this->groupingcategories->removeElement($groupingcategories);
    }

    /**
     * Get groupingcategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupingcategories() {
        return $this->groupingcategories;
    }

    /**
     * Set lightdata
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata
     * @return GroupingLD
     */
    public function setLightdata(\MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata = null) {
        $this->lightdata = $lightdata;

        return $this;
    }

    /**
     * Get lightdata
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\Lightdata 
     */
    public function getLightdata() {
        return $this->lightdata;
    }


    /**
     * Add groupingtouchpointcategorymap
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingTouchpointCategoryMapLD $groupingtouchpointcategorymap
     * @return GroupingLD
     */
    public function addGroupingtouchpointcategorymap(\MissionControl\Bundle\LightdataBundle\Entity\GroupingTouchpointCategoryMapLD $groupingtouchpointcategorymap)
    {
        $this->groupingtouchpointcategorymap[] = $groupingtouchpointcategorymap;

        return $this;
    }

    /**
     * Remove groupingtouchpointcategorymap
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\GroupingTouchpointCategoryMapLD $groupingtouchpointcategorymap
     */
    public function removeGroupingtouchpointcategorymap(\MissionControl\Bundle\LightdataBundle\Entity\GroupingTouchpointCategoryMapLD $groupingtouchpointcategorymap)
    {
        $this->groupingtouchpointcategorymap->removeElement($groupingtouchpointcategorymap);
    }

    /**
     * Get groupingtouchpointcategorymap
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupingtouchpointcategorymap()
    {
        return $this->groupingtouchpointcategorymap;
    }
}
