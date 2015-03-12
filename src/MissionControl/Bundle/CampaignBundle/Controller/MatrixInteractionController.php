<?php

namespace MissionControl\Bundle\CampaignBundle\Controller;

use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\LightdataBundle\Entity\Project;
use MissionControl\Bundle\LightdataBundle\Entity\Lightdata;
use MissionControl\Bundle\LightdataBundle\Entity\SetupLD;
use MissionControl\Bundle\LightdataBundle\Entity\ClientLD;
use MissionControl\Bundle\LightdataBundle\Entity\SurveyLD;
use MissionControl\Bundle\LightdataBundle\Entity\TargetLD;
use MissionControl\Bundle\LightdataBundle\Entity\ObjectiveLD;
use MissionControl\Bundle\LightdataBundle\Entity\GroupingLD;
use MissionControl\Bundle\LightdataBundle\Entity\GroupingCategoryLD;
use MissionControl\Bundle\LightdataBundle\Entity\GroupingTouchpointCategoryMapLD;
use MissionControl\Bundle\LightdataBundle\Entity\TouchpointLD;
use MissionControl\Bundle\LightdataBundle\Entity\TouchpointAttributeScoreLD;
use MissionControl\Bundle\LightdataBundle\Entity\TouchpointObjectiveScoreLD;
use MissionControl\Bundle\LightdataBundle\Entity\CPRAttributeLD;
use MissionControl\Bundle\LightdataBundle\Entity\BudgetAllocationLD;
use MissionControl\Bundle\LightdataBundle\Entity\BAAllocatedTouchpointLD;
use MissionControl\Bundle\LightdataBundle\Entity\BAATAllocationLD;
use MissionControl\Bundle\LightdataBundle\Entity\BAATAResultLD;
use MissionControl\Bundle\LightdataBundle\Entity\BAATARIndividualPerformanceLD;
use MissionControl\Bundle\LightdataBundle\Entity\BATotalLD;
use MissionControl\Bundle\LightdataBundle\Entity\BATOAllocationLD;
use MissionControl\Bundle\LightdataBundle\Entity\BATOAResultLD;
use MissionControl\Bundle\LightdataBundle\Entity\BATOARIndividualPerformanceLD;
use MissionControl\Bundle\LightdataBundle\Entity\TimeAllocationLD;
use MissionControl\Bundle\LightdataBundle\Entity\TAAllocatedTouchpointLD;
use MissionControl\Bundle\LightdataBundle\Entity\TAATAllocationByPeriod;
use MissionControl\Bundle\LightdataBundle\Entity\TAATABPResult;
use MissionControl\Bundle\LightdataBundle\Entity\TAATABPRIndividualPerformance;
use MissionControl\Bundle\LightdataBundle\Entity\TATotalLD;
use MissionControl\Bundle\LightdataBundle\Entity\TATOAllocationByPeriod;
use MissionControl\Bundle\LightdataBundle\Entity\TATOABPResult;
use MissionControl\Bundle\LightdataBundle\Entity\TATOABPRIndividualPerformance;
use MissionControl\Bundle\LightdataBundle\Entity\WhatIfResult;
use MissionControl\Bundle\LightdataBundle\Entity\WIRConfig;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPoint;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPCMDetail;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPCMTotal;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPOMDetail;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPOMTotal;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPSTMDetail;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPSTMTotal;
use MissionControl\Bundle\LightdataBundle\Entity\WIRCOptimizedFunction;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPCurrentMix;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPOptimizedMix;
use MissionControl\Bundle\LightdataBundle\Entity\WIRPSingleTouchpointMix;
use MissionControl\Bundle\CampaignBundle\Entity\Product;
use MissionControl\Bundle\CampaignBundle\Model\FileType;
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

class MatrixInteractionController extends FOSRestController {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * @ApiDoc(
     *      description = "Method generates a link that will be used for opening the Matrix application for the first time.",
     *      statusCodes = {
     *     200 = "Returned when matrix link has been succesfully returned",
     *     403 = "Invalid API KEY",
     *     404 = "Campaign does not exist for that id.",
     *     500 = "Header x-wsse does not exist"
     *    },
     *      requirements = {
     *          { "name"="campaignId", "dataType"="string", "description"="The campaign unique id."}
     *      }
     * 
     * )
     *
     * @Route("v1/campaigns/{campaignId}/createurl", name="_get_create_matrix_link")
     * @Method("GET")
     *
     *
     *
     */
    public function getCreateLinkAction($campaignId, Request $request) {
        $response = new Response();
        // Retrieve repository for the Campaign:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);


        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist for that id.'
            )));

            return $response;
        }
        // Create link for indicating action for Matrix:
        $matrixLink = 'mdtp://matrix?action=create&token=' . $campaign->getToken();

        // Prepare response to be returned to client:

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent(json_encode(array(
            'Matrix Link' => $matrixLink
                        )
        ));

        return $response;
    }

// End of getCreateLinkAction() method.

    /**
     * @ApiDoc(
     *      description = "Method generates a link that will be used for opening the Matrix application for editing subsequent steps.",
     *      statusCodes={
     *          200 = "Returned when matrix link has been succesfully returned.",
     *          403 = "Invalid API KEY",
     *          404 = "Campaign does not exist for that id.",
     *          500 = "Header x-wsse does not exist"
     *      },
     *      requirements = {
     *          { "name"="campaignId", "dataType"="string", "description"="The campaign unique id."}
     *      }
     * )
     *
     * @Route("v1/campaigns/{campaignId}/editurl", name="_get_edit_matrix_link")
     * @Method("GET")
     *
     *
     *
     */
    public function getEditLinkAction($campaignId, Request $request) {
        $response = new Response();
        // Retrieve repository for the Campaign:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);

        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Campaign does not exist for that id.'
            )));

            return $response;
        }


        // Create link for indicating action for Matrix:
        $matrixLink = 'mdtp://matrix?action=edit&token=' . $campaign->getToken();

        // Prepare response to be returned to client:

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent(json_encode(array(
            'Matrix Link' => $matrixLink
                        )
        ));

        return $response;
    }

