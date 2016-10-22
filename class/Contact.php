<?php

class Contact {

  private $DB;
  private $Verify;
  private $error;
  private $id;

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addContact($EMail) {

    if (!filter_var($EMail, FILTER_VALIDATE_EMAIL)) { $this->error = "Invalid EMail."; }
    if (strlen($EMail) > 50) {$this->error = "The EMail is to long";}

    if ($this->error == "") {

      $USER_ID = $this->Verify->getUserID();

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO emails(USER_ID,EMail) VALUES (?,?)");
      $stmt->bind_param('is',$USER_ID, $EMail);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }

  }

  public function removeContact() {

      if ($this->CheckifContactIsInUse() === false) {

        if ($this->error == "") {

          $stmt = $this->DB->GetConnection()->prepare("DELETE FROM emails WHERE ID = ?");
          $stmt->bind_param('i', $this->id);
          $rc = $stmt->execute();
          if ( false===$rc ) { $this->error = "MySQL Error"; }
          $stmt->close();

        }

      } else {

        $this->error = "Contact still in use";

      }

  }

  public function getEMailbyID() {

    $stmt = $this->DB->GetConnection()->prepare("SELECT EMail FROM emails WHERE ID = ? LIMIT 1");
    $stmt->bind_param('i', $this->id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->bind_result($db_email);
    $stmt->fetch();
    $stmt->close();

    return $db_email;

  }

  public function setID($id) {

    if (php_sapi_name() == 'cli') {
      $this->id = $id;
    } else {
      if ($this->Verify->checkContactID($id,0) === true) {
        $this->id = $id;
      } else {
        $this->error = "Invalid ID";
      }
    }

  }

  public function CheckifContactIsInUse() {

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM checks WHERE EMAIL_ID = ? LIMIT 1");
    $stmt->bind_param('i', $this->id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->bind_result($db_id);
    $stmt->fetch();
    $stmt->close();

    if (isset($db_id)) {
      return true;
    } else {
      return false;
    }

  }

  public function getLastError() {
    return $this->error;
  }

}

?>
