<?php

namespace MissionControl\Bundle\CampaignBundle;

use MissionControl\Bundle\CampaignBundle\DependencyInjection\Security\Factory\WsseFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use \Symfony\Component\DependencyInjection\ContainerBuilder;

class CampaignBundle extends Bundle
{
	public function build(ContainerBuilder $container){
		parent::build($container);

		$extension = $container->getExtension('security');
		$extension->addSecurityListenerFactory(new WsseFactory());
	}
}
