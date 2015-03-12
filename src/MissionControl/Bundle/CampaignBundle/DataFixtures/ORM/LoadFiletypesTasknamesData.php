<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use MissionControl\Bundle\CampaignBundle\Entity\Filetype;

class LoadFiletypeData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/filetypestasknames.csv';

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
                    
                    
                    $TaskName = $manager->getRepository('TaskBundle:Taskname')->find($line[0]);
                    $FileType = $manager->getRepository('CampaignBundle:Filetype')->find($line[1]);
                    
                    $TaskName->addFiletype($FileType);
                    $FileType->addTaskname($TaskName);
                    
                   
                    
                    $manager->persist($TaskName);
                    $manager->persist($FileType);
                }
            }

            $manager->flush();
            echo 'FiletypesTasknames mapping data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load FiletypesTasknames mapping from filetypestasknames.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 3;
    }

}
