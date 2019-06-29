<?php

use PHPUnit\Framework\TestCase;

class Contact_Tests extends TestCase {

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
    //Add a Group to Account 1
    $this->Group->addGroup("Test");
    $this->assertEquals($this->Group->getLastError(),NULL);
    #Add a Contact to Account 1 with this Group and enable the Account fully
    $activation_hash = $this->Contact->addContact("test2@test.com",$groups=array(1),true);
    $this->assertEquals($this->Contact->getLastError(),NULL);
    //Validate our Hash that the Object gave us
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash),true);
    //Try a wrong Hash which should be incorrect
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash.'a'),false);
    //Enable the Contact
    $this->assertEquals($this->Contact->enableContact($activation_hash),NULL);

    //Switching to Account 2
    $this->switchtoID(2);
    #Add a Group to Account 2
    $this->Group->addGroup("Test");
    $this->assertEquals($this->Group->getLastError(),NULL); //Check for Errors
    #Add a Contact to Account 2 with this Group and Enable the Contact fully
    $activation_hash = $this->Contact->addContact("test4@test.com",$groups=array(2),true);
    $this->assertEquals($this->Contact->getLastError(),NULL);
    //Validate our Hash that the Object gave us
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash),true);
    //Try a wrong Hash which should be incorrect
    $this->assertEquals($this->Verify->checkEmailHash($activation_hash.'a'),false);
    //Enable the Contact
    $this->assertEquals($this->Contact->enableContact($activation_hash),NULL);

    //Group correct setID
    $this->Group->setID(2); //Should be okay
    $this->assertEquals($this->Group->getLastError(),NULL);

    //Group incorrect setID
    $this->Group->setID(1); //Should result in error
    $this->assertEquals($this->Group->getLastError(),'Invalid ID');

    //Add Contact with incorrect GroupID
    $this->Contact->addContact("test8@test.com",$groups=array(222),true);
    $this->assertEquals($this->Contact->getLastError(),'Invalid Groups');
    $this->Contact->resetError();

    //Check if Verify allows us to set 1/3 as ID to contact, which should not work, since 1/3 is assigned to the Account before
    $this->Contact->setID(1);
    $this->assertEquals($this->Contact->getLastError(),"Invalid ID");
    $this->Contact->resetError();
    $this->Contact->setID(3);
    $this->assertEquals($this->Contact->getLastError(),"Invalid ID");
    $this->Contact->resetError();
    //But we should be able to acess our own Contact
    $this->Contact->setID(2);
    $this->assertEquals($this->Contact->getLastError(),NULL);
    $this->Contact->setID(4);
    $this->assertEquals($this->Contact->getLastError(),NULL);

    //Edit a Contact with incorrect GroupID
    //$this->Contact->updateContact("test9@test.net",$groups=array(1),true);
    //$this->assertEquals($this->Contact->getLastError(),'Invalid Groups');
    //$this->Contact->resetError();

    //Add a Check to Account 1
    $this->Main->addCheck("8.8.8.8","53",$groups=array(2),"Testcheck",10);
    $this->assertEquals($this->Main->getLastError(),NULL);

    //Add a Check to Account 1 with incorrect GroupsID
    $this->Main->addCheck("8.8.8.8","53",$groups=array(1),"Testcheck",10);
    $this->assertEquals($this->Main->getLastError(),'Invalid Groups');

    //Add a Check to Account 1 with incorrect GroupsIDs
    $this->Main->addCheck("8.7.7.7","53",$groups=array(2,1,11),"Testcheck",10);
    $this->assertEquals($this->Main->getLastError(),'Invalid Groups');
    $this->Main->resetError();

    $this->Main->setID(1);
    $this->assertEquals($this->Main->getLastError(),NULL);

    $this->Main->setID(3);
    $this->assertEquals($this->Main->getLastError(),"Invalid ID");
  }
}

?>
