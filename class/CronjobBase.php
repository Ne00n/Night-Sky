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

      for ($i = 0; $i <= count($this->Checks[$this->threadId]); $i = $i +5) {

        $Check_Thread = array_slice($this->Checks[$this->threadId], $i, $i +5, true);

        $t[$i] = new CronjobServ($this->threadId,$i,$Check_Thread);
        $t[$i]->start();

      }

    //  $start = microtime(true);
    //  for ($i = 1; $i <= 10; $i++) {
  //        $t[$i] = new CronjobServ($i);
    //      $t[$i]->start();
          //$t[$i]->join();
    //  }
    //  echo microtime(true) - $start . "\n";
      echo "CronjobBase end\n";


  }

}

?>
