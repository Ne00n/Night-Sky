<?php

class ThreadLock {

  private $DB;
  private $THREAD_LOCK = 0;
  private $THREAD_ID = "";
  private $error;

  public function __construct($DB) {
      $this->DB = $DB;
  }

  public function setThreadLock($THREAD_ID_IN) {
    $this->THREAD_ID = $THREAD_ID_IN;

    $stmt = $this->DB->GetConnection()->prepare("SELECT THREAD_LOCK FROM threads WHERE THREAD_ID = ?");
    $stmt->bind_param('s', $THREAD_ID_IN);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = $stmt->error; }
    $stmt->bind_result($DB_THREAD_LOCK);
    $stmt->fetch();
    $stmt->close();

    if (isset($DB_THREAD_LOCK)) {
      $this->THREAD_LOCK = $DB_THREAD_LOCK;
    } else {

      $THREAD_LOCK_IN = 1;

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO threads(THREAD_ID,THREAD_LOCK) VALUES (?,?)");
      $stmt->bind_param('si',$THREAD_ID_IN,$THREAD_LOCK_IN);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = $stmt->error; }
      $stmt->close();

    }

    #Check if we got any MySQL errors, if Yes, we lock the Thread
    if (!empty($this->error)) {
      $this->THREAD_LOCK = 1;
    }
  }

  public function setLock() {
    $THREAD_LOCK = 1;

    $stmt = $this->DB->GetConnection()->prepare("UPDATE threads SET THREAD_LOCK = ?  WHERE THREAD_ID = ?");
    $stmt->bind_param('is', $THREAD_LOCK,$this->THREAD_ID);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->close();
  }

  public function setUnlock() {
    $THREAD_LOCK = 0;

    $stmt = $this->DB->GetConnection()->prepare("UPDATE threads SET THREAD_LOCK = ?  WHERE THREAD_ID = ?");
    $stmt->bind_param('is', $THREAD_LOCK,$this->THREAD_ID);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->close();
  }

  public function getThreadLock() {
    return $this->THREAD_LOCK;
  }

}

?>
