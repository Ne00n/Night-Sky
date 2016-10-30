<?php

if (php_sapi_name() == 'cli') {

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

  include '../content/config.php';
  include '../class/AsyncMail.php';
  include '../class/Page.php';
  include '../class/History.php';

  function dat_loader($class) {
      include '../class/' . $class . '.php';
  }

  spl_autoload_register('dat_loader');

  $DB = new Database;
  $DB->InitDB();

  $Verify = new Verify($DB);
  $C = new Contact($DB,$Verify);

  $R = new Remote($DB);
  $Remote = $R->getRemote();

  for ($i_out = 1; $i_out <= 6; $i_out++) {

    $start = microtime(true);
    for ($i = 1; $i <= 10; $i++) {

        $Checks = fetchAll($DB,$C);

        if (isset($Checks[$i])) {
          printf("Night Base\n",$i);
          $t[$i] = new CronjobBase($i,$Checks,$Remote);
          $t[$i]->run();
        } else {
          printf("Night Base No Job\n",$i);
        }
        sleep(1);
    }
    echo microtime(true) - $start . "\n";
    echo "10 Slots done, next round"."\n";
  }

}

?>
