<?php

namespace MissionControl\Bundle\CampaignBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\UserBundle\Entity\User;
use MissionControl\Bundle\CampaignBundle\Entity\Teammember;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class AddTestTeammembersCommand extends ContainerAwareCommand {

    public function configure() {
        $this->setName('testmember')
                ->setDescription('This commands description ')
                ->addArgument(
                        'number', InputArgument::OPTIONAL, 'number')
                ->addOption('add', null, InputOption::VALUE_NONE, 'If this is set , the call will add a specified number of test campaigns to the db.')
                ->addOption('remove', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE all the test campaigns from the db.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        // $howmany = 0;
        $manager = $this->getContainer()->get('doctrine')->getManager();

        if ($input->getOption('add')) {
            $max_teammembers = $input->getArgument('number');


            if ($max_teammembers > 15) {
                die('This is intended to add up-to 15 Members to a team AT A TIME ONLY! Please change your value and retry');
            }


            $number_of_initial_teammembers = count($manager->getRepository('CampaignBundle:Teammember')->findAll());

            $all_test_campaigns = $manager->getRepository('CampaignBundle:Campaign')->findByCampaignidea('thisisadeletabletestcampaign');
            $all_test_users = $manager->getRepository('UserBundle:User')->findByConfirmationToken('deletable');



            $all_user_ids = array();
            foreach ($all_test_users as $test_user) {
                if ($test_user->hasRole('ROLE_VIEWER')) {
                    $all_user_ids[] = $test_user->getId();
                }
            }
            /// FOR EACH TEST CAMPAIGN 



            foreach ($all_test_campaigns as $test_campaign) {
                //FOR EACH CAMPAING 
                //NUMBER OF MEMBERS WILL BE RANDOM BETWEEN 1 AND MAX TEAMMEMBERS
                $number_of_members = rand(1, $max_teammembers);


                ///RANDOMLY PICK $NUMBER_OF_MEMBERS MEMBERS FROM THE TESTUSERS
                $random_ids = array();
                for ($i = 1; $i < $number_of_members; $i++) {

                    $random_id = rand(0, (count($all_user_ids) - 1));
                    $random_ids[] = $all_user_ids[$random_id];
                }


                //TRY TO GET ONLY THE UNIQUE RANDOM IDS... :(
                $unique_random_ids = array_unique($random_ids);
                /// HERE WE ARE IN A CAMPAIGN , AND WE HAVE A RANDOM NUMBER OF USER ID'S
                //Logic :
                // For each random user id , add a TEAMMBEMER LINK TO THE CURRENT CAMPAIGN

                foreach ($unique_random_ids as $random_user_id) {
                    //$output->writeln($random_user_id);
                    $user = $manager->getRepository('UserBundle:User')->find($random_user_id);
                    //Check if there isnt already a link for this user ,.
                    $already_a_teammember = $manager->getRepository('CampaignBundle:Teammember')->findOneBy(['campaign' => $test_campaign, 'member' => $user]);


                    $recheck = $manager->getRepository('CampaignBundle:Teammember')->findOneBy([ 'member' => $user, 'campaign' => $test_campaign]);

                    if (!($already_a_teammember || $recheck)) {
                        $random_reviewer = rand(1, 100);
                        $test_teammember = new Teammember();
                        $test_teammember->setCampaign($test_campaign);
                        $test_teammember->setMember($user);
                        if ($random_reviewer > 49) {
                            $test_teammember->setIsReviewer(true);
                        } else {
                            $test_teammember->setIsReviewer(false);
                        }
                    }

                    if (isset($test_teammember)) {
                        //$howmany += 1;
                        $manager->persist($test_teammember);
                    }
                }
            }
            $manager->flush();





            //RUN A SECURITY CHECK TO SEE IF DOUBLES WERE INSERTED

            $error = false;
            $errored_on = array();
            $all_test_campaigns = $manager->getRepository('CampaignBundle:Campaign')->findByCampaignidea('thisisadeletabletestcampaign');
            foreach ($all_test_campaigns as $campaign) {
                $testmembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                $this_campaigns_members = array();
                foreach ($testmembers as $testmember) {
                    $this_campaigns_members[] = $testmember->getMember()->getId();
                }
                $number_of_testmembers = count($this_campaigns_members);
                $number_of_unique_testmembers = count(array_unique($this_campaigns_members));

                if ($number_of_testmembers != $number_of_unique_testmembers) {
                    $error = true;
                    $errored_on[] = $campaign->getId();
                }
            }


            if ($error) {
                print_r('THERE IS AT LEAST ONE DUPLICATE INSERTION ...  !');
                print_r('THERE IS AT LEAST ONE DUPLICATE INSERTION ...  !');
                print_r('THERE IS AT LEAST ONE DUPLICATE INSERTION ...  !');
                print_r($errored_on);
            } else {
                $number_of_teammembers_after_insertion = count($manager->getRepository('CampaignBundle:Teammember')->findAll());
                $difference = ($number_of_teammembers_after_insertion - $number_of_initial_teammembers);
                $output->writeln(' ');
                $output->writeln("Successfully random generated teammembers for the TESTCAMPAIGNS. Used only TESTUSERS for this.      ");
                $output->writeln('Total added : ' . $difference);
                $output->writeln(' ');
            }
        }

        if ($input->getOption('remove')) {
            $count_how_many = 0;
            $all_test_campaigns = $manager->getRepository('CampaignBundle:Campaign')->findByCampaignidea('thisisadeletabletestcampaign');
            foreach ($all_test_campaigns as $campaign) {
                $testmembers = $manager->getRepository('CampaignBundle:Teammember')->findByCampaign($campaign);
                foreach ($testmembers as $testmember) {
                    $manager->remove($testmember);
                    $count_how_many++;
                }
            }
            $manager->flush();
            $output->writeln(' ');
            $output->writeln('Removed '.$count_how_many.' entries from teammembers (assigned to test campaigns)');
            $output->writeln(' ');
            
        }
    }

}
