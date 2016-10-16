<?php

include '../class/AsyncMail.php';

$mail_to_list = array('');
foreach($mail_to_list as $mail_to) {
    $asynchMail = new AsyncMail($mail_to,'Hello World','Hello');
    $asynchMail->start();
}


?>
