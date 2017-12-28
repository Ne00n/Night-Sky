<?php

class StatusPage {

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

  public function addPage($name,$group) {
    if(!preg_match(_regex_NAME,$name)){ $this->error = "The Name contains invalid letters.";}
    if (strlen($name) > _max_Name) {$this->error = "The Name is to long";}
    if (strlen($name) < _min_Name) {$this->error = "The Name is to short";}
    if (!$this->checkLimit()) { $this->error = "Limit reached";}

    $GR = new Group($this->DB,$this->Verify);
    if ($GR->checkGroupID($group) === false) { $this->error = "Invalid Group"; }

    if ($this->error == "") {

      $user_id = $this->Verify->getUserID();
      $token = bin2hex(random_bytes(20));

      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO status_pages(UserID,GroupID,Name,Token) VALUES (?,?,?,?)");
      $stmt->bind_param('iiss',$user_id, $group,$name,$token);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function editPage($name,$group) {
    if(!preg_match(_regex_NAME,$name)){ $this->error = "The Name contains invalid letters.";}
    if (strlen($name) > _max_Name) {$this->error = "The Group is to long";}
    if (strlen($name) < _min_Name) {$this->error = "The Group is to short";}

    $GR = new Group($this->DB,$this->Verify);
    if ($GR->checkGroupID($group) === false) { $this->error = "Invalid Group"; }

    if ($this->error == "") {

      $stmt = $this->DB->GetConnection()->prepare("UPDATE status_pages SET Name = ?, GroupID = ? WHERE ID = ?");
      $stmt->bind_param('sii',$name,$group,$this->id);
      $rc = $stmt->execute();
      if ( false===$rc ) { $this->error = "MySQL Error"; }
      $stmt->close();

    }
  }

  public function removePage() {
    if ($this->error == "") {

        $stmt = $this->DB->GetConnection()->prepare("DELETE FROM status_pages WHERE ID = ?");
        $stmt->bind_param('i', $this->id);
        $rc = $stmt->execute();
        if ( false===$rc ) { $this->error = "MySQL Error"; }
        $stmt->close();

    }
  }

  public function checkPageID($id) {
    if(!preg_match(_regex_ID,$id)){ return false;}

    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM status_pages WHERE ID = ? AND UserID = ? LIMIT 1");
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
      if ($this->checkPageID($id) === true) {
        $this->id = $id;
      } else {
        $this->error = "Invalid ID";
      }
  }

  public function getServersbyToken($token) {
    $servers = array('servers' => array());
    if(!preg_match(_regex_TOKEN,$token)) { return false; }
    $query = "SELECT checks.Name,checks.ONLINE,status_pages.Name as SName FROM status_pages INNER JOIN groups_checks ON status_pages.GroupID=groups_checks.GroupID INNER JOIN checks ON checks.ID=groups_checks.CheckID WHERE status_pages.Token = ? ";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      array_push($servers['servers'], array("Name" => $row['Name'],"Status" => $row['ONLINE']));
      $servers['name'] = $row['SName'];
      if ($row['ONLINE'] == 0) {
        $servers['operational'] = 0;
      }
    }
    if(sizeof($servers['servers']) { return false; }
    return $servers;

  }

  public function checkLimit() {
    $user_id = $this->Verify->getUserID();

    $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM status_pages WHERE UserID = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < $this->Verify->getStatusLimit()) {
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
