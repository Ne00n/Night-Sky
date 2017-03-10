<?php

use PHPUnit\Framework\TestCase;

class History_Tests extends TestCase {

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
    $this->History = new History($this->DB,$this->Verify);
  }

  public function launch() {
    //Switching to Account 2
    $this->switchtoID(2);
    //Add some History to Account 2 and Account 1
    $this->History->addHistory(2,1,0);
    $this->History->addHistory(1,2,1);
    //Testing Correct History ID
    $this->assertEquals($this->History->checkHistoryID(1),true);
    //Testing Incorrect History ID from a different Account
    $this->assertEquals($this->History->checkHistoryID(2),false);
  }
}

?>
