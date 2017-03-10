<?php

die();

include 'content/config.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
}

spl_autoload_register('dat_loader');

$DB = new Database;
$DB->InitDB();

if (php_sapi_name() == 'cli') {

$text = "Hey,

Today we will deploy a new Update to Night Sky, which will remove the link between the Contacts and Alerts.
Since we are changing everything to Groups, the old links will stop working and you will not get any alerts anymore.

You will NOT lose any Contacts or Checks, just the connection inbetween.

So please login to your Account today at 18 o'clock CET and recreate the Link with a Group between your Alerts and Contacts.

We also added new features like the Status Page and increased the Checks limit to 20 instead 10, check it out!

Night Sky.";


$query = "SELECT EMail FROM emails WHERE Status = 1 ORDER by ID DESC";
$stmt = $DB->GetConnection()->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $Mail = new Mail($row['EMail'],"Night-Sky Notification Changes",$text);
  $Mail->run();
}

}

?>
