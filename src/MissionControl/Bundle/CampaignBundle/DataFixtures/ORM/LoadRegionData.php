<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\CampaignBundle\Entity\Region;
use Symfony\Component\Config\Definition\Exception\Exception;

class LoadRegionData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/region.csv';

            if (!file_exists($filename)) {
                throw new Exception($filename . ' was not found !');
            }

            $thefile = fopen($filename, 'r');
            if (!$thefile) {
                throw new Exception($filename . ' open failed.');
            }

            while (!feof($thefile)) {

                $line = fgetcsv($thefile);
                if ($line[0] && $line[1] != NULL) {
                    $newRegion = new Region();
                    $newRegion->setId($line[0]);
                    $newRegion->setName($line[1]);
                    $newRegion->setCreatedAt($creationDate);
                    $newRegion->setUpdatedAt($creationDate);
                    $manager->persist($newRegion);
                }
            }

            $manager->flush();
            echo 'Region data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Region data from region.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
