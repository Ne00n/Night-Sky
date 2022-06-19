<?php

class Main {

  private $DB;
  private $Verify;
  private $error;
  private $id;
  private $name;
  private $port;
  private $ip;
  private $email;
  private $interval;
  private $lastrun;
  private $connect;
  private $statusCodes;
  private $timeout;
  private $type;
  private $mtr;

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addCheck($IP,$PORT,$groups,$NAME,$interval,$type = 'TCP',$timeout = 1.5,$connection = 1.5,$httpcodes = "200",$mtr = 0) {
    if ($type == 'TCP' && filter_var($IP, FILTER_VALIDATE_IP) == false && preg_match(_regex_DOMAIN,$IP) == false) { $this->error = "Invalid Domain/IP."; }
    if ($type == 'HTTP' && preg_match(_regex_URL,$IP) == false) { $this->error = "Invalid URL."; }
    if(!preg_match(_regex_NAME,$NAME)){ $this->error = "The Name contains invalid letters.";}
    if(!preg_match(_regex_PORT,$PORT)){ $this->error = "Invalid Port.";}
    if(!preg_match(_regex_Timeout,$timeout)){ $this->error = "Invalid Timeout.";}
    if(!preg_match(_regex_Timeout,$connection)){ $this->error = "Invalid Connect Timeout.";}
    if(!preg_match(_regex_StatusCodes,$httpcodes)){ $this->error = "Invalid Status Codes e.g 200 or 200,300.";}
    if (strlen($NAME) > _max_Name OR strlen($NAME) < _min_Name) {$this->error = "The length of the Name should be between "._min_Name." and "._max_Name.".";}
    if ($PORT > 65535 OR $PORT < 1) {$this->error = "The Port should be between 1 and 65535.";}
    if (!$this->checkLimit()) { $this->error = "Limit reached";}
    if (!$this->checkIPLimit($IP)) { $this->error = "Limit reached";}
    if (!$this->checkIP_Global_Limit($IP)) { $this->error = "Limit reached";}

    $allowed_interval = array('10','20','30','60');
    if (!in_array($interval, $allowed_interval)) { $this->error = "Invalid Interval"; }
    if ($type != 'TCP' && $type != 'HTTP') { $this->error = "Invalid Type."; }
    if (isset($mtr)) { $mtr = 1; } else { $mtr = 0; }

    if ($this->error == "") {

      $USER_ID = $this->Verify->getUserID();

      $L = new LoadBalancer($this->DB);
      $SLOT = $L->balanceCheck();

      if ($SLOT != false) {

        $type = strtolower($type);

        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO checks(USER_ID,IP,PORT,SLOT,NAME,Check_Interval,TYPE,TIMEOUT,CONNECT,MTR,Status_Codes) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('isiisisddis',$USER_ID, $IP, $PORT,$SLOT,$NAME,$interval,$type,$timeout,$connection,$mtr,$httpcodes);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $check_id = $stmt->insert_id;
        $stmt->close();

        if ($this->error == "") { $this->setID($check_id); }

        if ($this->error == "") { $this->processGroups($groups); }

      } else {
        $this->error = "Unable to find free slot.";
      }

    }
  }

  public function getChecks() {
    $response = array(); $userID = $this->Verify->getUserID();

    $query = "SELECT ID,IP,PORT,ENABLED,ONLINE,NAME,Lastrun FROM checks WHERE USER_ID = ? ORDER BY ONLINE,NAME";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $response[] = $row;
    }

