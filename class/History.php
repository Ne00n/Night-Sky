<?php

class History {

  private $error;
  private $DB;
  private $Verify;

  public function __construct($DB_IN,$Verify_IN = NULL) {
    $this->DB = $DB_IN;
    $this->Verify = $Verify_IN;
  }

  public function addHistory($userID,$checkID,$status) {
    $current_time = time();

    $stmt = $this->DB->GetConnection()->prepare("INSERT INTO history(USER_ID,CHECK_ID,Status,Timestamp) VALUES (?,?,?,?)");
    $stmt->bind_param('iiii',$userID, $checkID, $status, $current_time);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->close();
  }

  public function checkHistoryID($id) {
    if(!preg_match(_regex_ID,$id)){ $this->error = 'Invalid ID'; return false;}

    $userID = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM history WHERE CHECK_ID = ? AND USER_ID = ? LIMIT 1");
    $stmt->bind_param('ii', $id,$userID);
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

  public function getHistory($checkID) {
    $data = array();

    if(!preg_match(_regex_ID,$checkID)){ $this->error = 'Invalid ID'; return $data;}

    $userID = $this->Verify->getUserID();

    $query = "SELECT ID,Status,Timestamp FROM history WHERE USER_ID = ? AND CHECK_ID = ? ORDER by ID DESC";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->bind_param('ii', $userID,$checkID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $data[] = array("Status" => $row['Status'],"Timestamp" => $row['Timestamp']);
    }
    return $data;
  }

  public function getlastError() {
    return $this->error;
  }

}

?>
