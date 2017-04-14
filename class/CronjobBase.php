<?php

class CronjobBase {

  private $threadId;
  private $Checks;
  private $time;

  public function __construct($in_threadId,$in_Checks,$in_Time) {
      $this->threadId = $in_threadId;
      $this->Checks = $in_Checks;
      $this->time = $in_Time;
  }

  public function run() {
      printf("Cronjob Base\n",$this->threadId);
      $start = microtime(true);

      #Go throught the Servers which are in our Current Slot, and put 5 into each Thread
      for ($i = 0; $i <= count($this->Checks[$this->threadId]); $i = $i +5) {
        BackgroundProcess::startProcess("/usr/bin/php Runner.php -T ".$this->threadId." -I ".$i." -W ".$this->time);
      }

      echo "CronjobBase end\n";
      echo microtime(true) - $start . "\n";
  }

}

?>
