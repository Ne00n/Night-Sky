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
    
  }
}

?>
