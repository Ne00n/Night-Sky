<?php

use PHPUnit\Framework\TestCase;

class User_Tests extends TestCase {

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
    $this->switchtoID(1);
    //Add Account 1 and Generate some Random Password
    $password = Page::randomPassword();
    echo "Password used: ".$password;
    $activation_hash = $this->User->registerUser("Tester","test@test.com",$password,$password,"LET",true); //Which as obviously the ID 1
    $this->assertEquals($this->User->getLastError(),NULL);
    #Validate our Hash that the Object gave us
    $this->assertEquals($this->Verify->checkHash($activation_hash),true);
    //Try a wrong Hash which should be incorrect, before we enable the Hash
    $this->assertEquals($this->Verify->checkHash($activation_hash.'a'),false);
    //Enable the Account
    $this->User->enableUser($activation_hash);
    $this->assertEquals($this->User->getLastError(),NULL);
    //Try a wrong Hash which should be incorrect, after we enable the Hash
    $this->assertEquals($this->Verify->checkHash($activation_hash.'a'),false);

    //Add Account 2
    $activation_hash = $this->User->registerUser("Tester2","test3@test.com",$password,$password,"LET",true); //Which has obviously the ID 2
    $this->assertEquals($this->User->getLastError(),NULL);
    #Validate our Hash that the Object gave us
    $this->assertEquals($this->Verify->checkHash($activation_hash),true);
    //Try a wrong Hash which should be incorrect, before we enable the Hash
    $this->assertEquals($this->Verify->checkHash($activation_hash.'a'),false);
    //Enable Account 2
    $this->User->enableUser($activation_hash);
    $this->assertEquals($this->User->getLastError(),NULL);
    //Try a wrong Hash which should be incorrect, after we enable the Hash
    $this->assertEquals($this->Verify->checkHash($activation_hash.'a'),false);

    //Check if the Login works fine with Account 1
    $this->Verify->ValidateLogin("Tester",$password);
    $this->assertEquals($this->Verify->getLastError(),NULL);
    //Check if the Login works fine with Account 2
    $this->Verify->ValidateLogin("Tester2",$password);
    $this->assertEquals($this->Verify->getLastError(),NULL);
    //Check a incorrect password on Account 1
    $this->Verify->ValidateLogin("Tester",$password.'a');
    $this->assertEquals($this->Verify->getLastError(),"Incorrect Login details");
  }
}

?>
