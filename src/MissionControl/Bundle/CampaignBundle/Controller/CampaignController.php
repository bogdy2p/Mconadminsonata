<?php

namespace MissionControl\Bundle\CampaignBundle\Controller;

use MissionControl\Bundle\CampaignBundle\Model\FileType;
use MissionControl\Bundle\UserBundle\Entity\User;
use MissionControl\Bundle\TaskBundle\Entity\Task;
use MissionControl\Bundle\TaskBundle\Entity\Taskmessage;
use MissionControl\Bundle\TaskBundle\Entity\Taskstatus;
use MissionControl\Bundle\CampaignBundle\Entity\Teammember;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\CampaignBundle\Entity\Brand;
use MissionControl\Bundle\CampaignBundle\Entity\Client;
use MissionControl\Bundle\CampaignBundle\Entity\Country;
use MissionControl\Bundle\CampaignBundle\Entity\Product;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\File;
use JMS\Serializer\SerializationContext;

class CampaignController extends FOSRestController {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns an array of campaigns for the authenticated user (the campaign's unique id , and the campaign's name)",
     *    statusCodes = {
     *     200 = "Returned when the request is without errors",
     *     400 = "This call is only for administrators.",
     *     403 = "Invalid API KEY",
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {"name" = "_format","requirement" = "json|xml"}
     *    },
     *  parameters={
     *       {"name"="filter",  "dataType"="integer","required"=false,"description"="Default filter is 0 
     *       ( 0 = All Visible Related Campaigns, 1 = Where User Should Work On, 2 = Campaignstatus (Build,Approved) , 3 = Campaignstatus(Completed,Cancelled) , 4 = Disabled Campaigns (ADMIN Only) "},
     *       
     *       
     * }
     * 
     * )
     * @return array
     * @View()
     */
// *       {"name"="limit",   "dataType"="integer","required"=false,"description"="Default limit is 1000"},
//*       {"name"="offset",  "dataType"="integer","required"=false,"description"="Default offset is 0"},
    public function getCampaignsAction(Request $request) {

        //Instantiate response
        $response = new Response();
        $all_campaigns_ids_array = array();
        //Validate the user
        $user = $this->getUser();
        $current_date_object = new \DateTime();
        $filters = $request->get('filter') ? $request->get('filter') : 0;


        $array_of_campaign_ids = array();

        switch ($filters) {
            case 0: 
                //
                ////RETURN ALL THE CAMPAIGNS WHERE ALLOWED TO SEE. //If user is admin , he can see EVERY VISIBLE CAMPAIGN.
                //
                if ($user->hasRole('ROLE_ADMINISTRATOR')) {
                    $campaigns = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findAll();
                    foreach ($campaigns as $campaign) {
                        $array_of_campaign_ids[] = $campaign->getId();
                    }
                } else {
                    $campaign_ids = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findAll();
                    foreach ($campaign_ids as $campaign_id) {
                        $array_of_campaign_ids[] = $campaign_id;
                    }
                }
                break;

            case 1:
                //
                //RETURN ALL THE CAMPAIGNS WHERE USER NEEDS TO WORK ON. //Grab all campaigns where user is reviewer
                //
                $campaigns_where_user_is_reviewer = array();
                $teammembers = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')->findByMember($user);
                if ($teammembers) {

                    foreach ($teammembers as $teammember) {
                        if ($teammember->getIsReviewer()) {
                            $campaigns_where_user_is_reviewer[] = $teammember->getCampaign();
                        }
                    }

                    $campaign_ids_where_user_is_reviewer = array();
                    foreach ($campaigns_where_user_is_reviewer as $campaign_where_user_is_reviewer) {
                        if ($campaign_where_user_is_reviewer->getNotVisible() == false) {
                            $campaign_ids_where_user_is_reviewer[] = $campaign_where_user_is_reviewer->getId();
                        }
                    }
                    $tasks_where_the_user_is_owner = $this->getDoctrine()->getRepository('TaskBundle:Task')->findByOwner($user);
                    $campaign_ids_where_user_is_taskowner = array();

                    foreach ($tasks_where_the_user_is_owner as $task_where_user_is_owner) {
                        if ($task_where_user_is_owner->getCampaign()->getNotVisible() == false) {
                            $campaign_ids_where_user_is_taskowner[] = $task_where_user_is_owner->getCampaign()->getId();
                        }
                    }
                    //Merge the two arrays and select unique values 
                    $campaigns_where_reviewer_or_taskowner = array();
                    foreach ($campaign_ids_where_user_is_taskowner as $cidwuio) {
                        $campaigns_where_reviewer_or_taskowner[] = $cidwuio;
                    }
                    foreach ($campaign_ids_where_user_is_reviewer as $cidwuir) {
                        $campaigns_where_reviewer_or_taskowner[] = $cidwuir;
                    }
                    $unique_campaign_ids_for_this_filter = array_unique($campaigns_where_reviewer_or_taskowner);

                    $unique_campaign_ids_after_second_filter = array();
                    foreach ($unique_campaign_ids_for_this_filter as $campaign_id) {
                        $grabbed_campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaign_id);
                        if (($grabbed_campaign->getCampaignstatus()->getName() == "Build") || ($grabbed_campaign->getCampaignstatus()->getName() == "Approved")) {
                            $unique_campaign_ids_after_second_filter[] = $grabbed_campaign->getId();
                        }
                    }
                    //Return the final array
                    $count = count($unique_campaign_ids_after_second_filter);
                    $campaigns_to_display = $unique_campaign_ids_after_second_filter;
                    $array_of_campaign_ids = $campaigns_to_display;
                } else {
                    $array_of_campaign_ids = null;
                }
                break;
            case 2:
                //
                //THIS WILL ONLY DISPLAY THE VISIBLE CAMPAIGNS WHICH ARE IN OPEN STATE (BUILD/APPROVED)
                //
                if ($user->hasRole('ROLE_ADMINISTRATOR')) {
                    $campaigns = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findAll();
                    foreach ($campaigns as $campaign) {
                        $campaign_ids[] = $campaign->getId();
                    }
                } else {

                    $campaignids = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findAll();
                    foreach ($campaignids as $campaignid) {
                        $campaign_ids[] = $campaignid;
                    }
                }
                $array_of_campaign_ids = array();

                foreach ($campaign_ids as $campaign_id) {
                    $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaign_id);
                    if (($campaign->getCampaignstatus()->getName() == "Build") || ($campaign->getCampaignstatus()->getName() == "Approved")) {
                        $array_of_campaign_ids[] = $campaign_id;
                    }
                }
                $count = count($array_of_campaign_ids);

                break;
            case 3:
                //
                //THIS WILL ONLY DISPLAY THE VISIBLE CAMPAIGNS WHICH ARE IN CLOSED STATE
                //
                $campaign_ids = array();

                if ($user->hasRole('ROLE_ADMINISTRATOR')) {
                    $campaigns = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findAll();
                    foreach ($campaigns as $campaign) {
                        $campaign_ids[] = $campaign->getId();
                    }
                } else {
                    $campaignids = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findAll();
                    foreach ($campaignids as $campaignid) {
                        $campaign_ids[] = $campaignid;
                    }
                }
                $array_of_campaign_ids = array();
                foreach ($campaign_ids as $campaign_id) {
                    $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaign_id);
                    if (($campaign->getCampaignstatus()->getName() == "Complete") || ($campaign->getCampaignstatus()->getName() == "Cancelled")) {
                        $array_of_campaign_ids[] = $campaign_id;
                    }
                }
                $count = count($array_of_campaign_ids);
                break;

            case 4:
                //
                //ALL INVISIBLE CAMPAIGNS (ONLY DISPLAYABLE TO ADMINS)
                //
                if ($user->hasRole('ROLE_ADMINISTRATOR')) {
                    $campaigns = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findAllDisabled();
                    if ($campaigns) {
                        foreach ($campaigns as $campaign) {
                            $campaign_ids[] = $campaign['id'];
                        }
                    } else {
                        $response->setStatusCode(200);
                        $response->setContent(json_encode(array(
                            'success' => true,
                            'message' => 'There are no disabled campaigns.',
                                        )
                        ));
                        return $response;
                    }
                } else {
                    $response->setStatusCode(400);
                    $response->setContent(json_encode(array(
                        'success' => false,
                        'message' => 'This call is only for administrators.',
                                    )
                    ));
                    return $response;
                }

                $array_of_campaign_ids = $campaign_ids;
                $count = count($array_of_campaign_ids);
                break;

            default:
                //Return response for not availlable filter.
                
                $response->setStatusCode(200);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'There is no such filter.',
                                )
                ));
                return $response;
                break;
        }

        if (count($array_of_campaign_ids) == 0) {
            $response->setStatusCode(200);
            $response->setContent(json_encode(array(
                'Total campaigns for this filter' => 0,
                'Campaigns' => [],
                            )
            ));
            return $response;
        }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////// For each campaign fetched , validate the user is allowed to see the data about it.
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $campaign_ids = array();
        foreach ($array_of_campaign_ids as $campaign_id) {
            $campaign_ids[] = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaign_id);
        }
        $campaigns_array = array();

        foreach ($campaign_ids as $campaign) {
            
            //Call Validation Function
            $validated_to_display = self::validate_user_is_able_to_view_this_campaign($user, $campaign);

            if ($validated_to_display) {
                //Calculate campaign urgency:
                $deliverabledate = $campaign->getClientdeliverabledate()->getTimestamp();
                $current_date = $current_date_object->getTimestamp();
                $urgency = floor(($deliverabledate / (60 * 60 * 24)) - ($current_date / (60 * 60 * 24)));
                
                $tasks = $campaign->getTasks();

                $campaigns_array[] = array(
                    'CampaignID' => $campaign->getId(),
                    'CampaignName' => $campaign->getName(),
                    'ClientName' => $campaign->getClient()->getName(),
                    'Country' => $campaign->getCountry()->getName(),
                    'Brand' => $campaign->getBrand()->getName(),
                    'Product' => $campaign->getProduct()->getName(),
                    'Productline' => $campaign->getProductline()->getName(),
                    'Division' => $campaign->getDivision()->getName(),
                    'Completeness' => $campaign->getCompleteness(),
                    'Urgency' => $urgency,
                    'CampaignStatus' => $campaign->getCampaignstatus() ? $campaign->getCampaignstatus()->getName() : null,
                    'CompletionDate' => date('Y-m-d', $campaign->getCompletionDate()->getTimestamp()),
                    'CampaignLastModifiedDate' => date('Y-m-d', $campaign->getUpdatedAt()->getTimestamp()),
                    'ClientDeliverabledate' => date('Y-m-d', $campaign->getClientDeliverabledate()->getTimestamp()),
                    'PresentedToClient' => $campaign->getClientpresentation(),
                    'Token' => $campaign->getToken(),
                    'Screentype' => $campaign->getScreenType(),
                    'Brief_outline' => $campaign->getBriefOutline() ? $campaign->getBriefOutline() : null,
                    'MMO_brandshare' => $campaign->getMmoBrandshare() ? $campaign->getMmoBrandshare() : 0,
                    'MMO_penetration' => $campaign->getMmoPenetration() ? $campaign->getMmoPenetration() : 0,
                    'MMO_salesgrowth' => $campaign->getMmoSalesgrowth() ? $campaign->getMmoSalesgrowth() : 0,
                    'MMO_othermetric' => $campaign->getMmoOthermetric() ? $campaign->getMmoOthermetric() : 0,
                    'MMO_brandhealth_bhc' => $campaign->getMcoBrandhealthBhc() ? $campaign->getMcoBrandhealthBhc() : 0,
                    'MMO_awareness_increase' => $campaign->getMcoAwarenessincrease() ? $campaign->getMcoAwarenessincrease() : 0,
                    'MMO_brandhealth_performance' => $campaign->getMcoBrandhealthPerformance() ? $campaign->getMcoBrandhealthPerformance() : 0,
                    'not_visible' => $campaign->getNotvisible() ? true : false,
                    'Campaign_Start_Date' => $campaign->getStartDate() ? date('Y-m-d', $campaign->getStartDate()->getTimestamp()) : null,
                );
            } // End of campaigns foreach().
        }
        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'Total campaigns for this filter' => count($campaigns_array),
            'Campaigns' => $campaigns_array,
                        )
        ));

        return $response;
    }

