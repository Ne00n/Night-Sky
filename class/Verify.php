<?php

class Verify {

  private $DB;
  private $error;
  private $user_id;
  private $rank;
  private $check_limit = 0;
  private $contact_limit = 0;
  private $check_ip_limit = 0;
  private $group_limit = 0;
  private $status_limit = 0;
  private $webHookLimit = 0;

  public function __construct($DB,$testing = false,$user_id = 0) {
    $this->DB = $DB;
    if ($testing === true && php_sapi_name() == 'cli') {
      $this->user_id = $user_id;
      $this->contact_limit = 4;
      $this->check_limit = 15;
      $this->check_ip_limit = 2;
      $this->group_limit = 15;
      $this->status_limit = 4;
      $this->webHookLimit = 15;
    }
  }

  public function ValidateLogin($User,$Password) {
    if (strlen($User) < _min_Name) { $this->error = "The Username is to short.";}
    if (strlen($User) > _max_Name) { $this->error = "The Username is to long.";}
    if (strlen($Password) < _min_Password) { $this->error = "The Password is to short.";}
    if (strlen($Password) > _max_Password) { $this->error = "The Password is to long.";}
    if(!preg_match(_regex_USERNAME,$User)){ $this->error = "The Username contains invalid letters.";}

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
          if (password_needs_rehash($db_password, PASSWORD_DEFAULT,["cost" => 12])) {
            $db_password = password_hash($Password, PASSWORD_DEFAULT,["cost" => 12]);

            $stmt = $this->DB->GetConnection()->prepare("UPDATE users SET Password = ?  WHERE ID = ?");
            $stmt->bind_param('si', $db_password,$db_id);
            $rc = $stmt->execute();
            if ( false===$rc ) { $this->error = "MySQL Error"; }
            $stmt->close();

          }

      }

    }
  }

  public function isLoggedIN() {
    if(isset($_SESSION['user_id']) AND preg_match(_regex_ID,$_SESSION['user_id'])){

      $stmt = $this->DB->GetConnection()->prepare("SELECT Rank,ID,Check_Limit,Contact_Limit,Same_IP_Limit,Group_Limit,StatusPage_Limit,WebHookLimit FROM users WHERE ID = ? AND enabled = 1 LIMIT 1");
      $stmt->bind_param('i', $_SESSION['user_id']);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_rank,$db_id,$db_check_limit,$db_contact_limit,$db_same_ip_limit,$db_group_limit,$db_status_limit,$db_webhook_limit);
      $stmt->fetch();
      $stmt->close();

      $this->rank = $db_rank;
      $this->user_id = $db_id;
      $this->contact_limit = $db_contact_limit;
      $this->check_limit = $db_check_limit;
      $this->check_ip_limit = $db_same_ip_limit;
      $this->group_limit = $db_group_limit;
      $this->status_limit = $db_status_limit;
      $this->webHookLimit = $db_webhook_limit;

      if($stmt->rowCount() == 1) {
        return true; 
      }
    }
    return false;
  }

  public function checkHash($key) {
    if(!preg_match(_regex_TOKEN,$key)){ return false;}
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
     if(!preg_match(_regex_TOKEN,$key)){ return false;}
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
    if(!preg_match(_regex_ID,$id)){ return false;}

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

   public function checkCheckID($id) {
     if(!preg_match(_regex_ID,$id)){ return false;}

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

  public function getGroupLimit() {
    return $this->group_limit;
  }

  public function getStatusLimit() {
    return $this->status_limit;
  }

  public function getWebHookLimit() {
    return $this->webHookLimit;
  }

  public function getLastError() {
    return $this->error;
  }

  public function getUserID() {
    return $this->user_id;
  }

}

?>
