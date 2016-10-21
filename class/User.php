<?php

  class User {

    private $DB;
    private $error;

    public function __construct($DB) {
      $this->DB = $DB;
    }

    public function registerUser($username,$email,$password,$password_repeat,$code) {

      if ($password != $password_repeat) { $this->error = "Passwords are not equal"; }
      if (strlen($password) < 10 ) {$this->error = "The Password to short."; $error = true;}
      if (strlen($password) > 160 ) {$this->error = "The Password is to long."; $error = true;}
      if (strlen($username) < 3 ) {$this->error = "The Username is to short."; $error = true;}
      if (strlen($username) > 50 ) {$this->error = "The Username is to long."; $error = true;}
      if ($code == "LET") {
        //Okay
      } else {
        $this->error = "Invalid Code";
      }

      if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
         $this->error = "Invalid Email";
      } else {
        if ($this->checkifEMailExists($email) == true) {$this->error = "Email exists.";}
      }

      if(!preg_match("/^[a-zA-Z0-9]+$/",$username)){
         $this->error = "Invalid Username (A-Z,a-z,0-9)";
       } else {
         if ($this->checkifUserExists($username) == true) {$this->error = "Username exists.";}
       }

      if ($this->getlastError() == "") {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $rank = 2;

        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO users(Username,Password,rank) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $username,$hash,$rank);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $user_id = $stmt->insert_id;
        $stmt->close();

        $stmt = $this->DB->GetConnection()->prepare("INSERT INTO emails(USER_ID,EMail) VALUES (?, ?)");
        $stmt->bind_param('is', $user_id,$email);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $user_id = $stmt->insert_id;
        $stmt->close();

      }

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

  }

 ?>