// End of GET information for user campaigns method().

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns a campaign's tasks data and reviewers based on [campaignId]",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Campaign does not exist.",
     *         "Not allowed to view this campaign."
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignAction($campaignId) {
        $user = $this->getUser();
        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneBy(['id' => $campaignId, 'not_visible' => false]);


        if (!$campaign) {

            // Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

        $validated_that_user_is_able_to_view_this_campaign = self::validate_user_is_able_to_view_this_campaign($user, $campaign);
        if (!$validated_that_user_is_able_to_view_this_campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Not allowed to view this campaign.'
                    ))
            );
            return $response;
        }


        $tasks = $this->getDoctrine()->getRepository('TaskBundle:Task')->findByCampaign($campaignId);
        //Calculate campaign urgency
        $current_date_object = new \DateTime();
        $deliverabledate = $campaign->getClientdeliverabledate()->getTimestamp();
        $current_date = $current_date_object->getTimestamp();
        $urgency = floor(($deliverabledate / (60 * 60 * 24)) - ($current_date / (60 * 60 * 24)));



        //FROM LIGHTDATA ASSIGNED TO THIS CAMPAIGN
        $lightdata_id = $campaign->getLightdata();
        //at first we suppose there is no lightdata.
        $lightdata = null;
        if ($lightdata_id != null) {
            $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
        }

        //if lightdata exists for this campaign , fetch the values needed from it. Else , set all the values needed from it to null.
        if ($lightdata) {

            $campaign_start_date_timestamp = $lightdata->getSetup()->getStartdate()->getTimestamp();
            $campaign_start_date = date('Y-m-d', $campaign_start_date_timestamp);
            $campaign_nb_periods = $lightdata->getSetup()->getNbperiods();
            /////////////////////////

            $calculated_end_date_timestamp = $campaign_start_date_timestamp + ($campaign_nb_periods * 7 * 24 * 60 * 60);
            /////////////////////////
            $campaign_end_date = date('Y-m-d', $calculated_end_date_timestamp);
            $campaign_survey = $lightdata->getSetup()->getSurvey()->getName();
            $campaign_target = $lightdata->getSetup()->getTarget()->getName();
            $campaign_budget = $lightdata->getSetup()->getBudget();
            $campaign_currency = $lightdata->getSetup()->getBudgetCurrency();
        } else {
            $campaign_start_date = null;
            $campaign_end_date = null;
            $campaign_survey = null;
            $campaign_target = null;
            $campaign_budget = null;
            $campaign_currency = null;
        }

        $region = $campaign->getCountry()->getRegion()->getName();

        $campaignFields = $this->campaignFieldsToArrayAction($campaign->getId());
        $campaign_data_array = $campaignFields + array(
            'CampaignID' => $campaign->getId(),
            'CampaignName' => $campaign->getName(),
            'ClientName' => $campaign->getClient()->getName(),
            'Country' => $campaign->getCountry()->getName(),
            'Region' => $region,
            'Brand' => $campaign->getBrand()->getName(),
            'Product' => $campaign->getProduct()->getName(),
            'Productline' => $campaign->getProductline()->getName(),
            'Division' => $campaign->getDivision()->getName(),
            'Completeness' => $campaign->getCompleteness(),
            'Urgency' => $urgency,
            'Campaign_Start_Date' => $campaign->getStartDate() ? date('Y-m-d', $campaign->getStartDate()->getTimestamp()) : null,
            'Campaign_End_Date' => $campaign_end_date,
            'Survey' => $campaign_survey,
            'Target' => $campaign_target,
            'Budget' => $campaign_budget,
            'Currency' => $campaign_currency,
            'CampaignStatus' => $campaign->getCampaignstatus() ? $campaign->getCampaignstatus()->getName() : null,
            'CompletionDate' => date('Y-m-d', $campaign->getCompletionDate()->getTimestamp()),
            'CampaignLastModifiedDate' => date('Y-m-d', $campaign->getUpdatedAt()->getTimestamp()),
            'ClientDeliverabledate' => date('Y-m-d', $campaign->getClientDeliverabledate()->getTimestamp()),
            'PresentedToClient' => $campaign->getClientpresentation(),
            'Token' => $campaign->getToken(),
            'Screentype' => $campaign->getScreenType(),
            'Brief_outline' => $campaign->getBriefOutline() ? $campaign->getBriefOutline() : null,
            'MMO_brandshare' => $campaign->getMmoBrandshare() ? $campaign->getMmoBrandshare() : 0,
            'MMO_penetration' => $campaign->getMmoPenetration() ? $campaign->getMmoPenetration() : 0,
            'MMO_salesgrowth' => $campaign->getMmoSalesgrowth() ? $campaign->getMmoSalesgrowth() : 0,
            'MMO_othermetric' => $campaign->getMmoOthermetric() ? $campaign->getMmoOthermetric() : 0,
            'MMO_brandhealth_bhc' => $campaign->getMcoBrandhealthBhc() ? $campaign->getMcoBrandhealthBhc() : 0,
            'MMO_awareness_increase' => $campaign->getMcoAwarenessincrease() ? $campaign->getMcoAwarenessincrease() : 0,
            'MMO_brandhealth_performance' => $campaign->getMcoBrandhealthPerformance() ? $campaign->getMcoBrandhealthPerformance() : 0,
            'not_visible' => $campaign->getNotvisible() ? true : false,
        );

        $response = new Response();
        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'Campaign' => $campaign_data_array
                        )
        ));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "c [taskId]",
     *    statusCodes = {
     *     200 = "Returned when the task was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *         "Returned When the task was not found in database",
     *         "Returned When the task does not belong to the specified campaign",
     *         "Returned when the user does not have access to the task"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {"name"="taskId",     "dataType"="integer","requirement"="true", "description"="The task unique id"     },
     *       {"name"="campaignId", "dataType"="integer","requirement"="true", "description"="The campaign unique id" },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignsTaskAction($campaignId, $taskId) {

        $response = new Response();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
//IF NO CAMPAIGN , THROW ERROR.
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid campaign id.'
            )));
            return $response;
        }
        $task = $this->getDoctrine()->getRepository('TaskBundle:Task')->find($taskId);
        if (!$task) {
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid task id.'
                    ))
            );
            return $response;
        }
//Grab all the tasks for this campaign
        $all_tasks_for_this_campaign = $this->getDoctrine()->getRepository('TaskBundle:Task')->findByCampaign($campaign);
// if TASK DOES NOT BELONG TO THIS CAMPAIGN , THROW ERROR
        if (!in_array($task, $all_tasks_for_this_campaign)) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid campaign and task combination.'
            )));
            return $response;
        }


// If matrix file exists, retrieve information for it:
        $matrixFile = $campaign->getMatrixfileUuid() ?
                $this->getDoctrine()->getRepository('FileBundle:File')->find($campaign->getMatrixfileUuid()) :
                null;


        $return_array = array(
            'TaskID' => $task->getId(),
            'TaskName' => $task->getTaskname()->getName(),
            'TaskOwnerUserID' => $task->getOwner() ? $task->getOwner()->getId() : null,
            'TaskOwnerFirstName' => $task->getOwner() ? $task->getOwner()->getFirstname() : null,
            'TaskOwnerLastName' => $task->getOwner() ? $task->getOwner()->getLastname() : null,
            'TaskOwnerTitle' => $task->getOwner() ? $task->getOwner()->getTitle() : null,
            'TaskOwnerOffice' => $task->getOwner() ? $task->getOwner()->getOffice() : null,
            'TaskOwnerEmailAddress' => $task->getOwner() ? $task->getOwner()->getEmail() : null,
            'TaskOwnerPhone' => $task->getOwner() ? $task->getOwner()->getPhone() : null,
            'TaskOwnerProfilePictureLocation' => $task->getOwner() ? $task->getOwner()->getProfilepicture() : null,
            'LatestTaskStatus' => $task->getTaskstatus()->getName(),
            'LatestTaskMessage' => $task->getTaskmessage() ? $task->getTaskmessage()->getMessage() : null,
            'LatestTaskStatusDate' => $task->getTaskstatusdate() ? date('Y-m-dTH:i:s', $task->getTaskstatusdate()->getTimestamp()) : null,
            'MatrixFileVersion' => $matrixFile ? $matrixFile->getVersion() : null,
            'MatrixVersionDate' => $matrixFile ? date('Y-m-dTH:i:s', $matrixFile->getUpdatedAt()->getTimestamp()) : null,
            'MatrixVersionBy' => ( $matrixFile and $matrixFile->getUser() ) ? $matrixFile->getUser()->getFirstName() . ' ' . $matrixFile->getUser()->getLastname() : null
        );

        $response->setStatusCode(200);
        $response->setContent(json_encode($return_array));
        return $response;
    }

// End of retrieve task information method().

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns campaign tasks information.",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned When the campaign was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignTasksAction($campaignId) {

        $response = new Response();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
        $tasks = $this->getDoctrine()->getRepository('TaskBundle:Task')->findByCampaign($campaign);
// If matrix file exists, retrieve information for it:
        $matrixFile = $campaign->getMatrixfileUuid() ?
                $this->getDoctrine()->getRepository('FileBundle:File')->find($campaign->getMatrixfileUuid()) :
                null;

        if (!$campaign) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

        foreach ($tasks as $task) {

            $return_array[] = array(
                'TaskID' => $task->getId(),
                'TaskName' => $task->getTaskname()->getName(),
                'TaskOwnerUserID' => $task->getOwner() ? $task->getOwner()->getId() : null,
                'TaskOwnerFirstName' => $task->getOwner() ? $task->getOwner()->getFirstname() : null,
                'TaskOwnerLastName' => $task->getOwner() ? $task->getOwner()->getLastname() : null,
                'TaskOwnerTitle' => $task->getOwner() ? $task->getOwner()->getTitle() : null,
                'TaskOwnerOffice' => $task->getOwner() ? $task->getOwner()->getOffice() : null,
                'TaskOwnerEmailAddress' => $task->getOwner() ? $task->getOwner()->getEmail() : null,
                'TaskOwnerPhone' => $task->getOwner() ? $task->getOwner()->getPhone() : null,
                'TaskOwnerProfilePictureLocation' => $task->getOwner() ? $task->getOwner()->getProfilepicture() : null,
                'LatestTaskStatus' => $task->getTaskstatus()->getName(),
                'LatestTaskMessage' => $task->getTaskmessage() ? $task->getTaskmessage()->getMessage() : null,
                'LatestTaskStatusDate' => $task->getTaskstatusdate() ? date('Y-m-dTH:i:s', $task->getTaskstatusdate()->getTimestamp()) : null,
                'MatrixFileVersion' => $matrixFile ? $matrixFile->getVersion() : null,
                'MatrixVersionDate' => $matrixFile ? date('Y-m-dTH:i:s', $matrixFile->getUpdatedAt()->getTimestamp()) : null,
                'MatrixVersionBy' => ( $matrixFile and $matrixFile->getUser() ) ? $matrixFile->getUser()->getFirstName() . ' ' . $matrixFile->getUser()->getLastname() : null
            );
        } // End of tasks foreach() loop.

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'Tasks' => $return_array
                        )
        ));

        return $response;
    }

// End of GET information for all tasks of a campaign method().

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Insert a new campaign in the database",
     *    statusCodes = {
     *     201 = "Returned when the campaign was added to the database",
     *     400 = "Returned when the validation returns false ",
     *     403 = {"Invalid API KEY", "Incorrect combination of request inputs."},
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {"name"="_format",               "dataType"="string","requirement"="json|xml","description"="Format"},
     *    },
     *    parameters={
     *       {"name"="name",                  "dataType"="text",  "required"=true, "description"="The campaign name"},
     *       {"name"="client",                "dataType"="string","required"=true,"description"="The campaign client."},
     *       {"name"="brand",                 "dataType"="string","required"=true,"description"="The campaign brand."},
     *       {"name"="product",               "dataType"="string","required"=true,"description"="The campaign product."},
     *       {"name"="division",              "dataType"="string","required"=true,"description"="The campaign division."},
     *       {"name"="productline",           "dataType"="string","required"=true,"description"="The campaign productline."},
     *       {"name"="country",               "dataType"="string","required"=true,"description"="The campaign country."},
     *       {"name"="completion_date",       "dataType"="string","required"=true,"description"="The campaign completion date."},
     *       {"name"="client_deliverabledate","dataType"="string","required"=true,"description"="The campaign deliverable date."},
     * }
     * )
     * return string
     * @View()
     */
    public function postCampaignAction(Request $request) {
        $user = $this->getUser();

        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());

        $em = $this->getDoctrine()->getManager();
        $key = Uuid::uuid4()->toString();
        $token_key = Uuid::uuid4()->toString();
        $client_id = $request->get('client');
        $country_id = $request->get('country');
        $brand_id = $request->get('brand');
        $product_id = $request->get('product');
        $productline_id = $request->get('productline');
        $division_id = $request->get('division');
        $response = new Response();
/////////////////////////////////////////////////////////////////////////////////////
// Checks to verify object's existence into the database.
/////////////////////////////////////////////////////////////////////////////////////
        $client = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findOneById($client_id);
        if (!$client) {

            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Invalid ID provided for field client.')));
            return $response;
        }
        $division = $this->getDoctrine()->getRepository('CampaignBundle:Division')->findOneById($division_id);
        if (!$division) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Invalid ID provided for field division.')));
            return $response;
        }
        $brand = $this->getDoctrine()->getRepository('CampaignBundle:Brand')->findOneById($brand_id);
        if (!$brand) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Invalid ID provided for field brand.')));
            return $response;
        }
        $productline = $this->getDoctrine()->getRepository('CampaignBundle:Productline')->findOneById($productline_id);
        if (!$productline) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Invalid ID provided for field productline.')));
            return $response;
        }
        $product = $this->getDoctrine()->getRepository('CampaignBundle:Product')->findOneById($product_id);
        if (!$product) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Invalid ID provided for field product.')));
            return $response;
        }
        $country = $this->getDoctrine()->getRepository('CampaignBundle:Country')->findOneById($country_id);
        if (!$country) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Invalid ID provided for field country .')));
            return $response;
        }




//DISABLED VALIDATION HERE // THE CLIENT WANTS TO BE ABLE TO CREATE DUPLICATE CAMPAIGNS IN SELECT CASES , SO THEY WILL BE RESPONSIBLE FOR MONITORING THE DUPLICATES MANUALLY        
//        ///VERIFY THAT THERE IN'T ALREADY A CAMPAIGN CREATED BY THIS USER , USING THE SPECIFIED NAME.
//
//        $campaing_already_exists_for_creator_name_combo = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneBy([
//            'user' => $user,
//            'name' => $request->get('name')]);
//
//
//        if ($campaing_already_exists_for_creator_name_combo) {
//            $response->setStatusCode(403);
//            $response->setContent(json_encode(array('success' => false, 'message' => 'You already have a campaign that uses that campaign name. Please choose another one!')));
//            return $response;
//        }
//        /// End of newly added validation.
////////
/////////////////////////////////////////////////////////////////////////////////////
// END Checks to verify object's existence into the database.
////////////////////////////////////////////////////////////////////////////////////
////RELATIONAL CHECKS
////RELATIONAL CHECKS
////////////////////////////////////////////////////
// Client should have the respective division
// Division should have the respective brand
// Brand should have the respective productline
// Productline should have the respective product
//////////////////////////////////////////////////////////////////
//////////////////////
//Validate that the division specified belongs to the client specified.
//////////////////////
        if (!($division->getClient()->getId() == $client->getId())) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Division does not belong to this Client.')));
            return $response;
        }
//////////////////////
//Validate that the brand specified belongs to the division specified.
//////////////////////
        if (!($brand->getDivision()->getId() == $division->getId())) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Brand does not belong to this Division.')));
            return $response;
        }
//////////////////////
//Validate that the productline specified belongs to the brand specified.
//////////////////////
        if (!($productline->getBrand()->getId() == $brand->getId())) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Productline does not belong to this Brand.')));
            return $response;
        }
////////////////////////
//Validate that the product specified belongs to the productline specified.
//////////////////////
        if (!($product->getProductline()->getId() == $productline->getId())) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Product does not belong to this Productline.')));
            return $response;
        }

