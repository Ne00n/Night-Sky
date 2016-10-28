<?php

if (php_sapi_name() == 'cli') {

  include '../content/config.php';
  include '../class/CronjobServ/ThreadLock.php';

  function dat_loader($class) {
      include '../class/' . $class . '.php';
  }

  spl_autoload_register('dat_loader');

  $DB = new Database;
  $DB->InitDB();

  $T = new ThreadLock($DB);
  $T->setThreadLock('backlog');

  if ($T->getThreadLock() === 0) {

    $T->setLock();

    $query = "SELECT ID,Target,Subject,Content FROM emails_backlog";
    $stmt = $DB->GetConnection()->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {

      $Mail = new Mail($row['Target'],$row['Subject'],$row['Content']);
      $Mail->run();
      if ($Mail->checkSuccess()) {

        $stmt = $DB->GetConnection()->prepare("DELETE FROM emails_backlog WHERE ID = ? LIMIT 1");
        $stmt->bind_param('i', $row['ID']);
        $rc = $stmt->execute();
        $stmt->close();

      }

    }

    $T->setUnlock();

  }

}

 ?>
