<?php

use PHPUnit\Framework\TestCase;

class Cronjob_Tests extends TestCase {

  public function launch() {
    //Check if the Cronjob night runs through and not crashes
    $Night = new Night();
    $this->assertEquals($Night->run(true),true);
  }
}

?>