//////////////////////////////
//END RELATIONAL CHECKS
//////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
/////////////////////END OF CHECKS
////////////////////////////////////////////////////////////////////////////////////

        if (empty($request->get('completion_date'))) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'The completion_date field is required !'
            )));

            return $response;
        }
        if (empty($request->get('client_deliverabledate'))) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'The client_deliverabledate field is required !'
            )));

            return $response;
        }


        $completion_date_input = $request->get('completion_date');
// Inputs completion and deliverable dates:
        if ($completion_date_input) {
            $completion_date = new \DateTime($request->get('completion_date'));
            $completion_date->setTimezone(self::timezoneUTC());
        }

        $deliverable_date_input = $request->get('client_deliverabledate');

        if ($deliverable_date_input) {
            $deliverable_date = new \DateTime($request->get('client_deliverabledate'));
            $deliverable_date->setTimezone(self::timezoneUTC());
        }



//VALIDATE THAT THE COMPLETION DATE IS LATER THAN THE CLIENT_DELIVERABLEDATE
        if ($completion_date && $deliverable_date) {
            $seconds_in_one_day = 60 * 60 * 24;
            $ts_completion = $completion_date->getTimestamp();
            $ts_deliverable = $deliverable_date->getTimestamp();
            $difference = $ts_completion - $ts_deliverable;
            if ($difference < $seconds_in_one_day) {
                $response->setStatusCode(400);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'The Completion Date must be later than the Client Deliverable Date. (1 day minimum)'
                )));

                return $response;
            }
        }
//ERROR MESSAGE : The Completion Date must be later than the Client Deliverable Date.


        $campaign_status = $this->getDoctrine()->getRepository('CampaignBundle:Campaignstatus')->find(1);

// Populate the Campaign object with data from the Request:
        $campaign = new Campaign();
        $campaign->setId($key);
        $campaign->setUser($user);
//$campaign->setBriefOutline('This is the campaigns bief outline text. hardcoded.');
        $campaign->setClientPresentation(false);
        $campaign->setCompleteness(0);
        $campaign->setName($request->get('name'));
        $campaign->setClient($client);
        $campaign->setBrand($brand);
        $campaign->setProduct($product);
        $campaign->setProductline($productline);
        $campaign->setDivision($division);
        $campaign->setCountry($country);
        $campaign->setCampaignstatus($campaign_status);
        $campaign->setCompletionDate($completion_date);
        $campaign->setClientDeliverabledate($deliverable_date);
        $campaign->setToken($token_key);
        $campaign->setNotVisible(false);
        $campaign->setScreentype('10000');

// Set time for when the file was created:

        $campaign->setCreatedAt($creationDate);
        $campaign->setUpdatedAt($creationDate);


// Get validator service to check for errors:
        $validator = $this->get('validator');
        $errors = $validator->validate($campaign);

// Create and prepare the Response object to be sent back to client:
        $response = new Response();

        if (count($errors) > 0) {

// Return $errors in JSON format:
            $view = $this->view($errors, 400);
            return $this->handleView($view);
        }

// If no errors were found, instantiate entity_manager to begin.

        $em->persist($campaign);
/////////////////////////////////////////////////////
//Add the user who created the campaign to the campaign's team.
/////////////////////////////////////////////////////

        $add_as_teammember = new Teammember();
        $add_as_teammember->setCampaign($campaign);
        $add_as_teammember->setMember($user);
        $add_as_teammember->setIsReviewer(false);
        $em->persist($add_as_teammember);


//////////////////////////////////////////////////////
///        
/////////////////////////////////////////////////////
//Create the set of tasks for this campaign
/////////////////////////////////////////////////////

        $campaign_unique_id = $campaign->getId();

        $task_types = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findAll();

        $default_task_status = $this->getDoctrine()->getRepository('TaskBundle:Taskstatus')->find(1);

        foreach ($task_types as $tasktype) {

            $new_task = new Task();
            $new_task->setCampaign($campaign);
            $new_task->setTaskname($tasktype);
            $new_task->setOwner($user);
            $new_task->setTaskmessage(NULL);
            $new_task->setMatrixfileversion(0);
            $new_task->setTaskstatus($default_task_status);
            $new_task->setPhase($tasktype->getPhaseid());
            $new_task->setCreatedAt($creationDate);
            $new_task->setCreatedby($user);
            $new_task->setUpdatedAt($creationDate);
            $em->persist($new_task);
        }

//////////////////////////////////////////////////////
///

        $em->flush();

        $response->setStatusCode(201);
        $response->setContent(json_encode(array(
            'success' => true,
            'campaignID' => $campaign->getId(),
                ))
        );

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Update a campaign based on [campaignId]",
     *    statusCodes = {
     *     201 = "Returned when the campaign was updated.",
     *     400 = {
     *      "Returned when the validation returns false.",
     *     },
     *     403 = {"Invalid API KEY",
     *            "Returned when the user does not have access to update the campaign."  
     *     },
     *     404 = "No campaign exists for that ID.",
     *     500 = "Header x-wsse does not exist."
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign's unique identifier",
     *           "requirement" = "True"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    },
     * parameters={
     *       {"name"="name",                  "dataType"="text",    "required"=true,"description"="The campaign's new name"         },
     *       {"name"="client",                "dataType"="integer", "required"=true,"description"="The campaign client id."         },
     *       {"name"="brand",                 "dataType"="integer", "required"=true,"description"="The campaign brand id."          },
     *       {"name"="product",               "dataType"="integer", "required"=true,"description"="The campaign product id."        },
     *       {"name"="division",              "dataType"="integer", "required"=true,"description"="The campaign division id."       },
     *       {"name"="productline",           "dataType"="integer", "required"=true,"description"="The campaign productline id."    },
     *       {"name"="country",               "dataType"="integer", "required"=true,"description"="The campaign country id."        },
     *       {"name"="completion_date",       "dataType"="date",    "required"=true,"description"="The campaign completion date."   },
     *       {"name"="client_deliverabledate","dataType"="date",    "required"=true,"description"="The campaign deliverable date."  },
     *      {"name"="campaign_status",        "dataType"="integer", "required"=true,"description"="Campaign status (1 to 4)"        },
     *      {"name"="already_presented",      "dataType"="boolean", "required"=true,"description"="Boolean (1 = true , 0 = false)"  },
     * }
     * )
     * @return array
     * @View()
     */
    public function putCampaignAction($campaignId, Request $request) {
        $user = $this->getUser();
        $response = new Response();
        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());

// Return response error if the campaignId is empty
        if ($campaignId == NULL) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Missing campaignId.'
            )));
            return $response;
        }

// Check if the campaign exists for the specified campaign ID
        $campaign_exists = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

        if (!$campaign_exists) {
            $response = new Response();
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'No campaign exists for that id'
                    ))
            );
            return $response;
        }


// Check if the current user has access to the content / is the creator of the campaign
        $campaign = $campaign_exists;

        if ($user->hasRole('ROLE_VIEWER')) {
            $response = new Response();
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This authenticated user does not have access to update this campaign.'
                    ))
            );
            return $response;
        }

//        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')
//                ->findOneBy(['user' => $user, 'id' => $campaignId]);
//// Check if the campaign& exists
//        if (!$campaign) {
//            $response = new Response();
//            $response->setStatusCode(403);
//            $response->setContent(json_encode(array(
//                'success' => false,
//                'message' => 'This authenticated user does not have access to update this campaign.'
//                    ))
//            );
//            return $response;
//        }
//        



        $input_validated = TRUE;
        $required_fields = array(
            'name',
            'client',
            'brand',
            'product',
            'productline',
            'division',
            'country',
            'already_presented',
            'completion_date',
            'client_deliverabledate',
            'campaign_status'
        );

        $specialerrors = array();
        foreach ($required_fields as $field) {
            if ($request->get($field) == NULL) {
                $input_validated = FALSE;
                $specialerrors[] = $field;
            }
        }

//Check the input information has been set
        if ($input_validated) {

            $client = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findOneById($request->get('client'));
            $brand = $this->getDoctrine()->getRepository('CampaignBundle:Brand')->findOneById($request->get('brand'));
            $product = $this->getDoctrine()->getRepository('CampaignBundle:Product')->findOneById($request->get('product'));
            $productline = $this->getDoctrine()->getRepository('CampaignBundle:Productline')->findOneById($request->get('productline'));
            $country = $this->getDoctrine()->getRepository('CampaignBundle:Country')->findOneById($request->get('country'));
            $division = $this->getDoctrine()->getRepository('CampaignBundle:Division')->findOneById($request->get('division'));
            $campaignstatus = $this->getDoctrine()->getRepository('CampaignBundle:Campaignstatus')->findOneById($request->get('campaign_status'));

            $campaign->setName($request->get('name'));
            $campaign->setClientpresentation($request->get('already_presented'));

            $campaign->setClient($client);
            $campaign->setBrand($brand);
            $campaign->setProduct($product);
            $campaign->setProductline($productline);
            $campaign->setDivision($division);
            $campaign->setCountry($country);
            $campaign->setCampaignstatus($campaignstatus);
            $campaign->setUpdatedAt($updateDate);


//Set the new campaign dates
            $new_comp_Date = new \DateTime($request->get('completion_date'));
            $new_comp_Date->setTimezone(self::timezoneUTC());
            $campaign->setCompletionDate($new_comp_Date);
            $new_deliv_date = new \DateTime($request->get('client_deliverabledate'));
            $new_deliv_date->setTimezone(self::timezoneUTC());
            $campaign->setClientDeliverabledate($new_deliv_date);
        } else {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Data input failed validation.',
                'errors_on' => $specialerrors
            )));
            return $response;
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($campaign);
        $em->flush();

        $response->setStatusCode(201);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'Campaign updated.'
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Update a campaign's status based on [campaignId]",
     *    statusCodes = {
     *     200 = "Returned when the campaign was updated.",
     *     403 = "Invalid API KEY",
     *     404 = "Invalid request inputs.",
     *     500 = "Header x-wsse does not exist."
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign's unique identifier",
     *           "requirement" = "True"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    },
     * parameters={
     *      {"name"="campaign_status",        "dataType"="integer", "required"=true,"description"="Campaign status (1 to 4)"        },
     * }
     * )
     * @return array
     * @View()
     */
    public function putCampaignStatusAction($campaignId, Request $request) {
        $user = $this->getUser();
        $response = new Response();
        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());

// Return response error if the campaignId is empty
        if ($campaignId == NULL) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Missing campaignId.'
            )));
            return $response;
        }

// Check if the campaign exists for the specified campaign ID
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

        if (!$campaign) {
            $response = new Response();
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'No campaign for that id'
                    ))
            );
            return $response;
        }

        $campaignstatus = $this->getDoctrine()->getRepository('CampaignBundle:Campaignstatus')->findOneById($request->get('campaign_status'));
        if (!$campaignstatus) {
            $response = new Response();
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'No status type exists for that id'
                    ))
            );
            return $response;
        }

        $campaign->setCampaignstatus($campaignstatus);
        $campaign->setUpdatedAt($updateDate);



        $em = $this->getDoctrine()->getManager();
        $em->persist($campaign);
        $em->flush();

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'Campaign ' . $campaign->getId() . ' status updated to ' . $campaignstatus->getName() . ' '
                ))
        );
        return $response;
    }

//    /**
//     * @ApiDoc(
//     *    resource = true,
//     *    description = "Delete a campaign based on [campaignId]",
//     *    statusCodes = {
//     *     200 = "Returned when the campaign was deleted with success",
//     *     400 = {
//     *         "Returned When the campaign was not found in database",
//     *         "Returned when the user does not have access to the campaign"
//     *     },
//     *     403 = "Invalid API KEY",
//     *     500 = "Header x-wsse does not exist"
//     *    },
//     *    requirements = {
//     *       {
//     *           "name"="campaignId",
//     *           "dataType"="string",
//     *           "description"="The campaign unique id"
//     *       },
//     *       {
//     *          "name" = "_format",
//     *          "requirement" = "json|xml"
//     *       }
//     *    }
//     * )
//     * @return array
//     * @View()
//     */
//    public function deleteCampaignAction($campaignId) {
//        $user = $this->getUser();
//        $response = new Response();
//
//        if ($campaignId == NULL) {
//            $response->setStatusCode(400);
//            $response->setContent('The id parameter cannot be null.');
//        }
//
//        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')
//                ->findOneBy(['user' => $user, 'id' => $campaignId]);
//
//        $em = $this->getDoctrine()->getManager();
//
//        if (empty($campaign)) {
//            $response = new Response();
//            $response->setStatusCode(400);
//            $response->setContent(json_encode(array(
//                'success' => false,
//                'message' => 'Campaign does not exist.'
//                    ))
//            );
//            return $response;
//        }
//
//        $em->remove($campaign);
//        $em->flush();
//
//        $response->setStatusCode(200);
//        $response->setContent(json_encode(array(
//            'success' => true,
//            'message' => 'Campaign deleted.'
//                ))
//        );
//
//        return $response;
//    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns the Options array (Clients , Brands , Products , Countries",
     *    statusCodes = {
     *     200 = "Returned when the request is without errors",
     *     403 = "Invalid API KEY",
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *      {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *      }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getOptionsAction() {
        $user = $this->getUser();
        $response = new Response();

        $clients = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findAll();
        $brands = $this->getDoctrine()->getRepository('CampaignBundle:Brand')->findAll();
        $products = $this->getDoctrine()->getRepository('CampaignBundle:Product')->findAll();
        $countries = $this->getDoctrine()->getRepository('CampaignBundle:Country')->findAll();
        $task_statuses = $this->getDoctrine()->getRepository('TaskBundle:Taskstatus')->findAll();
        $campaign_statuses = $this->getDoctrine()->getRepository('CampaignBundle:Campaignstatus')->findAll();
        $product_lines = $this->getDoctrine()->getRepository('CampaignBundle:Productline')->findAll();
        $filetypes = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findAll();
        $divisions = $this->getDoctrine()->getRepository('CampaignBundle:Division')->findAll();
//        $phases = $this->getDoctrine()->getRepository('CampaignBundle:Phase')->findAll();

        $client_array = array();
        $brand_array = array();
        $product_array = array();
        $country_array = array();
        $task_statuses_array = array();
        $campaign_statuses_array = array();
        $product_lines_array = array();
        $filetype_array = array();
        $division_array = array();
//        $phase_array = array();

        foreach ($clients as $client) {
            $client_array[$client->getId()] = $client->getName();
        }
        foreach ($brands as $brand) {
            $brand_array[$brand->getId()] = $brand->getName();
        }
        foreach ($countries as $country) {
            $country_array[$country->getId()] = $country->getName();
        }
        foreach ($products as $product) {
            $product_array[$product->getId()] = $product->getName();
        }
        foreach ($task_statuses as $task_status) {
            $task_statuses_array[$task_status->getId()] = $task_status->getName();
        }
        foreach ($campaign_statuses as $campaign_status) {
            $campaign_statuses_array[$campaign_status->getId()] = $campaign_status->getName();
        }
        foreach ($product_lines as $product_line) {
            $product_lines_array[$product_line->getId()] = $product_line->getName();
        }
        foreach ($filetypes as $filetype) {
            $filetype_array[$filetype->getId()] = $filetype->getName();
        }
        foreach ($divisions as $division) {
            $division_array[$division->getId()] = $division->getName();
        }
//        foreach ($phases as $phase) {
//            $phase_array[$phase->getId()] = $phase->getName();
//        }


        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'clients' => $client_array,
            'brands' => $brand_array,
            'products' => $product_array,
            'countries' => $country_array,
            'task_statuses' => $task_statuses_array,
            'campaign_statuses' => $campaign_statuses_array,
            'product_lines' => $product_lines_array,
            'filetypes' => $filetype_array,
            'divisions' => $division_array,
//            'phases' => $phase_array,
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Update a task's status and message based on [taskId]",
     *    statusCodes = {
     *     200 = "Returned when the task was updated",
     *     403 = {"Invalid API KEY",
     *            "Returned when the status does not match one of the status names in the db.",
     *            "Returned when the user does not have access to the task" },
     *     404 = {
     *         "Returned When the task was not found in database",
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {"name"="campaignId",  "dataType"="string",    "description"="The campaign's unique Id",   "requirement"="true"  },
     *       {"name"="taskId",      "dataType"="integer",   "description"="The task's unique Id",       "requirement"="true"  },
     *       {"name" = "_format",      "requirement" = "json|xml"      }
     *    },
     *    parameters={
     *       {"name"="status",                "dataType"="integer", "required"=true,    "description"="The new task status (1 to 3)"       },
     *       {"name"="message",               "dataType"="text",    "required"=false,   "description"="The new task message. Can be null." },
     * }
     * )
     * @return array
     * @View()
     */
    public function putCampaignsTaskAction($campaignId, $taskId, Request $request) {
        $debug_array = array();
//Grab the current user browsing / using the app.
        $user = $this->getUser();
        $response = new Response();

//Grab the current date if needed
        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());
        $em = $this->getDoctrine()->getManager();

//Instantiate a campaign reviewers array
        $campaign_reviewers_array = array();
//Fetch the current campaign from the database
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid campaignId.'
            )));
            return $response;
        }
        $debug_array['initial_completeness'] = $campaign->getCompleteness();
