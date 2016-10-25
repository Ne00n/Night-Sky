<?php

class CheckServ {

  private $online = false;
  private $remote_boxes;

  public function __construct() {
    $this->remote_boxes[] = array('Location' => 'Germany','IP' => '','PORT' => '');
  }

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
      $external_one = mt_rand(0,count($this->remote_boxes));
      $external_second = mt_rand(0,count($this->remote_boxes));

      $res_one = $this->fetchRemote($this->remote_boxes[$external_one]['IP'],$this->remote_boxes[$external_one]['PORT'],$IP,$PORT);
      $res_two = $this->fetchRemote($this->remote_boxes[$external_second]['IP'],$this->remote_boxes[$external_second]['PORT'],$IP,$PORT);

      if ($res_one[0] == 1) {
        $this->online = true;
      } elseif ($res_two[0] == 1) {
        $this->online = true;
      }

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
