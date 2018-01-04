<?php

class Server {

  private $DB;
  private $Verify;
  private $error;
  private $id;
  private $name;
  private $group;

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addServer($name,$group) {
    if(!preg_match(_regex_NAME,$name)){ $this->error = "The Name contains invalid letters.";}
    if (strlen($name) > _max_Name OR strlen($name) < _min_Name) {$this->error = "The length of the Name should be between "._min_Name." and "._max_Name.".";}
    if (!$this->checkLimit()) { $this->error = "Limit reached";}

    $GR = new Group($this->DB,$this->Verify);
    if ($GR->checkGroupID($group) === false) { $this->error = "Invalid Group"; }

    if ($this->error == "") {

      $userID = $this->Verify->getUserID();
      $token = bin2hex(random_bytes(20));

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO serversToken(GroupID,UserID,Name,Token) VALUES (?,?,?,?)");
      $stmt->bind_param('iiss',$group, $userID,$name,$token);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function removeServer() {
    if ($this->error == "") {

        $stmt = $this->DB->GetConnection()->prepare("DELETE FROM serversToken WHERE ID = ?");
        $stmt->bind_param('i', $this->id);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

    }
  }

  public function checkServerID($id) {
    if(!preg_match(_regex_ID,$id)){ return false;}

    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM serversToken WHERE ID = ? AND UserID = ? LIMIT 1");
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

       $stmt = $this->DB->GetConnection()->prepare("SELECT Name,GroupID FROM status_pages WHERE ID = ? LIMIT 1");
       $stmt->bind_param('i', $this->id);
       $rc = $stmt->execute();
       if ( false===$rc ) { $this->error = "MySQL Error"; }
       $stmt->bind_result($db_name,$db_group);
       $stmt->fetch();
       $stmt->close();

       $this->name = $db_name;
       $this->group = $db_group;
     }
   }

  public function setID($id) {
      if ($this->checkServerID($id) === true) {
        $this->id = $id;
      } else {
        $this->error = "Invalid ID";
      }
  }

  public function checkLimit() {
    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM serversToken WHERE UserID = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < $this->Verify->getServerLimit()) {
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

  public function getLastError() {
    return $this->error;
  }

}

?>
