<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\CampaignBundle\Entity\Client;
use Symfony\Component\Config\Definition\Exception\Exception;

class LoadAllSpecialClientData extends AbstractFixture implements OrderedFixtureInterface {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());

        $allclient = new Client();
        $allclient->setName('all_clients');
        $allclient->setDbid(0);
        $allclient->setCreatedAt($creationDate);
        $allclient->setUpdatedAt($creationDate);
        $manager->persist($allclient);

        $temp_client = new Client();
        $temp_client->setName('temp_client');
        $temp_client->setDbid(0);
        $temp_client->setCreatedAt($creationDate);
        $temp_client->setUpdatedAt($creationDate);
        
        $manager->persist($temp_client);
        $manager->flush();
        echo 'The special case (all_clients and temp_client) inserted into the database.';
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
