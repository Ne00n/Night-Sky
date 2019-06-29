<?php

if (php_sapi_name() == 'cli') {

  $options = getopt("T:I:W:");

  function dat_loader($class) {
      include '../class/' . $class . '.php';
  }

  spl_autoload_register('dat_loader');

  $Runner = new Runner($options);
  $Runner->run();

}

?>