//Validate the status input !
        $status_id = $request->get('status');



        $status = $this->getDoctrine()->getRepository('TaskBundle:Taskstatus')->findOneById($status_id);

        if (!$status) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'That status code does not exist!'
            )));
            return $response;
        }


//FETCH THE CAMPAIGN'S TEAM.
        $teammembers = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')->findBy(['campaign' => $campaign]);
        if (!$teammembers) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'The campaign team must have at least one member in order to continue.'
            )));
        }


        $nr = count($teammembers);

        foreach ($teammembers as $teammember) {
            if ($teammember->getIsReviewer()) {
                $campaign_reviewers_array[] = $teammember->getMember();
            }
        }

//instantiate a boolean is_allowed_for_all_values
        $is_allowed_for_all_values = false;
//check if user is in the reviewers array
        if (in_array($user, $campaign_reviewers_array)) {
            $is_allowed_for_all_values = true;
        }

//Fetch the current task to be updated
        $task = $this->getDoctrine()->getRepository('TaskBundle:Task')
                ->find($taskId);

// Check if the task exists
        if (!$task) {
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Task does not exist.'
                    ))
            );
            return $response;
        }

        if ($request->get('status')) {
            $status = $this->getDoctrine()->getRepository('TaskBundle:Taskstatus')->findOneById($request->get('status'));
            if (!$status) {
                $response->setStatusCode(403);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'Invalid status!'
                )));
            }
            if ($request->get('status') == 3) {
                if ($is_allowed_for_all_values) {
                    $task->setTaskstatus($status);
                    $updated_completeness = self::recalculate_campaign_completeness($campaign);
                    $campaign->setCompleteness($updated_completeness);
                } else {
                    $response->setStatusCode(403);
                    $response->setContent(json_encode(array(
                        'success' => false,
                        'message' => 'You are not a campaign reviewer. You cannot change the status to completed.'
                    )));
                    return $response;
                }
            } else {
                $task->setTaskstatus($status);
                $updated_completeness = self::recalculate_campaign_completeness($campaign);
                $campaign->setCompleteness($updated_completeness);
            }

            $task->setTaskmessage(NULL);
            if ($request->get('message')) {
                $newMessage = new Taskmessage();
                $newMessage->setMessage($request->get('message'));
                $newMessage->setCreatedAt($updateDate);
                $newMessage->setUpdatedAt($updateDate);
                $em->persist($newMessage);
                $task->setTaskmessage($newMessage);
            }

            $task->setTaskstatusdate($updateDate);
            $task->setStatuschangedby($user);
            $em->flush();
            $debug_array['post_completeness'] = $campaign->getCompleteness();

            $response->setStatusCode(200);
            $response->setContent(json_encode(array(
                'success' => true,
                'message' => 'Task updated.',
                'debug_array' => $debug_array,
            )));
            return $response;
        } else {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Field status cannot be empty.'
                    ))
            );
            return $response;
        }
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Set a task's owner based on [campaignId], [taskId], [userId]",
     *    statusCodes = {
     *     200 = "Returned when the task's owner was updated",
     *     403 = {"Invalid API KEY",
     *            "Returned when the current user does not match the campaign owner / creator",
     *           },
     *     404 = {
     *         "Returned when a campaign was not found in database by the campaignId in the url.",
     *         "Returned when a task was not found in database  by the taskId in the url.",
     *         "Returned when a user was not found in database  by the userId in the url.",
     *         "Returned when the user is not part of the campaign's team",
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The ID of the campaign.",
     *           "requirement"="true"
     *       },
     *      {
     *           "name"="taskId",
     *           "dataType"="integer",
     *           "description"="The ID of the task.",
     *           "requirement"="true"
     *       },
     *      {
     *           "name"="userId",
     *           "dataType"="integer",
     *           "description"="The ID of the user.",
     *           "requirement"="true"
     *       },
     *    }
     * )
     * @return array
     * @View()
     */
    public function putCampaignsTaskOwnerAction($campaignId, $taskId, $userId, Request $request) {
//Grab the current user;
        $user = $this->getUser();
//instantiate a response
        $response = new Response();
        $em = $this->getDoctrine()->getManager();

//Verify that the campaign exists
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid campaignId.'
            )));
            return $response;
        }
//Verify that the user_to_be_owner exists
        $user_to_be_owner = $this->getDoctrine()->getRepository('UserBundle:User')->find($userId);
        if (!$user_to_be_owner) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid user id provided.'
            )));
            return $response;
        }

        $validated_as_contrib_or_admin = false;
        if (($user->hasRole('ROLE_CONTRIBUTOR')) || ($user->hasRole('ROLE_ADMINISTRATOR'))) {
            $validated_as_contrib_or_admin = true;
        }

        if (!$validated_as_contrib_or_admin) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'You do not have permissions to change the Task Owner. Only Contributors and Admins allowed.'
            )));
            return $response;
        }

        $current_user_id = $user->getId();

//This is validation that checks if the user is allowed to view the client-campaign-country combination
        $user_can_access_campaign = self::validate_user_can_view_campaign($current_user_id, $campaignId);


//        
//        print_r($user_can_access_campaign);
//        die('help');
//        
// If the user is administrator , he will bypass that validation !
        if ($user->hasRole('ROLE_ADMINISTRATOR')) {
            $user_can_access_campaign = true;
        }


        if (!$user_can_access_campaign) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'You do not have permissions to change the Task Owner. You are a contributor but you cannot access this campaign.'
            )));
            return $response;
        }

/// END OF VALIDATIONS MOMENTARELY , PROCEED TO THE ACTUAL IMPLEMENTATIOn
/////////////////////////////////////
/////////////////////////////////////
/////////////////////////////////////
/////////////////////////////////////


        $task = $this->getDoctrine()->getRepository('TaskBundle:Task')->findOneBy(['id' => $taskId, 'campaign' => $campaign]);
        if (!$task) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid campaign and task combo.'
            )));
            return $response;
        }

// Get the user we want to set as owner / or throw error
        $user_to_be_owner = $this->getDoctrine()->getRepository('UserBundle:User')->find($userId);
        if (!$user_to_be_owner) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid userId.'
            )));
            return $response;
        }

// Get the teammmembers array for this campaign
        $teammember = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')->findOneBy(['campaign' => $campaign, 'member' => $userId]);

// Check that the user we want to set as a task owner is a teammember  / or throw error
        if (!$teammember) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid teammember.'
            )));
            return $response;
        }

//If the validation passed , update the task's owner field with the provided user.

        $em = $this->getDoctrine()->getManager();
        $task->setOwner($user_to_be_owner);
        $em->flush();

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'User ' . $user_to_be_owner->getId() . ' has been set as taskowner for ' . $task->getTaskname()->getName() . ' task of the campaign ' . $campaign->getId() . '.'
        )));
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns an array of all the users in the system. The user ID and the user's FirstName + LastName",
     *    statusCodes = {
     *     200 = "Returned when the array was successfully generated.",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Could not find any users."
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getUsersAction() {
        $current_user = $this->getUser();
        $response = new Response();
        $users = $this->getDoctrine()->getRepository('UserBundle:User')->findAll();

        if (!$users) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Could not find any users.')));
            return $response;
        }

        $users_array = array();
        foreach ($users as $user) {
            $users_array[$user->getId()] = $user->getFirstname() . ' ' . $user->getLastname();
        }
        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'users' => $users_array
        )));
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns an array that contains the campaigns teammembers based on [campaignId]",
     *    statusCodes = {
     *     200 = {"Returned when successfully found at least 1 teammember for this campaign.",
     *             "Returned when call was successfull , but there were no teammembers for the campaign"},           
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignTeammembersAction($campaignId) {
        $user = $this->getUser();
        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')
                ->find($campaignId);

        $tasks = $this->getDoctrine()->getRepository('TaskBundle:Task')->findByCampaign($campaign);

        if (!$campaign) {

// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

        $teammembers = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')->findBy(['campaign' => $campaign]);

        if (!$teammembers) {
            $response->setStatusCode(200);
            $response->setContent(json_encode(array(
                'success' => true,
                'teammembers' => null
            )));
            return $response;
        }

        $return_array = array();

        foreach ($teammembers as $teammember) {
            $member['user_id'] = $teammember->getMember()->getId();
            $member['is_reviewer'] = $teammember->getIsReviewer();
//   $member['is_contributor'] = $teammember->getIsContributor();
//   $member['is_writer'] = $teammember->getIsWriter();
            $member['First Name'] = $teammember->getMember()->getFirstname();
            $member['Last Name'] = $teammember->getMember()->getLastname();
            $member['Title'] = $teammember->getMember()->getTitle();
            $member['Office'] = $teammember->getMember()->getOffice();
            $member['Email Adress'] = $teammember->getMember()->getEmail();
            $member['Phone'] = $teammember->getMember()->getPhone();
            $member['Profile Picture location'] = $teammember->getMember()->getProfilepicture();


            $return_array[] = $member;
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'teammembers' => $return_array
        )));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Update a campaign's JTBD data [campaignId]",
     *    statusCodes = {
     *     200 = "Returned when the task was updated",
     *     403 = {"Invalid API KEY", "This authenticated user does not have access to update this campaign."},
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign identifier for updating JTBD Data."}
     *      },
     *      parameters={
     *          {"name"="brief_outline", "dataType"="text", "required"=false, "description"="Text"},
     *          {"name"="mmo_brandshare", "dataType"="decimal", "required"=false, "description"="Decimal value"},
     *          {"name"="mmo_penetration", "dataType"="decimal", "required"=false, "description"="Decimal value"},
     *          {"name"="mmo_salesgrowth", "dataType"="decimal", "required"=false, "description"="Decimal value"},
     *          {"name"="mmo_othermetric", "dataType"="decimal", "required"=false, "description"="Decimal value"},
     *          {"name"="mco_brandhealth_bhc", "dataType"="decimal", "required"=false, "description"="Decimal value"},
     *          {"name"="mco_awareness_increase", "dataType"="decimal", "required"=false, "description"="Decimal value"},
     *          {"name"="mco_brandhealth_performance", "dataType"="decimal", "required"=false, "description"="Decimal value"}
     *      }
     * )
     * @return array
     * @View()
     */
    public function putCampaignsJtbdAction($campaignId, Request $request) {
//Grab the current user browsing / using the app.
        $user = $this->getUser();
        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

//Validation for Campaign.
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

//////////////////////////////////////////////////////////////
//ONLY THE CAMPAIGN OWNER CAN SET THE JTBD DATA MOMENTARELY
//////////////////////////////////////////////////////////////


        if ($user->hasRole('ROLE_VIEWER')) {
            $response = new Response();
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This authenticated user does not have access to update this campaign.'
                    ))
            );
            return $response;
        }




//        if ($campaign->getUser() != $user) {
//            $response->setStatusCode(403);
//            $response->setContent(json_encode(array(
//                'success' => false,
//                'message' => 'You are not the campaign owner.'
//            )));
//            return $response;
//        }
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
// VALIDATION FOR CHECKING IF THE CURRENT USER HAS THE CONTRIBUTOR/WRITTER ROLE NEEDED
//Grab the current date if needed
        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());
        $em = $this->getDoctrine()->getManager();

        $brief_outline = $request->get('brief_outline');
        $mmo_brandshare = $request->get('mmo_brandshare');
        $mmo_penetration = $request->get('mmo_penetration');
        $mmo_salesgrowth = $request->get('mmo_salesgrowth');
        $mmo_othermetric = $request->get('mmo_othermetric');

        $mco_brandhealthbhc = $request->get('mco_brandhealth_bhc');
        $mco_awarenessincrease = $request->get('mco_awareness_increase');
        $mco_brandhealthperformance = $request->get('mco_brandhealth_performance');

