<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use MissionControl\Bundle\CampaignBundle\Entity\Campaignstatus;

class LoadCampaignstatusData extends AbstractFixture implements OrderedFixtureInterface {

     public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());

        try {
            $filename = 'web/assets/datafiles/campaign_status.csv';

            if (!file_exists($filename)) {
                throw new Exception($filename . ' was not found !');
            }

            $thefile = fopen($filename, 'r');
            if (!$thefile) {
                throw new Exception($filename . ' open failed.');
            }

            while (!feof($thefile)) {

                $line = fgetcsv($thefile);
                if ($line[0] != NULL) {
                    $newCampaignstatus = new Campaignstatus();
                    $newCampaignstatus->setId($line[0]);
                    $newCampaignstatus->setName($line[1]);
                    $newCampaignstatus->setCreatedAt($creationDate);
                    $newCampaignstatus->setUpdatedAt($creationDate);
                    $manager->persist($newCampaignstatus);
                }
            }

            $manager->flush();
            echo 'Campaing_status data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Campaign_status data from division.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
