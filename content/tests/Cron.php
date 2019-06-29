<?php

use PHPUnit\Framework\TestCase;

class Cronjob_Tests extends TestCase {

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
    //Check if the Cronjob night runs through and not crashes
    $Night = new Night();
    $this->assertEquals($Night->run(true),true);
    //Switching to Account 2
    $this->switchtoID(2);
    $this->Main->setID(1);
    $this->assertEquals($this->Main->getLastError(),NULL);
    //Check if our checks we added before have been ran by the cronjob
    $this->Main->getData();
    $this->assertEquals($this->Main->getLastError(),NULL);
    $lastrun = $this->Main->getLastrun();
    $this->assertTrue((time() - $lastrun <= 10));

  }
}

?>
