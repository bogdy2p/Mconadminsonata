<?php

namespace MissionControl\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MissionControl\Bundle\UserBundle\Entity\Role;

class LoadRolesData implements FixtureInterface {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {

        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());
        ///Add Role Viewer :
        $role_viewer = new Role();
        $role_viewer->setId(1);
        $role_viewer->setName('ROLE_VIEWER');
        $role_viewer->setSystemname('Viewer');
        $role_contributor = new Role();
        $role_contributor->setId(2);
        $role_contributor->setName('ROLE_CONTRIBUTOR');
        $role_contributor->setSystemname('Contributor');
        $role_administrator = new Role();
        $role_administrator->setId(3);
        $role_administrator->setName('ROLE_ADMINISTRATOR');
        $role_administrator->setSystemname('Administrator');

        $manager->persist($role_viewer);
        $manager->persist($role_contributor);
        $manager->persist($role_administrator);

        $manager->flush();
        
        echo 'Viewer , Contributor , Administrator roles loaded .', "\n";
        
    }

}
