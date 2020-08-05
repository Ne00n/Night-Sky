<?php
//Add your IP here
$Whitelist = array('');

function createRequest($url,$timeout = 1,$connect = 1) {
	$result = array();
	$request = curl_init();
	curl_setopt($request, CURLOPT_URL,$url);
	curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($request, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($request, CURLOPT_CONNECTTIMEOUT ,$connect);
	curl_setopt($request, CURLOPT_TIMEOUT, $timeout);
	$result['content'] = curl_exec($request);
	$result['http'] = curl_getinfo($request, CURLINFO_HTTP_CODE);
	curl_close($request);
	return $result;
}


$method = $_SERVER['REQUEST_METHOD'];
$payload = json_decode(file_get_contents('php://input'),true);
$requestIP = $_SERVER['REMOTE_ADDR'];

if ($method == 'POST' && json_last_error() === 0 && in_array($requestIP, $Whitelist)) {
	if ((filter_var($payload['ip'], FILTER_VALIDATE_IP) || filter_var($payload['ip'], FILTER_VALIDATE_DOMAIN)) && is_numeric($payload['port']) && ($payload['type'] == 'tcp' || $payload['type'] == 'http')) {
		if ($payload['type'] == 'tcp') {
			if (filter_var($payload['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$socket = @fsockopen("[".$payload['ip']."]", $payload['port'], $errorNo, $errorStr, $payload['timeout']);
			} else {
				$socket = @fsockopen($payload['ip'], $payload['port'], $errorNo, $errorStr, $payload['timeout']);
			}
			if ($errorNo == 0) { echo json_encode(array('result' => 1,'info' => '')); } else { echo json_encode(array('result' => 0,'info' => $errorStr)); }
		} elseif ($payload['type'] == 'http') {
			$response = createRequest($payload['ip'].":".$payload['port'],$payload['timeout'],$payload['connect']);
			echo json_encode(array('http' => $response['http']));
		}
	}
}

?>
