<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * ObjectiveLD
 *
 * @ORM\Table(name = "lightdata_objective")
 * @ORM\Entity
 */
class ObjectiveLD {

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
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
     * @ORM\Column(name="score", type="float")
     */
    private $score;

    /**
     * @var Lightdata
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\LightdataBundle\Entity\Lightdata", inversedBy="objectives")
     * @ORM\JoinColumn(name="lightdata", referencedColumnName="id")
     * 
     * 
     */
    protected $lightdata;


    

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
     * Set name
     *
     * @param string $name
     * @return ObjectiveLD
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set htmlcolor
     *
     * @param string $htmlcolor
     * @return ObjectiveLD
     */
    public function setHtmlcolor($htmlcolor)
    {
        $this->htmlcolor = $htmlcolor;

        return $this;
    }

    /**
     * Get htmlcolor
     *
     * @return string 
     */
    public function getHtmlcolor()
    {
        return $this->htmlcolor;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     * @return ObjectiveLD
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean 
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Set score
     *
     * @param float $score
     * @return ObjectiveLD
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return float 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set lightdatauuid
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdatauuid
     * @return ObjectiveLD
     */
    public function setLightdatauuid(\MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdatauuid = null)
    {
        $this->lightdatauuid = $lightdatauuid;

        return $this;
    }

    /**
     * Get lightdatauuid
     *
     * @return \MissionControl\Bundle\LightdataBundle\Entity\Lightdata 
     */
    public function getLightdatauuid()
    {
        return $this->lightdatauuid;
    }

    /**
     * Set lightdata
     *
     * @param \MissionControl\Bundle\LightdataBundle\Entity\Lightdata $lightdata
     * @return ObjectiveLD
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
