<?php

namespace MissionControl\Bundle\TaskBundle\Tests\Controller;

use PHPUnit_Extensions_SeleniumTestCase;

class CheckAtLeastOneCampaignLoadedTest extends PHPUnit_Extensions_SeleniumTestCase {

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
        $this->click("css=div.evo-ico.evo-ico-flip-y > svg");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isElementPresent("//table[@id='project-table']/tbody/tr/td[2]"));
    }

}

?>