<?php

class Page {

  public static function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
  }

  public static function randomPassword() {
    return bin2hex(random_bytes(12));
  }

  public static function escape($text) {
    return htmlspecialchars($text,ENT_QUOTES);
  }

  public static function isJson($string) {
   json_decode($string);
   return (json_last_error() == JSON_ERROR_NONE);
  }

  public static function check_page($url) {
  	$socket = @fsockopen($url, 80, $errorNo, $errorStr, 1.5);
  	if ($errorNo == 0) {
  		return true;
  	} else {
  		return false;
  	}
  }

}

?>
