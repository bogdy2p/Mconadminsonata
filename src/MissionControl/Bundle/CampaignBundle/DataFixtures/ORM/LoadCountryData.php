<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\CampaignBundle\Entity\Country;
use MissionControl\Bundle\CampaignBundle\Entity\Region;
use Symfony\Component\Config\Definition\Exception\Exception;

class LoadCountryData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/country.csv';

            if (!file_exists($filename)) {
                throw new Exception($filename . ' was not found !');
            }

            $thefile = fopen($filename, 'r');
            if (!$thefile) {
                throw new Exception($filename . ' open failed.');
            }

            while (!feof($thefile)) {

                $line = fgetcsv($thefile);
                if ($line[0] && $line[1] && $line[2] != NULL) {
                    $newCountry = new Country();
                    $newCountry->setCountryId($line[0]);
                    $newCountry->setName($line[1]);

                    // Must fetch the region for each provided id from the database here.
                    // And assign each country a region.
                    $region = $manager->getRepository('CampaignBundle:Region')
                            ->find($line[2]);

                    $newCountry->setRegion($region);
                    $newCountry->setCountrycode($line[3]);
                    $newCountry->setCreatedAt($creationDate);
                    $newCountry->setUpdatedAt($creationDate);
                    $manager->persist($newCountry);
                }
            }

            $manager->flush();
            echo 'Country data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Country data from country.csv file. ';
            echo " ";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 2;
    }

}
