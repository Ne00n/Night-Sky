<?php

class CronjobBase {

  private $DB;
  private $threadId;
  private $Checks;

  public function __construct($threadId,$Checks)
  {
      $this->threadId = $threadId;
      $this->Checks = $Checks;
  }

  public function run()
  {

      printf("Cronjob Base\n",$this->threadId);
      $start = microtime(true);

      for ($i = 0; $i <= count($this->Checks[$this->threadId]); $i = $i +5) {

        $Check_Thread = array_slice($this->Checks[$this->threadId], $i, $i +5, true);

        $t[$i] = new CronjobServ($this->threadId,$i,$Check_Thread);
        $t[$i]->start();

      }

      echo "CronjobBase end\n";
      echo microtime(true) - $start . "\n";

  }

}

?>