// End of getEditLinkAction() method.

    /**
     * @ApiDoc(
     *      description = "Method returns information for matrix to make updates on specific screen.",
     *      statusCodes ={
     *      200 = "OK",
     *      404 = "No campaign for this token.",
     * },
     *      requirements = {
     *          { "name"="token", "dataType"="string", "description"="The campaign unique token."}
     *      }
     * )
     *
     * @Route("v1/campaignactions/{token}", name="_get_campaign_token")
     * @Method("GET")
     *
     *
     *
     */
    public function getCampaignTokenAction($token, Request $request) {

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneBy(['token' => $token]);
        $response = new Response();
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'No campaign for this token.'
            )));
            return $response;
        }
        // Prepare response to be returned to client:

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent(json_encode(array(
            'campaign_id' => $campaign->getId(),
            'matrix_file_uuid' => $campaign->getMatrixfileUuid(),
            'screen_type' => $campaign->getScreenType()
                        )
        ));

        return $response;
    }

// End of getCampaignTokenAction() method.
    /**
     * @ApiDoc(
     *      description = "Method that provides the latest lightdata STRING for a specific campaign.",
     *      statusCodes={
     *      200 = "OK",
     *      403 = "Returned when Api Key is invalid",
     *      404 = "Returned when campaign id provided is invalid",
     *      500 = "Header x-wsse does not exist",
     * },
     *      requirements = {
     *          { "name"="campaignId","dataType"="string","description"="The campaign unique id.",
     *            "name"="_format",               "dataType"="string","requirement"="json|xml","description"="Format"},
     *      }
     * )
     *
     * @Route("v1/campaigns/{campaignId}/lightdata.{_format}", name="_get_campaign_latest_lightdata")
     * @Method("GET")
     *
     *
     */
    public function getCampaignLightdata($campaignId, Request $request) {
        $response = new Response();
        $em = $this->getDoctrine()->getManager();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

        if (!$campaign) {

            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Requested campaign is not available in our database.'
                    ))
            );
            return $response;
        }

        $lightdata_id = $campaign->getLightdata();
        if ($lightdata_id) {

            $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);

            if ($lightdata) {

                $initial_string = $lightdata->getInputstring();
                $decoded_string = json_decode($initial_string, true);

                return $decoded_string;
            }
        }

        $response->setContent(json_encode(array(
            'success' => false,
            'message' => 'This campaign has no lightdata.',
                        )
        ));

        return $response;
    }

    /**
     * @ApiDoc(
     *      description = "Method that receives lightdata STRING (data) and stores it into the campaign table.",
     *      statusCodes = {
     *      200 = "OK",
     *      403 = {"Api Key provided is invalid", "Cannot update EMPTY data to campaign."},
     *      404 = "Campaign not availlabe in database",
     *      500 = "Token not found in header",
     *      
     * },
     *      requirements = {
     *          { "name"="campaignId",
     *            "dataType"="string",
     *            "description"="The campaign unique id."
     *          },
     *          {
     *           "name"="data",
     *           "dataType"="text",
     *           "description"="The lightdata string."
     *          }
     *      }
     * )
     *
     * @Route("v1/campaigns/{campaignId}/lightdata", name="_update_campaign_lightdata")
     * @Method("PUT")
     *
     *
     */
    public function putCampaignLightdata($campaignId, Request $request) {
        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());

        $em = $this->getDoctrine()->getManager();

        //Fetch The That The LightData Needs To Be Attached, or Error.

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

        if (!$campaign) {
            $response = new Response();
            // Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Requested campaign is not available in our database.'
                    ))
            );
            return $response;
        }

        //Fetch The Old Campaign Completeness
        $old_campaign_completeness = $campaign->getCompleteness();

        //Get the data string from the request.
        $data = $request->get('data');


        if (empty($data)) {
            $response = new Response();
            // Set response data:
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Cannot update EMPTY data to campaign.'
                    ))
            );
            return $response;
        }


        //Call the Parsing Function and Store the Response.
        $response_returned_after_parsing = self::parseTheLightdataStringFromRequest($data);


        //Grab lightdata Id 
        $lightdata_response_content = json_decode($response_returned_after_parsing->getContent('lightdata_id'), true);
        //Grab start_date from Lightdata Json
        $lightdata_start_date = json_decode($response_returned_after_parsing->getContent('start_date'), true);

        if ($lightdata_start_date['start_date']['date']) {
            $start_date_object = new \DateTime($lightdata_start_date['start_date']['date']);
            $start_date_object->setTimezone(self::timezoneUTC());
        } else {
            $start_date_object = null;
        }
        ////ERROR MESSAGE

        $error_message = null;
        $lightdata_had_errors = $lightdata_response_content['error_on_parsing'];
        if ($lightdata_had_errors) {
            $error_message = "There was a problem storing the matrix file. Please contact your administrator for campaign $campaignId";
        }
        /////////////

        $main_missing_nodes = $lightdata_response_content['main_missing_nodes'];
        $setup_missing_nodes = $lightdata_response_content['setup_missing_nodes'];
        $groupings_missing_nodes = $lightdata_response_content['groupings_missing_nodes'];
        $touchpoints_missing_nodes = $lightdata_response_content['touchpoints_missing_nodes'];
        $budgetallocation_missing_nodes = $lightdata_response_content['budgetallocation_missing_nodes'];
        $timeallocation_missing_nodes = $lightdata_response_content['timeallocation_missing_nodes'];

        $lightdata_id = $lightdata_response_content['lightdata_id'];

        $lightdata_object = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);


        $number_of_lightdatas_for_this_campaign = count($this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->findByCampaign($campaign->getId()));
        $version = $number_of_lightdatas_for_this_campaign + 1;

        $lightdata_object->setCampaign($campaign->getId());
        $lightdata_object->setVersion($version);
        $campaign->setStartdate($start_date_object);
        $campaign->setLightdata($lightdata_id);
        $campaign->setLightdataversion($version);
        $campaign->setUpdatedat($updateDate);

        $em->flush();


        if ($lightdata_had_errors) {

            $output_message = 'Parsing Errored . Campaign ' . $campaignId . '  update failed with the lightdata content provided.';
        } else {
            $new_campaign_completeness = self::recalculate_campaign_completeness($campaign);
            $output_message = 'Successfully Parsed . Campaign ' . $campaignId . ' successfully updated with the lightdata content provided.';
        }
        $campaign->setCompleteness($new_campaign_completeness);
        $em->flush();

        // Prepare response to be returned to client:
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent(json_encode(array(
            'success' => true,
            'message' => $output_message, //'Campaign ' . $campaignId . ' successfully updated with the lightdata content provided.',
            'version' => $version,
            'old_campaign_completeness' => $old_campaign_completeness,
            'new_campaign_completeness' => $new_campaign_completeness,
            'errors' => $lightdata_had_errors,
            'error_message' => $error_message,
//            'main_missing_nodes_DEBUG_ONLY' => $main_missing_nodes,
//            'setup_missing_nodes_DEBUG_ONLY' => $setup_missing_nodes,
//            'groupings_missing_nodes_DEBUG_ONLY' => $groupings_missing_nodes,
//            'touchpoints_missing_nodes_DEBUG_ONLY' => $touchpoints_missing_nodes,
//            'budgetallocation_missing_nodes_DEBUG_ONLY' => $budgetallocation_missing_nodes,
//            'timeallocation_missing_nodes_DEBUG_ONLY' => $timeallocation_missing_nodes,
                        )
        ));

        return $response;
    }

    public function parseTheLightdataStringFromRequest($datastring) {

        //Instantiate an empty array for each possible type of missing nodes within the lightdata string.
        $array_of_missing_nodes = array();
        $missing_setup_nodes = array();
        $missing_groupings_node = array();
        $touchpoints_missing_nodes = array();
        $budgetallocation_missing_nodes = array();
        $timeallocation_missing_nodes = array();
        $the_start_date = null;
        //Instantiate a variable to check if there were errors on parsing the data to the db (if the string was malformed)
        $error_on_parsing = false;

        $contentOfJsonFile = $datastring;
        $em = $this->getDoctrine()->getManager();

        //Create a new Lightdata UUID
        $lightdata_uuid = Uuid::uuid4()->toString();


        $lightdata = new Lightdata();

        $lightdata->setInputstring($contentOfJsonFile);

        //Decode the Json File to an Array.
        $arrayOfJsonObjects = json_decode($contentOfJsonFile, true);


        $project_data = $arrayOfJsonObjects;
        if (isset($arrayOfJsonObjects['Project'])) {
            $project_data = $arrayOfJsonObjects['Project'];
        }

        //INSTANTIATE THE LIGHTDATA OBJECT
        ////////////////////////////////////////////////////////////////
        //Assign Project SETUP Data
        ////////////////////////////////////////////////////////////////
        //Grab setup array from the whole array.


        if (isset($project_data['Setup'])) {
            $setup_data = $project_data['Setup'];

            $client = new ClientLD();
            $target = new TargetLD();
            $survey = new SurveyLD();
            $setup = new SetupLD();

            //Assign The Client Data or add Node to Missing Setup Nodes.

            if (isset($setup_data['Client'])) {
                $client->setName($setup_data['Client']['Name']);
                $client->setDbid($setup_data['Client']['DbID']);
                $setup->setClient($client);
            } else {
                $missing_setup_nodes[] = 'Client';
            }

            //Assign The Target Data or add Node to Missing Setup Nodes.

            if (isset($setup_data['Target'])) {
                $target->setName($setup_data['Target']['Name']);
                $target->setDbid($setup_data['Target']['DbID']);
                $setup->setTarget($target);
            } else {
                $missing_setup_nodes[] = 'Target';
            }

            //Assign The Survey Data or add Node to Missing Setup Nodes.

            if (isset($setup_data['Survey'])) {
                $survey->setName($setup_data['Survey']['Name']);
                $survey->setDbid($setup_data['Survey']['DbID']);
                $setup->setSurvey($survey);
            } else {
                $missing_setup_nodes[] = 'Survey';
            }

            //Assign The ProjectName Data or add Node to Missing Setup Nodes.

            if (isset($setup_data['ProjectName'])) {
                $setup->setProjectName($setup_data['ProjectName']);
            } else {
                $missing_setup_nodes[] = 'ProjectName';
            }

            //Assign The Start Date or add Node to Missing Setup Nodes.

            if (isset($setup_data['StartDate'])) {
                $setup->setStartDate(new \DateTime($setup_data['StartDate']));
                $the_start_date = new \DateTime($setup_data['StartDate']);
            } else {
                $missing_setup_nodes[] = 'StartDate';
                $the_start_date = null;
            }

            //Assign The PeriodType Data or add Node to Missing Setup Nodes.

            if (isset($setup_data['PeriodType'])) {
                $setup->setPeriodType($setup_data['PeriodType']);
            } else {
                $missing_setup_nodes[] = 'PeriodType';
            }

            //Assign The NbPeriods Data or add Node to Missing Setup Nodes.

            if (isset($setup_data['NbPeriods'])) {
                $setup->setNbPeriods($setup_data['NbPeriods']);
            } else {
                $missing_setup_nodes[] = 'NbPeriods';
            }

            //Assign The Budget Data or add Node to Missing Setup  Nodes.

            if (isset($setup_data['Budget'])) {
                $setup->setBudget($setup_data['Budget']);
            } else {
                $missing_setup_nodes[] = 'Budget';
            }

            //Assign The BudgetCurrency Data or add Node to Missing Setup Nodes.

            if (isset($setup_data['BudgetCurrency'])) {
                $setup->setBudgetCurrency($setup_data['BudgetCurrency']);
            } else {
                $missing_setup_nodes[] = 'BudgetCurrency';
            }
            $setup->setLightdata($lightdata);
            $lightdata->setSetup($setup);
        } else {
            $error_on_parsing = true;
            $array_of_missing_nodes[] = "Setup";
        }

        //Assign The CurrentGroupingIndex Data or add Node to Missing Nodes.

        if (isset($project_data['CurrentGroupingIndex'])) {
            $lightdata->setCurrentgroupingindex($project_data['CurrentGroupingIndex']);
        } else {
            $array_of_missing_nodes[] = 'CurrentGroupingIndex';
        }
        ////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////
        //Assign OBJECTIVES Data
        ////////////////////////////////////////////////////////////////
        //ONLY ADD IF EXISTS
        if (isset($project_data['Objectives'])) {
            $objectives_data = $project_data['Objectives'];

            foreach ($objectives_data as $objective_data) {
                $objective = new ObjectiveLD();
                $objective->setName($objective_data['Name']);
                $objective->setHtmlcolor($objective_data['HtmlColor']);
                $objective->setSelected($objective_data['Selected']);
                $objective->setScore($objective_data['Score']);
                $objective->setLightdata($lightdata);
                $lightdata->addObjective($objective);
            }
        } else {
            $array_of_missing_nodes[] = 'Objectives';
        }
        ////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////
        //Assign GROUPINGS Data
        ////////////////////////////////////////////////////////////////


        if (isset($project_data['Groupings'])) {
            $groupings_data = $project_data['Groupings'];


            foreach ($groupings_data as $grouping_data) {
                if (isset($grouping_data['Name'])) {
                    $grouping = new GroupingLD();

                    $grouping->setName($grouping_data['Name']);

                    $grouping->setLightdata($lightdata);

                    if (isset($grouping_data['Categories'])) {
                        foreach ($grouping_data['Categories'] as $groupings_cateogry) {
                            $groupingcategory = new GroupingCategoryLD();
                            $groupingcategory->setGrouping($grouping);
                            $groupingcategory->setName($groupings_cateogry['Name']);
                            $groupingcategory->setHtmlcolor($groupings_cateogry['HtmlColor']);
                            $grouping->addGroupingcategory($groupingcategory);
                        }
                    } else {
                        $missing_groupings_node[] = "Categories";
                    }

                    if (isset($grouping_data['TouchpointCategoryMap'])) {
                        foreach ($grouping_data['TouchpointCategoryMap'] as $key => $value) {
                            $groupingstouchpointcategorymap = new GroupingTouchpointCategoryMapLD();
                            $groupingstouchpointcategorymap->setGrouping($grouping);
                            $groupingstouchpointcategorymap->setName($key);
                            $groupingstouchpointcategorymap->setValue($value);
                            $grouping->addGroupingtouchpointcategorymap($groupingstouchpointcategorymap);
                        }
                    } else {
                        $missing_groupings_node[] = "TouchpointCategoryMap";
                    }
                    $lightdata->addGrouping($grouping);
                } else {
                    $missing_groupings_node[] = "Groupings Node Availlable, Data Inside Missing.";
                }
            }
        } else {
            $array_of_missing_nodes[] = "Groupings";
        }
        //END FOREACH GROUPING
        ////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////    
        //ASSIGN THE CURRENT GROUPING INDEX , TO THE LIGHTDATA ENTITI DIRECTLY
        //ASSIGN THE CURRENT GROUPING INDEX , TO THE LIGHTDATA ENTITI DIRECTLY    
        ////////////////////////////////////////////////////////////////    
        ////////////////////////////////////////////////////////////////
        //ASSIGN TOUCHPOINTS , OBJECTIVESCORES AND ATTRIBUTESCORES FOR EACH TOUCHPOINT
        //ASSIGN TOUCHPOINTS , OBJECTIVESCORES AND ATTRIBUTESCORES FOR EACH TOUCHPOINT    

        if (isset($project_data['Touchpoints'])) {
            $touchpoints_data = $project_data['Touchpoints'];

            foreach ($touchpoints_data as $touchpoint_data) {
                $touchpoint = new TouchpointLD();
                $touchpoint->setName($touchpoint_data['Name']);
                $touchpoint->setLocalname($touchpoint_data['LocalName']);
                $touchpoint->setHtmlcolor($touchpoint_data['HtmlColor']);
                $touchpoint->setSelected($touchpoint_data['Selected']);
                $touchpoint->setAggobjectivescore($touchpoint_data['AggObjectiveScore']);
                $touchpoint->setLightdata($lightdata);

                //EVEN IF TOUCHPOINTS NODE IS AVAILLABLE , WE SHOULD CHECK IF THERE ARE ANY TOUCHPOINTS SET.
                //EVEN IF TOUCHPOINTS NODE IS AVAILLABLE , WE SHOULD CHECK IF THERE ARE ANY TOUCHPOINTS SET.

                if (isset($touchpoint_data['ObjectiveScores'])) {
                    foreach ($touchpoint_data['ObjectiveScores'] as $touchpoint_objectivescore) {
                        $touchpointObjectiveScore = new TouchpointObjectiveScoreLD();
                        $touchpointObjectiveScore->setValue($touchpoint_objectivescore);
                        $touchpointObjectiveScore->setTouchpoint($touchpoint);
                        $touchpoint->addTouchpointobjectivescore($touchpointObjectiveScore);
                    }
                } else {
                    $touchpoints_missing_nodes[] = "ObjectiveScores";
                }

                if (isset($touchpoint_data['AttributeScores'])) {
                    foreach ($touchpoint_data['AttributeScores'] as $touchpoint_attributescore) {
                        $touchpointAttributeScore = new TouchpointAttributeScoreLD();
                        $touchpointAttributeScore->setValue($touchpoint_attributescore);
                        $touchpointAttributeScore->setTouchpoint($touchpoint);
                        $touchpoint->addTouchpointattributescore($touchpointAttributeScore);
                    }
                } else {
                    $touchpoints_missing_nodes[] = "AttributeScores";
                }
                $lightdata->addTouchpoint($touchpoint);
            }
        } else {
            $array_of_missing_nodes[] = "Touchpoints";
        }

        ////////////////////////////////////////////////////////////////    
        ////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        //ASSIGN CPRATTRIBUTES 
        ////////////////////////////////////////////////////

        if (isset($project_data['CPRAttributes'])) {

            $cprattributes_data = $project_data['CPRAttributes'];
            foreach ($cprattributes_data as $cprattribute_data) {
                $cprattribute = new CPRAttributeLD();
                $cprattribute->setName($cprattribute_data['Name']);
                $cprattribute->setDescription($cprattribute_data['Description']);
                $cprattribute->setSelected($cprattribute_data['Selected']);
                $cprattribute->setLightdata($lightdata);
                $lightdata->addCprattribute($cprattribute);
            }
        } else {
            $array_of_missing_nodes[] = "CPRAttributes";
        }
        ///////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////
        ////ASSIGN BUDGETALLOCATION
        ///////////////////////////////////////////////////////////////
        if (isset($project_data['BudgetAllocation'])) {
            $budgetallocation_data = $project_data['BudgetAllocation'];

            $budgetallocation = new BudgetAllocationLD();
            $lightdata->setBudgetallocation($budgetallocation);
            $budgetallocation->setLightdata($lightdata);

            ///ADD THE BUDGETALLOCATION ALLOCATEDTOUCHPOINTS
            if (isset($budgetallocation_data['AllocatedTouchpoints'])) {
                $ba_allocatedtouchpoints = $budgetallocation_data['AllocatedTouchpoints'];
                foreach ($ba_allocatedtouchpoints as $budgetallocation_allocatedtouchpoint) {
                    $AllocatedTouchpoint = new BAAllocatedTouchpointLD();
                    $AllocatedTouchpoint->setTouchpointname($budgetallocation_allocatedtouchpoint['TouchpointName']);
                    $AllocatedTouchpoint->setBudgetallocation($budgetallocation);
                    $budgetallocation->addAllocatedtouchpoint($AllocatedTouchpoint);

                    $allocation_data = $budgetallocation_allocatedtouchpoint['Allocation'];
                    $allocation = new BAATAllocationLD();
                    $allocation->setAllocatedtouchpoint($AllocatedTouchpoint);
                    $allocation->setBudget($allocation_data['Budget']);
                    $allocation->setCostpergrp($allocation_data['CostPerGRP']);
                    $allocation->setGrp($allocation_data['GRP']);
                    $AllocatedTouchpoint->setAllocation($allocation);

                    $result_data = $allocation_data['Result'];
                    $Result = new BAATAResultLD();
                    $Result->setAllocation($allocation);
                    $Result->setGlobalperformance($result_data['GlobalPerformance']);
                    $Result->setReach($result_data['Reach']);
                    $allocation->setResult($Result);


                    if (isset($result_data['IndividualPerformance'])) {
                        $individual_performances_data = $result_data['IndividualPerformance'];

                        foreach ($individual_performances_data as $individual_performance_data) {

                            $IndividualPerformance = new BAATARIndividualPerformanceLD();
                            $IndividualPerformance->setResult($Result);
                            $IndividualPerformance->setValue($individual_performance_data);
                            $Result->addIndividualperformance($IndividualPerformance);
                        }
                    } else {
                        //RETURN THAT NODE RESULT INDIVIDUALPERFORMANCE IS MISSING
                    }
                }
            } else {
                //RETURN THAT NODE RESULT ALLOCATEDTOUCHPOINTS IS MISSING
            }

            ///ADD THE BUDGETALLOCATION TOTAL
            if (isset($budgetallocation_data['Total'])) {
                $ba_total = $budgetallocation_data['Total'];
                $budgetallocation_total = new BATotalLD();
                $budgetallocation_total->setTouchpointname($ba_total['TouchpointName']);
                $budgetallocation_total->setBudgetallocation($budgetallocation);
                $budgetallocation->addTotal($budgetallocation_total);

                $total_data = $ba_total['Allocation'];
                $allocation = new BATOAllocationLD();
                $allocation->setAllocatedtouchpoint($budgetallocation_total);
                $allocation->setBudget($total_data['Budget']);
                $allocation->setCostpergrp($total_data['CostPerGRP']);
                $allocation->setGrp($total_data['GRP']);
                $budgetallocation_total->setAllocation($allocation);

                $result_data = $total_data['Result'];
                $Result = new BATOAResultLD();
                $Result->setAllocation($allocation);
                $Result->setGlobalperformance($result_data['GlobalPerformance']);
                $Result->setReach($result_data['Reach']);
                $allocation->setResult($Result);

                if (isset($result_data['IndividualPerformance'])) {
                    $individual_performances_data = $result_data['IndividualPerformance'];
                    foreach ($individual_performances_data as $individual_performance_data) {

                        $IndividualPerformance = new BATOARIndividualPerformanceLD();
                        $IndividualPerformance->setResult($Result);
                        $IndividualPerformance->setValue($individual_performance_data);
                        $Result->addIndividualperformance($IndividualPerformance);
                    }
                } else {
                    //RETURN THAT NODE RESULTDATA INDIVIDUALPERFORMANCE IS MISSING
                }
            } else {
                //RETURN THAT NODE BUDGETALLOCATION_TOTAL IS MISSING
            }
        } else {
            //RETURN THAT WHOLE BUDGETALLOCATION NODE IS MISSING
            $array_of_missing_nodes[] = "BudgetAllocation";
        }
        /////////////////////////////////////////
        // END OF ASSIGNS FOR BUDGETALLOCATION
        /////////////////////////////////////////
        ///////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////
        ////ASSIGN TIME ALLOCATION
        ///////////////////////////////////////////////////////////////
        if (isset($project_data['TimeAllocation'])) {
            $timeallocation_data = $project_data['TimeAllocation'];

            $timeallocation = new TimeAllocationLD();

            $lightdata->setTimeallocation($timeallocation);
            $timeallocation->setLightdata($lightdata);


            ///ADD THE TIMEALLOCATION ALLOCATEDTOUCHPOINTS
//$timeallocation_missing_nodes
            if (isset($timeallocation_data['AllocatedTouchpoints'])) {
                $timeallocation_allocatedtouchpoints_data = $timeallocation_data['AllocatedTouchpoints'];
                foreach ($timeallocation_allocatedtouchpoints_data as $ta_allocatedtouchpoint) {
                    $TAAllocatedTouchpoint = new TAAllocatedTouchpointLD();
                    $TAAllocatedTouchpoint->setTouchpointname($ta_allocatedtouchpoint['TouchpointName']);
                    $TAAllocatedTouchpoint->setReachfrequency($ta_allocatedtouchpoint['ReachFrequency']);
                    $TAAllocatedTouchpoint->setTimeallocation($timeallocation);
                    $timeallocation->addAllocatedtouchpoint($TAAllocatedTouchpoint);

                    $allocations_by_period_data = $ta_allocatedtouchpoint['AllocationByPeriod'];
                    foreach ($allocations_by_period_data as $allocation_by_period) {
                        $AllocationByPeriod = new TAATAllocationByPeriod();
                        $AllocationByPeriod->setAllocatedtouchpoint($TAAllocatedTouchpoint);
                        $AllocationByPeriod->setBudget($allocation_by_period['Budget']);
                        $AllocationByPeriod->setCostpergrp($allocation_by_period['CostPerGRP']);
                        $AllocationByPeriod->setGrp($allocation_by_period['GRP']);
                        $TAAllocatedTouchpoint->addAllocationbyperiod($AllocationByPeriod);

                        $ABPresult_data = $allocation_by_period['Result'];
                        $ABPResult = new TAATABPResult();
                        $ABPResult->setAllocationbyperiod($AllocationByPeriod);
                        $ABPResult->setGlobalperformance($ABPresult_data['GlobalPerformance']);
                        $ABPResult->setReach($ABPresult_data['Reach']);
                        $AllocationByPeriod->setResult($ABPResult);

                        $ABPindividual_performances_data = $ABPresult_data['IndividualPerformance'];
                        foreach ($ABPindividual_performances_data as $individual_performance_data) {

                            $ABPIndividualPerformance = new TAATABPRIndividualPerformance();
                            $ABPIndividualPerformance->setResult($ABPResult);
                            $ABPIndividualPerformance->setValue($individual_performance_data);
                            $ABPResult->addIndividualperformance($ABPIndividualPerformance);
                        }
                    }
                }
            } else {
                $timeallocation_missing_nodes[] = "AllocatedTouchpoints";
            }


            ///ADD THE TIMEALLOCATION TOTAL
            //ONLYADD TOTAL IF IS SET
            if (isset($timeallocation_data['Total'])) {
                $timeallocation_total_data = $timeallocation_data['Total'];
                $TATotal = new TATotalLD();
                $TATotal->setTimeallocation($timeallocation);
                $TATotal->setTouchpointname($timeallocation_total_data['TouchpointName']);
                $TATotal->setReachfrequency($timeallocation_total_data['ReachFrequency']);
                $timeallocation->addTotal($TATotal);


                if (isset($timeallocation_total_data['AllocationByPeriod'])) {
                    $allocations_by_period_data_total = $timeallocation_total_data['AllocationByPeriod'];
                    foreach ($allocations_by_period_data_total as $allocation_by_period) {
                        $TotalAllocationByPeriod = new TATOAllocationByPeriod();
                        $TotalAllocationByPeriod->setAllocatedtouchpoint($TATotal);
                        $TotalAllocationByPeriod->setBudget($allocation_by_period['Budget']);
                        $TotalAllocationByPeriod->setCostpergrp($allocation_by_period['CostPerGRP']);
                        $TotalAllocationByPeriod->setGrp($allocation_by_period['GRP']);
                        $TATotal->addAllocationbyperiod($TotalAllocationByPeriod);


                        $TotalABPresult_data = $allocation_by_period['Result'];
                        $TotalABPResult = new TATOABPResult();
                        $TotalABPResult->setAllocationbyperiod($TotalAllocationByPeriod);
                        $TotalABPResult->setGlobalperformance($TotalABPresult_data['GlobalPerformance']);
                        $TotalABPResult->setReach($TotalABPresult_data['Reach']);
                        $TotalAllocationByPeriod->setResult($TotalABPResult);

                        $TotalABPindividual_performances_data = $TotalABPresult_data['IndividualPerformance'];
                        foreach ($TotalABPindividual_performances_data as $individual_performance_data) {

                            $TOABPIndividualPerformance = new TATOABPRIndividualPerformance();
                            $TOABPIndividualPerformance->setResult($TotalABPResult);
                            $TOABPIndividualPerformance->setValue($individual_performance_data);
                            $TotalABPResult->addIndividualperformance($TOABPIndividualPerformance);
                        }
                    }
                }
            } else {
                $timeallocation_missing_nodes[] = "Total";
            }
        } else {
            $array_of_missing_nodes[] = "TimeAllocation";
        }
        ////END ASSIGN TIME ALLOCATION   
        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
        //ONLY IF THE WHATIFRESULT IS AVAILLABLE INTO THE JSON UPDATED
        if (isset($project_data['WhatIfResult'])) {



            $whatifresult_data = $project_data['WhatIfResult'];




            $WhatIfResult = new WhatIfResult();
            ////////////////////////////////////////////
            $WhatIfResult->setLightdata($lightdata);
            $lightdata->setWhatifresult($WhatIfResult);
            ////////////////////////////////////////////
            $wirconfig_data = $whatifresult_data['Config'];
            $WIRConfig = new WIRConfig();
            ////////////////////////////////////////////
            $WIRConfig->setWhatifresult($WhatIfResult);
            $WhatIfResult->setConfig($WIRConfig);
            ////////////////////////////////////////////
            $WIRConfig->setFirstperiod($wirconfig_data['FirstPeriod']);
            $WIRConfig->setLastperiod($wirconfig_data['LastPeriod']);
            $WIRConfig->setSourcebudget($wirconfig_data['SourceBudget']);
            $WIRConfig->setBudgetminpercent($wirconfig_data['BudgetMinPercent']);
            $WIRConfig->setBudgetmaxpercent($wirconfig_data['BudgetMaxPercent']);
            $WIRConfig->setBudgetsteppercent($wirconfig_data['BudgetStepPercent']);
            $WIRConfig->setHascurrentmix($wirconfig_data['HasCurrentMix']);
            $WIRConfig->setHassingletouchpointmix($wirconfig_data['HasSingleTouchpointMix']);
            $WIRConfig->setHasoptimizedmix($wirconfig_data['HasOptimizedMix']);
            $wircoptimizedfunction_data = $wirconfig_data['OptimizedFunction'];
            $WIRCOptimizedFunction = new WIRCOptimizedFunction();
            ////////////////////////////////////////////
            $WIRCOptimizedFunction->setConfig($WIRConfig);
            $WIRConfig->setOptimizedfunction($WIRCOptimizedFunction);
            ////////////////////////////////////////////
            $WIRCOptimizedFunction->setCalculationtype($wircoptimizedfunction_data['CalculationType']);
            $WIRCOptimizedFunction->setAttributeindex($wircoptimizedfunction_data['AttributeIndex']);


            $wirpoints_data = $whatifresult_data['Points'];
            foreach ($wirpoints_data as $wirpoint_data) {
                $WIRPoint = new WIRPoint();
                $WIRPoint->setWhatifresult($WhatIfResult);
                $WIRPoint->setStepposition($wirpoint_data['StepPosition']);
                $WIRPoint->setActualpercent($wirpoint_data['ActualPercent']);
                $WhatIfResult->addPoint($WIRPoint);


                //ASSIGN THE CURRENT MIX DATA
                //ASSIGN THE CURRENT MIX DATA
                $currentmix_data = $wirpoint_data['CurrentMix'];
                $WIRPCurrentMix = new WIRPCurrentMix();
                $WIRPCurrentMix->setPoint($WIRPoint);
                $WIRPoint->setCurrentmix($WIRPCurrentMix);

                $wirp_currentmix_details_data = $currentmix_data['Details'];
                foreach ($wirp_currentmix_details_data as $detail_data) {
                    $WIRPCMDetail = new WIRPCMDetail();
                    $WIRPCMDetail->setCurrentmix($WIRPCurrentMix);
                    $WIRPCMDetail->setTouchpointname($detail_data['TouchpointName']);
                    $WIRPCMDetail->setBudget($detail_data['Budget']);
                    $WIRPCMDetail->setFunctionvalue($detail_data['FunctionValue']);
                    $WIRPCurrentMix->addDetail($WIRPCMDetail);
                }

                $wirp_currentmix_total_data = $currentmix_data['Total'];
                $WIRPCMTotal = new WIRPCMTotal();
                $WIRPCMTotal->setCurrentmix($WIRPCurrentMix);
                $WIRPCMTotal->setTouchpointname($wirp_currentmix_total_data['TouchpointName']);
                $WIRPCMTotal->setBudget($wirp_currentmix_total_data['Budget']);
                $WIRPCMTotal->setFunctionvalue($wirp_currentmix_total_data['FunctionValue']);
                $WIRPCurrentMix->setTotal($WIRPCMTotal);
                //END ASSIGN THE CURRENT MIX DATA
                //ASSIGN THE OPTIMIZED MIX DATA
                //ASSIGN THE OPTIMIZED MIX DATA
                $optimizedmix_data = $wirpoint_data['OptimizedMix'];
                $WIRPOptimizedMix = new WIRPOptimizedMix();
                $WIRPOptimizedMix->setPoint($WIRPoint);
                $WIRPoint->setOptimizedmix($WIRPOptimizedMix);

                $wirp_optimizedmix_details_data = $optimizedmix_data['Details'];
                foreach ($wirp_optimizedmix_details_data as $detail_data) {
                    $WIRPOMDetail = new WIRPOMDetail();
                    $WIRPOMDetail->setOptimizedmix($WIRPOptimizedMix);
                    $WIRPOMDetail->setTouchpointname($detail_data['TouchpointName']);
                    $WIRPOMDetail->setBudget($detail_data['Budget']);
                    $WIRPOMDetail->setFunctionvalue($detail_data['FunctionValue']);
                    $WIRPOptimizedMix->addDetail($WIRPOMDetail);
                }
                $wirp_optimizedmix_total_data = $optimizedmix_data['Total'];
                $WIRPOMTotal = new WIRPOMTotal();
                $WIRPOMTotal->setOptimizedmix($WIRPOptimizedMix);
                $WIRPOMTotal->setTouchpointname($wirp_optimizedmix_total_data['TouchpointName']);
                $WIRPOMTotal->setBudget($wirp_optimizedmix_total_data['Budget']);
                $WIRPOMTotal->setFunctionvalue($wirp_optimizedmix_total_data['FunctionValue']);
                $WIRPOptimizedMix->setTotal($WIRPOMTotal);


                //END ASSIGN THE OPTIMIZED MIX DATA
                //ASSIGN THE SINGLETOUCHPOINT MIX DATA
                //ASSIGN THE SINGLETOUCHPOINT MIX DATA
                $singletouchpointmix_data = $wirpoint_data['SingleTouchpointMix'];
                $WIRPSingleTouchpointMix = new WIRPSingleTouchpointMix();
                $WIRPSingleTouchpointMix->setPoint($WIRPoint);
                $WIRPoint->setSingletouchpointmix($WIRPSingleTouchpointMix);

                $wirp_singletouchpointmix_details_data = $singletouchpointmix_data['Details'];
                foreach ($wirp_singletouchpointmix_details_data as $detail_data) {
                    $WIRPSTMDetail = new WIRPSTMDetail();
                    $WIRPSTMDetail->setSingletouchpointmix($WIRPSingleTouchpointMix);
                    $WIRPSTMDetail->setTouchpointname($detail_data['TouchpointName']);
                    $WIRPSTMDetail->setBudget($detail_data['Budget']);
                    $WIRPSTMDetail->setFunctionvalue($detail_data['FunctionValue']);
                    $WIRPSingleTouchpointMix->addDetail($WIRPSTMDetail);
                }
                $wirp_singletouchpointmix_total_data = $singletouchpointmix_data['Total'];
                $WIRPSTMTotal = new WIRPSTMTotal();
                $WIRPSTMTotal->setSingletouchpointmix($WIRPSingleTouchpointMix);
                $WIRPSTMTotal->setTouchpointname($wirp_singletouchpointmix_total_data['TouchpointName']);
                $WIRPSTMTotal->setBudget($wirp_singletouchpointmix_total_data['Budget']);
                $WIRPSTMTotal->setFunctionvalue($wirp_singletouchpointmix_total_data['FunctionValue']);
                $WIRPSingleTouchpointMix->setTotal($WIRPSTMTotal);

                //END ASSIGN THE SINGLETOUCHPOINT MIX DATA
            }

            $em->persist($WhatIfResult);
        } else {
            $array_of_missing_nodes[] = "WhatIfResult";
        }
        $em->persist($lightdata);
        $em->flush();


        $response = new Response();

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'lightdata_id' => $lightdata->getId(),
            'start_date' => $the_start_date,
            'error_on_parsing' => $error_on_parsing,
            'main_missing_nodes' => $array_of_missing_nodes,
            'setup_missing_nodes' => $missing_setup_nodes,
            'groupings_missing_nodes' => $missing_groupings_node,
            'touchpoints_missing_nodes' => $touchpoints_missing_nodes,
            'budgetallocation_missing_nodes' => $budgetallocation_missing_nodes,
            'timeallocation_missing_nodes' => $timeallocation_missing_nodes,
        )));
        return $response;
    }

    /**
     * Function to calculate the completeness of a campaign.
     * 
     * @param type $campaign
     * @return int
     */
    public function recalculate_campaign_completeness($campaign) {
        $completeness = 0;

        //Get all the tasks of this campaign
        $tasks = $campaign->getTasks();

        //For each task , if it's set as completed increment completeness by 2. 
        //If not set as completed , enter validation for task requirements.        

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

        //Initial validation is false
        ////////////////////////////
        $is_validated = false;
        /////////////////////////////////
        //Fetch the campaign of the task.
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
                //If any of TimeAllocation.Total.AllocationByPeriod[i].GRP  > 0     , is validated.           
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
                //if there is a file uploaded for this campaign with file_type_id=1 and task_name_id=8, then add 1 point

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

}
