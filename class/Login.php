<?php

class Login {

  private $DB;
  private $error;

  public function __construct($DB) {
    $this->DB = $DB;
  }

  public function check_blocked_ip($ip_remote) {

    if (!$this->isValidIP($ip_remote)) { $ip_remote = 0; }

    $time = time();
    $query = "SELECT `id` FROM `login_blacklist` WHERE (ip_remote = ?) AND timestamp_expires > ? ";

    if ($stmt = $this->DB->GetConnection()->prepare($query)){

            $stmt->bind_param("si",$ip_remote,$time);

            if($stmt->execute()){
                $stmt->store_result();

                $check= "";
                $stmt->bind_result($check);
                $stmt->fetch();

                if ($stmt->num_rows >= 3){
                  return true;
                } else {
                  return false;
                }
            }
        }
  }

    public function addtoBlacklist($ip_remote) {

      if (!$this->isValidIP($ip_remote)) { $ip_remote = 0; }

      $timestamp = time();
      $expires = strtotime('+30 minutes', $timestamp);

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO login_blacklist(ip_remote,timestamp,timestamp_expires) VALUES (?, ?, ?)");
      $stmt->bind_param('sii', $ip_remote,$timestamp,$expires);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }

    public function isValidIP($ip) {
      return filter_var($ip,  FILTER_VALIDATE_IP);
    }

}

?>
