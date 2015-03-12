<?php

//namespace MissionControl\Bundle\UserBundle\DataFixtures\ORM;
//
//use Doctrine\Common\DataFixtures\FixtureInterface;
//use Doctrine\Common\Persistence\ObjectManager;
//use MissionControl\Bundle\UserBundle\Entity\User;
//
//class LoadUserData implements FixtureInterface {
//
//    public function timezoneUTC() {
//        return new \DateTimeZone('UTC');
//    }
//
//    /**
//     * {@inheritDoc}
//     */
//    public function load(ObjectManager $manager) {
//
////        $creationDate = new \DateTime();
////        $creationDate->setTimezone(self::timezoneUTC());
////
////
////
////        $userAdmin = new User();
////        $userAdmin->setCreatedAt($creationDate);
////        $userAdmin->setUpdatedAt($creationDate);
////        $userAdmin->setUsername('asd123');
////        $userAdmin->setEmail('asd123@asd.com');
////        $userAdmin->setUsernameCanonical('asd123');
////        $userAdmin->setEmailCanonical('asd123@asd.com');
////        $userAdmin->setEnabled(0);
////        $userAdmin->setPassword('yourpasswordisincorrect');
////        $userAdmin->setLocked(0);
////        $userAdmin->setExpired(0);
////        $userAdmin->setConfirmationToken(NULL);
////        $userAdmin->setCredentialsExpired(0);
////        $userAdmin->setApiKey('920dea58-f4f6-4b25-9957-467d721b569d');
////        $userAdmin->setFirstname('John');
////        $userAdmin->setLastname('JohnLN');
////        $userAdmin->setPhone('01234567890');
////        $userAdmin->setOffice('FederalBureau');
////        $userAdmin->setTitle('Investigator');
////        //$userAdmin->setLastLogin(NULL);
////        //$userAdmin->setExpiresAt(NULL);
////        //$userAdmin->setPasswordRequestedAt(NULL);
////        //$userAdmin->setCredentialsExpireAt(NULL);
////        for ($i = 1; $i < 50; $i++) {
////            $dummyUser = new User();
////            $dummyUser->setCreatedAt($creationDate);
////            $dummyUser->setUpdatedAt($creationDate);
////            $dummyUser->setUsername('dummyuser' . $i);
////            $dummyUser->setEmail('dummyuser' . $i . '@asd.com');
////            $dummyUser->setUsernameCanonical('dummyuser' . $i);
////            $dummyUser->setEmailCanonical('dummyuser' . $i . '@asd.com');
////            $dummyUser->setEnabled(0);
////            $dummyUser->setPassword('randompassword');
////            $dummyUser->setLocked(0);
////            $dummyUser->setExpired(0);
////            $dummyUser->setConfirmationToken(NULL);
////            $dummyUser->setCredentialsExpired(0);
////            $dummyUser->setApiKey($i);
////            $dummyUser->setFirstname('firstname' . $i);
////            $dummyUser->setLastname('lastname' . $i);
////            $dummyUser->setPhone('01234567890');
////            $dummyUser->setOffice('FederalBureau' . $i);
////            $dummyUser->setTitle('Investigator' . $i);
////            $manager->persist($dummyUser);
////        }
////        $manager->persist($userAdmin);
////        $manager->flush();
////    }
//
//}
