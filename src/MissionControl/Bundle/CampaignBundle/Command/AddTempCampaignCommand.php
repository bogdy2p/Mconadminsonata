<?php

namespace MissionControl\Bundle\CampaignBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\CampaignBundle\Entity\Teammember;
use MissionControl\Bundle\TaskBundle\Entity\Task;
use MissionControl\Bundle\UserBundle\Entity\User;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class AddTempCampaignCommand extends ContainerAwareCommand {

    public function configure() {
        $this->setName('tempcampaign')
                ->setDescription('This commands description ')
                ->addOption('add', null, InputOption::VALUE_NONE, 'If this is set , the call will add temp campaign to the db.')
                ->addOption('remove', null, InputOption::VALUE_NONE, 'If this is set , the call will REMOVE the temp campaign from the db.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $manager = $this->getContainer()->get('doctrine')->getManager();
        $creationDate = new \DateTime();
        $creationDate->setTimezone(new \DateTimeZone('UTC'));

        if ($input->getOption('add')) {
            
        } elseif ($input->getOption('remove')) {
            
        } else {
            $output->writeln(' ');
            $output->writeln(' ');
            $output->writeln('You can use --add / --remove options to change what the command should do.The command is for tempcampaign generation purposes only.');
            $output->writeln(' ');
            $output->writeln(' ');
        }
    }

}
