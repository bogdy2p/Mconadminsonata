<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Productline
 *
 * @ORM\Table(name="productline")
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Productline {

    public function __construct() {

        $this->campaigns = new ArrayCollection();
        $this->products = new ArrayCollection();
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

//    /**
//     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Client", inversedBy="productlines", cascade={"persist"})
//     * @ORM\JoinColumn(name="client_id", nullable=FALSE, referencedColumnName="id" )
//     */
//    private $client;

//    /**
//     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Division", inversedBy="productlines", cascade={"persist"})
//     * @ORM\JoinColumn(name="division_id", nullable=FALSE, referencedColumnName="id" )
//     */
//    private $division;

    /**
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Brand", inversedBy="productlines", cascade={"persist"})
     * @ORM\JoinColumn(name="brand_id", nullable=FALSE, referencedColumnName="id" )
     */
    private $brand;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Product", mappedBy="productline")
     */
    private $products;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", unique=FALSE, length=255)
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
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", mappedBy="productline")
     */
    protected $campaigns;

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
     * @return Productline
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Productline
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Productline
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Add campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     * @return Productline
     */
    public function addCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns) {
        $this->campaigns[] = $campaigns;

        return $this;
    }

    /**
     * Remove campaigns
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns
     */
    public function removeCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaigns) {
        $this->campaigns->removeElement($campaigns);
    }

    /**
     * Get campaigns
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampaigns() {
        return $this->campaigns;
    }

    /**
     * Set brand
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Brand $brand
     * @return Productline
     */
    public function setBrand(\MissionControl\Bundle\CampaignBundle\Entity\Brand $brand) {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Brand 
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Add products
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Product $products
     * @return Productline
     */
    public function addProduct(\MissionControl\Bundle\CampaignBundle\Entity\Product $products) {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Product $products
     */
    public function removeProduct(\MissionControl\Bundle\CampaignBundle\Entity\Product $products) {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * Set division
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Division $division
     * @return Productline
     */
    public function setDivision(\MissionControl\Bundle\CampaignBundle\Entity\Division $division) {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Division 
     */
    public function getDivision() {
        return $this->division;
    }

    /**
     * Set client
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Client $client
     * @return Productline
     */
    public function setClient(\MissionControl\Bundle\CampaignBundle\Entity\Client $client) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Client 
     */
    public function getClient() {
        return $this->client;
    }

}
