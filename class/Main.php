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

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addCheck($IP,$PORT,$EMAIL_ID,$NAME) {

    if (!filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) { $this->error = "Invalid IP."; }
    if(!preg_match("/^[a-zA-Z0-9._\- ]+$/",$NAME)){ $this->error = "The Name contains invalid letters.";}
    if(!preg_match("/^[0-9]+$/",$PORT)){ $this->error = "Invalid Port.";}
    if ($this->Verify->checkContactID($EMAIL_ID) === false) { $this->error = "Invalid EMail";}
    if (strlen($NAME) > 50) {$this->error = "The Name is to long";}
    if (strlen($NAME) < 3) {$this->error = "The Name is to short";}
    if ($PORT > 65535) {$this->error = "The Port is to big";}
    if ($PORT < 1) {$this->error = "The Port should be at least 1";}

    if ($this->error == "") {

      $USER_ID = $this->Verify->getUserID();

      $L = new LoadBalancer($this->DB);
      $SLOT = $L->balanceCheck();

      if ($SLOT != false) {

        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO checks(USER_ID,EMAIL_ID,IP,PORT,SLOT,NAME) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('iisiis',$USER_ID, $EMAIL_ID, $IP, $PORT,$SLOT,$NAME);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

      } else {
        $this->error = "Unable to find free slot.";
      }

    }

  }

  public function updateCheck($IP,$PORT,$EMAIL_ID,$NAME) {

    if (!filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) { $this->error = "Invalid IP."; }
    if(!preg_match("/^[a-zA-Z0-9._\- ]+$/",$NAME)){ $this->error = "The Name contains invalid letters.";}
    if(!preg_match("/^[0-9]+$/",$PORT)){ $this->error = "Invalid Port.";}
    if ($this->Verify->checkContactID($EMAIL_ID) === false) { $this->error = "Invalid EMail";}
    if (strlen($NAME) > 50) {$this->error = "The Name is to long";}
    if (strlen($NAME) < 3) {$this->error = "The Name is to short";}
    if ($PORT > 65535) {$this->error = "The Port is to big";}
    if ($PORT < 1) {$this->error = "The Port should be at least 1";}

    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("UPDATE checks SET EMAIL_ID = ?, IP = ?, PORT = ?, NAME = ? WHERE ID = ?");
      $stmt->bind_param('isisi', $USER_ID,$IP,$PORT,$NAME,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }

  }

  public function getData() {

    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("SELECT NAME,IP,PORT FROM checks WHERE ID = ? LIMIT 1");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_name,$db_ip,$db_port);
      $stmt->fetch();
      $stmt->close();

      $this->name = $db_name;
      $this->port = $db_port;
      $this->ip = $db_ip;

    }

  }

  public function removeCheck() {

    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM checks WHERE ID = ?");
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

  public function setID($id) {

    if ($this->Verify->checkCheckID($id) === true) {
      $this->id = $id;
    } else {
      $this->error = "Invalid ID";
    }

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

}

?>
