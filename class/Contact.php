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
      $activation_hash = bin2hex(random_bytes(20));

      $Mail = new Mail($EMail,'Night-Sky - EMail confirmation','Please confirm your added Mail: https://night.x8e.ru/index.php?p=contact?key='.$activation_hash);
      $Mail->run();

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO emails(USER_ID,EMail,activation_hash) VALUES (?,?,?)");
      $stmt->bind_param('iss',$USER_ID, $EMail,$activation_hash);
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

  public function enableContact($key) {

    $enabled = 1;

    #Enable Contact
    $stmt = $this->DB->GetConnection()->prepare("UPDATE emails SET Status = ?  WHERE activation_hash = ?");
    $stmt->bind_param('is', $enabled,$key);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->close();

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
