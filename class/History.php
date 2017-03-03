<?php

class History {

  private $error;
  private $DB;

  public function __construct($DB_IN) {
    $this->DB = $DB_IN;
  }

  public function addHistory($USER_ID,$CHECK_ID,$STATUS) {
    $current_time = time();

    $stmt = $this->DB->GetConnection()->prepare("INSERT INTO history(USER_ID,CHECK_ID,Status,Timestamp) VALUES (?,?,?,?)");
    $stmt->bind_param('iiii',$USER_ID, $CHECK_ID, $STATUS, $current_time);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->close();
  }

  public function getHistory($USER_ID,$CHECK_ID) {
    $data = array();

    $query = "SELECT ID,Status,Timestamp FROM history WHERE USER_ID = ? AND CHECK_ID = ? ORDER by ID DESC";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->bind_param('ii', $USER_ID,$CHECK_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $data[$row['ID']] = array("Status" => $row['Status'],"Timestamp" => $row['Timestamp']);
    }
    return $data;
  }

}

?>
