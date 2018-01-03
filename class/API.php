<?php

  class API {

    private $DB;

    public function __construct($DB) {
      $this->DB = $DB;
    }

    public function validateArray($array,&$threshold) {
      $threshold++;
      if ($threshold > 10) { $this->memeCode('414',true);  }
      foreach ($array as $element) {
        if(is_array($element)) {
          $this->validateArray($element,$threshold);
        } else {
          if (!is_numeric($element)) { $this->memeCode('400',true); }
        }
      }
    }

    public function memeCode($code,$brexit = false) {
      echo json_encode(array('meme' => 'http.cat/'.$code));
      if ($brexit == true) { exit; }
    }

    public function tokenExist($token) {
        $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM serversToken WHERE Token = ? LIMIT 1");
        $stmt->bind_param('i', $token);
        $rc = $stmt->execute();
        $stmt->bind_result($dbID);
        $stmt->fetch();
        $stmt->close();

        if ($dbID != "") {
          return $dbID;
        } else {
          $this->memeCode('401',true);
        }

    }
  }

?>
