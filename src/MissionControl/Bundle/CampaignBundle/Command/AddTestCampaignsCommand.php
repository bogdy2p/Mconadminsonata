<?php

namespace MissionControl\Bundle\CampaignBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\CampaignBundle\Entity\Teammember;
use MissionControl\Bundle\TaskBundle\Entity\Task;
use MissionControl\Bundle\UserBundle\Entity\User;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class AddTestCampaignsCommand extends ContainerAwareCommand {

    public function configure() {
        $this->setName('testcampaigns')
                ->setDescription('This commands description ')
                ->addOption('add', null, InputOption::VALUE_NONE, 'If this is set , the call will add a specified number of test campaigns to the db.')
                ->addOption('remove', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE all the test campaigns from the db.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $manager = $this->getContainer()->get('doctrine')->getManager();

        $creationDate = new \DateTime();
        $creationDate->setTimezone(new \DateTimeZone('UTC'));

        if ($input->getOption('add')) {

            $existing_test_campaigns = $manager->getRepository('CampaignBundle:Campaign')->findByCampaignidea('thisisadeletabletestcampaign');

            if (count($existing_test_campaigns) > 0) {
                $output->writeln('Found test_campaigns in database. Will delete ' . count($existing_test_campaigns) . ' test_campaigns from database.');
                sleep(1);
                foreach ($existing_test_campaigns as $campaign) {

                    $teammember_entries = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                    if ($teammember_entries) {
                        foreach ($teammember_entries as $entry) {
                            $manager->remove($entry);
                        }
                    }


                    $manager->remove($campaign);
                }
                $manager->flush();
                sleep(1);
                $output->writeln('Succesfully deleted ' . count($existing_test_campaigns) . ' test_campaigns from database.');
            }

            $role_contrib = $manager->getRepository('UserBundle:Role')->find(2);
            $role_admin = $manager->getRepository('UserBundle:Role')->find(3);

            $contributors = array();
            $administrators = array();

            $all_users = $manager->getRepository('UserBundle:User')->findByConfirmationToken('deletable');
            foreach ($all_users as $user) {
                if ($user->hasRole('ROLE_CONTRIBUTOR')) {
                    $contributors[] = $user;
                }
                if ($user->hasRole('ROLE_ADMINISTRATOR')) {
                    $administrators[] = $user;
                }
            }

            $output->writeln('All users: ' . count($all_users));
            $output->writeln('Contributors: ' . count($contributors));
            $output->writeln('Administrators: ' . count($administrators));

            foreach ($contributors as $contributor) {

                $campaign = new Campaign();
                $key = Uuid::uuid4()->toString();
                /////RANDOMLY GRAB DATA TO POPULATE THIS CAMPAIGN
                /////RANDOMLY GRAB DATA TO POPULATE THIS CAMPAIGN
                $products_in_database = $manager->getRepository('CampaignBundle:Product')->findAll();
                $number_of_products = count($products_in_database);
                $random_pick = rand(1, $number_of_products);
                $product = $manager->getRepository('CampaignBundle:Product')->find($random_pick);
                $productline = $product->getProductline();
                $brand = $productline->getBrand();
                $division = $brand->getDivision();
                $client = $division->getClient();

                $random_status_id = rand(1, 1);
                $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find($random_status_id);
                /////END RANDOMLY GRAB DATA TO POPULATE THIS CAMPAIGN

                $campaign_name = $contributor->getUsername() . 'campaignTEST';

                ///ASSIGN
                $campaign->setCampaignidea('thisisadeletabletestcampaign');
                $campaign->setId($key);
                $campaign->setUser($contributor);
                $campaign->setClient($client);
                $campaign->setDivision($division);
                $campaign->setBrand($brand);
                $campaign->setProductline($productline);
                $campaign->setProduct($product);
                $campaign->setName($campaign_name);
                $campaign->setCampaignstatus($status);

                /////RANDOM GRAB COUNTRY
                $countries = $manager->getRepository('CampaignBundle:Country')->findAll();
                $country_number = count($countries);
                $random_country_id = rand(1, $country_number);
                $country = $manager->getRepository('CampaignBundle:Country')->find($random_country_id);

                /////END RANDOM GRAB COUNTRY
                ///ASSIGN

                $campaign->setCountry($country);
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

                ////// SET THE CONTRIB USER AS A TEAMMEMBER FOR THIS CAMPAIGN
                ////// SET THE CONTRIB USER AS A TEAMMEMBER FOR THIS CAMPAIGN

                $add_as_teammember = new Teammember();
                $add_as_teammember->setCampaign($campaign);
                $add_as_teammember->setMember($contributor);
                $add_as_teammember->setIsReviewer(false);
                $manager->persist($add_as_teammember);


                //////////////ADD TASKS FOR THIS CONTRIB CAMPAIGN HERE
                //////////////ADD TASKS FOR THIS CONTRIB CAMPAIGN HERE
                //////////////ADD TASKS FOR THIS CONTRIB CAMPAIGN HERE
                //Fetch all the task types
                $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                //Fetch the default task status
                $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                //For each task type , add to this campaign
                foreach ($task_types as $tasktype) {

                    $new_task = new Task();
                    $new_task->setCampaign($campaign);
                    $new_task->setTaskname($tasktype);
                    $new_task->setOwner($contributor);
                    $new_task->setTaskmessage(NULL);
                    $new_task->setMatrixfileversion(0);
                    $new_task->setTaskstatus($default_task_status);
                    $new_task->setPhase($tasktype->getPhaseid());
                    $new_task->setCreatedAt($creationDate);
                    $new_task->setCreatedby($contributor);
                    $new_task->setUpdatedAt($creationDate);
                    $manager->persist($new_task);
                }
            }

            foreach ($administrators as $administrator) {

                $admin_campaign = new Campaign();

                $key = Uuid::uuid4()->toString();
                /////RANDOMLY GRAB DATA TO POPULATE THIS CAMPAIGN
                /////RANDOMLY GRAB DATA TO POPULATE THIS CAMPAIGN
                $products_in_database = $manager->getRepository('CampaignBundle:Product')->findAll();
                $number_of_products = count($products_in_database);
                $random_pick = rand(1, $number_of_products);
                $product = $manager->getRepository('CampaignBundle:Product')->find($random_pick);
                $productline = $product->getProductline();
                $brand = $productline->getBrand();
                $division = $brand->getDivision();
                $client = $division->getClient();

                $random_status_id = rand(1, 1);
                $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find($random_status_id);
                /////END RANDOMLY GRAB DATA TO POPULATE THIS CAMPAIGN

                $campaign_name = $contributor->getUsername() . 'campaignTEST';

                ///ASSIGN
                $admin_campaign->setCampaignidea('thisisadeletabletestcampaign');
                $admin_campaign->setId($key);
                $admin_campaign->setUser($administrator);
                $admin_campaign->setClient($client);
                $admin_campaign->setDivision($division);
                $admin_campaign->setBrand($brand);
                $admin_campaign->setProductline($productline);
                $admin_campaign->setProduct($product);
                $admin_campaign->setName($campaign_name);
                $admin_campaign->setCampaignstatus($status);

                /////RANDOM GRAB COUNTRY
                $countries = $manager->getRepository('CampaignBundle:Country')->findAll();
                $country_number = count($countries);
                $random_country_id = rand(1, $country_number);
                $country = $manager->getRepository('CampaignBundle:Country')->find($random_country_id);

                /////END RANDOM GRAB COUNTRY
                ///ASSIGN

                $admin_campaign->setCountry($country);
                $admin_campaign->setClientPresentation(true);
                $admin_campaign->setNotVisible(false);
                $admin_campaign->setCompleteness(0);
                $admin_campaign->setCreatedAt($creationDate);
                $admin_campaign->setUpdatedAt($creationDate);

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

                $admin_campaign->setClientDeliverabledate($random_deliverable_timestamp_object);
                $admin_campaign->setCompletionDate($rand_completion_timestamp_object);

                $manager->persist($admin_campaign);

                ////// SET THE ADMIN USER AS A TEAMMEMBER FOR THIS CAMPAIGN
                ////// SET THE ADMIN USER AS A TEAMMEMBER FOR THIS CAMPAIGN

                $add_as_teammember = new Teammember();
                $add_as_teammember->setCampaign($campaign);
                $add_as_teammember->setMember($administrator);
                $add_as_teammember->setIsReviewer(false);
                $manager->persist($add_as_teammember);

                //////////////ADD TASKS FOR THIS ADMIN CAMPAIGN HERE
                //////////////ADD TASKS FOR THIS ADMIN CAMPAIGN HERE
                //////////////ADD TASKS FOR THIS ADMIN CAMPAIGN HERE
                //Fetch all the task types
                $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
                //Fetch the default task status
                $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);
                //For each task type , add to this campaign
                foreach ($task_types as $tasktype) {

                    $new_task = new Task();
                    $new_task->setCampaign($admin_campaign);
                    $new_task->setTaskname($tasktype);
                    $new_task->setOwner($administrator);
                    $new_task->setTaskmessage(NULL);
                    $new_task->setMatrixfileversion(0);
                    $new_task->setTaskstatus($default_task_status);
                    $new_task->setPhase($tasktype->getPhaseid());
                    $new_task->setCreatedAt($creationDate);
                    $new_task->setCreatedby($administrator);
                    $new_task->setUpdatedAt($creationDate);
                    $manager->persist($new_task);
                }
            }
            $manager->flush();
        } elseif ($input->getOption('remove')) {

            $existing_test_campaigns = $manager->getRepository('CampaignBundle:Campaign')->findByCampaignidea('thisisadeletabletestcampaign');
            $count = 0;
            foreach ($existing_test_campaigns as $campaign) {

                $teammember_entries = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                if ($teammember_entries) {
                    foreach ($teammember_entries as $entry) {
                        $manager->remove($entry);
                    }
                }


                $manager->remove($campaign);
                $count += 1;
            }
            $manager->flush();
            $output->writeln('Removed ' . $count . ' TEST_campaigns.');
        } else {
            $output->writeln(' ');
            $output->writeln(' ');
            $output->writeln('You can use --add / --remove options to change what the command should do.The command is for testing purposes only.');
            $output->writeln(' ');
            $output->writeln(' ');
        }
    }

}
