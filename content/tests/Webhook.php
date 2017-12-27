<?php

use PHPUnit\Framework\TestCase;

class Webhook_Tests extends TestCase {

  private function switchtoID($id) {
    //Switches all to a different UserID
    $this->DB = new Database;
    $this->DB->InitDB();
    $this->Verify = new Verify($this->DB,true,$id);
    $this->WebHook = new WebHook($this->DB,$this->Verify);
  }

  public function launch() {
    $this->switchtoID(1);
    //Create a GET WebHook
    $this->WebHook->addHook('Test','1','https://test.com','','','https://test.com','','',1);
    $this->assertEquals($this->WebHook->getLastError(),NULL);
    //Create a POST WebHook
    $this->WebHook->addHook('Test','2','https://test.com','{ "content": "wololo! server went to nuts."}','"Content-Type: application/json"','https://test.com','{ "content": "wololo! its back!."}','"Content-Type: application/json"',1);
    $this->assertEquals($this->WebHook->getLastError(),NULL);
    //Edit the POST WebHook to PUT
    $this->WebHook->setID(2);
    $this->WebHook->editHook('Test','3','https://test.com/bla','{ "content": "wololo! server went to nuts."}','"Content-Type: application/json"','https://test.com/bla','{ "content": "wololo! its back!."}','"Content-Type: application/json"',1);
    $this->assertEquals($this->WebHook->getLastError(),NULL);
    //Delete the PUT WebHook
    $this->WebHook->setID(2);
    $this->WebHook->removeHook();
    $this->assertEquals($this->WebHook->getLastError(),NULL);
    //Lets see if its still there, should be gone
    $this->WebHook->setID(2);
    $this->WebHook->editHook('Test','3','https://test.com/bla','{ "content": "wololo! server went to nuts."}','"Content-Type: application/json"','https://test.com/bla','{ "content": "wololo! its back!."}','"Content-Type: application/json"',1);
    $this->assertEquals($this->WebHook->getLastError(),'Invalid ID');
    //Try as different user to edit the WebHook
    $this->switchtoID(2);
    $this->WebHook->setID(2);
    $this->WebHook->editHook('Test','3','https://test.com/bla','{ "content": "wololo! server went to nuts."}','"Content-Type: application/json"','https://test.com/bla','{ "content": "wololo! its back!."}','"Content-Type: application/json"',2);
    $this->assertEquals($this->WebHook->getLastError(),'Invalid ID');
  }
}

?>
