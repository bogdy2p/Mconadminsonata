<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Client {

    public function __construct() {


        $this->campaigns = new ArrayCollection();
        $this->divisions = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Expose
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Division", mappedBy="client", cascade={"persist"})
     */
    private $divisions;
//
//    /**
//     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Product", mappedBy="client", cascade={"persist"})
//     */
//    private $products;

//    /**
//     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Productline", mappedBy="client", cascade={"persist"})
//     */
//    private $productlines;

//    /**
//     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Brand", mappedBy="client", cascade={"persist"})
//     */
//    private $brands;

    /**
     * @var integer
     *
     * @ORM\Column(name="dbid", type="integer", nullable=true)
     */
    private $dbid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=FALSE, unique=TRUE)
     * @Expose
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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", mappedBy="client")
     */
    protected $campaigns;

   

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
     * @return Client
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
     * @return Client
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
     * @return Client
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
     * @return Client
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
     * Add divisions
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Division $divisions
     * @return Client
     */
    public function addDivision(\MissionControl\Bundle\CampaignBundle\Entity\Division $divisions)
    {
        $this->divisions[] = $divisions;

        return $this;
    }

    /**
     * Remove divisions
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Division $divisions
     */
    public function removeDivision(\MissionControl\Bundle\CampaignBundle\Entity\Division $divisions)
    {
        $this->divisions->removeElement($divisions);
    }

    /**
     * Get divisions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDivisions()
    {
        return $this->divisions;
    }

    /**
     * Add products
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Product $products
     * @return Client
     */
    public function addProduct(\MissionControl\Bundle\CampaignBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Product $products
     */
    public function removeProduct(\MissionControl\Bundle\CampaignBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add productlines
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Productline $productlines
     * @return Client
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

    /**
     * Add brands
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Brand $brands
     * @return Client
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
     * Add campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     * @return Client
     */
    public function addCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns)
    {
        $this->campaigns[] = $campaigns;

        return $this;
    }

    /**
     * Remove campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     */
    public function removeCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns)
    {
        $this->campaigns->removeElement($campaigns);
    }

    /**
     * Get campaigns
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }
}
