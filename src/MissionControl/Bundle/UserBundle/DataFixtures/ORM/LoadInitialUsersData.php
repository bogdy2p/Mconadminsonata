<?php

namespace MissionControl\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\UserBundle\Entity\User;
use MissionControl\Bundle\CampaignBundle\Entity\Useraccess;
use MissionControl\Bundle\UserBundle\Entity\Role;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class LoadInitialUsersData extends AbstractFixture implements OrderedFixtureInterface {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {

        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());

        $arrays_of_user_data = array(
            array('bleece', 'password', 'Bret', 'Leece', 'Bret.Leece@Initiative.com', 'ROLE_ADMINISTRATOR', '8f81fa58-a901-45a5-b444-c11dac04e384'),
            array('jgladysz', 'password', 'John', 'Gladysz', 'John.Gladysz@Initiative.com', 'ROLE_ADMINISTRATOR', 'cd78185e-6cbf-4da6-82ee-7a737df7afb7'),
            array('smoseley', 'password', 'Sue', 'Moseley', 'Sue.Moseley@Initiative.com', 'ROLE_ADMINISTRATOR', '805d323c-7f91-4b62-a7e2-94acb2b054f5'),
            array('nsantos', 'password', 'Nathalie', 'Santos', 'Nathalie.Santos@Initiative.com', 'ROLE_CONTRIBUTOR', '4ededec2-5c43-4735-a106-9edf3e0c9431'),
            array('sivey', 'password', 'Sarah', 'Ivey', 'Sarah.Ivey@Initiative.com', 'ROLE_ADMINISTRATOR', '482f35f3-2faa-488c-b11b-9db8e4e0eb2e'),
            array('lmeranus', 'password', 'Leah', 'Meranus', 'Leah.Meranus@Initiative.com', 'ROLE_CONTRIBUTOR', 'ade80d1f-8581-47fe-8ce4-3546ba4788ea'),
            array('sebrahim', 'password', 'Samira', 'Ebrahim', 'Samira.Ebrahim@Initiative.com', 'ROLE_ADMINISTRATOR', '09f0ae7c-0ce8-4f5d-86f6-4b4941134df6'),
            array('jelms', 'password', 'Jim', 'Elms', 'Jim.Elms@Initiative.com', 'ROLE_ADMINISTRATOR', '6123986c-dd7a-4496-819a-17cd1af38e3d'),
            array('jmerchan', 'password', 'Jaime', 'Merchan', 'Jaime.Merchan@Initiative.com', 'ROLE_ADMINISTRATOR', '512c13a4-c255-4787-b370-8ae63c87c8cc'),
            array('gvillasboas', 'password', 'Goncalo', 'Boas', 'Goncalo.Boas@Initiative.com', 'ROLE_ADMINISTRATOR', 'bf4bff30-4664-48f9-87d5-fb78520df136'),
            array('vsoldano', 'password', 'Veronica', 'Soldano', 'Veronica.Soldano@Initiative.com', 'ROLE_CONTRIBUTOR', 'dbc89dcf-4cbe-4b25-b80a-1f7499e993aa'),
            array('srivalsbodin', 'password', 'Sophie', 'Bodin', 'Sophie.Bodin@Initiative.com', 'ROLE_ADMINISTRATOR', 'ac414394-b595-4129-be18-616e67221e84'),
            array('ecabral', 'password', 'Erika', 'Cabral', 'Erika.Cabral@Initiative.com', 'ROLE_CONTRIBUTOR', 'ecf1a749-883e-4f2d-aeeb-fe3d50aa69cb'),
            array('acardoso', 'password', 'Ana', 'Cardoso', 'Ana.Cardoso@Initiative.com', 'ROLE_CONTRIBUTOR', 'c7f27c68-9fa5-46dd-ae89-75bde9ffedf1'),
            array('bosdoit', 'password', 'Bertrand', 'Osdoit', 'Bertrand.Osdoit@Initiative.com', 'ROLE_ADMINISTRATOR', 'a7f27c68-b599-47e6-bd48-e08e45c9e58a'),
            array('bertrand', 'Test1234', 'Bertrands Matrix User', 'Do not Edit', 'b@b.com', 'ROLE_ADMINISTRATOR', 'e2a0ff6a-ec64-47e6-bd48-e08e45c9e58a'),
            array('qa_user', 'password', 'Quality', 'Assurance User', 'a@a.com', 'ROLE_ADMINISTRATOR', '8f45fa58-4664-b11b-82ee-7a737df7afb7')
        );
        $count = 0;
        foreach ($arrays_of_user_data as $array_of_user_data) {
            //Generate an aPI KEY//
            $count += 1;
            $rolename = $array_of_user_data[5];
            $role = $manager->getRepository('UserBundle:Role')->findOneByName($rolename);

            // $api_key = Uuid::uuid4()->toString();
            $originalUser = new User();
            $originalUser->setCreatedAt($creationDate);
            $originalUser->setUpdatedAt($creationDate);
            $originalUser->setEnabled(1);
            $originalUser->setLocked(0);
            $originalUser->setExpired(0);
            $originalUser->setCredentialsExpired(0);
            $originalUser->setOffice('New York');
            $originalUser->setTitle('noTitleInformationyet');
            $originalUser->setConfirmationToken('GeneratedOriginalUser');


            $originalUser->addRole($role->getName());
            $originalUser->setUsername($array_of_user_data[0]);
            $originalUser->setEmail($array_of_user_data[4]);
            $originalUser->setUsernameCanonical($array_of_user_data[0]);
            $originalUser->setEmailCanonical($array_of_user_data[4]);
            $originalUser->setPassword(md5($array_of_user_data[1]));
            $originalUser->setApiKey($array_of_user_data[6]);
            $originalUser->setFirstname($array_of_user_data[2]);
            $originalUser->setLastname($array_of_user_data[3]);
            $originalUser->setPhone('2126057000');
            $manager->persist($originalUser);


            //ADD USERACCESS TO TEMP CLIENT FOR EACH OF THIS USERS

            $temp_client = $manager->getRepository('CampaignBundle:Client')->findOneByName('temp_client');
            $global_region = $manager->getRepository('CampaignBundle:Region')->find(1);
            $temp_useraccess = new Useraccess();
            $temp_useraccess->setUser($originalUser);
            $temp_useraccess->setClient($temp_client);
            $temp_useraccess->setRegion($global_region);
            $temp_useraccess->setAllCountries(true);
            $manager->persist($temp_useraccess);




            //END ADD USERACCESS
        }
        echo'Loaded ' . $count . ' Original Users Successfully.';
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 3;
    }

}
