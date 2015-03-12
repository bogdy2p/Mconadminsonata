<?php

namespace MissionControl\Bundle\TaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\Serializer\SerializationContext;
use MissionControl\Bundle\TaskBundle\Entity\Taskmessage;
use MissionControl\Bundle\TaskBundle\Entity\Taskstatus;
use MissionControl\Bundle\CampaignBundle\Entity\Teammember;

class TaskController extends FOSRestController {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

}
