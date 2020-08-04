<?php

class CheckServ {

  private $online = false;
  private $remote_boxes;
  private $status_detail;
  private $external_before;

  public function __construct($in_Remote) {
    $this->remote_boxes = $in_Remote;
  }

  public function getStatus() {
    return $this->online;
  }

  public function getStatusDetail() {
    return $this->status_detail;
  }

  public function getUniqueRemote($start,$end) {
    $i = 1;
    while ($i <= 15) {
      $r = mt_rand($start,$end);
      if ($r != $this->external_before) {
        $this->external_before = $r;
        return $r;
      }
      $i++;
    }
    //We try to give out 2 unique Remote Servers but if just have One, we still need to output something
    return $r;
  }

  public function checkAvailability($IP,$PORT,$TYPE = 'tcp',$timeout,$connect,$statusCodes) {
    //Reset, since use this objective for up to 5 servers
    $this->status_detail = [];
    $this->external_before = NULL;

    #Check if we can reach the Server from here.
    if ($TYPE == 'tcp') {
      if (filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $fp = fsockopen("[".$IP."]",$PORT, $errno, $errstr, $timeout);
      } else {
        $fp = fsockopen($IP,$PORT, $errno, $errstr, $timeout);
      }
    } elseif ($TYPE == 'http') {
      $Request = new Request();
      $response = $Request->createRequest($IP.":".$PORT);
      if ($response['http'] == 200) {
        $fp = true;
      } elseif ($response['http'] == 0) {
         $fp = false; $errstr = "Connection timed out.";
       } else {
         $fp = false; $errstr = "HTTP Code: ".$response['http'];
       }
    }

    #YAY, its alive
    if ($fp) {
      $this->online = true;
      $this->status_detail[] = array('Location' => 'Localhost','Status' => 'Online','Reason' => 'Success');
    } else {
      $this->online = false;
      $this->status_detail[] = array('Location' => 'Localhost','Status' => 'Offline','Reason' => $errstr);

      $external_one = $this->getUniqueRemote(0,count($this->remote_boxes)-1);
      $external_second = $this->getUniqueRemote(0,count($this->remote_boxes)-1);

      $res_one = $this->fetchRemote($this->remote_boxes[$external_one]['IP'],$this->remote_boxes[$external_one]['Port'],$IP,$PORT,$TYPE,$timeout,$connect,$statusCodes);
      $this->status_detail[] = array('Location' => $this->remote_boxes[$external_one]['Location'],'Status' => ($res_one[0] ? 'Online' : 'Offline'),'Reason' => $res_one[1],'Totaltime' => $res_one[2]);

      $res_two = $this->fetchRemote($this->remote_boxes[$external_second]['IP'],$this->remote_boxes[$external_second]['Port'],$IP,$PORT,$timeout,$connect,$statusCodes);
      $this->status_detail[] = array('Location' => $this->remote_boxes[$external_second]['Location'],'Status' => ($res_two[0] ? 'Online' : 'Offline'),'Reason' => $res_two[1],'Totaltime' => $res_two[2]);

      if ($res_one[0] == 1) {
        $this->online = true;
      } elseif ($res_two[0] == 1) {
        $this->online = true;
      } elseif ($fp === false && $res_one[1] === "Remote did not respond to request." && $res_two[1] === "Remote did not respond to request.") {
        //If we cannot connect directly and we cannot connect to our Remote servers, its most likely that our Network has issues so return true
        $this->online = true;
      }
    }
  }

  public function fetchRemote($ip,$port,$checkIP,$checkPort,$type = 'tcp',$timeout = 1,$connect = 1,$statusCodes) {
    $url = "https://".$ip.":".$port."/check.php";
    $payload = json_encode(array('ip' => $checkIP,'port' => $checkPort,'type' => $type,'timeout' => $timeout,'connect' => $connect));
    $result = array();

    $Request = new Request();
    $response = $Request->createRequest($url,'POST',$payload);
    $datablock = json_decode($response['content'],true);

    if ($response['http'] == 200) {
      if ($type == 'http') {
        if (in_array($datablock['http'], $statusCodes)) { $result[0] = 1; } else { $result[0] = 0; }
        if ($datablock['http'] == 0) {
          $result[1] = "Connection timed out.";
        } else {
          $result[1] = "HTTP Code ".$datablock['http'];
        }
      } else {
        $result[0] = $datablock['result'];
        $result[1] = $datablock['info'];
      }
      $result[2] = $response['totaltime'];
    } else {
      $result[0] = 0;
      $result[1] = "Remote did not respond to request.";
      $result[2] = $response['totaltime'];
    }
    return $result;
  }

}

?>
