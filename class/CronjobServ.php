 <?php

include 'CronjobServ/ThreadLock.php';
include 'CronjobServ/Status.php';
include 'CronjobServ/CheckServ.php';
include 'CronjobServ/WebHookRequest.php';

class CronjobServ {

  private $slot;
  private $threadId;
  private $Check;
  private $Remote;
  private $time;
  private $error;

  public function __construct($in_slot,$in_threadId,$in_Check,$in_Remote,$in_Time) {
      $this->threadId = $in_threadId;
      $this->slot = $in_slot;
      $this->Check = $in_Check;
      $this->Remote = $in_Remote;
      $this->time = $in_Time;
  }

  public function run() {
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

        $interval['1'] = array(10,30,60); //0
        $interval['2'] = array(10,20); //10
        $interval['3'] = array(10); //20...
        $interval['4'] = array(10,20,30);
        $interval['5'] = array(10);
        $interval['6'] = array(10,20);

        foreach ($this->Check as $key => $element) {

          //Check if we need to run in case its a different interval
          if (in_array($element['INTERVAL'], $interval[$this->time])) {

            $CS->checkAvailability($element['IP'],$element['PORT']);

            $S->setID($key);
            $S->getOnlineStatus();

            if ($CS->getStatus() === false) {

              //Online => Offline
              if ($S->getStatus() === 1) {

                $S->setStatus(0);

                $time = time();

                //Mail
                $email = 'Server '.Page::escape($element['NAME']).' went offline. Detected: '.date("d.m.Y H:i:s",Page::escape($time));
                $email .= "\n\n";
                foreach ($CS->getStatusDetail() as $serv => $elementary) {
                  $email .= $elementary['Location'].": ".$elementary['Reason']."\n";
                }

                if (!empty($element['EMAIL'])) {
                  foreach($element['EMAIL'] as $mail)
                  {
                    $Mail = new Mail($mail,'Night-Sky - Downtime Alert '.Page::escape($element['NAME']),$email);
                    $Mail->run();
                  }
                }

                //WebHook
                if (!empty($element['WEBHOOK'])) {
                  foreach($element['WEBHOOK'] as $webhook)
                  {
                    $WH = new WebHookRequest();
                    $WH->run($webhook['urlDown'],$webhook['method'],$webhook['jsonDown'],$webhook['headersDown']);
                  }
                }

                //History
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

                //Mail
                if (!empty($element['EMAIL'])) {
                  foreach($element['EMAIL'] as $mail)
                  {
                    $Mail = new Mail($mail,'Night-Sky - Uptime Alert '.Page::escape($element['NAME']),'Server '.Page::escape($element['NAME']).' is back Online. Detected: '.date("d.m.Y H:i:s",Page::escape($time)));
                    $Mail->run();
                  }
                }

                //WebHook
                if (!empty($element['WEBHOOK'])) {
                  foreach($element['WEBHOOK'] as $webhook)
                  {
                    $WH = new WebHookRequest();
                    $WH->run($webhook['urlUp'],$webhook['method'],$webhook['jsonUp'],$webhook['headersUp']);
                  }
                }

                //History
                $H = new History($DB);
                $H->addHistory($element['USER_ID'],$key,1);

              }

            }

         } else {
           echo "Skipped Check for Server: ".$element['NAME'].", interval is set to ".$element['INTERVAL']. " but currently we are at ".($this->time -1)."0"."\n";
         }

        }

        #Unlock the Thread
        $T->setUnlock();

      }
  }

}

?>
