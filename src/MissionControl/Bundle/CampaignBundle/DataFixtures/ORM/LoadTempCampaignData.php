<?php

namespace MissionControl\Bundle\CampaignBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\CampaignBundle\Entity\Product;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\CampaignBundle\Entity\Productline;
use MissionControl\Bundle\CampaignBundle\Entity\Brand;
use MissionControl\Bundle\CampaignBundle\Entity\Division;
use MissionControl\Bundle\CampaignBundle\Entity\Client;
use MissionControl\Bundle\CampaignBundle\Entity\Teammember;
use MissionControl\Bundle\CampaignBundle\Entity\Country;
use MissionControl\Bundle\TaskBundle\Entity\Task;
use MissionControl\Bundle\TaskBundle\Entity\Taskname;
use MissionControl\Bundle\TaskBundle\Entity\Taskstatus;
use MissionControl\Bundle\UserBundle\Entity\User;
use MissionControl\Bundle\FileBundle\Entity\File;
use MissionControl\Bundle\CampaignBundle\Entity\Campaignstatus;

//This fixture must only load AFTER the original users are already loaded to the database.
// WHAT WE KNOW : CAMPAIGN UUID : f29a70c2-2ea1-4dbc-bbf8-c4787e48092f
// USER IS ONE OF ORIGINALS
//



class LoadTempCampaignData extends AbstractFixture implements OrderedFixtureInterface {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());

        $creator_email = 'Bret.Leece@Initiative.com';
        $creator_user = $manager->getRepository('UserBundle:User')->findOneByEmail($creator_email);

        //FIELDS NECCESARY :
        //'id','name', 'product','country','status','presented','completion_date','deliverable_date',token, 
        ///////

        $temp_campaign_data = array(
            //id
            '00000000-0ea0-0dbc-bbf0-c0000e00000f',
            //name
            'TEMP CAMPAIGN TEMP USER',
            //product (this will fetch the other related)
            'temp_product',
            //country
            'Colombia', // can fetch the region
            //campaign_status
            '1', //build
            //presented_to_client
            true, // true
            // CompletionDate
            '2015-05-05',
            // DeliverableDate
            '2015-05-05',
            //TOKEN KEY (another uUID)
            '1e594217-8ee5-442d-b328-8a6651890877',
        );

        //Reverse fetch this by the unique product id of this campaign.
        $product = $manager->getRepository('CampaignBundle:Product')->findOneByName($temp_campaign_data[2]);
        $country = $manager->getRepository('CampaignBundle:Country')->findOneByName($temp_campaign_data[3]);
        $status = $manager->getRepository('CampaignBundle:Campaignstatus')->find($temp_campaign_data[4]);


        $productline = $product->getProductline();
        $brand = $productline->getBrand();
        $division = $brand->getDivision();
        $client = $division->getClient();

        $completion_date = new \DateTime($temp_campaign_data[6]);
        $completion_date->setTimezone(self::timezoneUTC());
        $deliverable_date = new \DateTime($temp_campaign_data[7]);
        $deliverable_date->setTimezone(self::timezoneUTC());


        $campaign = new Campaign();

        $campaign->setUser($creator_user);
        $campaign->setId($temp_campaign_data[0]);
        $campaign->setName($temp_campaign_data[1]);
        $campaign->setClient($client);
        $campaign->setDivision($division);
        $campaign->setBrand($brand);
        $campaign->setProductline($productline);
        $campaign->setProduct($product);
        $campaign->setCountry($country);
        $campaign->setCampaignstatus($status);
        $campaign->setNotVisible(false);
        $campaign->setScreentype('10000');
        $campaign->setCompleteness(0);
        $campaign->setCompletionDate($completion_date);
        $campaign->setClientDeliverabledate($deliverable_date);
        $campaign->setClientPresentation($temp_campaign_data[5]);
        $campaign->setToken($temp_campaign_data[8]);
        $campaign->setCreatedAt($creationDate);
        $campaign->setUpdatedAt($creationDate);



//
//        //HARDCODE THE FILE DATA ABOUT THIS CAMPAIGN
//
//        $axe_matrix_file = new File();
//
//        $axe_matrix_file->setUuid('8f2cf5ce-0067-4fca-8a46-044bad239adf');
//        $axe_matrix_file->setCampaign($campaign);
//        $axe_matrix_file->setUser($creator_user);
//        $axe_matrix_file->setTask(NULL);
//        $axe_matrix_file->setFileType(NULL);
//        $axe_matrix_file->setVersion(1);
//        $axe_matrix_file->setFileName('8f2cf5ce-0067-4fca-8a46-044bad239adf.mtrx');
//        $axe_matrix_file->setOriginalName('Axe_2015 OCTO III W2.mtrx');
//        $axe_matrix_file->setContentType('.mtrx');
//        $axe_matrix_file->setFileLength('913595');
//        $axe_matrix_file->setCreatedAt($creationDate);
//        $axe_matrix_file->setUpdatedAt($creationDate);
//        $axe_matrix_file->setPath('uploads/xml/8f2cf5ce-0067-4fca-8a46-044bad239adf.mtrx');
//
//        $manager->persist($axe_matrix_file);

//        $campaign->setMatrixfileUuid('8f2cf5ce-0067-4fca-8a46-044bad239adf');
//        $campaign->setMatrixfileVersion('1');



//        ORIGINAL FILENAME WAS : Axe_2015 OCTO III W2.mtrx
//CAMPAIGN INFORMATION CHANGED
//matrixfile_uuid : 8f2cf5ce-0067-4fca-8a46-044bad239adf
//matrixfile_version : 1
//A NEW ENTRY INTO PROJECT_FILE TABLE :
//
//uuid : 8f2cf5ce-0067-4fca-8a46-044bad239adf
//campaign_uuid : f29a70c2-2ea1-4dbc-bbf8-c4787e48092f
//user_creator_id : 2
//task_id : NULL
//file_type_id : NULL
//version : 1
//file_name : 8f2cf5ce-0067-4fca-8a46-044bad239adf.mtrx
//original_name : Axe_2015 OCTO III W2.mtrx
//content_type : .mtrx 
//file_length : 913595
//created_at : 2015-02-19 10:37:41
//updated_at : 2015-02-19 10:37:41
//path : uploads/xml/8f2cf5ce-0067-4fca-8a46-044bad239adf.mtrx
        ////??????????????


        $manager->persist($campaign);

        $add_as_teammember = new Teammember();
        $add_as_teammember->setCampaign($campaign);
        $add_as_teammember->setMember($creator_user);
        $add_as_teammember->setIsReviewer(false);
        $manager->persist($add_as_teammember);





        //GENERATE THE TASKS FOR THIS CAMPAIGN.....

        $task_types = $manager->getRepository('TaskBundle:Taskname')->findAll();
        $default_task_status = $manager->getRepository('TaskBundle:Taskstatus')->find(1);

        foreach ($task_types as $tasktype) {

            $new_task = new Task();
            $new_task->setCampaign($campaign);
            $new_task->setTaskname($tasktype);
            $new_task->setOwner($creator_user);
            $new_task->setTaskmessage(NULL);
            $new_task->setMatrixfileversion(0);
            $new_task->setTaskstatus($default_task_status);
            $new_task->setPhase($tasktype->getPhaseid());
            $new_task->setCreatedAt($creationDate);
            $new_task->setCreatedby($creator_user);
            $new_task->setUpdatedAt($creationDate);
            $manager->persist($new_task);
        }





        $manager->flush();






        echo ' SUCCESS ';
        //echo 'Temp campaign created successfully. Tasks Created Too. Added user as teammember & set as taskowner.';
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 10;
    }

}
