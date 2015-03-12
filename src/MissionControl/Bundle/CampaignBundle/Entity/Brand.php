<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Brand
 *
 * @ORM\Table(name="brand")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Brand {

    public function __construct() {
        //parent::__construct();

        $this->campaigns = new ArrayCollection();
        $this->productlines = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Division", inversedBy="brands", cascade={"persist"})
     * @ORM\JoinColumn(name="division_id", nullable=FALSE, referencedColumnName="id")
     */
    private $division;

//    /**
//     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Client", inversedBy="brands", cascade={"persist"})
//     * @ORM\JoinColumn(name="client_id", nullable=FALSE, referencedColumnName="id")
//     */
//    private $client;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Productline", mappedBy="brand")
     */
    private $productlines;

//    /**
//     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Product", mappedBy="brand")
//     */
//    private $products;

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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", mappedBy="brand")
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
     * @return Brand
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
     * @return Brand
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
     * @return Brand
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
     * Set division
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Division $division
     * @return Brand
     */
    public function setDivision(\MissionControl\Bundle\CampaignBundle\Entity\Division $division)
    {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Division 
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set client
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Client $client
     * @return Brand
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
     * Add productlines
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Productline $productlines
     * @return Brand
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
     * Add products
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Product $products
     * @return Brand
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
     * Add campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     * @return Brand
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
