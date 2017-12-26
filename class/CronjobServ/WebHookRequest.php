<?php

class WebHookRequest {

  public function run($url,$payload,$headers) {
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL,$url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 2);

    if ($payload != "") {
      curl_setopt($request, CURLOPT_POST, true);
      curl_setopt($request, CURLOPT_POSTFIELDS, $payload);
    }

    if ($headers != "") {
      curl_setopt($request, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
    }

    $result =  curl_exec($request);
    $httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    $result = json_decode($result,true);

    if (json_last_error() === 0 && $httpcode == 200) {
      return $result;
    } else {
      return false;
    }

  }

}

?>
