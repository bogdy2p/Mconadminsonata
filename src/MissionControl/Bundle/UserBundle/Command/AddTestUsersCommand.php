<?php

namespace MissionControl\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MissionControl\Bundle\UserBundle\Entity\User;
use MissionControl\Bundle\CampaignBundle\Entity\Useraccess;
use MissionControl\Bundle\CampaignBundle\Entity\Region;
use MissionControl\Bundle\CampaignBundle\Entity\Client;

class AddTestUsersCommand extends ContainerAwareCommand {

    public function configure() {
        $this->setName('testusers')
                ->setDescription('This commands description ')
                ->addArgument(
                        'number', InputArgument::OPTIONAL, 'number')
                ->addOption('add', null, InputOption::VALUE_NONE, 'If this is set , the call will add a specified number of test users to the db.')
                ->addOption('remove', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE all the test users from the db.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $manager = $this->getContainer()->get('doctrine')->getManager();



        $creationDate = new \DateTime();
        $creationDate->setTimezone(new \DateTimeZone('UTC'));


        if ($input->getOption('add')) {

            $existing_users = $manager->getRepository('UserBundle:User')->findByConfirmationToken('deletable');

            if (count($existing_users) > 0) {
                $output->writeln('Found test users in database. Will delete ' . count($existing_users) . ' test users from database.');
                sleep(1);
                foreach ($existing_users as $user) {
                    if ($user->hasRole('ROLE_ADMINISTRATOR')) {

                        $client = $manager->getRepository('CampaignBundle:Client')->find(1);
                        $region = $manager->getRepository('CampaignBundle:Region')->find(1);
                        $useraccess = $manager->getRepository('CampaignBundle:Useraccess')->findOneBy([
                            'user' => $user,
                            'client' => $client,
                            'region' => $region,
                        ]);
                        if ($useraccess) {
                            $manager->remove($useraccess);
                        }
                    }
                    $manager->remove($user);
                    $temp_client = $manager->getRepository('CampaignBundle:Client')->find(2);
                    $temp_useraccess = $manager->getRepository('CampaignBundle:Useraccess')->findOneBy([
                        'user' => $user,
                        'client' => $temp_client,
                    ]);
                    $manager->remove($temp_useraccess);
                }
                $manager->flush();
                sleep(1);
                $output->writeln('Succesfully deleted ' . count($existing_users) . ' test users from database.');
            }

            $number = $input->getArgument('number');
            for ($i = 1; $i <= $number; $i++) {

                $user = new User();
                $role = 'ROLE_VIEWER';
                if ($i % 4 == 0) {
                    $role = 'ROLE_CONTRIBUTOR';
                }
                if ($i % 20 == 0) {
                    $role = 'ROLE_ADMINISTRATOR';
                }
                $user->setCreatedAt($creationDate);
                $user->setUpdatedAt($creationDate);
                $user->setUsername('testuser' . $i);
                $user->setEmail('testuser' . $i . 'email@email.com');
                $user->setUsernameCanonical('testuser' . $i);
                $user->setEmailCanonical('testuser' . $i . 'email@email.com');
                $user->setEnabled(1);
                $user->setPassword(md5('password' . $i));
                $user->setLocked(0);
                $user->addRole($role);
                $user->setExpired(0);
                $user->setConfirmationToken('deletable');
                $user->setCredentialsExpired(0);
                $user->setApiKey($i);
                $user->setFirstname('testuser' . $i . '_FN');
                $user->setLastname('testuser' . $i . '_LN');
                $user->setPhone('01234567890');
                $user->setOffice('testuser' . $i);
                $user->setTitle('testuser' . $i);
                $manager->persist($user);
                if ($i % 20 == 0) {
                    $all_clients = $manager->getRepository('CampaignBundle:Client')->find(1);
                    $global_region = $manager->getRepository('CampaignBundle:Region')->find(1);
                    $useraccess = new Useraccess();
                    $useraccess->setUser($user);
                    $useraccess->setClient($all_clients);
                    $useraccess->setRegion($global_region);
                    $useraccess->setAllCountries(true);
                    $manager->persist($useraccess);
                }
                //ADD USERACCESS TO TEMP CLIENT REGARDLESS OF THE USER ROLE

                $temp_client = $manager->getRepository('CampaignBundle:Client')->findOneByName('temp_client');
                $global_region = $manager->getRepository('CampaignBundle:Region')->find(1);
                $temp_useraccess = new Useraccess();
                $temp_useraccess->setUser($user);
                $temp_useraccess->setClient($temp_client);
                $temp_useraccess->setRegion($global_region);
                $temp_useraccess->setAllCountries(true);
                $manager->persist($temp_useraccess);

                //




                $manager->flush();
                $output->writeln('Created user ' . $i . ' role : ' . $role . '     Userid:' . $user->getId());
            }
        } elseif ($input->getOption('remove')) {

            $existing_users = $manager->getRepository('UserBundle:User')->findByConfirmationToken('deletable');
            $count = 0;
            foreach ($existing_users as $user) {

                if ($user->hasRole('ROLE_ADMINISTRATOR')) {
                    $client = $manager->getRepository('CampaignBundle:Client')->find(1);
                    $region = $manager->getRepository('CampaignBundle:Region')->find(1);
                    $useraccess = $manager->getRepository('CampaignBundle:Useraccess')->findOneBy([
                        'user' => $user,
                        'client' => $client,
                        'region' => $region,
                    ]);
                    if ($useraccess) {
                        $manager->remove($useraccess);
                    }
                }
                $temp_client = $manager->getRepository('CampaignBundle:Client')->find(2);
                $temp_useraccess = $manager->getRepository('CampaignBundle:Useraccess')->findOneBy([
                    'user' => $user,
                    'client' => $temp_client,
                ]);
                $manager->remove($temp_useraccess);
                $manager->remove($user);
                $count += 1;
            }
            $manager->flush();
            $output->writeln(' ');
            $output->writeln('Removed ' . $count . ' test users.');
            $output->writeln(' ');
        } else {
            $number = $input->getArgument('number');
            $output->writeln(' ');
            $output->writeln(' ');
            $output->writeln('You can use --add / --remove options to change what the command should do.The command is for testing purposes only.');
            $output->writeln(' ');
            $output->writeln(' ');
        }
    }

}
