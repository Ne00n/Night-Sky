<?php

  class User {

    private $DB;
    private $error;
    private $warning;
    private $Verify;

    public function __construct($DB_IN,$Verify_IN = NULL) {
      $this->DB = $DB_IN;
      $this->Verify = $Verify_IN;
    }

    public function registerUser($username,$email,$password,$password_repeat,$code,$testing = false) {
      if ($password != $password_repeat) { $this->error = "Passwords are not equal"; }
      if (strlen($password) < _min_Password) {$this->error = "The Password to short."; $error = true;}
      if (strlen($password) > _max_Password) {$this->error = "The Password is to long."; $error = true;}
      if (strlen($username) < _min_Name) {$this->error = "The Username is to short."; $error = true;}
      if (strlen($username) > _max_Name) {$this->error = "The Username is to long."; $error = true;}
      if (!in_array($code, _signUpCodes)) { $this->error = "Invalid Code"; }

      if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
         $this->error = "Invalid Email";
      } else {
        if ($this->checkifEMailExists($email) == true) {$this->error = "Email exists.";}
      }

      if(!preg_match(_regex_USERNAME,$username)){
         $this->error = "Invalid Username (A-Z,a-z,0-9)";
       } else {
         if ($this->checkifUserExists($username) == true) {$this->error = "Username exists.";}
       }

      if ($this->getlastError() == "") {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $rank = 2;
        $activation_hash = bin2hex(random_bytes(20));

        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO users(Username,Password,rank,activation_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssis', $username,$hash,$rank,$activation_hash);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $user_id = $stmt->insert_id;
        $stmt->close();

        if (empty($this->error)) {
          $stmt = $this->DB->GetConnection()->prepare("INSERT INTO emails(USER_ID,EMail) VALUES (?, ?)");
          $stmt->bind_param('is', $user_id,$email);
          $rc = $stmt->execute();
          if ( false===$rc ) { $this->error = "MySQL Error"; }
          $stmt->close();

          if ($testing === true && php_sapi_name() == 'cli') {
            return $activation_hash;
          } else {
            $Mail = new Mail($email,'Night-Sky - Registration','Activate your Account: https://'._Domain.'/index.php?key='.$activation_hash);
            $Mail->run();
          }

        }

      }
    }

    public function enableUser($key) {
      $enabled = 1;

      #Enable User Account
      $stmt = $this->DB->GetConnection()->prepare("UPDATE users SET enabled = ?  WHERE activation_hash = ?");
      $stmt->bind_param('is', $enabled,$key);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

      #Get user ID
      $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM users WHERE activation_hash = ?");
      $stmt->bind_param('i', $key);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($db_user_id);
      $stmt->fetch();
      $stmt->close();

      #Enable Email
      $stmt = $this->DB->GetConnection()->prepare("UPDATE emails SET Status = ?  WHERE USER_ID = ? AND Status = 0");
      $stmt->bind_param('ii', $enabled,$db_user_id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();
    }

    public function changePassword($old_pw,$new_pw,$new_pw_2) {
      if (strlen($new_pw) < _min_Password) {$this->error = "Passwords to short."; }
      if (strlen($new_pw) > _max_Password) {$this->error = "Passwords are to long."; }
      if ($new_pw != $new_pw_2) {$this->error = "Passwords not equal."; }

      //Get userID
      $userID = $this->Verify->getUserID();
      //Fetch Password from User
      $stmt = $this->DB->GetConnection()->prepare("SELECT Password FROM users WHERE ID = ?");
      $stmt->bind_param('i', $userID);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($password_db);
      $stmt->fetch();
      $stmt->close();

      if ($this->error == "") {
        if (password_verify($old_pw, $password_db)) {

          $hash = password_hash($new_pw, PASSWORD_DEFAULT);

          $stmt = $this->DB->GetConnection()->prepare("UPDATE users SET Password = ?  WHERE ID = ?");
          $stmt->bind_param('si',$hash,$this->Verify->getUserID());
          $rc = $stmt->execute();
          if ( false===$rc ) { $this->error = "MySQL Error"; }
          $stmt->close();

        } else {
          $this->error = "Old Password is incorrect.";
        }
      }
    }

    public function deleteAccount($current_password) {
      if (strlen($current_password) < _min_Password ) {$this->error = "Password to short."; }
      if (strlen($current_password) > _max_Password ) {$this->error = "Password is to long."; }

      $user_id = $this->Verify->getUserID();

      $stmt = $this->DB->GetConnection()->prepare("SELECT Password FROM users WHERE ID = ?");
      $stmt->bind_param('i', $user_id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($password_db);
      $stmt->fetch();
      $stmt->close();

      if ($this->error == "") {
        if (password_verify($current_password, $password_db)) {

          #Delete History
          $stmt = $this->DB->GetConnection()->prepare("DELETE FROM history WHERE USER_ID = ?");
          $stmt->bind_param('i', $user_id);
          $rc = $stmt->execute();
          $stmt->close();

          #Emails
          $stmt = $this->DB->GetConnection()->prepare("DELETE FROM emails WHERE USER_ID = ?");
          $stmt->bind_param('i', $user_id);
          $rc = $stmt->execute();
          $stmt->close();

          #Checks
          $stmt = $this->DB->GetConnection()->prepare("DELETE FROM checks WHERE USER_ID = ?");
          $stmt->bind_param('i', $user_id);
          $rc = $stmt->execute();
          $stmt->close();

          #Account iself
          $stmt = $this->DB->GetConnection()->prepare("DELETE FROM users WHERE ID = ? LIMIT 1");
          $stmt->bind_param('i', $user_id);
          $rc = $stmt->execute();
          $stmt->close();

        } else {
          $this->error = "Current Password is incorrect.";
        }
      }
    }

    public function checkUserAmmount() {
        $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM users");
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows < _user_limit ) {
          return true;
        } else {
          $this->error = "Registration closed";
          return false;
        }
        $stmt->close();
    }

    public function checkifUserExists($name) {
      $stmt = $this->DB->GetConnection()->prepare("SELECT id FROM users WHERE Username = ? LIMIT 1");
      $stmt->bind_param('s', $name);
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

    public function getlastError() {
      return $this->error;
    }

    public function getlastWarning() {
      return $this->warning;
    }

  }

 ?>
