<?php

include 'content/configs/config.php';
include 'content/configs/regex.php';

spl_autoload_register(function ($class) { include 'class/'.$class . '.php'; });

$DB = new Database;
$DB->InitDB();
$API = new API($DB);

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];
$data = json_decode(file_get_contents('php://input'),true);

$threshold = 0;

if ($method == 'POST' && isset($data['token']) && isset($data['cpu']) && isset($data['network']) && isset($data['memory']) && isset($data['swap']) && isset($data['disk'])) {
  //Validating Token
  if(!preg_match(_regex_TOKEN,$data['token'])){ $API->memeCode('400',true); }
  $token = $data['token'];
  unset($data['token']);
  //Validating the Payload
  $API->validateArray($data,$threshold);
  //Validating the Token with our DB
  $id = $API->tokenExist($token);
  //Feed our DB
  #var_dump($data);

  $API->memeCode('200');
} else {
  $API->memeCode('400',true);
}

?>
