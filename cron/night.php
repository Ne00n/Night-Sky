<?php

if (php_sapi_name() == 'cli') {

  include '../content/configs/config.php';
  include '../content/configs/regex.php';

  function dat_loader($class) {
      include '../class/' . $class . '.php';
  }

  spl_autoload_register('dat_loader');

  $DB = new Database;
  $DB->InitDB();

  function fetchAll($DB) {

    $Checks = array();

    $query = "SELECT SLOT,ID,IP,PORT,USER_ID,NAME FROM checks WHERE ENABLED = 1 ORDER by ID";
    $stmt = $DB->GetConnection()->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $Checks[$row['SLOT']][$row['ID']] = array("NAME" => $row['NAME'],"USER_ID" => $row['USER_ID']); //Just for counting how much are in there, the Data gets pulled again later so we dont need all data, rest removed.
    }
    return $Checks;
  }

  $Verify = new Verify($DB);

  $Checks = fetchAll($DB);

  for ($i_out = 1; $i_out <= 6; $i_out++) {

    $connection = false;
    $probes = array('google.com', 'wikipedia.org', 'facebook.com');
    foreach($probes as $probe) {
      if(Page::check_page($probe)) {
        $connection = true;
        break;
      }
    }
    
    if ($connection) {
      echo "Connected to the Internet\n";

      $start = microtime(true);
      for ($i = 1; $i <= 10; $i++) {
          if (isset($Checks[$i])) {
            echo("Night Base\n");
            $CB = new CronjobBase($i,$Checks,$i_out);
            $CB->run();
          } else {
            printf("Night Base No Job\n",$i);
          }
          sleep(1);
      }
      echo microtime(true) - $start . "\n";
      echo "10 Slots done, next round"."\n";

    }

  }

}

?>
