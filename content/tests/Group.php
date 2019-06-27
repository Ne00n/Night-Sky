<?php

use PHPUnit\Framework\TestCase;

class Group_Tests extends TestCase {

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
    //Switching to Account 1
    $this->switchtoID(1);

    //Set ID fo edit of Group
    $this->Group->setID(1);
    $this->assertEquals($this->Group->getLastError(),NULL);

    //Edit Group name
    $this->Group->editGroup('myCluster');
    $this->assertEquals($this->Group->getLastError(),NULL);

    //Set ID of Group that this account does not own
    $this->Group->setID(2);
    $this->assertEquals($this->Group->getLastError(),'Invalid ID');

    //Set ID of Group that does not exist
    $this->Group->setID(55);
    $this->assertEquals($this->Group->getLastError(),'Invalid ID');

  }
}

?>
