<?php

namespace MissionControl\Bundle\TaskBundle\Tests\Controller;

use PHPUnit_Extensions_SeleniumTestCase;

class UserAbleToDownloadFileTest extends PHPUnit_Extensions_SeleniumTestCase {

    protected function setUp() {
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://dashboard2.itstrategists.com/");
    }

    public function testUserAbleToClickDownloadFileLinkCase() {
        $this->deleteAllVisibleCookies();
        $this->open("/login");
        $this->type("id=InputEmail", "qa_user");
        $this->type("id=InputPassword", "password");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad("30000");
        $this->assertEquals("Unilever Dash", $this->getTitle());
        $this->click("css=div.evo_action_ico.loader2 > #menu-trigger > div.evo-ico > svg");
        $this->assertEquals("Dashboard", $this->getText("xpath=(//a[@id='menu-trigger']/h2)[2]"));
        $this->assertTrue($this->isElementPresent("css=div.evo_action_ico.loader > a.btn_action > div.evo-ico > svg > #W6Ba75_1_ > g > path"));
        $this->click("css=div.evo_action_ico.loader > a.btn_action > div.evo-ico > svg > #W6Ba75_1_ > g > path");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isElementPresent("link=f29a70c2-2ea1-4dbc-bbf8-c4787e48092f_flow.xlsx"));
        $this->click("link=f29a70c2-2ea1-4dbc-bbf8-c4787e48092f_flow.xlsx");
        sleep(2);
    }

}

?>