//VERIFY THAT THE DECIMAL VALUES ARE REALLY A NUMBER (not string)
//
//VERIFY THAT ALL THE FIELDS HAVE BEEN COMPLETED - NOT NEEDED , FIELDS CAN BE SET NULL.
//SET THE CAMPAIGN TO THE FIELD VALUES
//

        $campaign->setBriefOutline($brief_outline);
        $campaign->setMmoBrandshare($mmo_brandshare);
        $campaign->setMmoPenetration($mmo_penetration);
        $campaign->setMmoSalesgrowth($mmo_salesgrowth);
        $campaign->setMmoOthermetric($mmo_othermetric);
        $campaign->setMcoBrandhealthBhc($mco_brandhealthbhc);
        $campaign->setMcoAwarenessincrease($mco_awarenessincrease);
        $campaign->setMcoBrandhealthPerformance($mco_brandhealthperformance);

        $em->flush();
// CREATE THE METHOD... 
        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'Campaign JTBD data updated.'
        )));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Update / add a campaign's teammember",
     *    statusCodes = {
     *     200 = "Returned when the campaign's team was updated.",
     *     201 = "New team member created.",
     *     403 = {
     *          "Invalid API KEY", 
     *          "Only contributors and administrators are allowed to add/modify campaign Teammembers.",
     *          "You are a contributor , but you are not allowed to view/edit/modify this campaign due to user-client-region-country combination."
     *     },
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *         "Returned when the user was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign identifier for updating teammember Data."},
     *          {"name"="userId", "dataType"="string", "description"="User identifier for updating teammember Data."}
     *      },
     *      parameters={
     *          {"name"="is_reviewer",      "dataType"="boolean", "required"=true, "description"="Boolean (1/0) value"},
     *
     *      }
     * )
     * @return array
     * @View()
     */
    public function putCampaignsTeammembersAction($campaignId, $userId, Request $request) {
        $user = $this->getUser();
        $response = new Response();
        $em = $this->getDoctrine()->getManager();


//Grab the values from the response , if nothing set , default to FALSE ( 0 )
        $is_reviewer = $request->get('is_reviewer') ? $request->get('is_reviewer') : false;


        $validated_as_contrib_or_admin = false;
        if (($user->hasRole('ROLE_CONTRIBUTOR')) || ($user->hasRole('ROLE_ADMINISTRATOR'))) {
            $validated_as_contrib_or_admin = true;
        }


        if (!$validated_as_contrib_or_admin) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Only contributors and administrators are allowed to add/modify campaign Teammembers.'
            )));
            return $response;
        }

        $current_user_id = $user->getId();
        $user_can_access_campaign = self::validate_user_can_view_campaign($current_user_id, $campaignId);

// If the user is administrator , he will bypass that validation !
        if ($user->hasRole('ROLE_ADMINISTRATOR')) {
            $user_can_access_campaign = true;
        }

        if (!$user_can_access_campaign) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'You are a contributor , but you are not allowed to view/edit/modify this campaign due to user-client-region-country combination.'
            )));
            return $response;
        }


        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);
        if (!$campaign) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Wrong campaign Id.'
            )));
            return $response;
        }

        $user_to_be_member = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($userId);
        if (!$user_to_be_member) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Wrong user Id.'
            )));
            return $response;
        }


//Check if user is already a member of the team's campaign
        $teammember_already_exists = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')
                ->findOneBy(['campaign' => $campaign, 'member' => $user_to_be_member]);

        if ($teammember_already_exists) {

            $teammember = $teammember_already_exists;
            $teammember->setIsReviewer($is_reviewer);

            $em->flush();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array(
                'success' => true,
                'message' => 'Team member updated.'
            )));
            return $response;
        } else {
            $teammember = new Teammember();
            $teammember->setCampaign($campaign);
            $teammember->setMember($user_to_be_member);
            $teammember->setIsReviewer($is_reviewer);

            $em->persist($teammember);
            $em->flush();
// ELSE , CREATE A NEW ENTRY INTO THE TEAMMEMBER TABLE
            $response->setStatusCode(201);
            $response->setContent(json_encode(array(
                'success' => true,
                'message' => 'New teammember created.'
            )));
            return $response;
        }
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Remove a campaign's teammember",
     *    statusCodes = {
     *     200 = "Returned when the campaign's team was updated",
     *     403 = {"Invalid API KEY",
     *           },
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *         "Returned when the user was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign identifier for updating teammember Data."},
     *          {"name"="userId", "dataType"="string", "description"="User identifier for updating teammember Data."}
     *      },
     *     
     * )
     * @return array
     * @View()
     */
    public function deleteCampaignsTeammemberAction($campaignId, $userId) {
        $user = $this->getUser();
        $response = new Response();
        $em = $this->getDoctrine()->getManager();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Wrong campaign Id.')));
            return $response;
        }
        $user_to_be_removed = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($userId);
        if (!$user_to_be_removed) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Wrong user Id.')));
            return $response;
        }


///VALIDATION NEEDED. ONLY CONTRIBUTORS THAT HAVE ACCESS && ADMINISTRATORS CAN ACCESS THIS CALL
///VALIDATION NEEDED. ONLY CONTRIBUTORS THAT HAVE ACCESS && ADMINISTRATORS CAN ACCESS THIS CALL
///VALIDATION NEEDED. ONLY CONTRIBUTORS THAT HAVE ACCESS && ADMINISTRATORS CAN ACCESS THIS CALL
///VALIDATION NEEDED. ONLY CONTRIBUTORS THAT HAVE ACCESS && ADMINISTRATORS CAN ACCESS THIS CALL
///VALIDATION NEEDED. ONLY CONTRIBUTORS THAT HAVE ACCESS && ADMINISTRATORS CAN ACCESS THIS CALL
//Check if there is anything to remove
        $teammember_exists = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')
                ->findOneBy(['campaign' => $campaignId, 'member' => $user_to_be_removed]);

        if ($teammember_exists) {


            $tasks_of_this_campaign = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy([
                'campaign' => $campaign,
                'owner' => $user_to_be_removed
            ]);
            if ($tasks_of_this_campaign) {
                foreach ($tasks_of_this_campaign as $task_of_this_campaign) {
                    $task_of_this_campaign->setOwner(NULL);
                }
                $em->flush();
            }


//Fetch this campaign's tasks , and for each task , verify if the user is assigned as a taskowner.
//If he is , set the taskowner of that task to NULL.






            $em->remove($teammember_exists);

            $em->flush();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array(
                'success' => true,
                'message' => 'Team member removed.'
            )));
            return $response;
        }
        $response->setStatusCode(403);
        $response->setContent(json_encode(array(
            'success' => false,
            'message' => 'Nothing to remove.'
        )));
        return $response;
    }

    /**
     * Method returns array with campaign fields used in POST or PUT campaign method.
     */
    public function campaignFieldsToArrayAction($campaignId) {

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);

        $campaignFields = array(
            'name' => $campaign->getName(),
            'client' => $campaign->getClient()->getName(),
            'country' => $campaign->getCountry()->getName(),
            'brand' => $campaign->getBrand()->getName(),
            'product' => $campaign->getProduct()->getName(),
            'productline' => $campaign->getProductline()->getName(),
            'division' => $campaign->getDivision()->getName(),
            'status' => $campaign->getCampaignstatus() ? $campaign->getCampaignstatus()->getName() : null,
            'completion_date' => date('Y-m-d', $campaign->getCompletionDate()->getTimestamp()),
            'client_deliverabledate' => date('Y-m-d', $campaign->getClientDeliverabledate()->getTimestamp())
        );

        return $campaignFields;
    }

// End of modifiable campaign fields method().

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns campaign selected_tasks (objectives from lightdata) information.",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = {
     *          "Invalid API KEY",
     *          "The lightdata set does not include any data for Objectives. Cannot generate SelectedTasksInformation data without it.",
     *     },
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *         "Returned When the lightdata for that campaign was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignSelectedtasksinformationAction($campaignId) {

        $response = new Response();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);



        if (!$campaign) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }
        $lightdata_id = $campaign->getLightdata();

        $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->findOneById($lightdata_id);

        if (!$lightdata) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Lightdata does not exist for this campaign.'
                    ))
            );
            return $response;
        }
        $objectives = $this->getDoctrine()->getRepository('LightdataBundle:ObjectiveLD')->findBy(['lightdata' => $lightdata]);

        if (count($objectives) == 0) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'The lightdata set does not include any data for Objectives. Cannot generate SelectedTasksInformation data without it.'
            )));
            return $response;
        }

        foreach ($objectives as $objective) {

            $return_array[] = array(
                'Uid' => $objective->getId(),
                'Name' => $objective->getName(),
                'HtmlColor' => $objective->getHtmlcolor(),
                'Selected' => $objective->getSelected(),
                'Score' => $objective->getScore(),
            );
        } // End of tasks foreach() loop.

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'Objectives' => $return_array
                        )
        ));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns campaign channel_ranking (Groupings and Touchpoints from lightdata) information.",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned when the campaign was not found in database",
     *         "Returned when the lightdata for that campaign was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignChannelrankingAction($campaignId) {

        $response = new Response();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);



        if (!$campaign) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }
        $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($campaign->getLightdata());
        if (!$lightdata) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Lightdata does not exist for this campaign.'
                    ))
            );
            return $response;
        }


        $touchpoints = $this->getDoctrine()->getRepository('LightdataBundle:TouchpointLD')->findByLightdata($lightdata);
        $groupings = $this->getDoctrine()->getRepository('LightdataBundle:GroupingLD')->findByLightdata($lightdata);

        foreach ($touchpoints as $touchpoint) {

            $objectivescores = $this->getDoctrine()->getRepository('LightdataBundle:TouchpointObjectiveScoreLD')->findByTouchpoint($touchpoint);
            $objectivescores_array = array();
            foreach ($objectivescores as $objectivescore) {
                $objectivescores_array[] = $objectivescore->getValue();
            }

            $attributescores = $this->getDoctrine()->getRepository('LightdataBundle:TouchpointAttributeScoreLD')->findByTouchpoint($touchpoint);
            $attributescores_array = array();
            foreach ($attributescores as $attributescore) {
                $attributescores_array[] = $attributescore->getValue();
            }
            $return_array['Touchpoints'][] = array(
                'Name' => $touchpoint->getName(),
                'LocalName' => $touchpoint->getLocalname(),
                'HtmlColor' => $touchpoint->getHtmlcolor(),
                'Selected' => $touchpoint->getSelected(),
                'AggObjectiveScore' => $touchpoint->getAggobjectivescore(),
                'ObjectiveScores' => $objectivescores_array,
                'AttributeScores' => $attributescores_array,
            );
        }

        foreach ($groupings as $grouping) {
            $categories = $this->getDoctrine()->getRepository('LightdataBundle:GroupingCategoryLD')->findByGrouping($grouping);
            $categories_array = array();
            $initial_count = 0;
            foreach ($categories as $category) {
                $categories_array[$initial_count]['Name'] = $category->getName();
                $categories_array[$initial_count]['Htmlcolor'] = $category->getHtmlColor();
                $initial_count++;
            }


            $touchpointcategorymaps = $this->getDoctrine()->getRepository('LightdataBundle:GroupingTouchpointCategoryMapLD')->findByGrouping($grouping);
            $touchpointcategorymaps_array = array();
            foreach ($touchpointcategorymaps as $touchpointcategorymap) {
                $touchpointcategorymaps_array[] = $touchpointcategorymap->getName() . ' : ' . $touchpointcategorymap->getValue();
            }

            $return_array['Groupings'][] = array(
                'Name' => $grouping->getName(),
                'Categories' => $categories_array,
                'TouchpointCategoryMap' => $touchpointcategorymaps_array,
            );
        }


        $return_array['CurrentGroupingIndex'] = $lightdata->getCurrentgroupingindex();

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'ChannelRanking' => $return_array
                        )
        ));


        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns BudgetAllocation Data for a campaign (from the lightdata).",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = {"Invalid API KEY", "The lightdata you requested has no allocated touchpoints data in it."},
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *         "Returned When the lightdata for that campaign was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignChannelallocationAction($campaignId) {

        $response = new Response();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);



        if (!$campaign) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }
        $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($campaign->getLightdata());
        if (!$lightdata) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Lightdata does not exist for this campaign.'
                    ))
            );
            return $response;
        }

//die('dead');
        $budgetallocation = $this->getDoctrine()->getRepository('LightdataBundle:BudgetAllocationLD')->findByLightdata($lightdata);

