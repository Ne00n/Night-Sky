<?php

class Main {

  private $DB;
  private $Verify;
  private $error;
  private $id;

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addCheck($IP,$PORT,$EMAIL_ID) {

    if (!filter_var($IP, FILTER_VALIDATE_IP)) { $this->error = "Invalid IP."; }
    if(!preg_match("/^[0-9]+$/",$PORT)){ $this->error = "Invalid Port.";}
    if ($this->Verify->checkContactID($EMAIL_ID) === false) { $this->error = "Invalid EMail";}

    if ($this->error == "") {

      $USER_ID = $this->Verify->getUserID();

      $L = new LoadBalancer($this->DB);
      $SLOT = $L->balanceCheck();

      if ($SLOT != false) {

        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO checks(USER_ID,EMAIL_ID,IP,PORT,SLOT) VALUES (?,?,?,?,?)");
        $stmt->bind_param('iisii',$USER_ID, $EMAIL_ID, $IP, $PORT,$SLOT);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

      } else {
        $this->error = "Unable to find free slot.";
      }

    }

  }

  public function removeCheck() {

      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM checks WHERE ID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

  }

  public function setID($id) {

    $Verify = new Verify($this->DB);

    if ($Verify->checkCheckID($id) === true) {
      $this->id = $id;
    } else {
      $this->error = "Invalid ID";
    }

  }

  public function getLastError() {
    return $this->error;
  }

}

?>
