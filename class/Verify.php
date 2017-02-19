<?php

class Verify {

  private $DB;
  private $error;
  private $user_id;
  private $rank;
  private $check_limit = 0;
  private $contact_limit = 0;
  private $check_ip_limit = 0;

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

    if(isset($_SESSION['user_id']) AND preg_match("/^[0-9]+$/",$_SESSION['user_id'])){

      $stmt = $this->DB->GetConnection()->prepare("SELECT Rank,ID,Check_Limit,Contact_Limit,Same_IP_Limit FROM users WHERE ID = ? AND enabled = 1 LIMIT 1");
      $stmt->bind_param('i', $_SESSION['user_id']);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_rank,$db_id,$db_check_limit,$db_contact_limit,$db_same_ip_limit);
      $stmt->fetch();
      $stmt->close();

      $this->rank = $db_rank;
      $this->user_id = $db_id;
      $this->contact_limit = $db_contact_limit;
      $this->check_limit = $db_check_limit;
      $this->check_ip_limit = $db_same_ip_limit;

      if ($db_id != "") {
        return true;
      } else {
        return false;
      }

    } else {
      return false;
    }

  }

  public function checkHash($key) {

    if(!preg_match("/^[a-zA-Z0-9]+$/",$key)){ return false;}
    if (strlen($key) != 40) {return false;}

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM users WHERE activation_hash = ? AND enabled = 0 LIMIT 1");
    $stmt->bind_param('s', $key);
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

   public function checkEmailHash($key) {

     if(!preg_match("/^[a-zA-Z0-9]+$/",$key)){ return false;}
     if (strlen($key) != 40) {return false;}

     $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM emails WHERE activation_hash = ? AND Status = 0 LIMIT 1");
     $stmt->bind_param('s', $key);
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

  public function checkContactID($id,$status_check = 1) {
    if(!preg_match("/^[0-9]+$/",$id)){ return false;}

     $user_id = $this->getUserID();

    if ($status_check == 1) {
      $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM emails WHERE ID = ? AND Status = 1 AND USER_ID = ? LIMIT 1");
    } elseif ($status_check == 0) {
      $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM emails WHERE ID = ? AND USER_ID = ? LIMIT 1");
    }
    $stmt->bind_param('ii', $id,$user_id);
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

   public function checkHistoryID($id) {
     if(!preg_match("/^[0-9]+$/",$id)){ return false;}

      $user_id = $this->getUserID();

     $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM history WHERE CHECK_ID = ? AND USER_ID = ? LIMIT 1");
     $stmt->bind_param('ii', $id,$user_id);
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

   public function checkCheckID($id) {
     if(!preg_match("/^[0-9]+$/",$id)){ return false;}

     $user_id = $this->getUserID();

     $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM checks WHERE ID = ? AND USER_ID = ? LIMIT 1");
     $stmt->bind_param('ii', $id,$user_id);
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

  public function getCheckLimit() {
    return $this->check_limit;
  }

  public function getCheckIPLimit() {
    return $this->check_ip_limit;
  }

  public function getContactLimit() {
    return $this->contact_limit;
  }

  public function getLastError() {
    return $this->error;
  }

  public function getUserID() {
    return $this->user_id;
  }

}

?>
