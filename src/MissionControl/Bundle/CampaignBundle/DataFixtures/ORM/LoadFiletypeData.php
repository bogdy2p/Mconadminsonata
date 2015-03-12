<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use MissionControl\Bundle\CampaignBundle\Entity\Filetype;

class LoadFiletypesTasknamesData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/filetype.csv';

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
                    $newFiletype = new Filetype();
                    $newFiletype->setId($line[0]);
                    $newFiletype->setName($line[1]);
                    $newFiletype->setCreatedAt($creationDate);
                    $newFiletype->setUpdatedAt($creationDate);
                    $manager->persist($newFiletype);
                }
            }

            $manager->flush();
            echo 'Filetype data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Filetype data from filetype.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
