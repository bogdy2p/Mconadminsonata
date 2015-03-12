<?php

namespace MissionControl\Bundle\TaskBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\TaskBundle\Entity\Taskname;
use Symfony\Component\Config\Definition\Exception\Exception;

class LoadTasknameData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/taskname.csv';

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

                    $newTaskname = new Taskname();

                    $newTaskname->setId($line[0]);
                    $newTaskname->setName($line[1]);
                    //This should change after phases implementation , should be a link to the phase.
                    $newTaskname->setPhaseid($line[2]);
                    $newTaskname->setCreatedAt($creationDate);
                    $newTaskname->setUpdatedAt($creationDate);
                    $manager->persist($newTaskname);
                }
            }

            $manager->flush();
            echo 'Taskname data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Taskname data from taskname.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
