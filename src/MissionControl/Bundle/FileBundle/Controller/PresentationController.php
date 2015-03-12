<?php

namespace MissionControl\Bundle\FileBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\File;
#PHPPowerPoint:
// use PhpOffice\PHPPowerPoint\IOfactory;
use PhpOffice\PhpPowerpoint\Slide\Layout;
use PhpOffice\PhpPowerpoint\Style\Alignment;
use PhpOffice\PhpPowerpoint\Style\Fill;
use PhpOffice\PhpPowerpoint\Style\Font;
use PhpOffice\PhpPowerpoint\Style\Color;
use PhpOffice\PhpPowerpoint\Style\Border;
# API documentation:
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use MissionControl\Bundle\FileBundle\Model\TaskType;

class PresentationController extends FOSRestController {

    const GALLERY_DIRECTORY = '/../web/uploads/presentations/gallery';
    const INITIATIVE_LOGO_BLUE = '/../web/uploads/presentations/gallery/initiative_logo_blue.png';

    /**
     * @Route("/api/v1/campaigns/{campaignId}/presentations/final-plan-outcome", name="_create_final_output")
     * @Method("GET")
     *
     * @ApiDoc(
     *      description="Generates the final outcome PPTx presentation.",
     *      statusCodes={
     *          200 = "OK. File has been sent as attachment.",
     *          403 = "Invalid API KEY",
     *          500 = {"Header x-wsse does not exist", "Request failed. Final outcome PPTX could not be generated."}
     *      },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign ID for selected campaign."}
     *      }
     * )
     */
    public function createFinalOutputAction($campaignId) {

        require_once $this->get('kernel')->getRootDir() . '/../vendor/phpoffice/phppowerpoint/src/PhpPowerpoint/Autoloader.php';
        \PhpOffice\PhpPowerpoint\Autoloader::register();

        // Retrieve campaign data:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);


        // Create a new document instance:
        $finalOutcomeBuilder = new \PhpOffice\PhpPowerpoint\PhpPowerpoint();

        // Set layout information:
        $documentLayout = $finalOutcomeBuilder->getLayout();
        $documentLayout->setDocumentLayout($documentLayout::LAYOUT_SCREEN_16X9);


        #
        #1st slide:
        #
        $currentSlide = $finalOutcomeBuilder->getActiveSlide();
        
        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);    

            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Placeholder for product image:
            $imagePlaceholder = $currentSlide->createRichTextShape();
            $imagePlaceholder->setOffsetX(676)
                         ->setOffsetY(215)
                         ->setHeight(100)
                         ->setWidth(250);              
            // Add outline content:
            $imagePlaceholder->createTextRun('Insert product or brand image here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_WHITE));

            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));


        //Content (first slide):
            //Left hand text:
            $campaignInfoShape = $currentSlide->createRichTextShape();
            $campaignInfoShape->setHeight(133)
                              ->setWidth(500)
                              ->setOffsetX(0)
                              ->setOffsetY(282);
            // Set fill:
            $fill = new Fill();
            $fill->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('43BBEF'));
            $campaignInfoShape->setFill($fill);
            
                //Text:
                $firstP = $campaignInfoShape->getActiveParagraph();
                $firstP->createTextRun($campaign->getName())
                        ->getFont()->setName('KG Second Chances Sketch')
                                   ->setSize(32)
                                   ->setColor(new Color(Color::COLOR_WHITE));

                $secondP = $campaignInfoShape->createParagraph();
                $secondP->createTextRun('Planning Cycle - ' .$campaign->getBrand()->getName())
                        ->getFont()->setName('KG Second Chances Sketch')
                                   ->setSize(18)
                                   ->setColor(new Color(Color::COLOR_WHITE));

                $thirdP = $campaignInfoShape->createParagraph();
                $thirdP->createTextRun($campaign->getCountry()->getName())
                        ->getFont()->setName('KG Second Chances Sketch')
                                   ->setSize(29)
                                   ->setColor(new Color(Color::COLOR_WHITE));


        #
        #2nd slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide()->setSlideLayout('black');

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);

            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Placeholder for portrait image:
            $portraitPlaceholder = $currentSlide->createRichTextShape();
            $portraitPlaceholder->setOffsetX(676)
                         ->setOffsetY(215)
                         ->setHeight(100)
                         ->setWidth(250);              
            // Add outline content:
            $portraitPlaceholder->createTextRun('Insert portrait here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_WHITE));

        //Content:
            // Determine circle:
            $determineCircle = $currentSlide->createDrawingShape();
            $determineCircle->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/circle_determine_tasks.png')
                            ->setOffsetX(19)->setoffsetY(0)
                            ->setHeight(402)->setWidth(520);


        #
        #3rd slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('JTBD Simplified')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));

        //Content:
            //Brief outline:
            $outlineShape = $currentSlide->createRichTextShape();
            $outlineShape->setOffsetX(46)
                         ->setOffsetY(100)
                         ->setHeight(219)
                         ->setWidth(823);              
            // Add outline content:
            $outlineShape->createTextRun('What you really asked us:')
                         ->getFont()->setName('DINbek Bold')
                                    ->setSize(16)
                                    ->setColor(new Color('43BBEF'));
            $outlineShape->createBreak();
            $outlineShape->createTextRun($campaign->getBriefOutline() ? $campaign->getBriefOutline() : 'Brief outline for the campaign here.')
                         ->getFont()->setName('DINbek Bold')
                                    ->setSize(14)
                                    ->setColor(new Color('676363'));
            
                //Circles content:
                // Retrieve info from database:
                $lightDataId = $campaign->getLightdata();
                $lightDataEntity = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->findOneById($lightDataId);

            //Three circles:
            //1st Circle:
            $budgetCircle = $currentSlide->createDrawingShape();
            $budgetCircle->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/jtbd_budget.png')
                            ->setOffsetX(48)->setoffsetY(193)
                            ->setHeight(289)->setWidth(293);
                

                //Budget circle content:
                if (!empty($lightDataEntity)) {

                    $campaignBudget = $lightDataEntity->getSetup()->getBudget() ? (number_format($lightDataEntity->getSetup()->getBudget() / 1000, 0)) . 'm' : 'BUDGET';
                    $campaignBudgetCurrency = $lightDataEntity->getSetup()->getBudgetCurrency() ? $lightDataEntity->getSetup()->getBudgetCurrency() : 'CURRENCY';
                    $campaignStartDate = $lightDataEntity->getSetup()->getStartDate() ? date('M Y', $lightDataEntity->getSetup()->getStartDate()->getTimestamp()) : 'START DATE';

                    //Campaign period:
                    if ($lightDataEntity->getSetup()->getNbperiods()) {

                        $campaignPeriodInDays = $lightDataEntity->getSetup()->getNbperiods() * 7;
                        
                        $endDate = $lightDataEntity->getSetup()->getStartDate();
                        $endDate->modify('+' . $campaignPeriodInDays . ' day');
                    } // End of campaign period If.

                } else {

                    $campaignBudget = 'BUDGET';
                    $campaignBudgetCurrency = 'CURRENCY';
                    $campaignStartDate = 'START DATE';
                    $endDate = FALSE;
                
                } // End of content IF.

                $campaignEndDate = $endDate ? date('M Y', $endDate->getTimestamp()) : 'END DATE';

                // Input content in paragraphs:
                $budgetCircleText = $currentSlide->createRichTextShape();
                $budgetCircleText->setOffsetX(100)
                                 ->setOffsetY(300)
                                 ->setHeight(100)
                                 ->setWidth(230);
                    $firstP = $budgetCircleText->getActiveParagraph();
                    $firstP->createTextRun('  ')
                            ->getFont()->setName('KG Second Chances Solid')
                                       ->setSize(14)
                                       ->setColor(new Color(Color::COLOR_WHITE));
                    $secondP = $budgetCircleText->createParagraph();
                    $secondP->createTextRun($campaignBudget . ' ' .  $campaignBudgetCurrency)
                            ->getFont()->setName('KG Second Chances Solid')
                                       ->setSize(14)
                                       ->setColor(new Color(Color::COLOR_WHITE));
                    $thirdP = $budgetCircleText->createParagraph();
                    $thirdP->createTextRun($campaignStartDate . ' - ' . $campaignEndDate)
                            ->getFont()->setName('KG Second Chances Solid')
                                       ->setSize(14)
                                       ->setColor(new Color(Color::COLOR_WHITE));
            //2nd circle:
            $goalsCircle = $currentSlide->createDrawingShape();
            $goalsCircle->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/jtbd_goals_pressure_test.png')
                            ->setOffsetX(333)->setoffsetY(194)
                            ->setHeight(289)->setWidth(293);

                //Goals circle content:
                $brandShare = $campaign->getMmoBrandshare() ? ($campaign->getMmoBrandshare() * 100) . ' BPS' : 'SHARE';
                $penetration = $campaign->getMmoPenetration() ? ($campaign->getMmoPenetration() * 100) . ' BPS' : 'PENETRATION';

                //Input content in paragraphs:
                $goalsCircleText = $currentSlide->createRichTextShape();
                $goalsCircleText->setOffsetX(370)
                                 ->setOffsetY(300)
                                 ->setHeight(100)
                                 ->setWidth(240);
                    $firstP = $goalsCircleText->getActiveParagraph();
                    $firstP->createTextRun('Brand share: ' . $brandShare)
                            ->getFont()->setName('KG Second Chances Solid')
                                       ->setSize(14)
                                       ->setColor(new Color(Color::COLOR_WHITE));
                    $secondP = $goalsCircleText->createParagraph();
                    $secondP->createTextRun('Penetration: ' . $penetration)
                            ->getFont()->setName('KG Second Chances Solid')
                                       ->setSize(14)
                                       ->setColor(new Color(Color::COLOR_WHITE));
                    $thirdP = $goalsCircleText->createParagraph();
                    $thirdP->createTextRun('% of Target needed: ')
                            ->getFont()->setName('KG Second Chances Solid')
                                       ->setSize(14)
                                       ->setColor(new Color(Color::COLOR_WHITE));
            //3rd circle:                    
            $coverageCircle = $currentSlide->createDrawingShape();
            $coverageCircle->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/jtbd_coverage_pressure_test.png')
                            ->setOffsetX(619)->setoffsetY(193)
                            ->setHeight(289)->setWidth(293);

                //Coverage circle content:

                // //Input content in paragraphs:
                // $coverageCircleText = $currentSlide->createRichTextShape();          
                // $coverageCircleText->setOffsetX(680)
                //               ->setOffsetY(300)
                //               ->setHeight(100)
                //               ->setWidth(200);
                //  $firstP = $coverageCircleText->getActiveParagraph();
                //  $firstP->createTextRun('SOV/SOM: ')
                //          ->getFont()->setName('KG Second Chances Solid')
                //                     ->setSize(14)
                //                     ->setColor(new Color(Color::COLOR_WHITE));
                //  $secondP = $coverageCircleText->createParagraph();
                //  $secondP->createTextRun('Video GRPs: ')
                //          ->getFont()->setName('KG Second Chances Solid')
                //                     ->setSize(14)
                //                     ->setColor(new Color(Color::COLOR_WHITE));
                //  $thirdP = $coverageCircleText->createParagraph();
                //  $thirdP->createTextRun('Weeks: ')
                //          ->getFont()->setName('KG Second Chances Solid')
                //                     ->setSize(14)
                //                     ->setColor(new Color(Color::COLOR_WHITE));       


        #
        #4th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Market summary')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));

            // Placeholder 1st text:
            $firstTextPlaceholder = $currentSlide->createRichTextShape();
            $firstTextPlaceholder->setOffsetX(150)
                         ->setOffsetY(200)
                         ->setHeight(400)
                         ->setWidth(300);              
            // Add outline content:
            $firstTextPlaceholder->createTextRun('Insert text here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_BLACK));


            // Placeholder 2nd text:
            $secondTextPlaceholder = $currentSlide->createRichTextShape();
            $secondTextPlaceholder->setOffsetX(500)
                         ->setOffsetY(200)
                         ->setHeight(400)
                         ->setWidth(300);              
            // Add outline content:
            $secondTextPlaceholder->createTextRun('Insert text here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_BLACK));

            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));


        #
        #5th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);    
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Strategic Clarity')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));       
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));


        #
        #6th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();
        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('We need communications to deliver')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));


            // Placeholder for copy:
            $copyPlaceholder = $currentSlide->createRichTextShape();
            $copyPlaceholder->setOffsetX(100)
                         ->setOffsetY(150)
                         ->setHeight(400)
                         ->setWidth(300);              
            // Add copy default text:
            $copyPlaceholder->createTextRun('Copy goes here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_BLACK));


            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));
        
        //Content:
            //Communication graph:

            //Chart database data:
            $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findOneById(TaskType::TYPE_COMM_TASKS);
            $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                                                                          ->where('t.campaign = :campaign AND t.taskname = :taskName')
                                                                          ->setParameter('campaign', $campaign)
                                                                          ->setParameter('taskName', $taskName)
                                                                          ->getQuery();
            $task = $taskQuery->getOneOrNullResult();

            $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(11);

            $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                                                                               ->where('f.task = :task AND f.fileType = :fileType')
                                                                               ->orderBy('f.updatedAt', 'DESC')
                                                                               ->setMaxResults(1)
                                                                               ->setParameter('task', $task)
                                                                               ->setParameter('fileType', $fileType)
                                                                               ->getQuery();
            $file = $fileQuery->getOneOrNullResult();
            
            // Check if file to be inserted exists:
            if (!empty($file)) {

                $commGraph = $currentSlide->createDrawingShape();
                // $commGraph->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/graph_communications.png')
                $commGraph->setPath($file->getPath())
                          ->setOffsetX(389)->setOffsetY(115)
                          ->setHeight(221)->setWidth(521);

            } // End of "Notice and talk about graph" IF.


        #
        #7th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);
            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Placeholder for portrait image:
            $portraitPlaceholder = $currentSlide->createRichTextShape();
            $portraitPlaceholder->setOffsetX(676)
                         ->setOffsetY(215)
                         ->setHeight(100)
                         ->setWidth(250);              
            // Add outline content:
            $portraitPlaceholder->createTextRun('Insert portrait here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_WHITE));

        //Content:
            // Determine circle:
            $determineCircle = $currentSlide->createDrawingShape();
            $determineCircle->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/circle_design_tasks.png')
                            ->setOffsetX(19)->setoffsetY(5)
                            ->setHeight(402)->setWidth(535);


        #
        #8th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);
            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Real Lives: Their values and lifestyle')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(24)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));

        //Content

            //Real Lives image 01:
            //Image database data:
            $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findOneById(TaskType::TYPE_REAL_LIVES);
            $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                                                                          ->where('t.campaign = :campaign AND t.taskname = :taskName')
                                                                          ->setParameter('campaign', $campaign)
                                                                          ->setParameter('taskName', $taskName)
                                                                          ->getQuery();
            $task = $taskQuery->getOneOrNullResult();

            $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(18);

            $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                                                                               ->where('f.task = :task AND f.fileType = :fileType')
                                                                               ->orderBy('f.updatedAt', 'DESC')
                                                                               ->setMaxResults(1)
                                                                               ->setParameter('task', $task)
                                                                               ->setParameter('fileType', $fileType)
                                                                               ->getQuery();
            $file = $fileQuery->getOneOrNullResult();
            
            // Check if file to be inserted exists:
            if (!empty($file)) {

                $realLivesImage = $currentSlide->createDrawingShape();
                // $commGraph->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/graph_communications.png')
                $realLivesImage->setPath($file->getPath())
                          ->setOffsetX(50)->setOffsetY(115)
                          ->setHeight(221)->setWidth(521);

            } // End of "Real Lives image 01" IF.


        #
        #9th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);
            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Real Lives: The brand in their lives')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(24)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));

        //Content

            //Real Lives image 02:
            //Image database data:
            $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findOneById(TaskType::TYPE_REAL_LIVES);
            $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                                                                          ->where('t.campaign = :campaign AND t.taskname = :taskName')
                                                                          ->setParameter('campaign', $campaign)
                                                                          ->setParameter('taskName', $taskName)
                                                                          ->getQuery();
            $task = $taskQuery->getOneOrNullResult();

            $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(19);

            $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                                                                               ->where('f.task = :task AND f.fileType = :fileType')
                                                                               ->orderBy('f.updatedAt', 'DESC')
                                                                               ->setMaxResults(1)
                                                                               ->setParameter('task', $task)
                                                                               ->setParameter('fileType', $fileType)
                                                                               ->getQuery();
            $file = $fileQuery->getOneOrNullResult();
            
            // Check if file to be inserted exists:
            if (!empty($file)) {

                $realLivesImage = $currentSlide->createDrawingShape();
                // $commGraph->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/graph_communications.png')
                $realLivesImage->setPath($file->getPath())
                          ->setOffsetX(50)->setOffsetY(115)
                          ->setHeight(221)->setWidth(521);

            } // End of "Real Lives image 01" IF.


        #
        #10th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);
            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(0)
                       ->setOffsetY(70)
                       ->setHeight(35)
                       ->setWidth(958);
            $titleShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);        
            // Add title content:
            $titleShape->createTextRun('The idea')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color(Color::COLOR_WHITE));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));

        //Content:
            //Content values:
            $ideaTitle = $campaign->getCampaignideatitle() ? $campaign->getCampaignideatitle() : 'Idea title';
            $ideaDescription = $campaign->getCampaignidea() ? $campaign->getCampaignidea() : 'Idea description';

            //Idea title coordinates
            $ideaTitleShape = $currentSlide->createRichTextShape();
            $ideaTitleShape->setOffsetX(0)
                         ->setOffsetY(140)
                         ->setHeight(35)
                         ->setWidth(958);
            $ideaTitleShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);        
            //Idea title content:
            $ideaTitleShape->createTextRun($ideaTitle)
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color('43BBEF'));

            //Idea description coordinates:
            $ideaDescriptionShape = $currentSlide->createRichTextShape();
            $ideaDescriptionShape->setOffsetX(55)
                                 ->setOffsetY(200)
                                 ->setHeight(219)
                                 ->setWidth(850);              
            //Idea description content:
            $ideaDescriptionShape->createTextRun($ideaDescription)
                                 ->getFont()->setName('Arial')
                                 ->setSize(16)
                                 ->setColor(new Color(Color::COLOR_WHITE));


        #
        #11th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('The fundamental channels')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));


            // Text placeholder:
            $textPlaceholder = $currentSlide->createRichTextShape();
            $textPlaceholder->setOffsetX(600)
                         ->setOffsetY(200)
                         ->setHeight(400)
                         ->setWidth(300);              
            // Text placeholder default content:
            $textPlaceholder->createTextRun('Insert text here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_BLACK));            


            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));
        //Content

            //Channels graph (Touchpoint shortlist):

            //Chart database data:
            $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findOneById(TaskType::TYPE_TOUCHPOINT_SHORT);
            $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                                                                          ->where('t.campaign = :campaign AND t.taskname = :taskName')
                                                                          ->setParameter('campaign', $campaign)
                                                                          ->setParameter('taskName', $taskName)
                                                                          ->getQuery();
            $task = $taskQuery->getSingleResult();

            $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(11);

            $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                                                                               ->where('f.task = :task AND f.fileType = :fileType')
                                                                               ->orderBy('f.updatedAt', 'DESC')
                                                                               ->setMaxResults(1)
                                                                               ->setParameter('task', $task)
                                                                               ->setParameter('fileType', $fileType)
                                                                               ->getQuery();
            $file = $fileQuery->getOneOrNullResult();
            
            // Check if file to be inserted exists:
            if (!empty($file)) {

                $channelsGraph = $currentSlide->createDrawingShape();
                // $channelsGraph->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/graph_fundamental_channels.png')
                $channelsGraph->setPath($file->getPath())
                              ->setOffsetX(50)->setoffsetY(120)
                              ->setHeight(347)->setWidth(544);

            } // End of "Touchpoint shortlist graph" IF.


        #
        #12th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Budget allocation')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));
        //Content

            //Optimal Budget Allocation graph:
            //Chart database data:
            $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findOneById(TaskType::TYPE_BUDGET_ALLOC);
            $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                                                                          ->where('t.campaign = :campaign AND t.taskname = :taskName')
                                                                          ->setParameter('campaign', $campaign)
                                                                          ->setParameter('taskName', $taskName)
                                                                          ->getQuery();
            $task = $taskQuery->getSingleResult();

            $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(2);

            $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                                                                               ->where('f.task = :task AND f.fileType = :fileType')
                                                                               ->orderBy('f.updatedAt', 'DESC')
                                                                               ->setMaxResults(1)
                                                                               ->setParameter('task', $task)
                                                                               ->setParameter('fileType', $fileType)
                                                                               ->getQuery();
            $file = $fileQuery->getOneOrNullResult();
            
            // Check if file to be inserted exists:
            if (!empty($file)) {

                $optimalBudgetGraph = $currentSlide->createDrawingShape();
                // $optimalBudgetGraph->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/graph_optimal_budget_allocation.png')
                $optimalBudgetGraph->setPath($file->getPath())
                              ->setOffsetX(250)->setoffsetY(120)
                              ->setHeight(347)->setWidth(544);

            } // End of "Optimal budget allocation graph" IF.


            //Video neutral image:
            //Chart database data:
            $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findOneById(TaskType::TYPE_BUDGET_ALLOC);
            $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                                                                          ->where('t.campaign = :campaign AND t.taskname = :taskName')
                                                                          ->setParameter('campaign', $campaign)
                                                                          ->setParameter('taskName', $taskName)
                                                                          ->getQuery();
            $task = $taskQuery->getSingleResult();

            $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(17);

            $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                                                                               ->where('f.task = :task AND f.fileType = :fileType')
                                                                               ->orderBy('f.updatedAt', 'DESC')
                                                                               ->setMaxResults(1)
                                                                               ->setParameter('task', $task)
                                                                               ->setParameter('fileType', $fileType)
                                                                               ->getQuery();
            $file = $fileQuery->getOneOrNullResult();
            
            // Check if file to be inserted exists:
            if (!empty($file)) {

                $optimalBudgetGraph = $currentSlide->createDrawingShape();
                // $optimalBudgetGraph->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/graph_optimal_budget_allocation.png')
                $optimalBudgetGraph->setPath($file->getPath())
                              ->setOffsetX(50)->setoffsetY(120)
                              ->setHeight(347)->setWidth(544);

            } // End of "Video neutral image" IF.     


        #
        #13th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);
            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('The Brand Experience')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));


        #
        #14th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Scorecard slide - Please insert custom title')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(20)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));


        #
        #15th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);  
            // Set background color:
            $this->setSlideBackgroundColor($currentSlide);

            // Placeholder for portrait image:
            $portraitPlaceholder = $currentSlide->createRichTextShape();
            $portraitPlaceholder->setOffsetX(676)
                         ->setOffsetY(215)
                         ->setHeight(100)
                         ->setWidth(250);              
            // Add outline content:
            $portraitPlaceholder->createTextRun('Insert portrait here')
                         ->getFont()->setName('Arial')
                                    ->setSize(18)
                                    ->setColor(new Color(Color::COLOR_WHITE));

        //Content:
            // Determine circle:
            $determineCircle = $currentSlide->createDrawingShape();
            $determineCircle->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/circle_deliver_tasks.png')
                            ->setOffsetX(19)->setoffsetY(0)
                            ->setHeight(402)->setWidth(520);


        #
        #16th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Optimized laydown of touchpoints')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));
        
        //Content

            //Optimal Budget graph:
            //Chart database data:
            $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->findOneById(TaskType::TYPE_PHASING);
            $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                                                                          ->where('t.campaign = :campaign AND t.taskname = :taskName')
                                                                          ->setParameter('campaign', $campaign)
                                                                          ->setParameter('taskName', $taskName)
                                                                          ->getQuery();
            $task = $taskQuery->getSingleResult();

            $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(11);

            $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                                                                               ->where('f.task = :task AND f.fileType = :fileType')
                                                                               ->orderBy('f.updatedAt', 'DESC')
                                                                               ->setMaxResults(1)
                                                                               ->setParameter('task', $task)
                                                                               ->setParameter('fileType', $fileType)
                                                                               ->getQuery();
            $file = $fileQuery->getOneOrNullResult();
            
            // Check if file to be inserted exists:
            if (!empty($file)) {

                $optimalBudgetGraph = $currentSlide->createDrawingShape();
                // $optimalBudgetGraph->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/graph_visualization.png')
                $optimalBudgetGraph->setPath($file->getPath())
                          ->setOffsetX(50)->setoffsetY(120)
                          ->setHeight(347)->setWidth(544);

            } // End of "Video neutral image" IF.
                          

        #
        #17th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);           
            // Add title coordinates:
            $titleShape = $currentSlide->createRichTextShape();
            $titleShape->setOffsetX(50)
                       ->setOffsetY(50)
                       ->setHeight(94)
                       ->setWidth(880);
                       
            // Add title content:
            $titleShape->createTextRun('Detailed In-market plan')
                       ->getFont()->setName('KG Second Chances Sketch')
                                  ->setSize(30)
                                  ->setColor(new Color('43BBEF'));
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));


        #
        #18th slide:
        #
        $currentSlide = $finalOutcomeBuilder->createSlide();

        //Layout:
            $currentSlide->setSlideLayout(Layout::BLANK);
            // Add logos:
            // Initiative logo:
            $initiativeLogo = $currentSlide->createDrawingShape();
            $initiativeLogo->setPath($this->get('kernel')->getRootDir() . self::INITIATIVE_LOGO_BLUE)
                           ->setOffsetX(812)->setoffsetY(487)
                           ->setHeight(21)->setWidth(103);
            // Separating line:
            $separateLine = $currentSlide->createLineShape(786, 476, 786, 524);
            $separateLine->getBorder()->setColor(new Color('43BBEF'));



        // Generate the document:
        $finalOutcomePPTXName = 'final_outcome_' . $campaign->getId() . '.pptx';
        $finalOutcomePath = $this->get('kernel')->getRootDir() . '/../web/uploads/presentations/' . $finalOutcomePPTXName;

        $writerPPTX = \PhpOffice\PhpPowerpoint\IOFactory::createWriter($finalOutcomeBuilder, 'PowerPoint2007');
        $writerPPTX->save($finalOutcomePath);

        if (file_exists($finalOutcomePath)) { // File was created.

            // Send document back to client:
            $response = new Response(file_get_contents($finalOutcomePath));

            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $finalOutcomePPTXName);
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Length', filesize($finalOutcomePath));

            unlink($finalOutcomePath); // Delete generated PPTX from server directory.
            
            return $response;

        } else { // File was not created.

            // Send error message back to client:
            $response = new Response();
            $response->setStatusCode(500)
                     ->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array(
                'success' => FALSE,
                'message' => 'Request failed. Final outcome PPTX could not be generated.'
                )
            ));

            return $response;

        } // End of send file IF.

    } // End of final output PPTX.



    // Set slide background to black:
    public function setSlideBackgroundColor($currentSlide) {

      // Set background color:
      $backgroundColorShape = $currentSlide->createDrawingShape();
      $backgroundColorShape->setPath($this->get('kernel')->getRootDir() . self::GALLERY_DIRECTORY . '/slide_background_black.png')
                      ->setOffsetX(0)->setoffsetY(0)
                      ->setHeight(563)->setWidth(965);

    }

}
