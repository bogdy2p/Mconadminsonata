<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * TouchpointLD
 *
 * @ORM\Table(name = "lightdata_touchpoint")
 * @ORM\Entity
 */
class TouchpointLD {

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
     * @var string
     *
     * @ORM\Column(name="localname", type="string", length=255)
     */
    private $localname;

    /**
     * @var string
     *
     * @ORM\Column(name="htmlcolor", type="string", length=255)
     */
    private $htmlcolor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="selected", type="boolean")
     */
    private $selected;

    /**
     * @var float
     *
     * @ORM\Column(name="aggobjectivescore", type="float")
     */
    private $aggobjectivescore;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TouchpointObjectiveScoreLD", mappedBy="touchpoint", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $touchpointobjectivescores;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\TouchpointAttributeScoreLD", mappedBy="touchpoint", cascade={"persist","remove"})
     * @Expose
     *
     */
    protected $touchpointattributescores;

    /**
     * @var Lightdata
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\Lightdata", inversedBy="touchpoints")
     * @ORM\JoinColumn(name="lightdata", referencedColumnName="id")
     * 
     * 
     */
    protected $lightdata;

    /**
     * Constructor
     */
    public function __construct() {
        $this->touchpointattributescores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->touchpointobjectivescores = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return TouchpointLD
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
     * Set localname
     *
     * @param string $localname
     * @return TouchpointLD
     */
    public function setLocalname($localname) {
        $this->localname = $localname;

        return $this;
    }

    /**
     * Get localname
     *
     * @return string 
     */
    public function getLocalname() {
        return $this->localname;
    }

    /**
     * Set htmlcolor
     *
     * @param string $htmlcolor
     * @return TouchpointLD
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
     * Set selected
     *
     * @param boolean $selected
     * @return TouchpointLD
     */
    public function setSelected($selected) {
        $this->selected = $selected;

        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean 
     */
    public function getSelected() {
        return $this->selected;
    }

    /**
     * Set aggobjectivescore
     *
     * @param float $aggobjectivescore
     * @return TouchpointLD
     */
    public function setAggobjectivescore($aggobjectivescore) {
        $this->aggobjectivescore = $aggobjectivescore;

        return $this;
    }

    /**
     * Get aggobjectivescore
     *
     * @return float 
     */
    public function getAggobjectivescore() {
        return $this->aggobjectivescore;
    }

    /**
     * Add touchpointattributescores
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TouchpointAttributeScoreLD $touchpointattributescores
     * @return TouchpointLD
     */
    public function addTouchpointattributescore(\MissionControl\Bundle\LightdataBundle\Entity\TouchpointAttributeScoreLD $touchpointattributescores) {
        $this->touchpointattributescores[] = $touchpointattributescores;

        return $this;
    }

    /**
     * Remove touchpointattributescores
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TouchpointAttributeScoreLD $touchpointattributescores
     */
    public function removeTouchpointattributescore(\MissionControl\Bundle\LightdataBundle\Entity\TouchpointAttributeScoreLD $touchpointattributescores) {
        $this->touchpointattributescores->removeElement($touchpointattributescores);
    }

    /**
     * Get touchpointattributescores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTouchpointattributescores() {
        return $this->touchpointattributescores;
    }

    /**
     * Add touchpointobjectivescores
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TouchpointObjectiveScoreLD $touchpointobjectivescores
     * @return TouchpointLD
     */
    public function addTouchpointobjectivescore(\MissionControl\Bundle\LightdataBundle\Entity\TouchpointObjectiveScoreLD $touchpointobjectivescores) {
        $this->touchpointobjectivescores[] = $touchpointobjectivescores;

        return $this;
    }

    /**
     * Remove touchpointobjectivescores
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\TouchpointObjectiveScoreLD $touchpointobjectivescores
     */
    public function removeTouchpointobjectivescore(\MissionControl\Bundle\LightdataBundle\Entity\TouchpointObjectiveScoreLD $touchpointobjectivescores) {
        $this->touchpointobjectivescores->removeElement($touchpointobjectivescores);
    }

    /**
     * Get touchpointobjectivescores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTouchpointobjectivescores() {
        return $this->touchpointobjectivescores;
    }

    /**
     * Set lightdata
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata
     * @return TouchpointLD
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

}
