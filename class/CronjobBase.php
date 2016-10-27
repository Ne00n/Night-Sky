<?php

class CronjobBase {

  private $threadId;
  private $Checks;
  private $Remote;

  public function __construct($in_threadId,$in_Checks,$in_Remote)
  {
      $this->threadId = $in_threadId;
      $this->Checks = $in_Checks;
      $this->Remote = $in_Remote;
  }

  public function run()
  {

      printf("Cronjob Base\n",$this->threadId);
      $start = microtime(true);

      #Go throught the Servers which are in our Current Slot, and put 5 into each Thread
      for ($i = 0; $i <= count($this->Checks[$this->threadId]); $i = $i +5) {

        #Slice 5 Servers from the Main Array to a Secondary array
        $Check_Thread = array_slice($this->Checks[$this->threadId], $i, $i +5, true);

        #Create a new Thread and pass our stuff to it
        $t[$i] = new CronjobServ($this->threadId,$i,$Check_Thread,$this->Remote);

        #Launch it
        $t[$i]->start();

      }

      echo "CronjobBase end\n";
      echo microtime(true) - $start . "\n";

  }

}

?>
