<?php

class Status {

  private $DB;
  private $id;
  private $status;
  private $error;

  public function __construct($DB_IN) {
      $this->DB = $DB_IN;
  }

  public function setID($id_in) {
    $this->id = $id_in;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getOnlineStatus() {
    $stmt = $this->DB->GetConnection()->prepare("SELECT ONLINE FROM checks WHERE ID = ?");
    $stmt->bind_param('i', $this->id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->bind_result($DB_CHECK_ONLINE);
    $stmt->fetch();
    $stmt->close();

    if (isset($DB_CHECK_ONLINE)) {
      $this->status = $DB_CHECK_ONLINE;
    }
  }

  public function setStatus($status_in) {
    $stmt = $this->DB->GetConnection()->prepare("UPDATE checks SET ONLINE = ?  WHERE ID = ?");
    $stmt->bind_param('ii', $status_in,$this->id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->close();
  }

}

?>
