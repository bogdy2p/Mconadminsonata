<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Division
 *
 * @ORM\Table(name="division")
 * @ORM\Entity
 */
class Division
{

    public function __construct() {

        $this->brands = new ArrayCollection();

    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Client", inversedBy="divisions", cascade={"persist"})
     * @ORM\JoinColumn(name="client_id", nullable=FALSE, referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Brand", mappedBy="division")
     */
    private $brands;
    
//      /**
//     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Productline", mappedBy="division")
//     */
//    private $productlines;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", unique=FALSE, length=255)
     */
    private $name;

     /**
     * @ORM\Column(type="datetime" , name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime" , name="updated_at", nullable=false)
     */
    protected $updatedAt;
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
     * @return Division
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Division
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Division
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Division
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set client
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Client $client
     * @return Division
     */
    public function setClient(\MissionControl\Bundle\CampaignBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Add brands
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Brand $brands
     * @return Division
     */
    public function addBrand(\MissionControl\Bundle\CampaignBundle\Entity\Brand $brands)
    {
        $this->brands[] = $brands;

        return $this;
    }

    /**
     * Remove brands
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Brand $brands
     */
    public function removeBrand(\MissionControl\Bundle\CampaignBundle\Entity\Brand $brands)
    {
        $this->brands->removeElement($brands);
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Add productlines
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Productline $productlines
     * @return Division
     */
    public function addProductline(\MissionControl\Bundle\CampaignBundle\Entity\Productline $productlines)
    {
        $this->productlines[] = $productlines;

        return $this;
    }

    /**
     * Remove productlines
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Productline $productlines
     */
    public function removeProductline(\MissionControl\Bundle\CampaignBundle\Entity\Productline $productlines)
    {
        $this->productlines->removeElement($productlines);
    }

    /**
     * Get productlines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductlines()
    {
        return $this->productlines;
    }
}
