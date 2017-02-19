<?php

  if (php_sapi_name() == 'cli') {

    include '../content/config.php';

    function dat_loader($class) {
        include '../class/' . $class . '.php';
    }

    spl_autoload_register('dat_loader');

    function fetchAll($DB,$C) {

      $Checks = array();

      $query = "SELECT SLOT,ID,IP,PORT,EMAIL_ID,USER_ID,NAME FROM checks WHERE ENABLED = 1 ORDER by ID";
      $stmt = $DB->GetConnection()->prepare($query);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {

        $C->setID($row['EMAIL_ID']);

        $Checks[$row['SLOT']][$row['ID']] = array("IP" => $row['IP'],"PORT" => $row['PORT'],"EMAIL_ID" => $row['EMAIL_ID'],"EMAIL" => $C->getEMailbyID(),"NAME" => $row['NAME'],
        "USER_ID" => $row['USER_ID']);

      }
      return $Checks;
    }

    $DB = new Database;
    $DB->InitDB();

    $R = new Remote($DB);
    $Remote = $R->getRemote();

    $Verify = new Verify($DB);
    $C = new Contact($DB,$Verify);

    $options = getopt("T:I:");

    $threadID = $options['T'];
    $i = $options['I'];

    $Checks = fetchAll($DB,$C);

    $Check_Thread = array_slice($Checks[$threadID], $i, $i +5, true);

    $CS = new CronjobServ($threadID,$i,$Check_Thread,$Remote);
    $CS->run();

  }

?>
