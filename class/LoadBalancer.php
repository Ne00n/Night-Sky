<?php

class LoadBalancer {

  private $DB;

  public function __construct($DB) {
    $this->DB = $DB;
  }

  public function balanceCheck() {
    for ($i = 1; $i <= 25; $i = $i + 1) {

      $slot = mt_rand (1,9);

      $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM checks WHERE SLOT = ?");
      $stmt->bind_param('i', $slot);
      $stmt->execute();
      $stmt->store_result();
      if ($stmt->num_rows < _checks_limit_global) {
        return $slot;
      }
      $stmt->close();
    }
    return false;
  }
}

?>
