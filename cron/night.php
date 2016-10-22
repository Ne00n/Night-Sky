<?php

if (php_sapi_name() == 'cli') {

  include '../content/config.php';
  include '../class/AsyncMail.php';
  include '../class/Page.php';

  function dat_loader($class) {
      include '../class/' . $class . '.php';
  }

  spl_autoload_register('dat_loader');

  $DB = new Database;
  $DB->InitDB();

  $Verify = new Verify($DB);
  $C = new Contact($DB,$Verify);

  $Checks = array();

  $query = "SELECT SLOT,ID,IP,PORT,EMAIL_ID,NAME FROM checks WHERE ENABLED = 1 ORDER by ID";
  $stmt = $DB->GetConnection()->prepare($query);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {

    $C->setID($row['EMAIL_ID']);

    $Checks[$row['SLOT']][$row['ID']] = array("IP" => $row['IP'],"PORT" => $row['PORT'],"EMAIL_ID" => $row['EMAIL_ID'],"EMAIL" => $C->getEMailbyID(),"NAME" => $row['NAME']);

  }

  for ($i_out = 1; $i_out <= 6; $i_out++) {

    for ($i = 1; $i <= 10; $i++) {

        if (isset($Checks[$i])) {
          printf("Night Base\n",$i);
          $t[$i] = new CronjobBase($i,$Checks);
          $t[$i]->run();
        } else {
          printf("Night Base No Job\n",$i);
        }
        sleep(1);
    }

  }

}

?>
