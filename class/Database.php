<?php

class Database {

  private $database;

  public function InitDB() {
      $this->database = new mysqli(_db_host, _db_user, _db_password, _db_database);
      if ($this->database->connect_error) {
         echo "Not connected, error: " .   $this->database->connect_error;
         exit;
      }
  }

  public function GetConnection() {
      return $this->database;
  }

}

?>
