 <?php

include 'CronjobServ/ThreadLock.php';

class CronjobServ extends Thread {

  private $slot;
  private $threadId;
  private $Check;
  private $error;

  public function __construct($slot,$threadId,$Check)
  {
      $this->threadId = $threadId;
      $this->slot = $slot;
      $this->Check = $Check;
  }

  public function run()
  {

      #Create a new Database Object
      $DB = new Database;
      $DB->InitDB();

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
        $T->setThreadLock();

        foreach ($this->Check as $key => $element) {
          $fp = fsockopen($element['IP'],$element['PORT'], $errno, $errstr, 1.5);

          $stmt = $DB->GetConnection()->prepare("SELECT ONLINE FROM checks WHERE ID = ?");
          $stmt->bind_param('s', $key);
          $rc = $stmt->execute();
          if ( false===$rc ) { $this->error = "MySQL Error"; }
          $stmt->bind_result($THREAD_ONLINE);
          $stmt->fetch();
          $stmt->close();

          if (!$fp) {

            //Online => Offline
            if ($THREAD_ONLINE == 1) {

              $ONLINE = 0;

              $stmt = $DB->GetConnection()->prepare("UPDATE checks SET ONLINE = ?  WHERE ID = ?");
              $stmt->bind_param('ii', $ONLINE,$key);
              $rc = $stmt->execute();
              if ( false===$rc ) { $this->error = "MySQL Error"; }
              $stmt->close();

              $time = time();
              $asynchMail = new AsyncMail($element['EMAIL'],'Night-Sky - Downtime Alert '.page::escape($element['NAME']),'Server '.page::escape($element['NAME']).' went offline. Detected: '.date("d.m.Y H:i:s",page::escape($time)));
              $asynchMail->start();

            //Still Offine
            } elseif ($THREAD_ONLINE == 0) {



            }

          } else {

            //Still Online
            if ($THREAD_ONLINE == 1) {



            //Offline => Online
            } elseif ($THREAD_ONLINE == 0) {

              $ONLINE = 1;

              $stmt = $DB->GetConnection()->prepare("UPDATE checks SET ONLINE = ?  WHERE ID = ?");
              $stmt->bind_param('ii', $ONLINE,$key);
              $rc = $stmt->execute();
              if ( false===$rc ) { $this->error = "MySQL Error"; }
              $stmt->close();

              $time = time();
              $asynchMail = new AsyncMail($element['EMAIL'],'Night-Sky - Uptime Alert '.page::escape($element['NAME']),'Server '.page::escape($element['NAME']).' is back Online. Detected: '.date("d.m.Y H:i:s",page::escape($time)));
              $asynchMail->start();

            }

          }

        }

        #Unlock the Thread
        $T->setUnlock();

      }

  }

}

?>
