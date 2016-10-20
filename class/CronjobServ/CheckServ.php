<?php

class CheckServ {

  private $online = false;

  public function getStatus() {

    return $this->online;

  }

  public function checkAvailability($IP,$PORT) {

    #Check if we can reach the Server from here, 1.5sec Timeout
    $fp = fsockopen($IP,$PORT, $errno, $errstr, 1.5);

    #YAY, its alive
    if ($fp) {
      $this->online = true;
    } else {
      $this->online = false;

      #EXTERNAL CHECKS Todo



    }

  }

}

?>
