<?php

include 'content/header.html';
include 'content/config.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
}

spl_autoload_register('dat_loader');

session_start();

#CSRF token
if (!isset($_SESSION['Token'])) {
    $_SESSION['Token'] = bin2hex(random_bytes(40));
}

if (isset($_GET["p"])) {
  $p = $_GET["p"];
} elseif (isset($_GET["key"])) {
  $k = $_GET["key"];
}

if(!isset($p)) {
  $p="login";
}

$DB = new Database;
$DB->InitDB();

if ($p == "login") {
  include 'content/login.php';
}

if (Page::startsWith($p,"register")) {
  include 'content/register.php';
}

if (Page::startsWith($p,"main")) {
  include 'content/main.php';
}

if (Page::startsWith($p,"group")) {
  include 'content/group.php';
}

if (Page::startsWith($p,"contact")) {
  include 'content/contact.php';
}

if (Page::startsWith($p,"history")) {
  include 'content/history.php';
}

if (Page::startsWith($p,"tos")) {
  include 'content/tos.php';
}

if (Page::startsWith($p,"privacy")) {
  include 'content/privacy.php';
}

if (Page::startsWith($p,"account")) {
  include 'content/account.php';
}

if ($p=="logout") {
  session_unset();
  session_destroy();
  header('Location: index.php?p=login');
}

include 'content/footer.html';

?>
