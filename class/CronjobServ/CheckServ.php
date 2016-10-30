<?php

class CheckServ {

  private $online = false;
  private $remote_boxes;
  private $status_detail;

  public function __construct($in_Remote) {
    $this->remote_boxes = $in_Remote;
  }

  public function getStatus() {
    return $this->online;
  }

  public function getStatusDetail() {
    return $this->status_detail;
  }

  public function checkAvailability($IP,$PORT) {

    $this->status_detail = [];

    #Check if we can reach the Server from here, 1.5sec Timeout
    $fp = fsockopen($IP,$PORT, $errno, $errstr, 1.5);

    #YAY, its alive
    if ($fp) {
      $this->online = true;
      $this->status_detail[] = array('Location' => 'Localhost','Status' => 'Offline','Reason' => 'Success');
    } else {
      $this->online = false;
      $this->status_detail[] = array('Location' => 'Localhost','Status' => 'Offline','Reason' => $errstr);

      $external_one = mt_rand(0,count($this->remote_boxes) -1);
      $external_second = mt_rand(0,count($this->remote_boxes) -1);

      $res_one = $this->fetchRemote($this->remote_boxes[$external_one]['IP'],$this->remote_boxes[$external_one]['Port'],$IP,$PORT);
      $this->status_detail[] = array('Location' => $this->remote_boxes[$external_one]['Location'],'Status' => ($res_one[0] ? 'Online' : 'Offline'),'Reason' => $res_one[1],'Totaltime' => $res_one[2]);

      $res_two = $this->fetchRemote($this->remote_boxes[$external_second]['IP'],$this->remote_boxes[$external_second]['Port'],$IP,$PORT);
      $this->status_detail[] = array('Location' => $this->remote_boxes[$external_second]['Location'],'Status' => ($res_two[0] ? 'Online' : 'Offline'),'Reason' => $res_two[1],'Totaltime' => $res_two[2]);

      if ($res_one[0] == 1) {
        $this->online = true;
      } elseif ($res_two[0] == 1) {
        $this->online = true;
      }

    }

  }

  public function fetchRemote($IP,$Port,$IP_Check,$Port_Check) {

    $URL = "https://".$IP.":".$Port."/check.php?host=". $IP_Check .":" . $Port_Check;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); //Time for connection in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); //Time for execution in seconds
    $content = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totaltime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    curl_close($ch);

    $result = array();

    if ($httpcode == 200) {

      list($status,$details) =  explode(":", $content);
      $result[0] = $status;
      $result[1] = $details;
      $result[2] = $totaltime;

    } else {

      $result[0] = 0;
      $result[1] = "Did not return Response Code 200";
      $result[2] = $totaltime;

    }

    return $result;

  }

}

?>
