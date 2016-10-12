<?php

include 'content/header.html';
include 'content/config.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
}

spl_autoload_register('dat_loader');
session_start();

if (isset($_GET["p"])) {
  $p = $_GET["p"];
}

if(!isset($p)) {
  $p="login";
}

$DB = new Database;
$DB->InitDB();

if ($p == "login") {
  include 'content/login.php';
}

if ($p == "main") {
  include 'content/main.php';
}

include 'content/footer.html';

?>