//print_r(count($budgetallocation));


        $allocatedtouchpoints = $this->getDoctrine()->getRepository('LightdataBundle:BAAllocatedTouchpointLD')->findByBudgetallocation($budgetallocation);

        if (count($allocatedtouchpoints) == 0) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'The lightdata you requested has no allocated touchpoints data in it.'
            )));

            return $response;
        }

        $allocatedtouchpoints_array = array();
        foreach ($allocatedtouchpoints as $allocatedtouchpoint) {

            $ATallocation = $this->getDoctrine()->getRepository('LightdataBundle:BAATAllocationLD')->findOneByAllocatedtouchpoint($allocatedtouchpoint);
            $ATresult = $this->getDoctrine()->getRepository('LightdataBundle:BAATAResultLD')->findOneByAllocation($ATallocation);
            $ATindividualperformances = $this->getDoctrine()->getRepository('LightdataBundle:BAATARIndividualPerformanceLD')->findByResult($ATresult);
            $ATindividualperformances_array = array();
            foreach ($ATindividualperformances as $ATindividualperformance) {
                $ATindividualperformances_array[] = $ATindividualperformance->getValue();
            }

            $allocatedtouchpoints_array[] = array(
                "TouchpointName" => $allocatedtouchpoint->getTouchpointname(),
                'Allocation' => array(
                    'Budget' => $ATallocation->getBudget(),
                    'CostPerGrp' => $ATallocation->getCostpergrp(),
                    'GRP' => $ATallocation->getGrp(),
                    'Result' => array(
                        'GlobalPerformance' => $ATresult->getGlobalperformance(),
                        'Reach' => $ATresult->getReach(),
                        'IndividualPerformance' => $ATindividualperformances_array,
                    ),
                ),
            );
        }
        $return_array['AllocatedTouchpoints'] = $allocatedtouchpoints_array;


        $total = $this->getDoctrine()->getRepository('LightdataBundle:BATotalLD')->findOneByBudgetallocation($budgetallocation);
        $TOallocation = $this->getDoctrine()->getRepository('LightdataBundle:BATOAllocationLD')->findOneByAllocatedtouchpoint($total);
        $TOresult = $this->getDoctrine()->getRepository('LightdataBundle:BATOAResultLD')->findOneByAllocation($TOallocation);
        $TOindividualperformances = $this->getDoctrine()->getRepository('LightdataBundle:BATOARIndividualPerformanceLD')->findByResult($TOresult);
        $TOindividualperformances_array = array();
        foreach ($TOindividualperformances as $TOindividualperformance) {
            $TOindividualperformances_array[] = $TOindividualperformance->getValue();
        }


        $return_array['Total'] = array(
            'TouchpointName' => $total->getTouchpointName(),
            'Allocation' => array(
                'Budget' => $TOallocation->getBudget(),
                'CostPerGrp' => $TOallocation->getCostpergrp(),
                'GRP' => $TOallocation->getGrp(),
                'Result' => array(
                    'GlobalPerformance' => $TOresult->getGlobalperformance(),
                    'Reach' => $TOresult->getReach(),
                    'IndividualPerformance' => $TOindividualperformances_array,
                ),
            ),
        );

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'ChannelAllocation' => $return_array,
                        )
        ));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns TIMEALLOCATION Data ONLY for a campaign (from the lightdata).",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = {"Invalid API KEY", "Timeallocation within this lightdata set has no total data in it. Unable to generate Weekly Phasing without it."},
     *     404 = {
     *         "Returned when the campaign was not found in database",
     *         "Returned when the lightdata for that campaign was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignWeeklyphasingAction($campaignId) {

        $response = new Response();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);

        if (!$campaign) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }
        $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($campaign->getLightdata());
        if (!$lightdata) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Lightdata does not exist for this campaign.'
                    ))
            );
            return $response;
        }


        $timeallocation = $this->getDoctrine()->getRepository('LightdataBundle:TimeAllocationLD')->findByLightdata($lightdata);


///GRAB THE TIMEALLOCATION ALLOCATEDTOUCHPOINTS DATA
        $allocatedtouchpoints = $this->getDoctrine()->getRepository('LightdataBundle:TAAllocatedTouchpointLD')->findByTimeallocation($timeallocation);
        $allocatedtouchpoints_array = array();
        foreach ($allocatedtouchpoints as $allocatedtouchpoint) {
            $ATallocationsbyperiod = $this->getDoctrine()->getRepository('LightdataBundle:TAATAllocationByPeriod')->findByAllocatedtouchpoint($allocatedtouchpoint);

            $allocations_by_period_array = array();
            foreach ($ATallocationsbyperiod as $allocation_by_period) {

                $ATResult = $this->getDoctrine()->getRepository('LightdataBundle:TAATABPResult')->findOneByAllocationbyperiod($allocation_by_period);
                $ATindividualperformances = $this->getDoctrine()->getRepository('LightdataBundle:TAATABPRIndividualPerformance')->findByResult($ATResult);
                $ATindividualperformances_array = array();
                foreach ($ATindividualperformances as $ATindividualperformance) {
                    $ATindividualperformances_array[] = $ATindividualperformance->getValue();
                }
                $allocations_by_period_array[] = array(
                    'Budget' => $allocation_by_period->getBudget(),
                    'CostPerGrp' => $allocation_by_period->getCostpergrp(),
                    'GRP' => $allocation_by_period->getGrp(),
                    'Result' => array(
                        'GlobalPerformance' => $ATResult->getGlobalperformance(),
                        'Reach' => $ATResult->getReach(),
                        'IndividualPerformance' => $ATindividualperformances_array,
                    ),
                );
            }
            $allocatedtouchpoints_array[] = array(
                'AllocationByPeriod' => $allocations_by_period_array,
                'TouchpointName' => $allocatedtouchpoint->getTouchpointName(),
                'ReachFrequency' => $allocatedtouchpoint->getReachfrequency(),
            );
        }

///GRAB THE TIMEALLOCATION TOTAL DATA

        $total = $this->getDoctrine()->getRepository('LightdataBundle:TATotalLD')->findOneByTimeallocation($timeallocation);


        if (count($total) == 0) {

            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Timeallocation within this lightdata set has no total data in it. Unable to generate Weekly Phasing without it.'
            )));
            return $response;
        }

        $TOallocationsbyperiod = $this->getDoctrine()->getRepository('LightdataBundle:TATOAllocationByPeriod')->findByAllocatedtouchpoint($total);
        $to_allocations_by_period_array = array();
        foreach ($TOallocationsbyperiod as $TOallocationbyperiod) {

            $TOResult = $this->getDoctrine()->getRepository('LightdataBundle:TATOABPResult')->findOneByAllocationbyperiod($TOallocationbyperiod);
            $TOindividualperformances = $this->getDoctrine()->getRepository('LightdataBundle:TATOABPRIndividualPerformance')->findByResult($TOResult);
            $TOindividualperformances_array = array();
            foreach ($TOindividualperformances as $TOindividualperformance) {
                $TOindividualperformances_array[] = $TOindividualperformance->getValue();
            }
            $to_allocations_by_period_array[] = array(
                'Budget' => $TOallocationbyperiod->getBudget(),
                'CostPerGrp' => $TOallocationbyperiod->getCostpergrp(),
                'GRP' => $TOallocationbyperiod->getGrp(),
                'Result' => array(
                    'GlobalPerformance' => $TOResult->getGlobalperformance(),
                    'Reach' => $TOResult->getReach(),
                    'IndividualPerformance' => $TOindividualperformances_array,
                ),
            );
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'WeeklyPhasing' => array(
                'AllocatedTouchpoints' => $allocatedtouchpoints_array,
                'Total' => array(
                    'AllocationByPeriod' => $to_allocations_by_period_array,
                    'TouchpointName' => $total->getTouchpointname(),
                    'ReachFrequency' => $total->getReachfrequency(),
                ),
                'Campaign_Start_Date' => $campaign->getStartDate() ? date('Y-m-d', $campaign->getStartDate()->getTimestamp()) : null,
            )
        )));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns WHAT IF Data ONLY for a campaign (from the lightdata).",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *         "Returned When the lightdata for that campaign was not found in database"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignVideoneutralAction($campaignId) {

        $response = new Response();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);

        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }
        $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($campaign->getLightdata());
        if (!$lightdata) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Lightdata does not exist for this campaign.'
                    ))
            );
            return $response;
        }

        $whatifresult = $this->getDoctrine()->getRepository('LightdataBundle:WhatIfResult')->findOneByLightdata($lightdata);

        if (!$whatifresult) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'There is no whatifresult data.'
                    ))
            );
            return $response;
        }


        $what_if_result_return_array = array();


        $wirconfig = $this->getDoctrine()->getRepository('LightdataBundle:WIRConfig')->findOneByWhatifresult($whatifresult);
        $wircoptimizedfunction = $this->getDoctrine()->getRepository('LightdataBundle:WIRCOptimizedFunction')->findOneByConfig($wirconfig);

        $what_if_result_return_array['Config'] = array(
            'FirstPeriod' => $wirconfig->getFirstperiod(),
            'LastPeriod' => $wirconfig->getLastperiod(),
            'SourceBudget' => $wirconfig->getSourcebudget(),
            'BudgetMinPercent' => $wirconfig->getBudgetminpercent(),
            'BudgetMaxPercent' => $wirconfig->getBudgetmaxpercent(),
            'BudgetStepPercent' => $wirconfig->getBudgetsteppercent(),
            'HasCurrentMix' => $wirconfig->getHascurrentmix(),
            'HasSingleTouchpointMix' => $wirconfig->getHassingletouchpointmix(),
            'HasOptimizedMix' => $wirconfig->getHasoptimizedmix(),
            'OptimizedFunction' => array(
                'CalculationType' => $wircoptimizedfunction->getCalculationtype(),
                'AttributeIndex' => $wircoptimizedfunction->getAttributeindex(),
            )
        );


        $wirpoints = $this->getDoctrine()->getRepository('LightdataBundle:WIRPoint')->findByWhatifresult($whatifresult);

        foreach ($wirpoints as $wirpoint) {

            $currentmix = $this->getDoctrine()->getRepository('LightdataBundle:WIRPCurrentMix')->findByPoint($wirpoint);
            $optimizedmix = $this->getDoctrine()->getRepository('LightdataBundle:WIRPOptimizedMix')->findByPoint($wirpoint);
            $singletouchpointmix = $this->getDoctrine()->getRepository('LightdataBundle:WIRPSingleTouchpointMix')->findByPoint($wirpoint);

            $WIRPCMdetails = $this->getDoctrine()->getRepository('LightdataBundle:WIRPCMDetail')->findByCurrentmix($currentmix);
            $WIRPOMdetails = $this->getDoctrine()->getRepository('LightdataBundle:WIRPOMDetail')->findByOptimizedmix($optimizedmix);
            $WIRPSTMdetails = $this->getDoctrine()->getRepository('LightdataBundle:WIRPSTMDetail')->findBySingletouchpointmix($singletouchpointmix);

            $WIRPCMtotal = $this->getDoctrine()->getRepository('LightdataBundle:WIRPCMTotal')->findOneByCurrentmix($currentmix);
            $WIRPOMtotal = $this->getDoctrine()->getRepository('LightdataBundle:WIRPOMTotal')->findOneByOptimizedmix($optimizedmix);
            $WIRPSTMtotal = $this->getDoctrine()->getRepository('LightdataBundle:WIRPSTMTotal')->findOneBySingletouchpointmix($singletouchpointmix);

            $WIRPCM_details_array = array();
            foreach ($WIRPCMdetails as $WIRPCM_detail) {
                $WIRPCM_details_array[] = array(
                    'TouchpointName' => $WIRPCM_detail->getTouchpointname(),
                    'Budget' => $WIRPCM_detail->getBudget(),
                    'FunctionValue' => $WIRPCM_detail->getFunctionvalue(),
                );
            }
            $WIRPOM_details_array = array();
            foreach ($WIRPOMdetails as $WIRPOM_detail) {
                $WIRPOM_details_array[] = array(
                    'TouchpointName' => $WIRPOM_detail->getTouchpointname(),
                    'Budget' => $WIRPOM_detail->getBudget(),
                    'FunctionValue' => $WIRPOM_detail->getFunctionvalue(),
                );
            }
            $WIRPSTM_details_array = array();
            foreach ($WIRPSTMdetails as $WIRPSTM_detail) {
                $WIRPSTM_details_array[] = array(
                    'TouchpointName' => $WIRPSTM_detail->getTouchpointname(),
                    'Budget' => $WIRPSTM_detail->getBudget(),
                    'FunctionValue' => $WIRPSTM_detail->getFunctionvalue(),
                );
            }


            $what_if_result_return_array['Points'][] = array(
                'StepPosition' => $wirpoint->getStepposition(),
                'ActualPercent' => $wirpoint->getActualpercent(),
                'CurrentMix' => array(
                    'Details' => $WIRPCM_details_array,
                    'Total' => array(
                        'TouchpointName' => $WIRPCMtotal->getTouchpointname(),
                        'Budget' => $WIRPCMtotal->getBudget(),
                        'FunctionValue' => $WIRPCMtotal->getFunctionvalue(),
                    ),
                ),
                'OptimizedMix' => array(
                    'Details' => $WIRPOM_details_array,
                    'Total' => array(
                        'TouchpointName' => $WIRPOMtotal->getTouchpointname(),
                        'Budget' => $WIRPOMtotal->getBudget(),
                        'FunctionValue' => $WIRPOMtotal->getFunctionvalue(),
                    ),
                ),
                'SingleTouchpointMix' => array(
                    'Details' => $WIRPSTM_details_array,
                    'Total' => array(
                        'TouchpointName' => $WIRPSTMtotal->getTouchpointname(),
                        'Budget' => $WIRPSTMtotal->getBudget(),
                        'FunctionValue' => $WIRPSTMtotal->getFunctionvalue(),
                    ),
                ),
            );
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'WhatIfResult' => $what_if_result_return_array,
        )));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Update a campaign's RealLives data [campaignId]",
     *    statusCodes = {
     *     200 = "Returned when the task was updated",
     *     403 = {"Invalid API KEY",
     *           },
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign identifier for updating JTBD Data."}
     *      },
     *      parameters={
     *          {"name"="real_lives_url", "dataType"="text", "required"=true, "description"="the url"},
     *          {"name"="real_lives_password", "dataType"="text", "required"=true, "description"="the password"},
     *      }
     * )
     * @return array
     * @View()
     */
    public function putCampaignsReallivesAction($campaignId, Request $request) {
//Grab the current user browsing / using the app.
        $user = $this->getUser();
        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

//Validation for Campaign.
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

//NO VALIDATION YET FOR WHO CAN CHANGE THE REALLIVES FOR A CAMPAIGN
//NO VALIDATION YET FOR WHO CAN CHANGE THE REALLIVES FOR A CAMPAIGN
//ALSO NO VALIDATION YET FOR THE INPUT
//ALSO NO VALIDATION YET FOR THE INPUT

        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());
        $em = $this->getDoctrine()->getManager();

        $reallivesurl = $request->get('real_lives_url');
        $reallivespassword = $request->get('real_lives_password');
        $old_completeness = $campaign->getCompleteness();
        $campaign->setReallivesurl($reallivesurl);
        $campaign->setReallivespassword($reallivespassword);
        $campaign->setUpdatedat($updateDate);
        $updated_completeness = self::recalculate_campaign_completeness($campaign);
        $campaign->setCompleteness($updated_completeness);
        $em->flush();
        $new_completeness = $campaign->getCompleteness();

        //ADD RECALCULATION OF COMPLETENESS

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'Campaign Real Lives data updated.',
            'old_completeness' => $old_completeness,
            'new_completeness' => $new_completeness
        )));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns campaign real lives information.",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned when the campaign was not found in database",
     *         
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignReallivesAction($campaignId) {
        $user = $this->getUser();

        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

        $real_lives_url = $campaign->getReallivesurl();
        $real_lives_password = $campaign->getReallivespassword();

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'campaign_id' => $campaign->getId(),
            'real_lives_url' => $real_lives_url ? $real_lives_url : null,
            'real_lives_password' => $real_lives_password ? $real_lives_password : null,
                        )
        ));
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Update a campaign's IDEA data [campaignId]",
     *    statusCodes = {
     *     200 = "Returned when the task was updated",
     *     403 = {"Invalid API KEY",
     *           },
     *     404 = {
     *         "Returned when the campaign was not found in database",
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign identifier for updating JTBD Data."}
     *      },
     *      parameters={
     *          {"name"="campaign_idea_title", "dataType"="text", "required"=true, "description"="the idea text"},
     *          {"name"="campaign_idea", "dataType"="text", "required"=true, "description"="the idea text"},
     *      }
     * )
     * @return array
     * @View()
     */
    public function putCampaignsIdeaAction($campaignId, Request $request) {
        $user = $this->getUser();
        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

//NO VALIDATION YET FOR WHO CAN CHANGE THE IDEA FOR A CAMPAIGN
//NO VALIDATION YET FOR WHO CAN CHANGE THE IDEA FOR A CAMPAIGN
//ALSO NO VALIDATION YET FOR THE INPUT
//ALSO NO VALIDATION YET FOR THE INPUT

        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());
        $em = $this->getDoctrine()->getManager();

        $idea_title = $request->get('campaign_idea_title');
        $idea_data = $request->get('campaign_idea');

        $campaign->setCampaignideatitle($idea_title);
        $campaign->setCampaignidea($idea_data);
        $campaign->setUpdatedat($updateDate);

        $em->flush();
        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'Campaign IDEA title & data updated.'
        )));

        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns campaign IDEA information.",
     *    statusCodes = {
     *     200 = "Returned when the campaign was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *        
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="campaignId",
     *           "dataType"="string",
     *           "description"="The campaign unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getCampaignIdeaAction($campaignId) {
        $user = $this->getUser();

        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

        $campaign_idea_title = $campaign->getCampaignideatitle();
        $campaign_idea = $campaign->getCampaignidea();

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'campaign_id' => $campaign->getId(),
            'campaign_idea_title' => $campaign_idea_title ? $campaign_idea_title : null,
            'campaign_idea' => $campaign_idea ? $campaign_idea : null,
                        )
                )
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns the Clients Options array",
     *    statusCodes = {
     *     200 = "Returned when the request is without errors",
     *     403 = "Invalid API KEY",
     *     404 = "The database has no clients momentarely.",
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *      {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *      }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getOptionsClientsAction() {
        $user = $this->getUser();
        $response = new Response();
        $clients = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findAll();
        if (!$clients) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'The database has no clients momentarely.'
            )));
        }
        $clients_array = array();
        foreach ($clients as $client) {
            $clients_array[$client->getId()] = $client->getName();
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'clients' => $clients_array,
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns the Divisions Options array for a specified client.",
     *    statusCodes = {
     *     200 = "Returned when the request is without errors",
     *     403 = "Invalid API KEY",
     *     404 = {
     *          "Returned when a client could not be found for the specified client_id",
     *          "Returned when no divisions could be found for that existing client."
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *     requirements = {
     *       {
     *           "name"="client_id",
     *           "dataType"="string",
     *           "description"="The client unique id"
     *       },
     *      {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *      }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getOptionsDivisionsAction(Request $request) {
        $user = $this->getUser();
        $response = new Response();

        $client_id = $request->get('client_id');
        $client = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findOneById($client_id);

        if (!$client) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'There is no client for that client id.'
            )));
            return $response;
        }
        $divisions = $this->getDoctrine()->getRepository('CampaignBundle:Division')->findBy(['client' => $client]);
        if (!$divisions) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This client does not have any divisions associated.'
            )));
            return $response;
        }
        $divisions_array = array();
        foreach ($divisions as $division) {
            $divisions_array[$division->getId()] = $division->getName();
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'divisions' => $divisions_array,
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns the Brands Options array for a specified division.",
     *    statusCodes = {
     *     200 = "Returned when the request is without errors",
     *     403 = "Invalid API KEY",
     *     404 = {"Returned when a division could not be found for the specified division_id", "Returned when no brands could be found for that existing division."},
     *     500 = "Header x-wsse does not exist"
     *    },
     *     requirements = {
     *       {
     *           "name"="division_id",
     *           "dataType"="string",
     *           "description"="The division unique id"
     *       },
     *      {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *      }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getOptionsBrandsAction(Request $request) {
        $user = $this->getUser();
        $response = new Response();

        $division_id = $request->get('division_id');
        $division = $this->getDoctrine()->getRepository('CampaignBundle:Division')->findOneById($division_id);

        if (!$division) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'There is no division for that division id.'
            )));
            return $response;
        }

        $brands = $this->getDoctrine()->getRepository('CampaignBundle:Brand')->findBy(['division' => $division]);
        if (!$brands) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This division does not have any brands associated.'
            )));
            return $response;
        }

        $brands_array = array();
        foreach ($brands as $brand) {
            $brands_array[$brand->getId()] = $brand->getName();
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'brands' => $brands_array,
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns the Productlines Options array for a specified brand.",
     *    statusCodes = {
     *     200 = "Returned when the request is without errors",
     *     403 = "Invalid API KEY",
     *     404 = {"Returned when a brand could not be found for the specified brand_id", "Returned when no productlines could be found for that existing brand."},
     *     500 = "Header x-wsse does not exist"
     *    },
     *     requirements = {
     *       {
     *           "name"="brand_id",
     *           "dataType"="string",
     *           "description"="The brand unique id"
     *       },
     *      {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *      }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getOptionsProductlinesAction(Request $request) {
        $user = $this->getUser();
        $response = new Response();

        $brand_id = $request->get('brand_id');
        $brand = $this->getDoctrine()->getRepository('CampaignBundle:Brand')->findOneById($brand_id);

        if (!$brand) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'There is no brand for that brand id.'
            )));
            return $response;
        }

        $productlines = $this->getDoctrine()->getRepository('CampaignBundle:Productline')->findBy(['brand' => $brand]);
        if (!$productlines) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This brand does not have any productlines associated.'
            )));
            return $response;
        }

        $productlines_array = array();
        foreach ($productlines as $productline) {
            $productlines_array[$productline->getId()] = $productline->getName();
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'productlines' => $productlines_array,
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns the Products Options array for a specified productline.",
     *    statusCodes = {
     *     200 = "Returned when the request is without errors",
     *     403 = "Invalid API KEY",
     *     404 = {"Returned when a productline could not be found for the specified productline_id",
     *            "Returned when no products could be found for that existing productline."},
     *     500 = "Header x-wsse does not exist"
     *    },
     *     requirements = {
     *       {
     *           "name"="productline_id",
     *           "dataType"="string",
     *           "description"="The productline unique id"
     *       },
     *      {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *      }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getOptionsProductsAction(Request $request) {
        $user = $this->getUser();
        $response = new Response();

        $productline_id = $request->get('productline_id');
        $productline = $this->getDoctrine()->getRepository('CampaignBundle:Productline')->findOneById($productline_id);

        if (!$productline) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'There is no productline for that productline id.'
            )));
            return $response;
        }

        $products = $this->getDoctrine()->getRepository('CampaignBundle:Product')->findBy(['productline' => $productline]);
        if (!$products) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This productline does not have any products associated.'
            )));
            return $response;
        }

        $products_array = array();
        foreach ($products as $product) {
            $products_array[$product->getId()] = $product->getName();
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'products' => $products_array,
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "ENABLE or DISABLE a campaign (using not_visible flag)",
     *    statusCodes = {
     *     200 = "Returned when the task was updated",
     *     403 = {"Invalid API KEY",
     *           },
     *     404 = {
     *         "Returned When the campaign was not found in database",
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign identifier."}
     *      },
     *      parameters={
     *          {"name"="not_visible", "dataType"="boolean", "required"=true, "description"="Not visible TRUE or FALSE (1/0)"},
     *      }
     * )
     * @return array
     * @View()
     */
    public function putCampaignsNotvisibleAction($campaignId, Request $request) {
        $user = $this->getUser();
        $response = new Response();
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist.'
                    ))
            );
            return $response;
        }

