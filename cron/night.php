<?php

if (php_sapi_name() == 'cli') {

  function dat_loader($class) {
      include '../class/' . $class . '.php';
  }

  spl_autoload_register('dat_loader');

  $Night = new Night();
  $Night->run();
  $Night->checkStuckThreads();

}

?>
