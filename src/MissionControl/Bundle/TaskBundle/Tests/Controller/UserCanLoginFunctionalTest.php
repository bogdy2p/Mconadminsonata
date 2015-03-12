<?php

namespace MissionControl\Bundle\TaskBundle\Tests\Controller;

use PHPUnit_Extensions_SeleniumTestCase;

class UserCanLoginFunctionalTest extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://dashboard2.itstrategists.com/");
  }

  public function testMyTestCase()
  {
    $this->deleteAllVisibleCookies();
    $this->open("/login");
    $this->type("id=InputEmail", "qa_user");
    $this->type("id=InputPassword", "password");
    $this->click("//button[@type='submit']");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Unilever Dash", $this->getTitle());
    $this->click("css=div.evo_action_ico.loader2 > #menu-trigger > div.evo-ico > svg");
    $this->assertEquals("Dashboard", $this->getText("xpath=(//a[@id='menu-trigger']/h2)[2]"));
    
  }
}
?>

