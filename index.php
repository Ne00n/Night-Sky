<?php

include 'content/configs/config.php';
include 'content/configs/regex.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
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

$auth_pages = array('main', 'group', 'contact', 'history', 'account', 'status-page', 'webhook');
foreach($auth_pages as $page) {
    if(Page::startsWith($p, $page)) {
        $Login = new Verify($DB);
        if(!$Login->isLoggedIN()) {
            exit(header("Location: /index.php"));   
        }
        break;
    }
}
                                     
include 'content/header.html';
                                     
if ($p == "login") {
  include 'content/login.php';
} elseif (Page::startsWith($p,"register")) {
  include 'content/register.php';
} elseif (Page::startsWith($p,"main")) {
  include 'content/main.php';
} elseif (Page::startsWith($p,"group")) {
  include 'content/group.php';
} elseif (Page::startsWith($p,"contact")) {
  include 'content/contact.php';
} elseif (Page::startsWith($p,"history")) {
  include 'content/history.php';
} elseif (Page::startsWith($p,"tos")) {
  include 'content/tos.php';
} elseif (Page::startsWith($p,"privacy")) {
  include 'content/privacy.php';
} elseif (Page::startsWith($p,"account")) {
  include 'content/account.php';
} elseif (Page::startsWith($p,"status-page")) {
  include 'content/status-page.php';
} elseif (Page::startsWith($p,"webhook")) {
  include 'content/webhooks.php';
} elseif ($p=="logout") {
  session_unset();
  session_destroy();
  header('Location: index.php?p=login');
}

include 'content/footer.html';

?>
