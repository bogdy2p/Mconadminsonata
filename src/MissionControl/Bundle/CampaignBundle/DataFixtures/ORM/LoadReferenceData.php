<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\CampaignBundle\Entity\Client;
use MissionControl\Bundle\CampaignBundle\Entity\Division;
use MissionControl\Bundle\CampaignBundle\Entity\Brand;
use MissionControl\Bundle\CampaignBundle\Entity\Product;
use MissionControl\Bundle\CampaignBundle\Entity\Productline;
use Symfony\Component\Config\Definition\Exception\Exception;

class LoadReferenceData extends AbstractFixture implements OrderedFixtureInterface {

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
            $filename = 'web/assets/datafiles/reference1.csv';

            if (!file_exists($filename)) {
                throw new Exception($filename . ' was not found !');
            }

            $thefile = fopen($filename, 'r');
            if (!$thefile) {
                throw new Exception($filename . ' open failed.');
            }
//
            while (!feof($thefile)) {

                $line = fgetcsv($thefile);
                if ($line[0] != NULL) {

                    $client = $manager->getRepository('CampaignBundle:Client')->findOneByName($line[0]);
                    if (!$client) {
                        $client = new Client();
                        $client->setName($line[0]);
                        $client->setDbid(0);
                        $client->setCreatedAt($creationDate);
                        $client->setUpdatedAt($creationDate);
                        $manager->persist($client);
                    }
                    //FIND A A DIVISION FOR THIS CLIENT.
                    $division = $manager->getRepository('CampaignBundle:Division')->findOneBy([
                        'client' => $client,
                        'name' => $line[1]
                    ]);
                    if (!$division) {
                        $division = new Division();
                        $division->setName($line[1]);
                        $division->setClient($client);
                        $division->setCreatedAt($creationDate);
                        $division->setUpdatedAt($creationDate);
                        $manager->persist($division);
                    }

                    //FIND ONE BRAND FOR THIS CLIENT AND DIVISION
                    $brand = $manager->getRepository('CampaignBundle:Brand')->findOneBy([
                        //'client' => $client,
                        'division' => $division,
                        'name' => $line[2]
                    ]);

                    if (!$brand) {
                        $brand = new Brand();
                        $brand->setClient($client);
                        $brand->setName($line[2]);
                        $brand->setDivision($division);
                        $brand->setCreatedAt($creationDate);
                        $brand->setUpdatedAt($creationDate);
                        $manager->persist($brand);
                    }
                    //FIND A PRODUCLINE BY CLIENT , DIVISION AND BRAND.
                    $productline = $manager->getRepository('CampaignBundle:Productline')->findOneBy([
                        //'client' => $client,
                        //'division' => $division,
                        'brand' => $brand,
                        'name' => $line[3],
                    ]);
                    if (!$productline) {
                        $productline = new Productline();

                        $productline->setName($line[3]);
                        $productline->setBrand($brand);
                        $productline->setDivision($division);
                        $productline->setClient($client);
                        $productline->setCreatedAt($creationDate);
                        $productline->setUpdatedAt($creationDate);
                        
                        $manager->persist($productline);
                    }
                    $manager->flush();

                    $newProduct = new Product();
                    $newProduct->setName($line[4]);
                    $newProduct->setClient($client);
                    $newProduct->setBrand($brand);
                    $newProduct->setProductline($productline);
                    $newProduct->setCreatedAt($creationDate);
                    $newProduct->setUpdatedAt($creationDate);
                    $productline->addProduct($newProduct);
                    $manager->persist($newProduct);
                }
            }

            $manager->flush();
            echo 'Reference data succesfully imported from ' . $filename;
        } catch (Exception $e) {
            echo 'Could not load Reference data from reference1.csv file .', "\n";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 2;
    }

}
