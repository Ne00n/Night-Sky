 <?php

include 'CronjobServ/ThreadLock.php';
include 'CronjobServ/Status.php';
include 'CronjobServ/CheckServ.php';

class CronjobServ {

  private $slot;
  private $threadId;
  private $Check;
  private $Remote;
  private $error;

  public function __construct($in_slot,$in_threadId,$in_Check,$in_Remote)
  {
      $this->threadId = $in_threadId;
      $this->slot = $in_slot;
      $this->Check = $in_Check;
      $this->Remote = $in_Remote;
  }

  public function run()
  {

      #Create a new Database Object
      $DB = new Database;
      $DB->InitDB();

      #Create a new Status Object
      $S = new Status($DB);

      #Create a new Checkserv Object
      $CS = new Checkserv($this->Remote);

      #Create a new ThreadLock Object
      $T = new ThreadLock($DB);

      #Generate the ThreadID
      $THREAD_ID = $this->slot.'_'.$this->threadId;
      $THREAD_LOCK = 0;

      #Set ThreadID
      $T->setThreadLock($THREAD_ID);

      #Check if Thread is locked
      if ($T->getThreadLock() === 0) {

        #Lock the Thread
        $T->setLock();

        foreach ($this->Check as $key => $element) {

          $CS->checkAvailability($element['IP'],$element['PORT']);

          $S->setID($key);
          $S->getOnlineStatus();

          if ($CS->getStatus() === false) {

            //Online => Offline
            if ($S->getStatus() === 1) {

              $S->setStatus(0);

              $time = time();

              $email = 'Server '.Page::escape($element['NAME']).' went offline. Detected: '.date("d.m.Y H:i:s",Page::escape($time));
              $email .= "\n\n";
              foreach ($CS->getStatusDetail() as $serv => $elementary) {
                $email .= $elementary['Location'].": ".$elementary['Reason']."\n";
              }

              $Mail = new Mail($element['EMAIL'],'Night-Sky - Downtime Alert '.Page::escape($element['NAME']),$email);
              $Mail->run();

              $H = new History($DB);
              $H->addHistory($element['USER_ID'],$key,0);

            //Still Offine
            } elseif ($S->getStatus() === 0) {



            }

          } elseif ($CS->getStatus() === true) {

            //Still Online
            if ($S->getStatus() === 1) {



            //Offline => Online
            } elseif ($S->getStatus() === 0) {

              $S->setStatus(1);

              $time = time();
              $Mail = new Mail($element['EMAIL'],'Night-Sky - Uptime Alert '.Page::escape($element['NAME']),'Server '.Page::escape($element['NAME']).' is back Online. Detected: '.date("d.m.Y H:i:s",Page::escape($time)));
              $Mail->run();

              $H = new History($DB);
              $H->addHistory($element['USER_ID'],$key,1);

            }

          }

        }

        #Unlock the Thread
        $T->setUnlock();

      }

  }

}

?>
