<?php

  class Request {

    public function createRequest($url,$method = "GET",$payload = NULL,$debug = false,$timeout = 2,$connect = 2) {
      $result = array();
      $request = curl_init();
      curl_setopt($request, CURLOPT_URL,$url);
      curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($request, CURLOPT_SSL_VERIFYPEER, true);
      curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($request, CURLOPT_CONNECTTIMEOUT ,$connect);
      curl_setopt($request, CURLOPT_TIMEOUT, $timeout);

      switch($method) {
        case 'POST':
          curl_setopt($request, CURLOPT_POST, true);
          curl_setopt($request, CURLOPT_POSTFIELDS, $payload);
        break;
      }

      if ($debug == true) {
        curl_setopt($request, CURLINFO_HEADER_OUT, true);
        curl_setopt($request, CURLOPT_CERTINFO,true);
        curl_setopt($request, CURLOPT_VERBOSE,true);
        $result['content'] = curl_exec($request);
        $result['http'] = curl_getinfo($request, CURLINFO_HTTP_CODE);
        $result['totaltime'] = curl_getinfo($request, CURLINFO_TOTAL_TIME);
        $result['info'] = curl_getinfo($request);
        curl_close($request);
        return $result;
      } else {
        $result['content'] = curl_exec($request);
        $result['totaltime'] = curl_getinfo($request, CURLINFO_TOTAL_TIME);
        $result['http'] = curl_getinfo($request, CURLINFO_HTTP_CODE);
        curl_close($request);
        return $result;
      }

    }

  }

?>
