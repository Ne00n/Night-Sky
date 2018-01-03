<?php

  class API {

      public function validateArray($array,&$threshold) {
        $threshold++;
        if ($threshold > 10) { echo json_encode(array('meme' => 'http.cat/400')); exit;  }
        foreach ($array as $element) {
          if(is_array($element)) {
            $this->validateArray($element,$threshold);
          } else {
            if (!is_numeric($element)) { echo json_encode(array('meme' => 'http.cat/400')); exit; }
          }
        }
      }

      public function memeCode($code,$brexit = false) {
        echo json_encode(array('meme' => 'http.cat/'.$code));
        if ($brexit == true) { exit; }
      }

  }

?>