//NO VALIDATION YET FOR WHO CAN CHANGE THE NOT VISIBLE FOR A CAMPAIGN
//NO VALIDATION YET FOR WHO CAN CHANGE THE NOT VISIBLE FOR A CAMPAIGN
//ALSO NO VALIDATION YET FOR THE INPUT
//ALSO NO VALIDATION YET FOR THE INPUT

        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());
        $em = $this->getDoctrine()->getManager();

        $notvisible = $request->get('not_visible');


        $campaign->setNotVisible($notvisible);
        $campaign->setUpdatedat($updateDate);

        if ($notvisible == 1) {
            $extramessage = "Campaign is now disabled";
        } elseif ($notvisible == 0) {
            $extramessage = "Campaign is now enabled";
        } else {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid input provided ! Use 1 or 0 only.'
            )));
            return $response;
        }

        $em->flush();
        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'Campaign not_visible state updated. ' . $extramessage
        )));

        return $response;
    }

    function validate_user_can_view_campaign($user_id, $campaign_id) {

        $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($user_id);
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaign_id);


        // HERE WE ALREADY SUPPOSE USER IS CONTRIBUTOR OR ADMIN.
        $campaign_client = $campaign->getClient();
        $campaign_country = $campaign->getCountry();
        $campaign_region = $campaign_country->getRegion();

        $try1 = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $campaign_client,
            'country' => $campaign_country,
        ]);
        $try2 = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $campaign_client,
            'region' => $campaign_region,
            'all_countries' => true
        ]);
        $is_validated = false;
        if ($try1 || $try2) {
            $is_validated = true;
        }
        return $is_validated;
    }

    /**
     * Function to calculate the completeness of a campaign.
     * 
     * @param type $campaign
     * @return int
     */
    public function recalculate_campaign_completeness($campaign) {
        $completeness = 0;
        $tasks = $campaign->getTasks();

        //$em = $this->getDoctrine()->getManager();

        foreach ($tasks as $task) {
            $taskstatus = $task->getTaskstatus()->getName();
            if ($taskstatus == "Completed") {
                $completeness += 2;
            }
            if ($taskstatus == "Submitted") {
                $validated = $this->validate_task_requirements_are_met($task);
                if ($validated) {
                    $completeness += 1;
                }
            }
            if ($taskstatus == "Open") {
                $validated = $this->validate_task_requirements_are_met($task);
                if ($validated) {
                    $completeness += 1;
                }
            }
        }

        $campaign->setCompleteness($completeness);

        return $completeness;
    }

    function validate_task_requirements_are_met($task) {
        /////////////////////////////////////////////////
        //CHANGE THIS TO FALSEEE
        ////////////////////////////
        $is_validated = false;
        /////////////////////////////////
        /////////////////////////////////
        //$task = $this->getDoctrine()->getRepository('TaskBundle:Task')->find($task);
        $campaign = $task->getCampaign();

        switch ($task->getTaskname()->getName()) {
            case "JTBD":
                $campaign_start_date = $campaign->getStartdate();
                if ($campaign_start_date) {
                    $is_validated = true;
                }
                break;
            case "Comm Tasks":
//if ANY OF THE the Matrix Objective\<OBJECTIVE NAME>\Score > 0 , is validated.
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $objectives = $this->getDoctrine()->getRepository('LightdataBundle:ObjectiveLD')->findByLightdata($lightdata);

                        foreach ($objectives as $objective_value) {

                            $score = $objective_value->getScore();
                            if ($score > 0) {
                                $is_validated = true;
                            }
                        }
                    }
                }
                break;

            case "Real Lives":
// if the campaign has real lives url (not null) value , then is validated
                $reallivesurl = $campaign->getReallivesurl();
                if ($reallivesurl) {
                    $is_validated = true;
                }
                break;
            case "Media Idea":
// if there is a file uploadedd for this campaign with file_type_id = 15 , and attached to this task , then is validated
/// 
                $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(15);
                $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                        ->where('f.task = :task AND f.fileType = :fileType')
                        ->orderBy('f.updatedAt', 'DESC')
                        ->setMaxResults(1)
                        ->setParameter('task', $task)
                        ->setParameter('fileType', $fileType)
                        ->getQuery();
                $file = $fileQuery->getOneOrNullResult();

                if ($file) {
                    $is_validated = true;
                }

                break;
            case "Fundamental Channels":

//If ANY of the Matrix Touchpoints is selected , then is validated
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $touchpoints = $this->getDoctrine()->getRepository('LightdataBundle:TouchpointLD')->findByLightdata($lightdata);
                        foreach ($touchpoints as $touchpoint) {
                            $touchpoint_selected = $touchpoint->getSelected();
                            if ($touchpoint_selected) {
                                $is_validated = true;
                            }
                        }
                    }
                }

                break;
            case "Budget Allocation & Mapping":
