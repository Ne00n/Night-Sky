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

  public function fetchRemote($IP,$Port,$IP_Check,$Port_Check) {

    $ch = curl_init("https://".$IP.":".$PORT."/check.php?host=". $IP_Check .":" . $Port_Check ."");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,1); //Time for connection in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 1.5); //Time for execution in seconds
    $content = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = array();

    if ($httpcode == 200) {

      list($status,$details) =  explode(":", $content);
      $result[0] = $status;
      $result[1] = $details;

    } else {

      $result[0] = 0;
      $result[1] = "Did not return Response Code 200";

    }

    return $result;

  }

}

?>
