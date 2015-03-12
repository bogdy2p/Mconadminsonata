<?php

namespace MissionControl\Bundle\CampaignBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\CampaignBundle\Entity\Client;
use MissionControl\Bundle\TaskBundle\Entity\Task;
use MissionControl\Bundle\UserBundle\Entity\User;
use \MissionControl\Bundle\CampaignBundle\Entity\Teammember;
use MissionControl\Bundle\CampaignBundle\Entity\Useraccess;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class AddTestCampaignsForTask291Command extends ContainerAwareCommand {

    public function configure() {
        $this->setName('ninecampaignstest')
                ->setDescription('This commands description ')
                ->addArgument('number', InputArgument::REQUIRED, 'number')
                ->addOption('add', null, InputOption::VALUE_NONE, 'If this is set , the call will add a specified number of test campaigns to the db.')
                ->addOption('removecampaign', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE all the test campaigns from the db.')
                ->addOption('removeaccesses', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE all the test campaigns from the db.')
                ->addOption('removetestuser', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE all the test campaigns from the db.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $manager = $this->getContainer()->get('doctrine')->getManager();

        $creationDate = new \DateTime();
        $creationDate->setTimezone(new \DateTimeZone('UTC'));

        /*         * **********************************************************************************************************************************************
         * ******************************************************************************************************************************************************** 
         * 
         *          THE TEST DATA INSERTION PART STARTS HERE  ||||       THE TEST DATA INSERTION PART STARTS HERE 
         *          FIRST WE CHECK / MAKE SURE THE TEST USER IS ADDED IN THE DATABASE.
         * 
         * ******************************************************************************************************************************************************** 
         * ******************************************************************************************************************************************************** 
         */
        if ($input->getOption('add')) {

            $number = $input->getArgument('number');

            //CHECK THE EXISTENCE OF THE TEST USER.
            //IF EXISTS , USE HIM
            //IF NOT EXIST , CREATE AND USE HIM.
            $user = $manager->getRepository('UserBundle:User')->findOneByUsername('test_user_1_test');
            if ($user) {
                $output->writeln('Using existing test user from database. ( ApiKey = testuser1 )');
            } else {

                $user = new User();
                $user->setUsername('test_user_1_test');
                $user->setApiKey('testuser1');
                $user->setEmail('test_user_1_test@email.com');
                $user->setPassword(md5('password'));
                $user->setEnabled(true);
                $user->setCreatedAt($creationDate);
                $user->setUpdatedAt($creationDate);
                $user->addRole('ROLE_VIEWER');
                $manager->persist($user);
                $manager->flush();
                $output->writeln(' ');
                $output->writeln('Testuser was missing so it has been added. (ApiKey =  testuser1 )');
            }

            // WE MUST CHECK THAT THE USER ALREADY HAS ACCESS TO THIS REGION || COUNTRY COMBINATIONS
            $accessclient = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
            $access1_country = $manager->getRepository('CampaignBundle:Country')->findOneByName('USA');
            $access1_region = $access1_country->getRegion();
            $access2_region = $manager->getRepository('CampaignBundle:Region')->find(2);

            $already_has_access_to_Unilever_USA = $manager->getRepository('CampaignBundle:Useraccess')->findOneBy([
                'user' => $user,
                'client' => $accessclient,
                'country' => $access1_country,
                'all_countries' => false,
            ]);
            $already_has_access_to_Unilever_ASIA_PACIFIC_REGION = $manager->getRepository('CampaignBundle:Useraccess')->findOneBy([
                'user' => $user,
                'client' => $accessclient,
                'region' => $access2_region,
                'all_countries' => true,
            ]);


            //If not already exist , add the access
            //If not already exist , add the access
            if ($already_has_access_to_Unilever_USA) {
                $output->writeln('Test user already has access to Unilever country USA , proceeding further.');
            } else {
                $access1 = new Useraccess();
                $access1->setUser($user);
                $access1->setClient($accessclient);
                $access1->setRegion($access1_region);
                $access1->setCountry($access1_country);
                $access1->setAllCountries(FALSE);
                $manager->persist($access1);
                $output->writeln('Test user has been granted access  to client Unilever country USA, proceeding.');
            }
            if ($already_has_access_to_Unilever_ASIA_PACIFIC_REGION) {
                $output->writeln('Test user already has access to Unilever region ASIA PACIFIC , proceeding further.');
            } else {
                $access2 = new Useraccess();
                $access2->setUser($user);
                $access2->setClient($accessclient);
                $access2->setRegion($access2_region);
                $access2->setAllCountries(TRUE);
                $manager->persist($access2);
                $output->writeln('Test user has been granted access  to client Unilever region ASIA PACIFIC, proceeding.');
            }
            $manager->flush();
            $output->writeln(' ');
            //End adding of the access
            //End adding of the access
            $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
            
            switch ($number) {

                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE FIRST CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = USA , STATUS = BUILD.
                 * 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
                case 1:

                    //FIND THE CAMPAIGN , IF UNABLE TO FIND IT , CREATE IT !
                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('USA');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(1);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                    ]);

                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case1.Client=Unilever.Country=USA.Status=Build');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                       // $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                   
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);



                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case1.Client=Unilever.Country=USA.Status=Build');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    /// END ADD TASKS FOR THIS CAMPAIGN
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {

                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(true);
                        $manager->persist($teammember);
                        $manager->flush();
                        $output->writeln('The test user has been added as a normal teammember for this campaign. He is NOT reviewer.');
                        $output->writeln(' ');
                    }


                    //IF NOT , ADD HIM TO THIS CAMPAIGN ONLY AS A TEAMMEMBER 

                    $output->writeln('This case should work as intended. All data is availlable.');
                    $output->writeln(' ');

                    break;

                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE SECOND CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = USA , STATUS = APPROVED.
                 * 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
                case 2:

                    //CAMPAIGN2 IS where test user is taskowner for at least 2 tasks 
                    //and the campaign_status is approved. Clien is still Unilever and Country = USA

                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('USA');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(2);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                    ]);

                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case2.Client=Unilever.Country=USA.Status=Approved');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                       // $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case2.Client=Unilever.Country=USA.Status=Approved');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }

                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();

                    //Randomly assign the user as a taskowner for at leat two tasks.
                    $random_ids = array();
                    $random_number_of_tasks = 6;
                    for ($i = 2; $i < $random_number_of_tasks; $i++) {
                        $random_ids[] = rand(1, 9);
                    }

                    $unique_random_ids = array_unique($random_ids);
                    $all_tasks_of_this_campaign = $manager->getRepository('TaskBundle:Task')->findByCampaign($campaign->getId());



                    foreach ($all_tasks_of_this_campaign as $task) {
                        $task->setOwner(NULL);
                        if (in_array($task->getTaskname()->getId(), $unique_random_ids)) {
                            ///ADD SET THIS TASK TO HAVE THE OWNER THE TEST USER.
                            $task->setOwner($user);

                            $output->writeln($user->getUsername() . ' has been set as a taskowner for task ' . $task->getTaskname()->getName());
                            ;
                        }
                        $manager->persist($task);
                    }
                    $manager->flush();


                    break;

                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE THIRD CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = CHINA , STATUS = COMPLETED.
                 * 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */

                case 3:
                    //CAMPAIGN3 IS where test user is taskowner for at least 1 tasks 
                    //and the campaign_status is COMPLETE . Clien is still Unilever and Country = CHINA

                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('China');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(3);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                    ]);


                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. [case 3] ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case3.Client=Unilever.Country=China.Status=Completed');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                     //   $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case3.Client=Unilever.Country=China.Status=Completed');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();
                    //Randomly assign the user as a taskowner for at leat two tasks.
                    $random_ids = array();
                    $random_number_of_tasks = 6;
                    for ($i = 2; $i < $random_number_of_tasks; $i++) {
                        $random_ids[] = rand(1, 9);
                    }

                    $unique_random_ids = array_unique($random_ids);
                    $all_tasks_of_this_campaign = $manager->getRepository('TaskBundle:Task')->findByCampaign($campaign->getId());



                    foreach ($all_tasks_of_this_campaign as $task) {
                        $task->setOwner(NULL);
                        if (in_array($task->getTaskname()->getId(), $unique_random_ids)) {
                            ///ADD SET THIS TASK TO HAVE THE OWNER THE TEST USER.
                            $task->setOwner($user);

                            $output->writeln($user->getUsername() . ' has been set as a taskowner for task ' . $task->getTaskname()->getName());
                            ;
                        }
                        $manager->persist($task);
                    }
                    $manager->flush();

                    break;
                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 *  
                 *          THE FOURTH CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = CHINA , STATUS = CANCELLED.
                 *          The test user is a taskowner for at least 1 task
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
                case 4:

                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('China');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(4);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                    ]);


                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. [case 4] ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case4.Client=Unilever.Country=China.Status=Cancelled');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                     //   $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        for ($i = 1 ; $i<50; $i++){
                            print_r($campaign_creator->getUsername());
                        }
                        
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case4.Client=Unilever.Country=China.Status=Cancelled');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                       
                       
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();
                    //Randomly assign the user as a taskowner for at leat two tasks.
                    $random_ids = array();
                    $random_number_of_tasks = 6;
                    for ($i = 2; $i < $random_number_of_tasks; $i++) {
                        $random_ids[] = rand(1, 9);
                    }

                    $unique_random_ids = array_unique($random_ids);
                    $all_tasks_of_this_campaign = $manager->getRepository('TaskBundle:Task')->findByCampaign($campaign->getId());



                    foreach ($all_tasks_of_this_campaign as $task) {
                        $task->setOwner(NULL);
                        if (in_array($task->getTaskname()->getId(), $unique_random_ids)) {
                            ///ADD SET THIS TASK TO HAVE THE OWNER THE TEST USER.
                            $task->setOwner($user);

                            $output->writeln($user->getUsername() . ' has been set as a taskowner for task ' . $task->getTaskname()->getName());
                            ;
                        }
                        $manager->persist($task);
                    }
                    $manager->flush();



                    break;
                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE FIFTH CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = CHINA , STATUS = BUILD.
                 *          THE TEST USER IS IN THE CAMPAIGN'S TEAM , BUT HE IS NOT REVIEWER , AND NOT OWNER OF ANY TASK.
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */


                case 5:

                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('China');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(1);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                        'name' => 'Case5.Client=Unilever.Country=China.Status=Build',
                    ]);


                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. [case 5] ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case5.Client=Unilever.Country=China.Status=Build');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                      //  $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case5.Client=Unilever.Country=China.Status=Build');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();


                    $output->writeln('In this campaign , the user is NOT reviewer , and does not own any task.');
                    $output->writeln(' ');




                    break;
                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE SIXTH CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = CHINA , STATUS = COMPLETED.
                 *          TEST USER IS NOT REVIEWER NOT TASK OWNER 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
                case 6:

                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('China');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(4);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                        'name' => 'Case6.Client=Unilever.Country=China.Status=Cancelled',
                    ]);


                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. [case 6] ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case6.Client=Unilever.Country=China.Status=Cancelled');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                  //      $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case6.Client=Unilever.Country=China.Status=Cancelled');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();


                    break;
                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE SEVENTH CAMPAIGN CASE
                 *          CLIENT = testclientforcase7 , COUNTRY = CHINA , STATUS = CANCELLED.
                 * 
                 *          This test data should be just like case 6 , the difference is only the CLIENT ID. using client with name "testclientforcase7"
                 * 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
                case 7:

                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('China');

                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('testclientforcase7');

                    if ($client) {
                        $output->writeln('Found testclientforcase7 in the database. Proceeding..');
                    } else {
                        $client = new Client();
                        $client->setName('testclientforcase7');
                        $client->setDbid(0);
                        $client->setCreatedAt($creationDate);
                        $client->setUpdatedAt($creationDate);
                        $manager->persist($client);
                        $output->writeln('A new client , testclientforcase7 has been addeed. Proceeding...');
                        $manager->flush();
                    }



                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(4);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([

                        'name' => 'Case7.Client=testclientforcase7.Country=China.Status=Cancelled',
                    ]);


                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. [case 7] ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case7.Client=testclientforcase7.Country=China.Status=Cancelled');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                 //       $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case7.Client=testclientforcase7.Country=China.Status=Cancelled');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();


                    break;
                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE EIGHTH CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = PORTUGAL , STATUS = CANCELLED.
                 * 
                 *          This test data should be just like case 6 , the difference is only the COUNTRY.
                 * 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
                case 8:
                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('Portugal');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(4);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                        'name' => 'Case8.Client=Unilever.Country=Portugal.Status=Cancelled',
                    ]);


                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. [case 8] ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case8.Client=Unilever.Country=Portugal.Status=Cancelled');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                        
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case8.Client=Unilever.Country=Portugal.Status=Cancelled');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();

                    break;

                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE NINETH CAMPAIGN CASE
                 *          CLIENT = UNILEVER , COUNTRY = PORTUGAL , STATUS = COMPLETED.
                 * 
                 *          This test data should be just like case 3 , the difference is only COUNTRY
                 * 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
                case 9:

                    $country = $manager->getRepository('CampaignBundle:Country')->findOneByName('Portugal');
                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('Unilever');
                    $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find(3);

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneBy([
                        'client' => $client,
                        'country' => $country,
                        'campaignstatus' => $status,
                    ]);


                    if ($campaign) {
                        $existing_campaign_name = $campaign->getName();
                        $existing_campaign_id = $campaign->getId();
                        $output->writeln('A campaign for this specs already exists. [case 9] ');
                        $output->writeln($existing_campaign_name . ' -> ' . $existing_campaign_id);
                        $output->writeln('It is not the case to add another duplicate of this.');
                    } else {

                        $campaign = new Campaign();
                        $campaign_key = Uuid::uuid4()->toString();
                        $campaign->setId($campaign_key);
                        $campaign->setName('Case9.Client=Unilever.Country=Portugal.Status=Completed');
                        $campaign->setCampaignstatus($status);
                        $campaign->setCountry($country);
                        $product = $manager->getRepository('CampaignBundle:Product')->find(25);
                        $campaign->setProduct($product);
                        $productline = $product->getProductline();
                        $campaign->setProductline($productline);
                        $brand = $productline->getBrand();
                        $campaign->setBrand($brand);
                        $division = $brand->getDivision();
                        $campaign->setDivision($division);
                        $client = $division->getClient();
                        $campaign->setClient($client);
                        $campaign->setCampaignidea('ninecampaignstest');
                //        $campaign_creator = $manager->getRepository('UserBundle:User')->find(5);
                        $campaign->setUser($campaign_creator);
                        $campaign->setClientPresentation(true);
                        $campaign->setNotVisible(false);
                        $campaign->setCompleteness(0);
                        $campaign->setCreatedAt($creationDate);
                        $campaign->setUpdatedAt($creationDate);
                        $data = new \DateTime();
                        $timestamp = $data->getTimestamp();
                        $completion_min_days = 1;
                        $completion_max_days = 15;
                        $random_completion_time = rand(($completion_min_days * 86400), ($completion_max_days * 86400));
                        $random_completion_timestamp = $timestamp + $random_completion_time;
                        $rand_completion_timestamp_object = new \DateTime();
                        $rand_completion_timestamp_object->setTimestamp($random_completion_timestamp);
                        $deliverable_min_days = 15;
                        $deliverable_max_days = 30;
                        $random_deliverable_time = rand(($deliverable_min_days * 86400), ($deliverable_max_days * 86400));
                        $random_deliverable_timestamp = $timestamp + $random_deliverable_time;
                        $random_deliverable_timestamp_object = new \DateTime();
                        $random_deliverable_timestamp_object->setTimestamp($random_deliverable_timestamp);
                        $campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                        $campaign->setCompletionDate($rand_completion_timestamp_object);
                        $manager->persist($campaign);
                        
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($campaign_creator);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Created a Campaign with following combination Case9.Client=Unilever.Country=Portugal.Status=Completed');


                        //ADD THE TASKS FOR THIS CAMPAIGN
                        //Fetch all the task types
                        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                        //Fetch the default task status
                        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                        //For each task type , add to this campaign
                        foreach ($task_types as $tasktype) {

                            $new_task = new Task();
                            $new_task->setCampaign($campaign);
                            $new_task->setTaskname($tasktype);
                            $new_task->setOwner($campaign_creator);
                            $new_task->setTaskmessage(NULL);
                            $new_task->setMatrixfileversion(0);
                            $new_task->setTaskstatus($default_task_status);
                            $new_task->setPhase($tasktype->getPhaseid());
                            $new_task->setCreatedAt($creationDate);
                            $new_task->setCreatedby($campaign_creator);
                            $new_task->setUpdatedAt($creationDate);
                            $manager->persist($new_task);
                        }
                        $output->writeln(' ');
                        $output->writeln('Created the tasks for the campaign ' . $campaign->getName());
                        $output->writeln(' ');
                    }
                    //VERIFY USER IS IN THIS CAMPAIGN'S TEAM

                    $is_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([
                        'campaign' => $campaign,
                        'member' => $user,
                    ]);
                    if ($is_teammember) {
                        $output->writeln('The user is already set as a teammember for this campaign.');
                    } else {
                        $teammember = new Teammember();
                        $teammember->setCampaign($campaign);
                        $teammember->setMember($user);
                        $teammember->setIsReviewer(false);
                        $manager->persist($teammember);
                        $output->writeln('The test user has been added as a normal teammember for this campaign.');
                        $output->writeln(' ');
                    }
                    $manager->flush();
                    //Randomly assign the user as a taskowner for at leat two tasks.
                    $random_ids = array();
                    $random_number_of_tasks = 6;
                    for ($i = 2; $i < $random_number_of_tasks; $i++) {
                        $random_ids[] = rand(1, 9);
                    }

                    $unique_random_ids = array_unique($random_ids);
                    $all_tasks_of_this_campaign = $manager->getRepository('TaskBundle:Task')->findByCampaign($campaign->getId());



                    foreach ($all_tasks_of_this_campaign as $task) {
                        $task->setOwner(NULL);
                        if (in_array($task->getTaskname()->getId(), $unique_random_ids)) {
                            ///ADD SET THIS TASK TO HAVE THE OWNER THE TEST USER.
                            $task->setOwner($user);

                            $output->writeln($user->getUsername() . ' has been set as a taskowner for task ' . $task->getTaskname()->getName());
                            ;
                        }
                        $manager->persist($task);
                    }
                    $manager->flush();
                    break;








                /*                 * **************************************************************************************************************************************
                 * ******************************************************************************************************************************************************** 
                 * 
                 *          THE TEST DATA REMOVAL PART STARTS HERE  ||||       THE TEST DATA REMOVAL PART STARTS HERE 
                 * 
                 * ******************************************************************************************************************************************************** 
                 * ******************************************************************************************************************************************************** 
                 */
            }
        } elseif ($input->getOption('removecampaign')) {

            $number = $input->getArgument('number');


            switch ($number) {

                case 1:

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case1.Client=Unilever.Country=USA.Status=Build');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case1.Client=Unilever.Country=USA.Status=Build. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case1.Client=Unilever.Country=USA.Status=Build');
                        $output->writeln(' ');
                    }

                    break;


                case 2 :

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case2.Client=Unilever.Country=USA.Status=Approved');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case2.Client=Unilever.Country=USA.Status=Approved. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case2.Client=Unilever.Country=USA.Status=Approved');
                        $output->writeln(' ');
                    }


                    break;


                case 3 :

                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case3.Client=Unilever.Country=China.Status=Completed');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case3.Client=Unilever.Country=China.Status=Completed. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case3.Client=Unilever.Country=China.Status=Completed');
                        $output->writeln(' ');
                    }



                    break;


                case 4:
                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case4.Client=Unilever.Country=China.Status=Cancelled');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case4.Client=Unilever.Country=China.Status=Cancelled. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case4.Client=Unilever.Country=China.Status=Cancelled');
                        $output->writeln(' ');
                    }


                    break;

                case 5 :
                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case5.Client=Unilever.Country=China.Status=Build');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case5.Client=Unilever.Country=China.Status=Cancelled. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case5.Client=Unilever.Country=China.Status=Cancelled');
                        $output->writeln(' ');
                    }
                    break;

                case 6 :
                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case6.Client=Unilever.Country=China.Status=Cancelled');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case6.Client=Unilever.Country=China.Status=Cancelled. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case6.Client=Unilever.Country=China.Status=Cancelled');
                        $output->writeln(' ');
                    }


                    break;

                case 7:
                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case7.Client=testclientforcase7.Country=China.Status=Cancelled');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('testclientforcase7');
                        $manager->remove($client);


                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case7.Client=testclientforcase7.Country=China.Status=Cancelled. And the teammembers of this campaign.');
                        $output->writeln('Also removed the testclientforcase7 client. ');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case7.Client=testclientforcase7.Country=China.Status=Cancelled');
                        $output->writeln(' ');
                    }
                    break;

                case 8:
                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case8.Client=Unilever.Country=Portugal.Status=Cancelled');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case8.Client=Unilever.Country=Portugal.Status=Cancelled. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case8.Client=Unilever.Country=Portugal.Status=Cancelled');
                        $output->writeln(' ');
                    }


                    break;

                case 9:
                    $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneByName('Case9.Client=Unilever.Country=Portugal.Status=Completed');
                    //WHEN WE REMOVE CASE 1 ,
                    if ($campaign) {
                        //1. We must remove any teammember relationship related to the specified campaign before removing the campaign.

                        $teammembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                        foreach ($teammembers as $teammember) {
                            $manager->remove($teammember);
                        }

                        $manager->remove($campaign);
                        $manager->flush();
                        $output->writeln(' ');
                        $output->writeln('Removed campaign Case9.Client=Unilever.Country=Portugal.Status=Completed. And the teammembers of this campaign.');
                        $output->writeln(' ');
                    } else {
                        $output->writeln(' ');
                        $output->writeln('There was no campaign Case9.Client=Unilever.Country=Portugal.Status=Completed');
                        $output->writeln(' ');
                    }
                    break;
            }
        } else {
            
        }
    }

}