// if Matrix has any allocated touchpoints populated under BudgetAllocation node, then add 1 point
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $budgetallocation = $this->getDoctrine()->getRepository('LightdataBundle:BudgetAllocationLD')->find($lightdata_id);
                        if ($budgetallocation) {
                            $total = $this->getDoctrine()->getRepository('LightdataBundle:BATotalLD')->findOneBy([
                                'budgetallocation' => $budgetallocation
                            ]);
                            if ($total) {
                                $allocation = $this->getDoctrine()->getRepository('LightdataBundle:BATOAllocationLD')->find($total->getId());
                                if ($allocation) {
                                    $grp = $allocation->getGRP();
                                    if ($grp > 0) {
                                        $is_validated = true;
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case "Phasing":
// old : If Matrix has any data under the TimeAllocation node, then add 1 point
// new : If any of TimeAllocation.Total.AllocationByPeriod[i].GRP  > 0     , is validated.           
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $timeallocation = $this->getDoctrine()->getRepository('LightdataBundle:TimeAllocationLD')->find($lightdata_id);
                        if ($timeallocation) {
                            $total = $this->getDoctrine()->getRepository('LightdataBundle:TATotalLD')->findOneBy([
                                'timeallocation' => $timeallocation
                            ]);
                            if ($total) {
                                $allocationsbyperiod = $this->getDoctrine()->getRepository('LightdataBundle:TATOAllocationByPeriod')->findBy(['allocatedtouchpoint' => $total]);
                                if ($allocationsbyperiod) {
                                    foreach ($allocationsbyperiod as $allocation) {
                                        if ($allocation->getGRP() > 0) {
                                            $is_validated = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case "Final Plan":
//if there is a file uploaded for this campaign with file_type_id=12 and task_name_id=8, then add 1 point (check the project_file table)

                $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(1);
                $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                        ->where('f.task = :task AND f.fileType = :fileType')
                        ->orderBy('f.updatedAt', 'DESC')
                        ->setMaxResults(1)
                        ->setParameter('task', $task)
                        ->setParameter('fileType', $fileType)
                        ->getQuery();
                $file = $fileQuery->getOneOrNullResult();

                if ($file) {
                    $is_validated = true;
                }


                break;
            default:
                //echo "THIS ENTERED INTO DEFAULT MODE";
                break;
        }

        return $is_validated;
    }

    function validate_user_is_able_to_view_this_campaign($user, $campaign) {
        /**
         * Validate that the user is able to view this campaign before continuing 
         */
        $validated_to_display = false;
        $the_campaign_country = $campaign->getCountry();
        $the_campaign_client = $campaign->getClient();
        $the_campaign_region = $the_campaign_country->getRegion();
        $global_region = $this->getDoctrine()->getRepository('CampaignBundle:Region')->find(1);
        $all_clients = $this->getDoctrine()->getRepository('CampaignBundle:Client')->find(1);
        //$met_conditions[$campaign->getId()] = array();
//CASE 1.   
//The user is an administrator , he can see all the campaigns.
        $user_is_admin = $user->hasRole('ROLE_ADMINISTRATOR');

//CASE 2
//The user is allowed to see all countries and all regions for this client.
        $user_global_this_client = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $the_campaign_client,
            'region' => $global_region,
            'all_countries' => true,
        ]);

//CASE 3
//The user is allowed to see all clients for a specific country.
        $user_all_clients_this_country_false = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $all_clients,
            'country' => $the_campaign_country,
            'all_countries' => false,
        ]);

//CASE 4
//The user is allowed to see all clients in a specific region.
        $user_all_clients_one_region_true = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $all_clients,
            'country' => NULL,
            'region' => $the_campaign_region,
            'all_countries' => true,
        ]);

//CASE 5
//The user is allowed to see a client in a specific country.
        $user_client_country_false = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $the_campaign_client,
            'country' => $the_campaign_country,
            'all_countries' => false,
        ]);

        $user_client_region_true = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $the_campaign_client,
            'region' => $the_campaign_region,
            'all_countries' => true,
        ]);

        $user_all_clients_all_regions_true = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $all_clients,
            'region' => $global_region,
            'all_countries' => true,
        ]);
        if ($user_is_admin ||
                $user_client_country_false ||
                $user_client_region_true ||
                $user_all_clients_this_country_false ||
                $user_all_clients_one_region_true ||
                $user_global_this_client ||
                $user_all_clients_all_regions_true) {
            $validated_to_display = true;
        }

        return $validated_to_display;
        /*
         * End of validation for able to view.
         */
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Get individual user information , and tasks related to user.",
     *    statusCodes = {
     *     200 = "Returned when the task was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned When the task was not found in database",
     *         "Returned When the task does not belong to the specified campaign",
     *         "Returned when the user does not have access to the task"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {"name"="user_id",     "dataType"="integer","requirement"="true", "description"="The user unique id"     },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getUsersInfoAction($user_id, Request $request) {


        $response = new Response();

        $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($user_id);
//CHECK THAT THE USER EXISTS IN THE SYSTEM
        if (!$user) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array('success' => false, 'message' => 'There is no user for that user_id.')));
            return $response;
        }


        $roles = $user->getRoles();
        foreach ($roles as $role) {
            $db_role = $this->getDoctrine()->getRepository('UserBundle:Role')->findOneByName($role);
            if ($db_role) {
                $the_role_id = $db_role->getId();
                $the_role_name = $db_role->getName();
                $the_role_sysname = $db_role->getSystemname();
            }
        }


        $primary_user_data = array(
            'user_id' => $user->getId(),
            'user_role_id' => $the_role_id,
            'user_role_name' => $the_role_name,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
        );


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

        $all_the_tasks_of_this_user = array();

        $open_task_status = $this->getDoctrine()->getRepository('TaskBundle:Taskstatus')->find(1);
        $submitted_task_status = $this->getDoctrine()->getRepository('TaskBundle:Taskstatus')->find(2);


        $tasks_where_user_is_owner = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy([
            'owner' => $user,
            'taskstatus' => $open_task_status
        ]);

        foreach ($tasks_where_user_is_owner as $twuio) {
            if ($twuio->getCampaign()->getNotVisible() == false) {
                //ONLY ADD TASKS THAT HAVE THE CAMPAIGN VISIBILITY ENABLED (NOT_VISIBLE = FALSE)
                $all_the_tasks_of_this_user[] = $twuio->getId();
            }
        }
//        print_r($all_the_tasks_of_this_user);
//        die();
//        
//        $tasks_where_user_is_creator = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy(['createdby' => $user]);
//
//        foreach ($tasks_where_user_is_creator as $twuic) {
//            if ($twuic->getCampaign()->getNotvisible() == false) {
////ONLY ADD TASKS THAT HAVE THE CAMPAIGN VISIBILITY ENABLED (NOT_VISIBLE = FALSE) 
//                $all_the_tasks_of_this_user[] = $twuic->getId();
//            }
//        }
//PRELUAM TOATE INTRARILE DIN TEAMMEMBER UNDE USERUL E REVIEWER        
        $teammembers = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')->findBy(['member' => $user, 'is_reviewer' => true]);

//For each campaign where the user is reviewer, grab the campaign's tasks array.
        $campaign_ids_where_user_is_reviewer = array();
        foreach ($teammembers as $teammember) {
            $campaign_ids_where_user_is_reviewer[] = $teammember->getCampaign()->getId();
        }

        $task_ids_of_all_tasks_where_user_is_reviewer_within_campaign = array();
        foreach ($campaign_ids_where_user_is_reviewer as $campaign_id) {
            $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneBy(
                    ['id' => $campaign_id,
                        'not_visible' => false,]
            );
            $tasks_of_this_campaign = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy(['campaign' => $campaign]);
            foreach ($tasks_of_this_campaign as $task) {
                $task_ids_of_all_tasks_where_user_is_reviewer_within_campaign[] = $task->getId();
            }
        }
        foreach ($task_ids_of_all_tasks_where_user_is_reviewer_within_campaign as $task_id) {

            //Validate that the task status is SUBMITTED , and ADD THE ID IF SO.
            $is_task_status_submitted = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy([
                'id' => $task_id,
                'taskstatus' => $submitted_task_status
            ]);
            if ($is_task_status_submitted) {
                $all_the_tasks_of_this_user[] = $task_id;
            }
        }
        $unique_tasks_of_this_user = array_unique($all_the_tasks_of_this_user);
        // print_r($unique_tasks_of_this_user);
        // die("Died @ 3921");

        $returned_task_data_array = array();
        $build_cstatus = $this->getDoctrine()->getRepository('CampaignBundle:Campaignstatus')->find(1);
        $approved_cstatus = $this->getDoctrine()->getRepository('CampaignBundle:Campaignstatus')->find(2);

        foreach ($unique_tasks_of_this_user as $uniquetask) {


            $grabbed_task = $this->getDoctrine()->getRepository('TaskBundle:Task')->find($uniquetask);

            $campaign_of_this_task = $grabbed_task->getCampaign();
            $campaign_status = $campaign_of_this_task->getCampaignstatus();
            $validated_status = false;

            if (($campaign_status == $build_cstatus) || ($campaign_status == $approved_cstatus)) {
                $validated_status = true;
            }

            $proceeed = self::validate_user_is_able_to_view_this_campaign($user, $campaign_of_this_task);

            if ($proceeed && $validated_status) {

                $status_changer = $grabbed_task->getStatuschangedby();
                $task_status = $grabbed_task->getTaskstatus();
                $completed = $this->getDoctrine()->getRepository('TaskBundle:Taskstatus')->find(3);
                $task_status_name = $task_status->getName();
                $completed_name = $completed->getName();

                $task_data = array();
                if ($task_status_name !== $completed_name) {
                    $task_data = array(
                        'campaign_id' => $grabbed_task->getCampaign()->getId(),
                        'campaign_name' => $grabbed_task->getCampaign()->getName(),
                        'task_id' => $grabbed_task->getId(),
                        'task_name' => $grabbed_task->getTaskname()->getName(),
                        'last_task_status' => $grabbed_task->getTaskstatus()->getName(),
                        'last_task_message' => $grabbed_task->getTaskmessage() ? $grabbed_task->getTaskmessage()->getMessage() : null,
                        'last_task_status_date' => $grabbed_task->getTaskstatus() ? date('Y-m-d', $grabbed_task->getTaskstatus()->getUpdatedat()->getTimestamp()) : null,
                        'status_changer_user_id' => $status_changer ? $status_changer->getId() : null,
                        'status_changer_first_name' => $status_changer ? $status_changer->getFirstname() : null,
                        'status_changer_last_name' => $status_changer ? $status_changer->getLastname() : null,
                        'status_changer_profile_picture' => $status_changer ? $status_changer->getProfilepicture() : null,
                        'proceed' => $proceeed,
                    );
                }
                $returned_task_data_array[] = $task_data;
            }
        }
        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'user' => $primary_user_data,
            'tasks_data' => $returned_task_data_array
        )));
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Get individual user information , and tasks related to user.",
     *    statusCodes = {
     *     200 = "Returned when the task was found",
     *     403 = {"Invalid API KEY", "Invalid user id provided."},
     *     404 = {
     *         "Returned When the task was not found in database",
     *         "Returned When the task does not belong to the specified campaign",
     *         "Returned when the user does not have access to the task"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {"name"="user_id",     "dataType"="integer","requirement"="true", "description"="The user unique id"     },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getUsersProfileAction($user_id, Request $request) {


        $response = new Response();

        $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($user_id);
//CHECK THAT THE USER EXISTS IN THE SYSTEM
        if (!$user) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array('success' => false, 'message' => 'There is no user for that user_id.')));
            return $response;
        }


        $roles = $user->getRoles();
        foreach ($roles as $role) {
            $db_role = $this->getDoctrine()->getRepository('UserBundle:Role')->findOneByName($role);
            if ($db_role) {
                $the_role_id = $db_role->getId();
                $the_role_name = $db_role->getName();
                $the_role_sysname = $db_role->getSystemname();
            }
        }


        $primary_user_data = array(
            'user_id' => $user->getId(),
            //'user_role_id' => $the_role_id,
            //'user_role_name' => $the_role_name,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'user_role' => $the_role_sysname,
            'title' => $user->getTitle(),
            'office' => $user->getOffice(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'profile_picture_path' => $user->getProfilepicture(),
            'honey_id' => $user->getHoneyid(),
            'honey_uuid' => $user->getHoneyuuid(),
        );


/////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////



        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            $primary_user_data,
                        //'tasks_data' => $returned_task_data_array
        )));
        return $response;
    }

    /**
     * @Route("/api/v1/users/{user_id}/access ", name="_get_user_access")
     * @Method("GET")
     *
     * @ApiDoc(
     * 		resource = true,
     * 		description = "Get access combinations for a specified user",
     * 		statusCodes = {
     * 			201 = "Returned when the update succeded.",
     * 			400 = "Returned when parameters are not valid.",
     *          403 = "Invalid user ID provided.",
     *          404 = "This user has no user-client-region-country access combination in the database yet."
     * 		},
     * 		requirements = {
     *             {"name" = "user_id"},
     *              {"name" = "_format","requirement" = "json|xml"}
     * 		}
     * )
     *
     */
    public function getUserAccessAction($user_id) {

        $response = new Response();
        $user = $this->getDoctrine()->getRepository("UserBundle:User")->find($user_id);
//Check that the user really exists. Else error.
        if (!$user) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid user ID provided.'
            )));
            return $response;
        }

//If USER exists , grab all useraccesses for that user.      
        $useraccesses = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findByUser($user);
//IF NO USERACCESES FOUND FOR USER , RETURN AN ERROR / MESSAGE
        if (!$useraccesses) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This user has no user-client-region-country access combination in the database yet.'
            )));
            return $response;
        }
        $return_array = array();
        foreach ($useraccesses as $useraccess_entry) {

            $message_array = array(
                'client' => $useraccess_entry->getClient() ? $useraccess_entry->getClient()->getName() : null,
                'region' => $useraccess_entry->getRegion() ? $useraccess_entry->getRegion()->getName() : null,
                'country' => $useraccess_entry->getCountry() ? $useraccess_entry->getCountry()->getName() : null,
                'all_countries' => $useraccess_entry->getAllCountries() ? $useraccess_entry->getAllCountries() : false,
            );
            $return_array[] = $message_array;
        }
        $response->setStatusCode(201);
        $response->setContent(json_encode(array(
//'success' => true,
            'access' => $return_array
                ))
        );
        return $response;
    }

    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "Returns a list of filetype id's and filetype names for a specified taskname ID .",
     *    statusCodes = {
     *     200 = "Returned when the list was succesfully generated was found",
     *     403 = "Invalid API KEY",
     *     404 = {
     *         "Returned When task was not found",
     *         "Returned when the user does not have access to the campaign"
     *     },
     *     500 = "Header x-wsse does not exist"
     *    },
     *    requirements = {
     *       {
     *           "name"="tasknameId",
     *           "dataType"="integer",
     *           "description"="The task unique id"
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * @return array
     * @View()
     */
    public function getTasksFiletypesAction($tasknameId) {

        $response = new Response();

        $taskname = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->find($tasknameId);


        if (!$taskname) {
            $response = new Response();
// Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'That taskname does not exist.'
                    ))
            );
            return $response;
        }

        $filetypes = $taskname->getFiletypes();

        foreach ($filetypes as $filetype) {
            $return_array[] = array(
                'FileTypeId' => $filetype->getId(),
                'FileTypeName' => $filetype->getName(),
            );
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'FileTypes' => $return_array
                        )
        ));

        return $response;
    }

}
