<?php

include 'content/configs/config.php';
include 'content/configs/regex.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
}

function require_auth() {
    $Login = new Verify($DB);
    if(!$Login->isLoggedIN()) {
        exit(header("Location: /index.php?p=login"));   
    }
}

spl_autoload_register('dat_loader');

session_start();

#CSRF token
if (!isset($_SESSION['Token'])) {
    $_SESSION['Token'] = bin2hex(random_bytes(40));
}

$p = 'login';

if (isset($_GET["p"])) {
  $p = $_GET["p"];
} elseif (isset($_GET["key"])) {
  $k = $_GET["key"];
}

$DB = new Database;
$DB->InitDB();
                                     
include 'content/header.html';
                                     
if ($p == "login") {
  include 'content/login.php';
} elseif (Page::startsWith($p,"register")) {
  include 'content/register.php';
} elseif (Page::startsWith($p,"main")) {
  require_auth();
  include 'content/main.php';
} elseif (Page::startsWith($p,"group")) {
  require_auth();
  include 'content/group.php';
} elseif (Page::startsWith($p,"contact")) {
  require_auth();
  include 'content/contact.php';
} elseif (Page::startsWith($p,"history")) {
  require_auth();
  include 'content/history.php';
} elseif (Page::startsWith($p,"tos")) {
  include 'content/tos.php';
} elseif (Page::startsWith($p,"privacy")) {
  include 'content/privacy.php';
} elseif (Page::startsWith($p,"account")) {
  require_auth();
  include 'content/account.php';
} elseif (Page::startsWith($p,"status-page")) {
  require_auth();
  include 'content/status-page.php';
} elseif (Page::startsWith($p,"webhook")) {
  require_auth();
  include 'content/webhooks.php';
} elseif ($p=="logout") {
  require_auth();
  session_unset();
  session_destroy();
  header('Location: index.php?p=login');
}

include 'content/footer.html';

?>
