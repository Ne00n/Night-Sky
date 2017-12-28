<?php

class WebHookRequest {

  public function run($url,$method,$payload,$headers) {
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL,$url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);

    if ($payload != "") {
      curl_setopt($request, CURLOPT_POSTFIELDS, $payload);
    }

    if ($headers != "") {

      $headersArray = array();

      $slices = explode(",", $headers);
      //When the user uses a single element the slice wont work
      if (empty($slices)) {
        $headers = str_replace('"', "", $headers);
        array_push($headersArray, $headers);
      } else {
        foreach ($slices as $element) {
          $element = str_replace('"', "", $element);
          array_push($headersArray, $element);
        }
      }

      curl_setopt($request, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($request, CURLOPT_HTTPHEADER, $headersArray);
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
