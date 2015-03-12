<?php

namespace MissionControl\Bundle\CampaignBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;

/**
 * Teammember
 *
 * @ORM\Table(name = "campaign_teammembers"),uniqueConstraints={@UniqueConstraint(name="teammember_unique", columns={"campaign", "member"})}
 * @ORM\Entity
 */
class Teammember {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Campaign
     * 
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\CampaignBundle\Entity\Campaign", inversedBy="campaigns")
     * @ORM\JoinColumn(name="campaign", referencedColumnName="id", nullable=false)     
     */
    private $campaign;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="MissionControl\Bundle\UserBundle\Entity\User", inversedBy="users")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false)     
     */
    private $member;

    /**
     * @var bool
     * 
     * @ORM\Column(name="is_reviewer", type="boolean", nullable=false)
     */
    private $is_reviewer;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set is_reviewer
     *
     * @param boolean $isReviewer
     * @return Teammember
     */
    public function setIsReviewer($isReviewer) {
        $this->is_reviewer = $isReviewer;

        return $this;
    }

    /**
     * Get is_reviewer
     *
     * @return boolean 
     */
    public function getIsReviewer() {
        return $this->is_reviewer;
    }

    /**
     * Set campaign
     *
     * @param \MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaign
     * @return Teammember
     */
    public function setCampaign(\MissionControl\Bundle\CampaignBundle\Entity\Campaign $campaign) {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \MissionControl\Bundle\CampaignBundle\Entity\Campaign 
     */
    public function getCampaign() {
        return $this->campaign;
    }

    /**
     * Set member
     *
     * @param \MissionControl\Bundle\UserBundle\Entity\User $member
     * @return Teammember
     */
    public function setMember(\MissionControl\Bundle\UserBundle\Entity\User $member) {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \MissionControl\Bundle\UserBundle\Entity\User 
     */
    public function getMember() {
        return $this->member;
    }

}
