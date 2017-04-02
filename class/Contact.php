<?php

class Contact {

  private $DB;
  private $Verify;
  private $error;
  private $id;
  private $email;

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addContact($EMail,$groups,$testing = false) {
    if (!filter_var($EMail, FILTER_VALIDATE_EMAIL)) { $this->error = "Invalid Email."; }
    if (strlen($EMail) > _max_Mail) {$this->error = "The Email is to long";}
    if (strlen($EMail) < _min_Mail) {$this->error = "The Email is to short";}
    if ($this->checkifEMailExists($EMail) == true) {$this->error = "The Email exists.";}
    if (!$this->checkLimit()) { $this->error = "Limit reached";}

    if ($this->error == "") {

      $USER_ID = $this->Verify->getUserID();
      $activation_hash = bin2hex(random_bytes(20));

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO emails(USER_ID,EMail,activation_hash) VALUES (?,?,?)");
      $stmt->bind_param('iss',$USER_ID, $EMail,$activation_hash);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $contact_id = $stmt->insert_id;
      $stmt->close();

      if ($this->error == "") { $this->setID($contact_id); }

      if ($this->error == "") { $this->processGroups($groups); }

      if ($testing === true && php_sapi_name() == 'cli') {
        return $activation_hash;
      } else {
        if ($this->error == "") {
          $Mail = new Mail($EMail,'Night-Sky - EMail confirmation','Please confirm your added Mail: https://'._Domain.'/index.php?p=contact?key='.$activation_hash);
          $Mail->run();
        }
      }

    }

  }

  public function updateContact($mail,$groups,$testing = false) {
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) { $this->error = "Invalid Email."; }
    if (strlen($mail) > _max_Mail) {$this->error = "The Email is to long";}
    if (strlen($mail) < _min_Mail) {$this->error = "The Email is to short";}
    if ($this->email != $mail) {
      $this->error = "Add a new Contact if you want to change your email and conntect it to the same Group";
    }

    if ($this->error == "") { $this->processGroups($groups); }

    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("UPDATE emails SET EMail = ? WHERE ID = ?");
      $stmt->bind_param('si', $mail,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }

  }

  public function removeContact() {
    if ($this->error == "") {

      //Delete email
      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM emails WHERE ID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

      //Remove all existing links to Groups from this email
      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM groups_emails WHERE EmailID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  private function processGroups($groups) {
    if (count($groups) > 0) {
      $GR = new Group($this->DB,$this->Verify);
      if (count($groups) < 16) {
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
        $stmt = $this->DB->GetConnection()->prepare("DELETE FROM groups_emails WHERE EmailID = ?");
        $stmt->bind_param('i', $this->id);
        $rc = $stmt->execute();
        $stmt->close();

        #Create all links to Groups which have been inserted
        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO groups_emails(EmailID,GroupID) VALUES (?,?)");
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
      $stmt = $this->DB->GetConnection()->prepare("DELETE FROM groups_emails WHERE EmailID = ?");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      $stmt->close();
    }
  }

  public function getData() {
    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("SELECT EMail FROM emails WHERE ID = ? LIMIT 1");
      $stmt->bind_param('i', $this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_email);
      $stmt->fetch();
      $stmt->close();

      $this->email = $db_email;
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
      if ($this->Verify->checkContactID($id,0) === true) {
        $this->id = $id;
      } else {
        $this->error = "Invalid ID";
      }
  }

  public function checkifEMailExists($email) {
    $stmt = $this->DB->GetConnection()->prepare("SELECT id FROM emails WHERE EMail = ? LIMIT 1");
    $stmt->bind_param('s', $email);
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

  public function checkLimit() {
    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM emails WHERE USER_ID = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < $this->Verify->getContactLimit()) {
      return true;
    }
    $stmt->close();
  }

  public function resetError() {
    $this->error = NULL;
  }

  public function getLastError() {
    return $this->error;
  }

  public function getEmail() {
    return $this->email;
  }

}

?>
