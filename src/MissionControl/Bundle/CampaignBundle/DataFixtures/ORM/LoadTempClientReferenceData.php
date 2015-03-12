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

class LoadTempClientReferenceData extends AbstractFixture implements OrderedFixtureInterface {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());


        $client = $manager->getRepository('CampaignBundle:Client')->findOneByName('temp_client');

        $division = $manager->getRepository('CampaignBundle:Division')->findOneBy([
            'client' => $client,
            'name' => 'temp_division'
        ]);
        if (!$division) {
            $division = new Division();
            $division->setName('temp_division');
            $division->setClient($client);
            $division->setCreatedAt($creationDate);
            $division->setUpdatedAt($creationDate);
            $manager->persist($division);
        }

        $brand = $manager->getRepository('CampaignBundle:Brand')->findOneBy([
            'division' => $division,
            'name' => 'temp_brand'
        ]);

        if (!$brand) {
            $brand = new Brand();
            $brand->setClient($client);
            $brand->setName('temp_brand');
            $brand->setDivision($division);
            $brand->setCreatedAt($creationDate);
            $brand->setUpdatedAt($creationDate);
            $manager->persist($brand);
        }
        $productline = $manager->getRepository('CampaignBundle:Productline')->findOneBy([
            'brand' => $brand,
            'name' => 'temp_productline',
        ]);
        if (!$productline) {
            $productline = new Productline();
            $productline->setName('temp_productline');
            $productline->setBrand($brand);
            $productline->setDivision($division);
            $productline->setClient($client);
            $productline->setCreatedAt($creationDate);
            $productline->setUpdatedAt($creationDate);

            $manager->persist($productline);
        }


        $newProduct = new Product();
        $newProduct->setName('temp_product');
        $newProduct->setClient($client);
        $newProduct->setBrand($brand);
        $newProduct->setProductline($productline);
        $newProduct->setCreatedAt($creationDate);
        $newProduct->setUpdatedAt($creationDate);
        $productline->addProduct($newProduct);
        $manager->persist($newProduct);
        $manager->flush();

        echo 'Temp_client reference data succesfully created';
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 2;
    }

}
