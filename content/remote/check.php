<?php

$Whitelist = array('');

if (isset($_GET['host'])) {
	$remote = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	if (in_array($remote, $Whitelist) AND $forward == "") {
		$host = $_GET['host'];
		list($ip, $port) = explode(":", $host);
		check($ip,$port);
	}
}

function check($ip,$port) {
	$socket = @fsockopen($ip, $port, $errorNo, $errorStr, 1.0);
	if ($errorNo == 0) {
		echo "1:success";
	} else {
		echo "0:".$errorStr;
	}
}

?>
