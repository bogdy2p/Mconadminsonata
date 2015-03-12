<?php

namespace MissionControl\Bundle\CampaignBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\TaskBundle\Entity\Task;
use MissionControl\Bundle\UserBundle\Entity\User;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class AddDefaultJtbdToAxeCampaignCommand extends ContainerAwareCommand {

    public function configure() {
        $this->setName('testaxejtbd')
                ->setDescription('This commands description ')
                ->addOption('add', null, InputOption::VALUE_NONE, 'If this is set , the call will add default data for JTBD task  on the AXE test campaign.')
                ->addOption('remove', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE all the  data for JTBD task  on the AXE test campaign.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $manager = $this->getContainer()->get('doctrine')->getManager();

        if ($input->getOption('add')) {

            $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneById('f29a70c2-2ea1-4dbc-bbf8-c4787e48092f');

            $brief_outline = 'Thebriefoutlinetextfortheaxecampaign';
            $mmo_brandshare = 0.01;
            $mmo_penetration = 0.02;
            $mmo_salesgrowth = 0.03;
            $mmo_othermetric = 0.04;
            $mco_brandhealthbhc = 0.05;
            $mco_awarenessincrease = 0.06;
            $mco_brandhealthperformance = 0.07;

            $campaign->setBriefOutline($brief_outline);
            $campaign->setMmoBrandshare($mmo_brandshare);
            $campaign->setMmoPenetration($mmo_penetration);
            $campaign->setMmoSalesgrowth($mmo_salesgrowth);
            $campaign->setMmoOthermetric($mmo_othermetric);
            $campaign->setMcoBrandhealthBhc($mco_brandhealthbhc);
            $campaign->setMcoAwarenessincrease($mco_awarenessincrease);
            $campaign->setMcoBrandhealthPerformance($mco_brandhealthperformance);


            $manager->flush();
            $output->writeln(' ');
            $output->writeln('Added JTBD data to Axe Campaign.');
            $output->writeln(' ');
            //$manager->flush();
        } elseif ($input->getOption('remove')) {

            $campaign = $manager->getRepository('CampaignBundle:Campaign')->findOneById('f29a70c2-2ea1-4dbc-bbf8-c4787e48092f');

            $brief_outline = null;
            $mmo_brandshare = null;
            $mmo_penetration = null;
            $mmo_salesgrowth = null;
            $mmo_othermetric = null;
            $mco_brandhealthbhc = null;
            $mco_awarenessincrease = null;
            $mco_brandhealthperformance = null;

            $campaign->setBriefOutline($brief_outline);
            $campaign->setMmoBrandshare($mmo_brandshare);
            $campaign->setMmoPenetration($mmo_penetration);
            $campaign->setMmoSalesgrowth($mmo_salesgrowth);
            $campaign->setMmoOthermetric($mmo_othermetric);
            $campaign->setMcoBrandhealthBhc($mco_brandhealthbhc);
            $campaign->setMcoAwarenessincrease($mco_awarenessincrease);
            $campaign->setMcoBrandhealthPerformance($mco_brandhealthperformance);


            $manager->flush();
            $output->writeln(' ');
            $output->writeln('Removed Axe Campaign JTBD data .');
            $output->writeln(' ');
        } else {
            $output->writeln(' ');
            $output->writeln(' ');
            $output->writeln('You can use --add / --remove options to add or remove default data for the Axe Test Campaign');
            $output->writeln(' ');
            $output->writeln(' ');
        }
    }

}
