<?php

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

      $DB = new Database;
      $DB->InitDB();

      $THREAD_ID = $this->slot.'_'.$this->threadId;
      $THREAD_LOCK = 0;

      $stmt = $DB->GetConnection()->prepare("SELECT THREAD_LOCK FROM threads WHERE THREAD_ID = ?");
      $stmt->bind_param('s', $THREAD_ID);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->bind_result($THREAD_LOCK);
      $stmt->fetch();
      $stmt->close();

      if (empty($THREAD_LOCK)) {

        $THREAD_LOCK_IN = 1;

        $stmt = $DB->GetConnection()->prepare("INSERT INTO threads(THREAD_ID,THREAD_LOCK) VALUES (?,?)");
        $stmt->bind_param('si',$THREAD_ID,$THREAD_LOCK_IN);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

      }

      if ($THREAD_LOCK == 0) {

        $stmt = $DB->GetConnection()->prepare("UPDATE threads SET THREAD_LOCK = ?  WHERE THREAD_ID = ?");
        $stmt->bind_param('is', $THREAD_LOCK,$THREAD_ID);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

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

        $THREAD_LOCK = 0;

        $stmt = $DB->GetConnection()->prepare("UPDATE threads SET THREAD_LOCK = ?  WHERE THREAD_ID = ?");
        $stmt->bind_param('is', $THREAD_LOCK,$THREAD_ID);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

      }

  }

}

?>
