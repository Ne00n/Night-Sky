<?php

if (php_sapi_name() == 'cli') {

  include '../content/config.php';

  function dat_loader($class) {
      include '../class/' . $class . '.php';
  }

  spl_autoload_register('dat_loader');

  $DB = new Database;
  $DB->InitDB();

  $R = new Remote($DB);
  $R->checkRemote();

}

?>
