<?php

class Group {

  private $DB;
  private $Verify;
  private $error;
  private $id;
  private $name;

  public function __construct($DB,$Verify) {
    $this->DB = $DB;
    $this->Verify = $Verify;
  }

  public function addGroup($Name) {
    if(!preg_match(_regex_NAME,$Name)){ $this->error = "The Group contains invalid letters.";}
    if (strlen($Name) > _max_Name) {$this->error = "The Group is to long";}
    if (strlen($Name) < _min_Name) {$this->error = "The Group is to short";}
    if (!$this->checkLimit()) { $this->error = "Limit reached";}

    if ($this->error == "") {

      $USER_ID = $this->Verify->getUserID();

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO groups(USER_ID,Name) VALUES (?,?)");
      $stmt->bind_param('is',$USER_ID, $Name);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function editGroup($Name) {
    if(!preg_match(_regex_NAME,$Name)){ $this->error = "The Group contains invalid letters.";}
    if (strlen($Name) > _max_Name) {$this->error = "The Group is to long";}
    if (strlen($Name) < _min_Name) {$this->error = "The Group is to short";}

    if ($this->error == "") {

      $USER_ID = $this->Verify->getUserID();

      $stmt = $this->DB->GetConnection()->prepare("UPDATE groups SET Name = ? WHERE ID = ?");
      $stmt->bind_param('si',$Name,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function removeGroup() {
    if ($this->error == "") {
      if ($this->CheckifGroupIsInUse() === false) {

        $stmt = $this->DB->GetConnection()->prepare("DELETE FROM groups WHERE ID = ?");
        $stmt->bind_param('i', $this->id);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

      } else {
        $this->error = "Group still in use";
      }
    }
  }

  public function checkGroupID($id) {
    if(!preg_match(_regex_ID,$id)){ return false;}

    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM groups WHERE ID = ? AND USER_ID = ? LIMIT 1");
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

       $stmt = $this->DB->GetConnection()->prepare("SELECT Name FROM groups WHERE ID = ? LIMIT 1");
       $stmt->bind_param('i', $this->id);
       $rc = $stmt->execute();
       if ( false===$rc ) { $this->error = "MySQL Error"; }
       $stmt->bind_result($db_name);
       $stmt->fetch();
       $stmt->close();

       $this->name = $db_name;
     }
   }

  public function setID($id) {
      if ($this->checkGroupID($id) === true) {
        $this->id = $id;
      } else {
        $this->error = "Invalid ID";
      }
  }

  public function CheckifGroupIsInUse() {
    $in_use = false;

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM groups_checks WHERE GroupID = ? LIMIT 1");
    $stmt->bind_param('i', $this->id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->bind_result($db_id);
    $stmt->fetch();
    $stmt->close();

    if (isset($db_id)) { $in_use = true; }

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM groups_emails WHERE GroupID = ? LIMIT 1");
    $stmt->bind_param('i', $this->id);
    $rc = $stmt->execute();
    if ( false===$rc ) { $this->error = "MySQL Error"; }
    $stmt->bind_result($db_id);
    $stmt->fetch();
    $stmt->close();

    if (isset($db_id)) { $in_use = true; }

    return $in_use;

  }

  public function checkLimit() {
    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM groups WHERE USER_ID = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < $this->Verify->getGroupLimit()) {
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

  public function getLastError() {
    return $this->error;
  }

}

?>
