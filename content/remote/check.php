<?php

if (isset($_GET['host'])) {
	$remote = getenv($_SERVER['REMOTE_ADDR']);
	$forward = getenv($_SERVER['HTTP_X_FORWARDED_FOR']);
	if ($remote == "IP" AND $forward == "") {
		$host = $_GET['host'];
		list($ip, $port) = explode(":", $host);
		check($ip,$port);
	}
}

function check($ip,$port) {
	$socket = @fsockopen($ip, $port, $errorNo, $errorStr, 1.5);
	if ($errorNo == 0) {
		echo "1:success";
	} else {
		echo "0:".$errorStr;
	}
}

?>
