<?php

class Remote {

  private $error;
  private $DB;

  public function __construct($DB_IN) {
    $this->DB = $DB_IN;
  }

  public function checkRemote() {
    $query = "SELECT ID,IP,Port,Location FROM remote";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {

      $fp = fsockopen($row['IP'],$row['Port'], $errno, $errstr, 1.5);

      if ($fp) {

        $status = 1;

        echo("Online ".$row['Location']."\n");

        $stmt = $this->DB->GetConnection()->prepare("UPDATE remote SET Online = ? WHERE ID = ?");
        $stmt->bind_param('ii', $status,$row['ID']);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

      } else {

        $status = 0;

        echo("Offline ".$db_Location."\n");

        $stmt = $this->DB->GetConnection()->prepare("UPDATE remote SET Online = ? WHERE ID = ?");
        $stmt->bind_param('ii', $status,$row['ID']);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

      }
    }
  }

  public function getRemote() {
    $remote = array();
    $query = "SELECT ID,IP,Port,Location FROM remote WHERE Online = 1";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->execute();
    $stmt->bind_result($db_ID,$db_IP,$db_Port,$db_Location);
    while ($stmt->fetch()) {
      $remote[] = array('Location' => $db_Location,'IP' => $db_IP,'Port' => $db_Port);
    }
    return $remote;
  }

}

?>
