<?php

namespace MissionControl\Bundle\TaskBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\TaskBundle\Entity\Taskstatus;
use Symfony\Component\Config\Definition\Exception\Exception;

class LoadTaskstatusData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/taskstatus.csv';

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

                    $newTaskstatus = new Taskstatus();
                    $newTaskstatus->setId($line[0]);
                    $newTaskstatus->setName($line[1]);
                    $newTaskstatus->setCreatedAt($creationDate);
                    $newTaskstatus->setUpdatedAt($creationDate);
                    //This should change after phases implementation , should be a link to the phase.

                    $manager->persist($newTaskstatus);
                }
            }

            $manager->flush();
            echo 'Task Status data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Task Status data from taskstatus.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