    return $response;
  }

  public function updateCheck($IP,$PORT,$groups,$NAME,$interval,$type,$timeout,$connection,$httpcodes,$mtr) {
    if ($type == 'TCP' && filter_var($IP, FILTER_VALIDATE_IP) == false && preg_match(_regex_DOMAIN,$IP) == false) { $this->error = "Invalid Domain/IP."; }
    if ($type == 'HTTP' && preg_match(_regex_URL,$IP) == false) { $this->error = "Invalid URL."; }
    if(!preg_match(_regex_NAME,$NAME)){ $this->error = "The Name contains invalid letters.";}
    if(!preg_match(_regex_PORT,$PORT)){ $this->error = "Invalid Port.";}
    if(!preg_match(_regex_Timeout,$timeout)){ $this->error = "Invalid Timeout.";}
    if(!preg_match(_regex_Timeout,$connection)){ $this->error = "Invalid Connect Timeout.";}
    if(!preg_match(_regex_StatusCodes,$httpcodes)){ $this->error = "Invalid Status Codes e.g 200 or 200,300.";}
    if (strlen($NAME) > _max_Name OR strlen($NAME) < _min_Name) {$this->error = "The length of the Name should be between "._min_Name." and "._max_Name.".";}
    if ($PORT > 65535 OR $PORT < 1) {$this->error = "The Port should be between 1 and 65535.";}
    if ($this->ip != $IP) {
      if (!$this->checkIPLimit($IP,1)) { $this->error = "Limit reached";}
      if (!$this->checkIP_Global_Limit($IP)) { $this->error = "Limit reached";}
    }

    $allowed_interval = array('10','20','30','60');
    if (!in_array($interval, $allowed_interval)) { $this->error = "Invalid Interval"; }
    if ($type != 'TCP' && $type != 'HTTP') { $this->error = "Invalid Type."; }
    if (isset($mtr)) { $mtr = 1; } else { $mtr = 0; }

    if ($this->error == "") { $this->processGroups($groups); }

    if ($this->error == "") {

      $type = strtolower($type);

      $stmt = $this->DB->GetConnection()->prepare("UPDATE checks SET IP = ?, PORT = ?, NAME = ?, Check_Interval = ?, TYPE = ?, TIMEOUT = ?, CONNECT = ?, MTR = ?, Status_Codes = ? WHERE ID = ?");
      $stmt->bind_param('sisisddisi', $IP,$PORT,$NAME,$interval,$type,$timeout,$connection,$mtr,$httpcodes,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  private function processGroups($groups) {
    if (count($groups) > 0) {
      $GR = new Group($this->DB,$this->Verify);
      if (count($groups) <= _groups_limit_global) {
        foreach ($groups as &$id) {
          if ($GR->checkGroupID($id) === false) {
            $this->error = "Invalid Groups";
            break;
          }
        }
      } else {
        $this->error = "Invalid Groups";
      }

      if ($this->error == "") {

        #Remove all existing links to Groups from this Check
        $stmt = $this->DB->GetConnection()->prepare("DELETE FROM groups_checks WHERE CheckID = ?");
        $stmt->bind_param('i', $this->id);
        $rc = $stmt->execute();
        $stmt->close();

        #Create all links to Groups which have been inserted
        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO groups_checks(CheckID,GroupID) VALUES (?,?)");
        $stmt->bind_param('ii',$this->id,$group_id);
        $this->DB->GetConnection()->query("START TRANSACTION");

        foreach ($groups as &$id) {
          $group_id = $id;
          $stmt->execute();
        }

        $stmt->close();
        $this->DB->GetConnection()->query("COMMIT");
      }
    } else {
      #Remove all existing links to Groups from this Check
      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM groups_checks WHERE CheckID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      $stmt->close();
    }
  }

  public function getData() {
    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("SELECT NAME,PORT,TIMEOUT,CONNECT,Lastrun,Status_Codes,Check_Interval,TYPE,IP,MTR FROM checks WHERE ID = ? LIMIT 1");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_name,$db_port,$db_timeout,$db_connect,$dbLastrun,$db_status_codes,$db_interval,$dbType,$db_ip,$db_mtr);
      $stmt->fetch();
      $stmt->close();

      $this->name = $db_name;
      $this->port = $db_port;
      $this->timeout = $db_timeout;
      $this->connect = $db_connect;
      $this->lastrun = $dbLastrun;
      $this->statusCodes = $db_status_codes;
      $this->interval = $db_interval;
      $this->type = $dbType;
      $this->ip = $db_ip;
      $this->mtr = $db_mtr;
    }
  }

  public function removeCheck() {
    if ($this->error == "") {

      //History
      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM history WHERE CHECK_ID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

      //Check itself
      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM checks WHERE ID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

      #Remove all existing links to Groups from this Check
      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM groups_checks WHERE CheckID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function enable() {
    if ($this->error == "") {

      $enabled = 1;

      $stmt = $this->DB->GetConnection()->prepare("UPDATE checks SET ENABLED = ?  WHERE ID = ?");
      $stmt->bind_param('ii', $enabled,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function disable() {
    if ($this->error == "") {

      $enabled = 0;

      $stmt = $this->DB->GetConnection()->prepare("UPDATE checks SET ENABLED = ?  WHERE ID = ?");
      $stmt->bind_param('ii', $enabled,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function checkLimit() {
    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM checks WHERE USER_ID = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < $this->Verify->getCheckLimit()) {
      return true;
    }
    $stmt->close();
  }

  public function checkIPLimit($ip) {
    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM checks WHERE USER_ID = ? AND IP = ?");
    $stmt->bind_param('is', $user_id,$ip);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < $this->Verify->getCheckIPLimit()) {
      return true;
    }
    $stmt->close();
  }

  public function checkIP_Global_Limit($ip) {
    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM checks WHERE IP = ?");
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < _ip_limit_global) {
      return true;
    }
    $stmt->close();
  }

  public function setID($id) {
    if ($this->Verify->checkCheckID($id) === true) {
      $this->id = $id;
    } else {
      $this->error = "Invalid ID";
    }
  }

  public function resetError() {
    $this->error = NULL;
  }

  public function getLastError() {
    return $this->error;
  }

  public function getName(){
    return $this->name;
  }

  public function getIP() {
    return $this->ip;
  }

  public function getPort() {
    return $this->port;
  }

  public function getInterval() {
    return $this->interval;
  }

  public function getLastrun() {
    return $this->lastrun;
  }

  public function getType() {
    return $this->type;
  }

  public function getConnect() {
    return $this->connect;
  }

  public function getTimeout() {
    return $this->timeout;
  }

  public function getMTR() {
    return $this->mtr;
  }

  public function getStatusCodes() {
    return $this->statusCodes;
  }

}

?>
