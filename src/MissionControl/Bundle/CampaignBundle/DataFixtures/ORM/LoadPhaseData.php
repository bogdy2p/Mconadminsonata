<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\CampaignBundle\Entity\Phase;

class LoadPhaseData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/phase.csv';

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
                    $newPhase = new Phase();
                    $newPhase->setId($line[0]);
                    $newPhase->setName($line[1]);
                    $newPhase->setCreatedAt($creationDate);
                    $newPhase->setUpdatedAt($creationDate);
                    $manager->persist($newPhase);
                }
            }

            $manager->flush();
            echo 'Phase data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Phase data from phase.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
