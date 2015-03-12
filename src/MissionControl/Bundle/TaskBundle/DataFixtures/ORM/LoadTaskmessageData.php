<?php

namespace MissionControl\Bundle\TaskBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\TaskBundle\Entity\Taskmessage;
use Symfony\Component\Config\Definition\Exception\Exception;

class LoadTaskmessageData extends AbstractFixture implements OrderedFixtureInterface {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
//        $creationDate = new \DateTime();
//        $creationDate->setTimezone(self::timezoneUTC());
//
//        try {
//            $filename = 'web/assets/datafiles/taskmessage.csv';
//
//            if (!file_exists($filename)) {
//                throw new Exception($filename . ' was not found !');
//            }
//
//            $thefile = fopen($filename, 'r');
//            if (!$thefile) {
//                throw new Exception($filename . ' open failed.');
//            }
//
//            while (!feof($thefile)) {
//
//                $line = fgetcsv($thefile);
//                if ($line[0] && $line[1] != NULL) {
//
//                    $newTaskmessage = new Taskmessage();
//                    $newTaskmessage->setMessage($line[1]);
//                    $newTaskmessage->setCreatedAt($creationDate);
//                    $newTaskmessage->setUpdatedAt($creationDate);
//                    //This should change after phases implementation , should be a link to the phase.
//
//                    $manager->persist($newTaskmessage);
//                }
//            }
//
//            $manager->flush();
//            echo 'Task MESSAGE data succesfully imported from ' . $filename;
//        } catch (Exception $e) {
//            echo 'Could not load Task MESSAGE data from taskmessage.csv file .', "\n";
//        }
        echo 'Load Task Message has been disabled.';
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1;
    }

}
