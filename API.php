<?php

include 'content/configs/config.php';
include 'content/configs/regex.php';

spl_autoload_register(function ($class) { include 'class/'.$class . '.php'; });

$API = new API();

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];
$data = json_decode(file_get_contents('php://input'),true);

$threshold = 0;

if ($method == 'POST' && isset($data['token'])) {
  //Validating Token
  if(!preg_match(_regex_TOKEN,$data['token'])){ $API->memeCode('400',true); }
  $token = $data['token'];
  unset($data['token']);
  //Validating the Payload
  $API->validateArray($data,$threshold);
  //Validating the Token with our DB
  
  //Feed our DB


  $API->memeCode('200');
} else {
  $API->memeCode('400',true);
}

?>
