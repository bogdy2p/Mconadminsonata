<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Product {

    public function __construct() {
        //parent::__construct();

        $this->campaigns = new ArrayCollection();
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

//    /**
//     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Client", inversedBy="productlines", cascade={"persist"})
//     * @ORM\JoinColumn(name="client_id", nullable=FALSE, referencedColumnName="id" )
//     */
//    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Productline", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(name="productline_id", nullable=FALSE, referencedColumnName="id")
     */
    private $productline;

//    /**
//     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Brand", inversedBy="products", cascade={"persist"})
//     * @ORM\JoinColumn(name="brand_id", nullable=FALSE, referencedColumnName="id")
//     */
//    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=FALSE, unique=FALSE)
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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", mappedBy="product")
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
     * Set name
     *
     * @param string $name
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set client
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Client $client
     * @return Product
     */
    public function setClient(\MissionControl\Bundle\CampaignBundle\Entity\Client $client)
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
     * Set productline
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Productline $productline
     * @return Product
     */
    public function setProductline(\MissionControl\Bundle\CampaignBundle\Entity\Productline $productline)
    {
        $this->productline = $productline;

        return $this;
    }

    /**
     * Get productline
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Productline 
     */
    public function getProductline()
    {
        return $this->productline;
    }

    /**
     * Set brand
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Brand $brand
     * @return Product
     */
    public function setBrand(\MissionControl\Bundle\CampaignBundle\Entity\Brand $brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Brand 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Add campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     * @return Product
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
