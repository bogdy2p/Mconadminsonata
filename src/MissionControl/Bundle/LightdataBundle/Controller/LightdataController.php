<?php

namespace MissionControl\Bundle\LightdataBundle\Controller;

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
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\File;
use JMS\Serializer\SerializationContext;

class LightdataController extends FOSRestController {

//    /**
//     * @ApiDoc(
//     *    resource = true,
//     *    description = "THIS SHOULD RETURN THE DATA BASED ON THE  [projectId/campaign ID]",
//     *    statusCodes = {
//     *     200 = "Returned when the project was found",
//     *     403 = "Returned when user API KEY is not valid.",
//     *     404 = {
//     *         "Returned When the project was not found in database",
//     *         "Returned when the user does not have access to the project"
//     *     },
//     *     500 = "Returned when no token was found in header"
//     *    },
//     *    requirements = {
//     *       {
//     *           "name"="projectId",
//     *           "dataType"="string",
//     *           "description"="The project unique id"
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
//    public function getLightdataAction($lightdata, Request $request) {
//        $user = $this->getUser();
//
//
//        //INSTANTIATE THE JMS SERIALIZER
//        $serializer = $this->get('jms_serializer');
//
//
//        $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->findOneById($lightdata);
//
//
//        // \Doctrine\Common\Util\Debug::dump($lightdata);
//        // print_r(count($lightdata));
//        $asd = $lightdata->getObjectives();
//
//        //\Doctrine\Common\Util\Debug::dump($lightdata);
//        //die();
//
//
//
//
//        $jsonContent = $serializer->serialize($lightdata, 'json');
//        //  echo $jsonContent;
//
//
//        print_r($jsonContent);
//
//        //return $return;
//
//
//        die();
//    }
//
//    /**
//     * @ApiDoc(
//     *    resource = true,
//     *    description = "Save the JSON data to the database [under construction]",
//     *    statusCodes = {
//     *     201 = "Returned when the project was added to the database",
//     *     400 = "Returned when the validation returns false ",
//     *     403 = "Returned when user API KEY is not valid.",
//     *     500 = "Returned when no token was found in header"
//     *    },
//     *    requirements = {
//     *       {
//     *           "name"="name",
//     *           "dataType"="string",
//     *           "description"="The campaign's name / id / something else [Under construction]"
//     *       },
//     *       {
//     *           "name"="jsonFile",
//     *           "dataType"="file",
//     *           "description"="The lightdata JSON file."
//     *       },
//     *       {
//     *          "name" = "_format",
//     *          "requirement" = "json|xml"
//     *       }
//     *    }
//     * )
//     * return string
//     * @View()
//     */
//    public function postLightdataAction(Request $request) {
//
//        $user = $this->getUser();
//
//        //FETCH THE JSON FILE
//        $jsonFileData = $request->files->get('jsonFile');
//        $contentOfJsonFile = file_get_contents($jsonFileData);
//        $em = $this->getDoctrine()->getManager();
//        ///WE SHOULD CHECK THAT THE FILE HAS THE UTF-8 - Without BOM format before proceeding , or else it will fail :(
//        ///WE SHOULD CHECK THAT THE FILE HAS THE UTF-8 - Without BOM format before proceeding , or else it will fail :(
//        ///WE SHOULD CHECK THAT THE FILE HAS THE UTF-8 - Without BOM format before proceeding , or else it will fail :(
//
//
//        $lightdata_uuid = Uuid::uuid4()->toString();
//
//
//        $arrayOfJsonObjects = json_decode($contentOfJsonFile, true);
////        print_r($arrayOfJsonObjects);
////        die();
//        //GRAB THE PROJECT RELATED DATA FROM THE FILE INTO PROJECT_DATA ARRAY.
//
//
//        $project_data = $arrayOfJsonObjects;
//        if (isset($arrayOfJsonObjects['Project'])) {
//            $project_data = $arrayOfJsonObjects['Project'];
//        }
//
//
//
//
//        //INSTANTIATE THE LIGHTDATA OBJECT
//        $lightdata = new Lightdata();
//        //$lightdata->setLightdataUniqueIdentifier($lightdata_uuid);
////        $em->persist($lightdata);
////        $em->flush();
//        ////////////////////////////////////////////////////////////////
//        //Assign Project SETUP Data
//        ////////////////////////////////////////////////////////////////
//        //Grab setup array from the whole array.
//        $setup_data = $project_data['Setup'];
//
//        $client = new ClientLD();
//        $target = new TargetLD();
//        $survey = new SurveyLD();
//        $setup = new SetupLD();
//        $lightdata->setCurrentgroupingindex($project_data['CurrentGroupingIndex']);
//
//        $client->setName($setup_data['Client']['Name']);
//        $client->setDbid($setup_data['Client']['DbID']);
//        $target->setName($setup_data['Target']['Name']);
//        $target->setDbid($setup_data['Target']['DbID']);
//        $survey->setName($setup_data['Survey']['Name']);
//        $survey->setDbid($setup_data['Survey']['DbID']);
//
//        $setup->setClient($client);
//        $setup->setSurvey($survey);
//        $setup->setTarget($target);
//        $setup->setProjectName($setup_data['ProjectName']);
//        $setup->setStartDate(new \DateTime($setup_data['StartDate']));
//        $setup->setPeriodType($setup_data['PeriodType']);
//        $setup->setNbPeriods($setup_data['NbPeriods']);
//        $setup->setBudget($setup_data['Budget']);
//        $setup->setBudgetCurrency($setup_data['BudgetCurrency']);
//
//        $lightdata->setSetup($setup);
//
//        ////////////////////////////////////////////////////////////////
//        ////////////////////////////////////////////////////////////////
//        ////////////////////////////////////////////////////////////////
//        //Assign OBJECTIVES Data
//        ////////////////////////////////////////////////////////////////
//        $objectives_data = $project_data['Objectives'];
//
//        foreach ($objectives_data as $objective_data) {
//            $objective = new ObjectiveLD();
//            $objective->setName($objective_data['Name']);
//            $objective->setHtmlcolor($objective_data['HtmlColor']);
//            $objective->setSelected($objective_data['Selected']);
//            $objective->setScore($objective_data['Score']);
//            $objective->setLightdata($lightdata);
//            $lightdata->addObjective($objective);
//        }
//
//        ////////////////////////////////////////////////////////////////
//        ////////////////////////////////////////////////////////////////
//        ////////////////////////////////////////////////////////////////
//        //Assign GROUPINGS Data
//        ////////////////////////////////////////////////////////////////
//
//        $groupings_data = $project_data['Groupings'];
//        foreach ($groupings_data as $grouping_data) {
//            $grouping = new GroupingLD();
//            $grouping->setName($grouping_data['Name']);
//            $grouping->setLightdata($lightdata);
//            foreach ($grouping_data['Categories'] as $groupings_cateogry) {
//                $groupingcategory = new GroupingCategoryLD();
//                //$groupingcategory->setGroupingId($grouping);
//                $groupingcategory->setGrouping($grouping);
//                $groupingcategory->setName($groupings_cateogry['Name']);
//                $groupingcategory->setHtmlcolor($groupings_cateogry['HtmlColor']);
//                $grouping->addGroupingcategory($groupingcategory);
//            }
//
////            $touchpointcategoryMap = $grouping_data['TouchpointCategoryMap'];
////            
////                $groupingstouchpointcategorymap = new GroupingTouchpointCategoryMapLD();
////                $groupingstouchpointcategorymap->setGrouping($grouping);
////                $groupingstouchpointcategorymap->setName($touchpointcategoryMap);
////                $grouping->addGroupingtouchpointcategorymap($groupingstouchpointcategorymap);
//            foreach ($grouping_data['TouchpointCategoryMap'] as $key => $value) {
//                $groupingstouchpointcategorymap = new GroupingTouchpointCategoryMapLD();
//                $groupingstouchpointcategorymap->setGrouping($grouping);
//                // FOR THIS , WE SHOULD SET THE ID TO BE THE NAME , AND THE VALUE TO BE THE VALUE.
//
//                $groupingstouchpointcategorymap->setName($key);
//                $groupingstouchpointcategorymap->setValue($value);
//                $grouping->addGroupingtouchpointcategorymap($groupingstouchpointcategorymap);
//            }
//            $lightdata->addGrouping($grouping);
//        }
//        //END FOREACH GROUPING
//        ////////////////////////////////////////////////////////////////
//        ////////////////////////////////////////////////////////////////    
//        //ASSIGN THE CURRENT GROUPING INDEX , TO THE LIGHTDATA ENTITI DIRECTLY
//        //ASSIGN THE CURRENT GROUPING INDEX , TO THE LIGHTDATA ENTITI DIRECTLY    
//        //ASSIGN THE CURRENT GROUPING INDEX , TO THE LIGHTDATA ENTITI DIRECTLY
//        //ASSIGN THE CURRENT GROUPING INDEX , TO THE LIGHTDATA ENTITI DIRECTLY    
//        ////////////////////////////////////////////////////////////////    
//        ////////////////////////////////////////////////////////////////
//        //ASSIGN TOUCHPOINTS , OBJECTIVESCORES AND ATTRIBUTESCORES FOR EACH TOUCHPOINT
//        //ASSIGN TOUCHPOINTS , OBJECTIVESCORES AND ATTRIBUTESCORES FOR EACH TOUCHPOINT    
//
//
//        $touchpoints_data = $project_data['Touchpoints'];
//
//        foreach ($touchpoints_data as $touchpoint_data) {
//            $touchpoint = new TouchpointLD();
//            $touchpoint->setName($touchpoint_data['Name']);
//            $touchpoint->setLocalname($touchpoint_data['LocalName']);
//            $touchpoint->setHtmlcolor($touchpoint_data['HtmlColor']);
//            $touchpoint->setSelected($touchpoint_data['Selected']);
//            $touchpoint->setAggobjectivescore($touchpoint_data['AggObjectiveScore']);
//            $touchpoint->setLightdata($lightdata);
//
//            foreach ($touchpoint_data['ObjectiveScores'] as $touchpoint_objectivescore) {
//                $touchpointObjectiveScore = new TouchpointObjectiveScoreLD();
//                $touchpointObjectiveScore->setValue($touchpoint_objectivescore);
//                $touchpointObjectiveScore->setTouchpoint($touchpoint);
//                $touchpoint->addTouchpointobjectivescore($touchpointObjectiveScore);
//            }
//
//            foreach ($touchpoint_data['AttributeScores'] as $touchpoint_attributescore) {
//                $touchpointAttributeScore = new TouchpointAttributeScoreLD();
//                $touchpointAttributeScore->setValue($touchpoint_attributescore);
//                $touchpointAttributeScore->setTouchpoint($touchpoint);
//                $touchpoint->addTouchpointattributescore($touchpointAttributeScore);
//            }
//            $lightdata->addTouchpoint($touchpoint);
//        }
//
//        ////////////////////////////////////////////////////////////////    
//        ////////////////////////////////////////////////////////////////
//        ////////////////////////////////////////////////////
//        //ASSIGN CPRATTRIBUTES 
//        ////////////////////////////////////////////////////
//        $cprattributes_data = $project_data['CPRAttributes'];
//        foreach ($cprattributes_data as $cprattribute_data) {
//            $cprattribute = new CPRAttributeLD();
//            $cprattribute->setName($cprattribute_data['Name']);
//            $cprattribute->setDescription($cprattribute_data['Description']);
//            $cprattribute->setSelected($cprattribute_data['Selected']);
//            $cprattribute->setLightdata($lightdata);
//            $lightdata->addCprattribute($cprattribute);
//        }
//
//
//        ///////////////////////////////////////////////////////////////
//        ///////////////////////////////////////////////////////////////
//        ///////////////////////////////////////////////////////////////
//        ////ASSIGN BUDGETALLOCATION
//        ///////////////////////////////////////////////////////////////
//
//        $budgetallocation_data = $project_data['BudgetAllocation'];
//
//        $budgetallocation = new BudgetAllocationLD();
//        $lightdata->setBudgetallocation($budgetallocation);
//        $budgetallocation->setLightdata($lightdata);
//
//        ///ADD THE BUDGETALLOCATION ALLOCATEDTOUCHPOINTS
//
//        $ba_allocatedtouchpoints = $budgetallocation_data['AllocatedTouchpoints'];
//        foreach ($ba_allocatedtouchpoints as $budgetallocation_allocatedtouchpoint) {
//            $AllocatedTouchpoint = new BAAllocatedTouchpointLD();
//            $AllocatedTouchpoint->setTouchpointname($budgetallocation_allocatedtouchpoint['TouchpointName']);
//            $AllocatedTouchpoint->setBudgetallocation($budgetallocation);
//            $budgetallocation->addAllocatedtouchpoint($AllocatedTouchpoint);
//
//            $allocation_data = $budgetallocation_allocatedtouchpoint['Allocation'];
//            $allocation = new BAATAllocationLD();
//            $allocation->setAllocatedtouchpoint($AllocatedTouchpoint);
//            $allocation->setBudget($allocation_data['Budget']);
//            $allocation->setCostpergrp($allocation_data['CostPerGRP']);
//            $allocation->setGrp($allocation_data['GRP']);
//            $AllocatedTouchpoint->setAllocation($allocation);
//
//            $result_data = $allocation_data['Result'];
//            $Result = new BAATAResultLD();
//            $Result->setAllocation($allocation);
//            $Result->setGlobalperformance($result_data['GlobalPerformance']);
//            $Result->setReach($result_data['Reach']);
//            $allocation->setResult($Result);
//
//            $individual_performances_data = $result_data['IndividualPerformance'];
//            foreach ($individual_performances_data as $individual_performance_data) {
//
//                $IndividualPerformance = new BAATARIndividualPerformanceLD();
//                $IndividualPerformance->setResult($Result);
//                $IndividualPerformance->setValue($individual_performance_data);
//                $Result->addIndividualperformance($IndividualPerformance);
//            }
//        }
//
//        ///ADD THE BUDGETALLOCATION TOTAL
//
//        $ba_total = $budgetallocation_data['Total'];
//        $budgetallocation_total = new BATotalLD();
//        $budgetallocation_total->setTouchpointname($ba_total['TouchpointName']);
//        $budgetallocation_total->setBudgetallocation($budgetallocation);
//        $budgetallocation->addTotal($budgetallocation_total);
//
//        $total_data = $budgetallocation_allocatedtouchpoint['Allocation'];
//        $allocation = new BATOAllocationLD();
//        $allocation->setAllocatedtouchpoint($budgetallocation_total);
//        $allocation->setBudget($total_data['Budget']);
//        $allocation->setCostpergrp($total_data['CostPerGRP']);
//        $allocation->setGrp($total_data['GRP']);
//        $budgetallocation_total->setAllocation($allocation);
//
//        $result_data = $total_data['Result'];
//        $Result = new BATOAResultLD();
//        $Result->setAllocation($allocation);
//        $Result->setGlobalperformance($result_data['GlobalPerformance']);
//        $Result->setReach($result_data['Reach']);
//        $allocation->setResult($Result);
//
//        $individual_performances_data = $result_data['IndividualPerformance'];
//        foreach ($individual_performances_data as $individual_performance_data) {
//
//            $IndividualPerformance = new BATOARIndividualPerformanceLD();
//            $IndividualPerformance->setResult($Result);
//            $IndividualPerformance->setValue($individual_performance_data);
//            $Result->addIndividualperformance($IndividualPerformance);
//        }
//        /////////////////////////////////////////
//        // END OF ASSIGNS FOR BUDGETALLOCATION
//        /////////////////////////////////////////
//        ///////////////////////////////////////////////////////////////
//        ///////////////////////////////////////////////////////////////
//        ///////////////////////////////////////////////////////////////
//        ////ASSIGN TIME ALLOCATION
//        ///////////////////////////////////////////////////////////////
//
//        $timeallocation_data = $project_data['TimeAllocation'];
//
//        $timeallocation = new TimeAllocationLD();
//
//        $lightdata->setTimeallocation($timeallocation);
//        $timeallocation->setLightdata($lightdata);
//
//
//        ///ADD THE TIMEALLOCATION ALLOCATEDTOUCHPOINTS
//
//        $timeallocation_allocatedtouchpoints_data = $timeallocation_data['AllocatedTouchpoints'];
//        foreach ($timeallocation_allocatedtouchpoints_data as $ta_allocatedtouchpoint) {
//            $TAAllocatedTouchpoint = new TAAllocatedTouchpointLD();
//            $TAAllocatedTouchpoint->setTouchpointname($ta_allocatedtouchpoint['TouchpointName']);
//            $TAAllocatedTouchpoint->setReachfrequency($ta_allocatedtouchpoint['ReachFrequency']);
//            $TAAllocatedTouchpoint->setTimeallocation($timeallocation);
//            $timeallocation->addAllocatedtouchpoint($TAAllocatedTouchpoint);
//
//            $allocations_by_period_data = $ta_allocatedtouchpoint['AllocationByPeriod'];
//            foreach ($allocations_by_period_data as $allocation_by_period) {
//                $AllocationByPeriod = new TAATAllocationByPeriod();
//                $AllocationByPeriod->setAllocatedtouchpoint($TAAllocatedTouchpoint);
//                $AllocationByPeriod->setBudget($allocation_by_period['Budget']);
//                $AllocationByPeriod->setCostpergrp($allocation_by_period['CostPerGRP']);
//                $AllocationByPeriod->setGrp($allocation_by_period['GRP']);
//                $TAAllocatedTouchpoint->addAllocationbyperiod($AllocationByPeriod);
//
//                $ABPresult_data = $allocation_by_period['Result'];
//                $ABPResult = new TAATABPResult();
//                $ABPResult->setAllocationbyperiod($AllocationByPeriod);
//                $ABPResult->setGlobalperformance($result_data['GlobalPerformance']);
//                $ABPResult->setReach($result_data['Reach']);
//                $AllocationByPeriod->setResult($ABPResult);
//
//                $ABPindividual_performances_data = $ABPresult_data['IndividualPerformance'];
//                foreach ($ABPindividual_performances_data as $individual_performance_data) {
//
//                    $ABPIndividualPerformance = new TAATABPRIndividualPerformance();
//                    $ABPIndividualPerformance->setResult($ABPResult);
//                    $ABPIndividualPerformance->setValue($individual_performance_data);
//                    $ABPResult->addIndividualperformance($ABPIndividualPerformance);
//                }
//            }
//        }
//
//
//
//
//        ///ADD THE TIMEALLOCATION TOTAL
//
//        $timeallocation_total_data = $timeallocation_data['Total'];
//        $TATotal = new TATotalLD();
//        $TATotal->setTimeallocation($timeallocation);
//        $TATotal->setTouchpointname($timeallocation_total_data['TouchpointName']);
//        $TATotal->setReachfrequency($timeallocation_total_data['ReachFrequency']);
//        $timeallocation->addTotal($TATotal);
//
//        $allocations_by_period_data_total = $timeallocation_total_data['AllocationByPeriod'];
//        foreach ($allocations_by_period_data_total as $allocation_by_period) {
//            $TotalAllocationByPeriod = new TATOAllocationByPeriod();
//            $TotalAllocationByPeriod->setAllocatedtouchpoint($TATotal);
//            $TotalAllocationByPeriod->setBudget($allocation_by_period['Budget']);
//            $TotalAllocationByPeriod->setCostpergrp($allocation_by_period['CostPerGRP']);
//            $TotalAllocationByPeriod->setGrp($allocation_by_period['GRP']);
//            $TATotal->addAllocationbyperiod($TotalAllocationByPeriod);
//
//
//            $TotalABPresult_data = $allocation_by_period['Result'];
//            $TotalABPResult = new TATOABPResult();
//            $TotalABPResult->setAllocationbyperiod($TotalAllocationByPeriod);
//            $TotalABPResult->setGlobalperformance($result_data['GlobalPerformance']);
//            $TotalABPResult->setReach($result_data['Reach']);
//            $TotalAllocationByPeriod->setResult($TotalABPResult);
//
//            $TotalABPindividual_performances_data = $TotalABPresult_data['IndividualPerformance'];
//            foreach ($TotalABPindividual_performances_data as $individual_performance_data) {
//
//                $TOABPIndividualPerformance = new TATOABPRIndividualPerformance();
//                $TOABPIndividualPerformance->setResult($TotalABPResult);
//                $TOABPIndividualPerformance->setValue($individual_performance_data);
//                $TotalABPResult->addIndividualperformance($TOABPIndividualPerformance);
//            }
//        }
//
////        print_r(count($allocations_by_period_data_total));
////        die();
//        ////END ASSIGN TIME ALLOCATION   
//        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
//        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
//        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
//        ////ASSIGN THE WHATIFRESULT DATA////ASSIGN THE WHATIFRESULT DATA
//        //ONLY IF THE WHATIFRESULT IS AVAILLABLE INTO THE JSON UPDATED
//        if (isset($project_data['WhatIfResult'])) {
//
//
//
//            $whatifresult_data = $project_data['WhatIfResult'];
//
//
//            $WhatIfResult = new WhatIfResult();
//            ////////////////////////////////////////////
//            $WhatIfResult->setLightdata($lightdata);
//            $lightdata->setWhatifresult($WhatIfResult);
//            ////////////////////////////////////////////
//            $wirconfig_data = $whatifresult_data['Config'];
//            $WIRConfig = new WIRConfig();
//            ////////////////////////////////////////////
//            $WIRConfig->setWhatifresult($WhatIfResult);
//            $WhatIfResult->setConfig($WIRConfig);
//            ////////////////////////////////////////////
//            $WIRConfig->setFirstperiod($wirconfig_data['FirstPeriod']);
//            $WIRConfig->setLastperiod($wirconfig_data['LastPeriod']);
//            $WIRConfig->setSourcebudget($wirconfig_data['SourceBudget']);
//            $WIRConfig->setBudgetminpercent($wirconfig_data['BudgetMinPercent']);
//            $WIRConfig->setBudgetmaxpercent($wirconfig_data['BudgetMaxPercent']);
//            $WIRConfig->setBudgetsteppercent($wirconfig_data['BudgetStepPercent']);
//            $WIRConfig->setHascurrentmix($wirconfig_data['HasCurrentMix']);
//            $WIRConfig->setHassingletouchpointmix($wirconfig_data['HasSingleTouchpointMix']);
//            $WIRConfig->setHasoptimizedmix($wirconfig_data['HasOptimizedMix']);
//            $wircoptimizedfunction_data = $wirconfig_data['OptimizedFunction'];
//            $WIRCOptimizedFunction = new WIRCOptimizedFunction();
//            ////////////////////////////////////////////
//            $WIRCOptimizedFunction->setConfig($WIRConfig);
//            $WIRConfig->setOptimizedfunction($WIRCOptimizedFunction);
//            ////////////////////////////////////////////
//            $WIRCOptimizedFunction->setCalculationtype($wircoptimizedfunction_data['CalculationType']);
//            $WIRCOptimizedFunction->setAttributeindex($wircoptimizedfunction_data['AttributeIndex']);
//
//
//            $wirpoints_data = $whatifresult_data['Points'];
//            foreach ($wirpoints_data as $wirpoint_data) {
//                $WIRPoint = new WIRPoint();
//                $WIRPoint->setWhatifresult($WhatIfResult);
//                $WIRPoint->setStepposition($wirpoint_data['StepPosition']);
//                $WIRPoint->setActualpercent($wirpoint_data['ActualPercent']);
//                $WhatIfResult->addPoint($WIRPoint);
//
//
//                //ASSIGN THE CURRENT MIX DATA
//                //ASSIGN THE CURRENT MIX DATA
//                $currentmix_data = $wirpoint_data['CurrentMix'];
//                $WIRPCurrentMix = new WIRPCurrentMix();
//                $WIRPCurrentMix->setPoint($WIRPoint);
//                $WIRPoint->setCurrentmix($WIRPCurrentMix);
//
//                $wirp_currentmix_details_data = $currentmix_data['Details'];
//                foreach ($wirp_currentmix_details_data as $detail_data) {
//                    $WIRPCMDetail = new WIRPCMDetail();
//                    $WIRPCMDetail->setCurrentmix($WIRPCurrentMix);
//                    $WIRPCMDetail->setTouchpointname($detail_data['TouchpointName']);
//                    $WIRPCMDetail->setBudget($detail_data['Budget']);
//                    $WIRPCMDetail->setFunctionvalue($detail_data['FunctionValue']);
//                    $WIRPCurrentMix->addDetail($WIRPCMDetail);
//                }
//
//                $wirp_currentmix_total_data = $currentmix_data['Total'];
//                $WIRPCMTotal = new WIRPCMTotal();
//                $WIRPCMTotal->setCurrentmix($WIRPCurrentMix);
//                $WIRPCMTotal->setTouchpointname($wirp_currentmix_total_data['TouchpointName']);
//                $WIRPCMTotal->setBudget($wirp_currentmix_total_data['Budget']);
//                $WIRPCMTotal->setFunctionvalue($wirp_currentmix_total_data['FunctionValue']);
//                $WIRPCurrentMix->setTotal($WIRPCMTotal);
//                //END ASSIGN THE CURRENT MIX DATA
//                //ASSIGN THE OPTIMIZED MIX DATA
//                //ASSIGN THE OPTIMIZED MIX DATA
//                $optimizedmix_data = $wirpoint_data['OptimizedMix'];
//                $WIRPOptimizedMix = new WIRPOptimizedMix();
//                $WIRPOptimizedMix->setPoint($WIRPoint);
//                $WIRPoint->setOptimizedmix($WIRPOptimizedMix);
//
//                $wirp_optimizedmix_details_data = $optimizedmix_data['Details'];
//                foreach ($wirp_optimizedmix_details_data as $detail_data) {
//                    $WIRPOMDetail = new WIRPOMDetail();
//                    $WIRPOMDetail->setOptimizedmix($WIRPOptimizedMix);
//                    $WIRPOMDetail->setTouchpointname($detail_data['TouchpointName']);
//                    $WIRPOMDetail->setBudget($detail_data['Budget']);
//                    $WIRPOMDetail->setFunctionvalue($detail_data['FunctionValue']);
//                    $WIRPOptimizedMix->addDetail($WIRPOMDetail);
//                }
//                $wirp_optimizedmix_total_data = $optimizedmix_data['Total'];
//                $WIRPOMTotal = new WIRPOMTotal();
//                $WIRPOMTotal->setOptimizedmix($WIRPOptimizedMix);
//                $WIRPOMTotal->setTouchpointname($wirp_optimizedmix_total_data['TouchpointName']);
//                $WIRPOMTotal->setBudget($wirp_optimizedmix_total_data['Budget']);
//                $WIRPOMTotal->setFunctionvalue($wirp_optimizedmix_total_data['FunctionValue']);
//                $WIRPOptimizedMix->setTotal($WIRPOMTotal);
//
//
//                //END ASSIGN THE OPTIMIZED MIX DATA
//                //ASSIGN THE SINGLETOUCHPOINT MIX DATA
//                //ASSIGN THE SINGLETOUCHPOINT MIX DATA
//                $singletouchpointmix_data = $wirpoint_data['SingleTouchpointMix'];
//                $WIRPSingleTouchpointMix = new WIRPSingleTouchpointMix();
//                $WIRPSingleTouchpointMix->setPoint($WIRPoint);
//                $WIRPoint->setSingletouchpointmix($WIRPSingleTouchpointMix);
//
//                $wirp_singletouchpointmix_details_data = $singletouchpointmix_data['Details'];
//                foreach ($wirp_singletouchpointmix_details_data as $detail_data) {
//                    $WIRPSTMDetail = new WIRPSTMDetail();
//                    $WIRPSTMDetail->setSingletouchpointmix($WIRPSingleTouchpointMix);
//                    $WIRPSTMDetail->setTouchpointname($detail_data['TouchpointName']);
//                    $WIRPSTMDetail->setBudget($detail_data['Budget']);
//                    $WIRPSTMDetail->setFunctionvalue($detail_data['FunctionValue']);
//                    $WIRPSingleTouchpointMix->addDetail($WIRPSTMDetail);
//                }
//                $wirp_singletouchpointmix_total_data = $singletouchpointmix_data['Total'];
//                $WIRPSTMTotal = new WIRPSTMTotal();
//                $WIRPSTMTotal->setSingletouchpointmix($WIRPSingleTouchpointMix);
//                $WIRPSTMTotal->setTouchpointname($wirp_singletouchpointmix_total_data['TouchpointName']);
//                $WIRPSTMTotal->setBudget($wirp_singletouchpointmix_total_data['Budget']);
//                $WIRPSTMTotal->setFunctionvalue($wirp_singletouchpointmix_total_data['FunctionValue']);
//                $WIRPSingleTouchpointMix->setTotal($WIRPSTMTotal);
//
//                //END ASSIGN THE SINGLETOUCHPOINT MIX DATA
//            }
//
//            $em->persist($WhatIfResult);
//        }
//        $em->persist($lightdata);
//        $em->flush();
//
//
//        die('The data has been inserted into the database  ');
//
//
//        $em->persist($lightproject);
//        $em->persist($project);
//        $em->flush();
//
//        $response->setStatusCode(201);
//        $response->setContent(json_encode(array(
//            'Status' => 'Project has been added to the database.'
//                ))
//        );
//
//        return $response;
//    }
 /**
     * @ApiDoc(
     *    resource = true,
     *    description = "DELETE THE  JSON data from the database [under construction]",
     *    statusCodes = {
     *     201 = "Returned when the project was added to the database",
     *     400 = "Returned when the validation returns false ",
     *     403 = "Returned when user API KEY is not valid.",
     *     500 = "Returned when no token was found in header"
     *    },
     *    requirements = {
     *       {
     *           "name"="name",
     *           "dataType"="string",
     *           "description"="The campaign's name / id / something else [Under construction]"
     *       },
     *       {
     *           "name"="lightdataid",
     *           "dataType"="integer",
     *           "description"="The lightdata JSON id."
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * return string
     * @View()
     */
//    public function deleteLightdataAction(Request $request) {
//
//        $user = $this->getUser();
//
//        $lightdata_id = $request->get('lightdataid');
//        
//        $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
//        
//        $em = $this->getDoctrine()->getManager();
//        
//        $em->remove($lightdata);
//        $em->flush();
//        
//        
//        
//        
//        $response = new Response();
//
//        $response->setStatusCode(201);
//        $response->setContent(json_encode(array(
//            'Status' => 'Lightdata succesfully deleted [TEST]'
//                ))
//        );
//
//        return $response;
//    }
    
