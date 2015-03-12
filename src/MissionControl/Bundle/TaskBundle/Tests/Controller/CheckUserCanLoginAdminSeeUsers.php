<?php

namespace MissionControl\Bundle\TaskBundle\Tests\Controller;

use PHPUnit_Extensions_SeleniumTestCase;

class Example extends PHPUnit_Extensions_SeleniumTestCase {

    protected function setUp() {
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://dashboard2.itstrategists.com/");
    }

    public function testMyTestCase() {
        $this->deleteAllVisibleCookies();
        $this->open("/login");
        $this->type("id=InputEmail", "qa_user");
        $this->type("id=InputPassword", "password");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isElementPresent("//div[14]/a/div"));
        $this->click("css=div.evo_action_ico.loader2 > a > div.evo-ico > svg > #qrqpyY_1_ > g > path");
        $this->open("/admin");
        try {
            $this->assertEquals("Admin Panel", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        $this->type("name=username", "qa_user");
        $this->type("name=password", "password");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->click("link=Users");
        $this->waitForPageToLoad("30000");
        $this->verifyText("css=legend", "User List New User");
    }

}

?>