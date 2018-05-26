<?php

//Add your IP here
$Whitelist = array('');

$method = $_SERVER['REQUEST_METHOD'];
$payload = json_decode(file_get_contents('php://input'),true);
$requestIP = $_SERVER['REMOTE_ADDR'];

if ($method == 'POST' && son_last_error() === 0 && in_array($requestIP, $Whitelist)) {
	if ((filter_var($payload['ip'], FILTER_VALIDATE_IP) || filter_var($payload['ip'], FILTER_VALIDATE_URL)) && is_numeric($payload['port']) && ($payload['type'] == 'TCP' || $payload['type'] == 'HTTP')) {
		if ($payload['type'] == 'tcp') {
			$socket = @fsockopen($payload['ip'], $payload['port'], $errorNo, $errorStr, 1.0);
			if ($errorNo == 0) {
				echo json_encode(array('result' => 1,'info' => ''));
			} else {
				echo json_encode(array('result' => 0,'info' => $errorStr));
			}
		} elseif ($payload['type'] == 'http') {

		}
	}
}

?>