    /**
     * @ApiDoc(
     *    resource = true,
     *    description = "DELETE ALL THE  JSON data from the database UP TO PROVIDED ID",
     *    statusCodes = {
     *     201 = "Returned when the project was added to the database",
     *     400 = "Returned when the validation returns false ",
     *     403 = "Returned when user API KEY is not valid.",
     *     500 = "Returned when no token was found in header"
     *    },
     *    requirements = {
     *       {
     *           "name"="name",
     *           "dataType"="string",
     *           "description"="The campaign's name / id / something else [Under construction]"
     *       },
     *       {
     *           "name"="lightdataid",
     *           "dataType"="integer",
     *           "description"="The maximum lightdata JSON id."
     *       },
     *       {
     *          "name" = "_format",
     *          "requirement" = "json|xml"
     *       }
     *    }
     * )
     * return string
     * @View()
     */
//    public function deleteLightdataallAction(Request $request) {
//
//        $user = $this->getUser();
//
//        $lightdata_id = $request->get('lightdataid');
//        
//        for ($i = 1 ; $i < $lightdata_id ; $i++){
//            
//            try{
//             $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($i);
//             $em = $this->getDoctrine()->getManager();
//             if($lightdata){
//             $em->remove($lightdata);
//             }
//             $em->flush();
//            } catch (Symfony\Component\Security\Acl\Exception\Exception $e){
//                echo 'Exception here.';
//            }
//        }
//        
//
//        $response = new Response();
//
//        $response->setStatusCode(201);
//        $response->setContent(json_encode(array(
//            'Status' => 'All Lightdata succesfully deleted up to id '.$lightdata_id. ' (if they existed)',
//            //'Exceptions' => $e
//                ))
//        );
//
//        return $response;
//    }
}
