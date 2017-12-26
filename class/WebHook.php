<?php

class WebHook {

  private $DB;
  private $Verify;
  private $error;
  private $id;
  private $name;
  private $group;
  private $urlDown;
  private $jsonDown;
  private $headersDown;
  private $urlUp;
  private $jsonUp;
  private $headersUp;

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addHook($name,$urlDown,$jsonDown,$headersDown,$urlUp,$jsonUp,$headersUp,$group) {
    if(!preg_match(_regex_NAME,$name)){ $this->error = "The Name contains invalid letters.";}
    if (strlen($name) > _max_Name) {$this->error = "The Name is to long";}
    if (strlen($name) < _min_Name) {$this->error = "The Name is to short";}
    if (strlen($urlDown) > _max_URL OR strlen($urlDown) < _min_URL) {$this->error = "The length of the URL should be between "._min_URL." and "._max_URL.".";}
    if (strlen($urlUp) > _max_URL OR strlen($urlUp) < _min_URL) {$this->error = "The length of the URL should be between "._min_URL." and "._max_URL.".";}
    if (!filter_var($urlDown, FILTER_VALIDATE_URL) OR !filter_var($urlUp, FILTER_VALIDATE_URL)  ) { $this->error = "The URL is invalid."; }

    if ($jsonUp != "" OR $jsonDown != "") {
      if (!Page::isJson($jsonUp) OR !Page::isJson($jsonDown)) { $this->error = 'Invalid JSON.'; }
    }

    if ($headersUp != "" OR $headersDown != "") {
      if(!preg_match(_regex_HEADERS,$headersDown) OR !preg_match(_regex_HEADERS,$headersUp)){ $this->error = "Invalid Headers.";}
    }

    if (!$this->checkLimit()) { $this->error = "Limit reached";}

    $GR = new Group($this->DB,$this->Verify);
    if ($GR->checkGroupID($group) === false) { $this->error = "Invalid Group"; }

    if ($this->error == "") {

      $userID = $this->Verify->getUserID();

      preg_match(_regex_HEADERS,$headersDown,$headersDown);
      $headersDownOut = "";
      foreach ($headersDown as $element) {
        $headersDownOut .= $element;
      }

      preg_match(_regex_HEADERS,$headersUp,$headersUp);
      $headersUpOut = "";
      foreach ($headersUp as $element) {
        $headersUpOut .= $element;
      }

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO webhooks(UserID,GroupID,name,urlDown,jsonDown,headersDown,urlUp,jsonUp,headersUP) VALUES (?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param('iisssssss',$userID, $group,$name,$urlDown,$jsonDown,$headersDownOut,$urlUp,$jsonUp,$headersUpOut);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function editHook($name,$urlDown,$jsonDown,$headersDown,$urlUp,$jsonUp,$headersUp,$group) {
    if(!preg_match(_regex_NAME,$name)){ $this->error = "The Name contains invalid letters.";}
    if (strlen($name) > _max_Name) {$this->error = "The Name is to long";}
    if (strlen($name) < _min_Name) {$this->error = "The Name is to short";}
    if (strlen($urlDown) > _max_URL OR strlen($urlDown) < _min_URL) {$this->error = "The length of the URL should be between "._min_URL." and "._max_URL.".";}
    if (strlen($urlUp) > _max_URL OR strlen($urlUp) < _min_URL) {$this->error = "The length of the URL should be between "._min_URL." and "._max_URL.".";}
    if (!filter_var($urlDown, FILTER_VALIDATE_URL) OR !filter_var($urlUp, FILTER_VALIDATE_URL)  ) { $this->error = "The URL is invalid."; }

    if ($jsonUp != "" OR $jsonDown != "") {
      if (!Page::isJson($jsonUp) OR !Page::isJson($jsonDown)) { $this->error = 'Invalid JSON.'; }
    }

    if ($headersUp != "" OR $headersDown != "") {
      if(!preg_match(_regex_HEADERS,$headersDown) OR !preg_match(_regex_HEADERS,$headersUp)){ $this->error = "Invalid Headers.";}
    }

    $GR = new Group($this->DB,$this->Verify);
    if ($GR->checkGroupID($group) === false) { $this->error = "Invalid Group"; }

    if ($this->error == "") {

      $userID = $this->Verify->getUserID();

      preg_match(_regex_HEADERS,$headersDown,$headersDown);
      $headersDownOut = "";
      foreach ($headersDown as $element) {
        $headersDownOut .= $element;
      }

      preg_match(_regex_HEADERS,$headersUp,$headersUp);
      $headersUpOut = "";
      foreach ($headersUp as $element) {
        $headersUpOut .= $element;
      }

      $stmt = $this->DB->GetConnection()->prepare("UPDATE webhooks SET GroupID = ?,name = ?,urlDown = ?,jsonDown = ?,headersDown = ?,urlUp = ?,jsonUp = ?,headersUP = ? WHERE ID = ?");
      $stmt->bind_param('isssssssi',$group,$name,$urlDown,$jsonDown,$headersDownOut,$urlUp,$jsonUp,$headersUpOut,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }

  }

  public function removeHook() {
    if ($this->error == "") {

        $stmt = $this->DB->GetConnection()->prepare("DELETE FROM webhooks WHERE ID = ?");
        $stmt->bind_param('i', $this->id);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

    }
  }

  public function checkHookID($id) {
    if(!preg_match(_regex_ID,$id)){ return false;}

    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM webhooks WHERE ID = ? AND UserID = ? LIMIT 1");
    $stmt->bind_param('ii', $id,$user_id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();

    if (isset($result)) {
      return true;
    } else {
      return false;
     }
   }

   public function getData() {
     if ($this->error == "") {

       $stmt = $this->DB->GetConnection()->prepare("SELECT GroupID,name,urlDown,jsonDown,headersDown,urlUp,jsonUp,headersUP FROM webhooks WHERE ID = ? LIMIT 1");
       $stmt->bind_param('i', $this->id);
       $rc = $stmt->execute();
       if ( false===$rc ) { $this->error = "MySQL Error"; }
       $stmt->bind_result($dbGroupID,$dbName,$dbUrlDown,$dbJsonDown,$dbHeadersDown,$dbUrlUp,$dbJsonUp,$dbHeadersUp);
       $stmt->fetch();
       $stmt->close();

       $this->group = $dbGroupID;
       $this->name = $dbName;
       $this->urlDown = $dbUrlDown;
       $this->jsonDown = $dbJsonDown;
       $this->headersDown = $dbHeadersDown;
       $this->urlUp = $dbUrlUp;
       $this->jsonUp = $dbJsonUp;
       $this->headersUp = $dbHeadersUp;
     }
   }

  public function setID($id) {
      if ($this->checkHookID($id) === true) {
        $this->id = $id;
      } else {
        $this->error = "Invalid ID";
      }
  }

  public function checkLimit() {
    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM webhooks WHERE UserID = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < $this->Verify->getWebHookLimit()) {
      return true;
    }
    $stmt->close();
  }

  public function resetError() {
    $this->error = NULL;
  }

  public function getName() {
    return $this->name;
  }

  public function getGroupID() {
    return $this->group;
  }

  public function getUrlDown() {
      return $this->urlDown;
  }

  public function getJsonDown() {
    return $this->jsonDown;
  }

  public function getHeadersDown() {
    return $this->headersDown;
  }

  public function getUrlUp() {
    return $this->urlUp;
  }

  public function getJsonUp() {
    return $this->jsonUp;
  }

  public function getHeadersUp() {
    return $this->headersUp;
  }

  public function getLastError() {
    return $this->error;
  }

}

?>
