<?php

function dat_loader($class) {
    include '../../class/' . $class . '.php';
}

spl_autoload_register('dat_loader');

$pw = Page::randomPassword();

echo "Password: ".$pw;
echo "Hash: ".password_hash($pw, PASSWORD_DEFAULT);

 ?>
