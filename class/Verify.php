<?php

class Verify {

  private $DB;
  private $error;
  private $user_id;
  private $rank;

  public function __construct($DB) {
    $this->DB = $DB;
  }

  public function ValidateLogin($User,$Password) {

    if (strlen($User) < 4) { $this->error = "The Username is to short.";}
    if (strlen($User) > 50) { $this->error = "The Username is to long.";}
    if (strlen($Password) < 8) { $this->error = "The Password is to short.";}
    if (strlen($Password) > 150) { $this->error = "The Password is to long.";}
    if(!preg_match("/^[a-zA-Z0-9]+$/",$User)){ $this->error = "The Username contains invalid letters.";}

    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("SELECT Password,ID FROM users WHERE Username = ? AND enabled = 1 LIMIT 1");
      $stmt->bind_param('s', $User);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_password,$db_id);
      $stmt->fetch();
      $stmt->close();

      if (!password_verify($Password, $db_password)) {
          $this->error = "Incorrect Login details";
          $this->user_id = $db_id;
      } else {
          $this->user_id = $db_id;
      }

    }

  }

  public function isLoggedIN() {

    if(preg_match("/^[0-9]+$/",$_SESSION['user_id'])){

      $stmt = $this->DB->GetConnection()->prepare("SELECT Rank,ID FROM users WHERE ID = ? AND enabled = 1 LIMIT 1");
      $stmt->bind_param('i', $_SESSION['user_id']);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_rank,$db_id);
      $stmt->fetch();
      $stmt->close();

      $this->rank = $db_rank;
      $this->user_id = $db_id;

      if ($db_id != "") {
        return true;
      } else {
        return false;
      }

    } else {
      return false;
    }

  }

  public function checkContactID($id) {
    if(!preg_match("/^[0-9]+$/",$id)){ return false;}

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM emails WHERE ID = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();

    if (isset($result)) {
      return true;
    } else {
      return false;
    }

  }

  public function getLastError() {
    return $this->error;
  }

  public function getUserID() {
    return $this->user_id;
  }

}

?>
