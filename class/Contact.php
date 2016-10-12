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

      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM emails WHERE ID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

  }

  public function setID($id) {

    $Verify = new Verify($this->DB);

    if ($Verify->checkContactID($id) === true) {
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
