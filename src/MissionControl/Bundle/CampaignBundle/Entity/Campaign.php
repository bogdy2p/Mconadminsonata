<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MissionControl\Bundle\UserBundle\Entity\User;
use MissionControl\Bundle\CampaignBundle\Entity\Client;
// Relationship Mapping Metadata:
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
// For returning JSON content:
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Table(name = "campaign")
 *
 * @ORM\Entity(repositoryClass="MissionControl\Bundle\CampaignBundle\Entity\CampaignRepository")
 *
 * @ExclusionPolicy("all");
 */
class Campaign {

    /**
     * @var string
     * 
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id
     *
     * @Groups({"campaignInfo"})
     * @Expose
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Groups({"campaignInfo"})
     * @Expose
     */
    private $name;

    // THE CLIENT SHOULD BE AN ATTACHED OBJECT , CLIENT NAME MUST BE ABLE TO BE CHANGED INDEPENDENT OF THE CAMPAIGN.

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="campaigns")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * @Expose
     */
    protected $client;

    /**
     *  @var Country
     * 
     *  @ORM\ManyToOne(targetEntity="Country", inversedBy="campaigns")
     *  @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     *  @Expose
     */
    private $country;

    /**
     * @var Brand
     * 
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="campaigns")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     * @Expose
     */
    private $brand;

    /**
     *  @var Campaignstatus
     * 
     * @ORM\ManyToOne(targetEntity="Campaignstatus", inversedBy="campaigns")
     * @ORM\JoinColumn(name="campaignstatus", referencedColumnName="id", nullable=false)
     * @Expose
     */
    private $campaignstatus;

    /**
     *  @var Product
     * 
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="campaigns")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     * @Expose
     */
    private $product;

    /**
     *  @var Division
     * 
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="campaigns")
     * @ORM\JoinColumn(name="division_id", referencedColumnName="id", nullable=true)
     * @Expose
     */
    private $division;

    /**
     *  @var Productline
     * 
     * @ORM\ManyToOne(targetEntity="Productline", inversedBy="campaigns")
     * @ORM\JoinColumn(name="productline_id", referencedColumnName="id")
     * @Expose
     */
    private $productline;

    /**
     *  @var \DateTime
     * 
     *  @ORM\Column(name="completion_date", type="datetime", nullable=false)
     * @Expose
     */
    private $completion_date;

    /**
     *  @var \DateTime
     * 
     *  @ORM\Column(name="client_deliverabledate", type="datetime", nullable=false)
     * @Expose
     */
    private $client_deliverabledate;

    /**
     *  @var bool
     * 
     *  @ORM\Column(name="client_presentation", type="boolean", nullable=false)
     * @Expose
     */
    private $client_presentation;

     /**
     *  @var bool
     * 
     *  @ORM\Column(name="not_visible", type="boolean", nullable=false)
     * @Expose
     */
    private $not_visible;

    /**
     *  @var \DateTime
     * 
     *  @ORM\Column(name="start_date", type="datetime", nullable=true)
     * @Expose
     */
    private $start_date;

    /**
     *  @var \DateTime
     * 
     *  @ORM\Column(name="end_date", type="datetime", nullable=true)
     * @Expose
     */
    private $end_date;

    /**
     *  @var string
     * 
     *  @ORM\Column(name="real_lives_url", type="string", length=255, nullable=true)
     * @Expose
     */
    private $reallivesurl;

    /**
     *  @var string
     * 
     *  @ORM\Column(name="real_lives_password", type="string", length=255, nullable=true)
     * @Expose
     */
    private $reallivespassword;

    /**
     *  @var text
     * 
     *  @ORM\Column(name="campaign_idea", type="text", nullable=true)
     * @Expose
     */
    private $campaignidea;
    /**
     *  @var text
     * 
     *  @ORM\Column(name="campaign_idea_title", type="text", nullable=true)
     * @Expose
     */
    private $campaignideatitle;
    /**
     *  @var integer
     * 
     *  @ORM\Column(name="urgency", type="integer", nullable=true)
     * @Expose
     */
    private $urgency;

    /**
     *  @var integer
     * 
     *  @ORM\Column(name="completeness", type="integer", nullable=false)
     *
     */
    private $completeness;

    /**
     *  @var string
     * 
     *  @ORM\Column(name="budget", type="string", length=255, nullable=true)
     * @Expose
     */
    private $budget;

    /**
     *  @var string
     * 
     *  @ORM\Column(name="target", type="string", length=255, nullable=true)
     * @Expose
     */
    private $target;

    /**
     *  @var string
     * 
     *  @ORM\Column(name="matrixproject_id", type="string", length=36, nullable=true)
     * @Expose
     */
    private $matrixproject_id;

    /**
     *  @var text
     * 
     *  @ORM\Column(name="brief_outline", type="text", nullable=true)
     * @Expose
     */
    private $brief_outline;

    /**
     *  @var integer
     * 
     *  @ORM\Column(name="lightdata", type="integer", nullable=true)
     * @Expose
     */
    private $lightdata;

    /**
     * @var string
     *
     * 
     * @ORM\Column(name="lightdata_version", type="integer", nullable=true)
     * @Expose
     */
    protected $lightdataversion;

    /** @var float
     *
     * @ORM\Column(name="mmo_brandshare", type="float", precision=10, scale=0, nullable=true)
     */
    private $mmo_brandshare;

    /** @var float
     *
     * @ORM\Column(name="mmo_penetration", type="float", precision=10, scale=0, nullable=true)
     * @Expose
     */
    private $mmo_penetration;

    /** @var float
     *
     * @ORM\Column(name="mmo_salesgrowth", type="float", precision=10, scale=0, nullable=true)
     * @Expose
     */
    private $mmo_salesgrowth;

    /** @var float
     *
     * @ORM\Column(name="mmo_othermetric", type="float", precision=10, scale=0, nullable=true)
     * @Expose
     */
    private $mmo_othermetric;

    /** @var float
     *
     * @ORM\Column(name="mco_brandhealth_bhc", type="float", precision=10, scale=0, nullable=true)
     * @Expose
     */
    private $mco_brandhealth_bhc;

    /** @var float
     *
     * @ORM\Column(name="mco_awarenessincrease", type="float", precision=10, scale=0, nullable=true)
     * @Expose
     */
    private $mco_awarenessincrease;

    /** @var float
     *
     * @ORM\Column(name="mco_brandhealth_performance", type="float", precision=10, scale=0, nullable=true)
     * @Expose
     */
    private $mco_brandhealth_performance;

    /**
     * @var string
     * 
     *  @ORM\Column(name="matrixfile_uuid", type="string", length=36, nullable=true)
     * @Expose
     */
    private $matrixfile_uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=false, nullable=true)
     */
    private $matrixfile_version;

    /**
     * @var string
     * 
     *  @ORM\Column(name="token", type="string", length=36, nullable=true)
     */
    private $token;

    /**
     * @var string
     * 
     *  @ORM\Column(name="screen_type", type="string", length=36, nullable=true)
     */
    private $screentype;

    /**
     * @ORM\Column(type="datetime" , name="created_at")
     * @Expose
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime" , name="updated_at", nullable=false)
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\FileBundle\Entity\File", mappedBy="campaign")
     */
    protected $files;

    /**
     * @ORM\OneToMany(targetEntity="MissionControl\Bundle\TaskBundle\Entity\Task", mappedBy="campaign", cascade={"persist","remove"})
     */
    protected $tasks;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="campaigns")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Exclude
     */
    protected $user;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Campaign
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Campaign
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
     * Set completion_date
     *
     * @param \DateTime $completionDate
     * @return Campaign
     */
    public function setCompletionDate($completionDate)
    {
        $this->completion_date = $completionDate;

        return $this;
    }

    /**
     * Get completion_date
     *
     * @return \DateTime 
     */
    public function getCompletionDate()
    {
        return $this->completion_date;
    }

    /**
     * Set client_deliverabledate
     *
     * @param \DateTime $clientDeliverabledate
     * @return Campaign
     */
    public function setClientDeliverabledate($clientDeliverabledate)
    {
        $this->client_deliverabledate = $clientDeliverabledate;

        return $this;
    }

    /**
     * Get client_deliverabledate
     *
     * @return \DateTime 
     */
    public function getClientDeliverabledate()
    {
        return $this->client_deliverabledate;
    }

    /**
     * Set client_presentation
     *
     * @param boolean $clientPresentation
     * @return Campaign
     */
    public function setClientPresentation($clientPresentation)
    {
        $this->client_presentation = $clientPresentation;

        return $this;
    }

    /**
     * Get client_presentation
     *
     * @return boolean 
     */
    public function getClientPresentation()
    {
        return $this->client_presentation;
    }

    /**
     * Set not_visible
     *
     * @param boolean $notVisible
     * @return Campaign
     */
    public function setNotVisible($notVisible)
    {
        $this->not_visible = $notVisible;

        return $this;
    }

    /**
     * Get not_visible
     *
     * @return boolean 
     */
    public function getNotVisible()
    {
        return $this->not_visible;
    }

    /**
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return Campaign
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return Campaign
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set reallivesurl
     *
     * @param string $reallivesurl
     * @return Campaign
     */
    public function setReallivesurl($reallivesurl)
    {
        $this->reallivesurl = $reallivesurl;

        return $this;
    }

    /**
     * Get reallivesurl
     *
     * @return string 
     */
    public function getReallivesurl()
    {
        return $this->reallivesurl;
    }

    /**
     * Set reallivespassword
     *
     * @param string $reallivespassword
     * @return Campaign
     */
    public function setReallivespassword($reallivespassword)
    {
        $this->reallivespassword = $reallivespassword;

        return $this;
    }

    /**
     * Get reallivespassword
     *
     * @return string 
     */
    public function getReallivespassword()
    {
        return $this->reallivespassword;
    }

    /**
     * Set campaignidea
     *
     * @param string $campaignidea
     * @return Campaign
     */
    public function setCampaignidea($campaignidea)
    {
        $this->campaignidea = $campaignidea;

        return $this;
    }

    /**
     * Get campaignidea
     *
     * @return string 
     */
    public function getCampaignidea()
    {
        return $this->campaignidea;
    }

    /**
     * Set campaignideatitle
     *
     * @param string $campaignideatitle
     * @return Campaign
     */
    public function setCampaignideatitle($campaignideatitle)
    {
        $this->campaignideatitle = $campaignideatitle;

        return $this;
    }

    /**
     * Get campaignideatitle
     *
     * @return string 
     */
    public function getCampaignideatitle()
    {
        return $this->campaignideatitle;
    }

    /**
     * Set urgency
     *
     * @param integer $urgency
     * @return Campaign
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get urgency
     *
     * @return integer 
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * Set completeness
     *
     * @param integer $completeness
     * @return Campaign
     */
    public function setCompleteness($completeness)
    {
        $this->completeness = $completeness;

        return $this;
    }

    /**
     * Get completeness
     *
     * @return integer 
     */
    public function getCompleteness()
    {
        return $this->completeness;
    }

    /**
     * Set budget
     *
     * @param string $budget
     * @return Campaign
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return string 
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return Campaign
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set matrixproject_id
     *
     * @param string $matrixprojectId
     * @return Campaign
     */
    public function setMatrixprojectId($matrixprojectId)
    {
        $this->matrixproject_id = $matrixprojectId;

        return $this;
    }

    /**
     * Get matrixproject_id
     *
     * @return string 
     */
    public function getMatrixprojectId()
    {
        return $this->matrixproject_id;
    }

    /**
     * Set brief_outline
     *
     * @param string $briefOutline
     * @return Campaign
     */
    public function setBriefOutline($briefOutline)
    {
        $this->brief_outline = $briefOutline;

        return $this;
    }

    /**
     * Get brief_outline
     *
     * @return string 
     */
    public function getBriefOutline()
    {
        return $this->brief_outline;
    }

    /**
     * Set lightdata
     *
     * @param integer $lightdata
     * @return Campaign
     */
    public function setLightdata($lightdata)
    {
        $this->lightdata = $lightdata;

        return $this;
    }

    /**
     * Get lightdata
     *
     * @return integer 
     */
    public function getLightdata()
    {
        return $this->lightdata;
    }

    /**
     * Set lightdataversion
     *
     * @param integer $lightdataversion
     * @return Campaign
     */
    public function setLightdataversion($lightdataversion)
    {
        $this->lightdataversion = $lightdataversion;

        return $this;
    }

    /**
     * Get lightdataversion
     *
     * @return integer 
     */
    public function getLightdataversion()
    {
        return $this->lightdataversion;
    }

    /**
     * Set mmo_brandshare
     *
     * @param float $mmoBrandshare
     * @return Campaign
     */
    public function setMmoBrandshare($mmoBrandshare)
    {
        $this->mmo_brandshare = $mmoBrandshare;

        return $this;
    }

    /**
     * Get mmo_brandshare
     *
     * @return float 
     */
    public function getMmoBrandshare()
    {
        return $this->mmo_brandshare;
    }

    /**
     * Set mmo_penetration
     *
     * @param float $mmoPenetration
     * @return Campaign
     */
    public function setMmoPenetration($mmoPenetration)
    {
        $this->mmo_penetration = $mmoPenetration;

        return $this;
    }

    /**
     * Get mmo_penetration
     *
     * @return float 
     */
    public function getMmoPenetration()
    {
        return $this->mmo_penetration;
    }

    /**
     * Set mmo_salesgrowth
     *
     * @param float $mmoSalesgrowth
     * @return Campaign
     */
    public function setMmoSalesgrowth($mmoSalesgrowth)
    {
        $this->mmo_salesgrowth = $mmoSalesgrowth;

        return $this;
    }

    /**
     * Get mmo_salesgrowth
     *
     * @return float 
     */
    public function getMmoSalesgrowth()
    {
        return $this->mmo_salesgrowth;
    }

    /**
     * Set mmo_othermetric
     *
     * @param float $mmoOthermetric
     * @return Campaign
     */
    public function setMmoOthermetric($mmoOthermetric)
    {
        $this->mmo_othermetric = $mmoOthermetric;

        return $this;
    }

    /**
     * Get mmo_othermetric
     *
     * @return float 
     */
    public function getMmoOthermetric()
    {
        return $this->mmo_othermetric;
    }

    /**
     * Set mco_brandhealth_bhc
     *
     * @param float $mcoBrandhealthBhc
     * @return Campaign
     */
    public function setMcoBrandhealthBhc($mcoBrandhealthBhc)
    {
        $this->mco_brandhealth_bhc = $mcoBrandhealthBhc;

        return $this;
    }

    /**
     * Get mco_brandhealth_bhc
     *
     * @return float 
     */
    public function getMcoBrandhealthBhc()
    {
        return $this->mco_brandhealth_bhc;
    }

    /**
     * Set mco_awarenessincrease
     *
     * @param float $mcoAwarenessincrease
     * @return Campaign
     */
    public function setMcoAwarenessincrease($mcoAwarenessincrease)
    {
        $this->mco_awarenessincrease = $mcoAwarenessincrease;

        return $this;
    }

    /**
     * Get mco_awarenessincrease
     *
     * @return float 
     */
    public function getMcoAwarenessincrease()
    {
        return $this->mco_awarenessincrease;
    }

    /**
     * Set mco_brandhealth_performance
     *
     * @param float $mcoBrandhealthPerformance
     * @return Campaign
     */
    public function setMcoBrandhealthPerformance($mcoBrandhealthPerformance)
    {
        $this->mco_brandhealth_performance = $mcoBrandhealthPerformance;

        return $this;
    }

    /**
     * Get mco_brandhealth_performance
     *
     * @return float 
     */
    public function getMcoBrandhealthPerformance()
    {
        return $this->mco_brandhealth_performance;
    }

    /**
     * Set matrixfile_uuid
     *
     * @param string $matrixfileUuid
     * @return Campaign
     */
    public function setMatrixfileUuid($matrixfileUuid)
    {
        $this->matrixfile_uuid = $matrixfileUuid;

        return $this;
    }

    /**
     * Get matrixfile_uuid
     *
     * @return string 
     */
    public function getMatrixfileUuid()
    {
        return $this->matrixfile_uuid;
    }

    /**
     * Set matrixfile_version
     *
     * @param string $matrixfileVersion
     * @return Campaign
     */
    public function setMatrixfileVersion($matrixfileVersion)
    {
        $this->matrixfile_version = $matrixfileVersion;

        return $this;
    }

    /**
     * Get matrixfile_version
     *
     * @return string 
     */
    public function getMatrixfileVersion()
    {
        return $this->matrixfile_version;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Campaign
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set screentype
     *
     * @param string $screentype
     * @return Campaign
     */
    public function setScreentype($screentype)
    {
        $this->screentype = $screentype;

        return $this;
    }

    /**
     * Get screentype
     *
     * @return string 
     */
    public function getScreentype()
    {
        return $this->screentype;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Campaign
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
     * @return Campaign
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
     * @return Campaign
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
     * Set country
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Country $country
     * @return Campaign
     */
    public function setCountry(\MissionControl\Bundle\CampaignBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set brand
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Brand $brand
     * @return Campaign
     */
    public function setBrand(\MissionControl\Bundle\CampaignBundle\Entity\Brand $brand = null)
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
     * Set campaignstatus
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaignstatus $campaignstatus
     * @return Campaign
     */
    public function setCampaignstatus(\MissionControl\Bundle\CampaignBundle\Entity\Campaignstatus $campaignstatus)
    {
        $this->campaignstatus = $campaignstatus;

        return $this;
    }

    /**
     * Get campaignstatus
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Campaignstatus 
     */
    public function getCampaignstatus()
    {
        return $this->campaignstatus;
    }

    /**
     * Set product
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Product $product
     * @return Campaign
     */
    public function setProduct(\MissionControl\Bundle\CampaignBundle\Entity\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set division
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Division $division
     * @return Campaign
     */
    public function setDivision(\MissionControl\Bundle\CampaignBundle\Entity\Division $division = null)
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
     * Set productline
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Productline $productline
     * @return Campaign
     */
    public function setProductline(\MissionControl\Bundle\CampaignBundle\Entity\Productline $productline = null)
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
     * Add files
     *
     * @param \MissionControl\Bundle\FileBundle\Entity\File $files
     * @return Campaign
     */
    public function addFile(\MissionControl\Bundle\FileBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \MissionControl\Bundle\FileBundle\Entity\File $files
     */
    public function removeFile(\MissionControl\Bundle\FileBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add tasks
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $tasks
     * @return Campaign
     */
    public function addTask(\MissionControl\Bundle\TaskBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \MissionControl\Bundle\TaskBundle\Entity\Task $tasks
     */
    public function removeTask(\MissionControl\Bundle\TaskBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set user
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $user
     * @return Campaign
     */
    public function setUser(\MissionControl\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MissionControl\Bundle\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
