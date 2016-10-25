<?php

die();

include '../../content/config.php';

function dat_loader($class) {
    include '../../class/' . $class . '.php';
}

spl_autoload_register('dat_loader');

$DB = new Database;
$DB->initDB();

$name = "Test"; $password = "1234567899"; $rank = 1;

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $DB->GetConnection()->prepare("INSERT INTO users(Username,Password,Rank) VALUES (?, ?, ?)");
$stmt->bind_param('ssi', $name,$hash,$rank);
$rc = $stmt->execute();
if ( false===$rc ) { die("MySQL Error"); }
$stmt->close();

echo "okay";

 ?>
