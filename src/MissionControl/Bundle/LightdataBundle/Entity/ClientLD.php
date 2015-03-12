<?php

namespace MissionControl\Bundle\LightdataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * ClientLD
 *
 * @ORM\Table(name = "lightdata_client")
 * @ORM\Entity
 */
class ClientLD
{
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
     * @var integer
     *
     * @ORM\Column(name="dbid", type="integer",nullable=true)
     */
    private $dbid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable=true)
     */
    private $name;


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
     * Set dbid
     *
     * @param integer $dbid
     * @return ClientLD
     */
    public function setDbid($dbid)
    {
        $this->dbid = $dbid;

        return $this;
    }

    /**
     * Get dbid
     *
     * @return integer 
     */
    public function getDbid()
    {
        return $this->dbid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ClientLD
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
}
