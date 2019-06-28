<?php

use PHPUnit\Framework\TestCase;

class Status_Tests extends TestCase {

  private function switchtoID($id) {
    //Switches all to a different UserID
    $this->DB = new Database;
    $this->DB->InitDB();
    $this->Verify = new Verify($this->DB,true,$id);
    $this->Main = new Main($this->DB,$this->Verify);
    $this->Contact = new Contact($this->DB,$this->Verify);
    $this->User = new User($this->DB);
    $this->Group = new Group($this->DB,$this->Verify);
    $this->StatusPage = new StatusPage($this->DB,$this->Verify);
  }

  public function launch() {
    //Switching to Account 2
    $this->switchtoID(2);

    //Create a Status Page with Account 2
    $this->StatusPage->addPage("Test",2);
    $this->assertEquals($this->StatusPage->getLastError(),NULL);

    //Switching to Account 1
    $this->switchtoID(1);

    //Create a Status Page with Account 1
    $this->StatusPage->addPage("Test",1);
    $this->assertEquals($this->StatusPage->getLastError(),NULL);

    //Try not existing ID
    $this->StatusPage->setID(1);
    $this->assertEquals($this->StatusPage->getLastError(),"Invalid ID");
    $this->StatusPage->resetError();

    //Try to access Status Page 2
    $this->StatusPage->setID(2);
    $this->assertEquals($this->StatusPage->getLastError(),NULL);

    //Try to access Status Page 1, should go wrong
    $this->StatusPage->setID(1);
    $this->assertEquals($this->StatusPage->getLastError(),"Invalid ID");
    $this->StatusPage->resetError();

    //Try to access Status Page, negative ID
    $this->StatusPage->setID(-111);
    $this->assertEquals($this->StatusPage->getLastError(),"Invalid ID");
    $this->StatusPage->resetError();

    //Try to access Status Page with a random string instead of an ID
    $this->StatusPage->setID(bin2hex(random_bytes(20)));
    $this->assertEquals($this->StatusPage->getLastError(),"Invalid ID");
    $this->StatusPage->resetError();

    //Rename Status Page
    $this->StatusPage->editPage("My new status page",1);
    $this->assertEquals($this->StatusPage->getLastError(),NULL);

    //Rename Status Page from other group and user
    $this->StatusPage->editPage("My new status page",2);
    $this->assertEquals($this->StatusPage->getLastError(),'Invalid Group');
    $this->StatusPage->resetError();

    //Delete Status Page
    $this->StatusPage->removePage();
    $this->assertEquals($this->StatusPage->getLastError(),NULL);

    //Switching to Account 2
    $this->switchtoID(2);

    //Try to access Status Page 1
    $this->StatusPage->setID(1);
    $this->assertEquals($this->StatusPage->getLastError(),NULL);

    //Delete Status Page
    $this->StatusPage->removePage();
    $this->assertEquals($this->StatusPage->getLastError(),NULL);


  }
}

?>
