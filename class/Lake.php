<?php

class Lake {

  private $dbHost;
  private $dbUser;
  private $dbPassword;
  private $dbDatabase;
  private $success = true;
  private $errors = array();
  private $Database;

  //input
  private $type;
  private $insert;
  private $update;
  private $set;
  private $setRaw;
  private $select;
  private $into;
  private $intoRaw;
  private $from;
  private $where;
  private $orderby;
  private $groupby;
  private $limit;
  private $whereRaw;
  private $var;
  private $sqlRaw;

  public function __construct($dbHost,$dbUser,$dbPassword,$dbDatabase) {
    $this->dbHost = $dbHost;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;
    $this->dbDatabase = $dbDatabase;
    $this->initDB();
  }

  private function initDB() {
      $this->Database = new mysqli($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbDatabase);
      if ($this->Database->connect_error) {
        $this->success = false;
        $this->errors[] ="Not connected, error: " .   $this->Database->connect_error;
      }
  }

  public function INSERT($input) {
    $this->type = 'insert';
    $this->insert = $input;
    return $this;
  }

  public function UPDATE($input) {
    $this->type = 'update';
    $this->update = $input;
    return $this;
  }

  public function SET($input) {
    $i = 1;
    foreach ($input as $key => $value) {
      if ($i == count($input)) {
        $this->set .= $key.' = ?';
      } else {
        $this->set .= $key.' = ?,';
      }
      $i++;
    }
    $this->setRaw = $input;
    return $this;
  }

  public function INTO($input) {
    $i = 1;
    foreach ($input as $key => $value) {
      if ($i == count($input)) {
        $this->into .= $key;
      } else {
        $this->into .= $key.',';
      }
      $i++;
    }
    $this->intoRaw = $input;
    return $this;
  }

  public function DELETE() {
    $this->type = 'delete';
    return $this;
  }

  public function SELECT($input) {
    $this->type = 'select';
    $this->select = implode(',',$input);
    return $this;
  }

  public function FROM($input) {
    $this->from = $input;
    return $this;
  }

  public function JOIN($join,$table) {
    $this->from .= ' '.$join.' '.$table;
    return $this;
  }

  public function ON($table1,$table2) {
    $this->from .= ' ON '.$table1.' = '.$table2;
    return $this;
  }

  public function LIKE($input,$param = '',$operator = 'AND') {
    $this->WHERE($input,$param,'LIKE',$operator);
    return $this;
  }

  public function WHERE($input,$param = '',$seperator = "=",$operator = "AND") {
    $i = 1;
    foreach ($input as $key => $value) {
      if ($i == count($input)) {
        $this->where .= $key.' '.(!empty($param) ? $param : '').$seperator.' ?';
      } else {
        $this->where .= $key.' '.(!empty($param) ? $param : '').$seperator.' ? '.$operator.' ';
      }
      $i++;
      $this->whereRaw[] = $value;
    }
    return $this;
  }

  public function ORDERBY($input,$sort='ASC') {
    $this->orderby .= 'ORDER BY '.$input.' '.$sort;
    return $this;
  }

  public function GROUPBY($input) {
    $this->groupby .= 'GROUP BY '.$input;
    return $this;
  }

  public function LIMIT($start,$end,$multiplier) {
    $this->limit .= 'LIMIT '.$start.','.$end*$multiplier;
    return $this;
  }

  public function OR() {
    $this->where .= ' OR ';
    return $this;
  }

  public function EE() {
    $this->where .= ' ( ';
    return $this;
  }

  public function EEND() {
    $this->where .= ' ) ';
    return $this;
  }

  public function AND() {
    $this->where .= ' AND ';
    return $this;
  }

  public function VAR($input) {
    $this->var = array($input);
    return $this;
  }

  public function DONE() {
    $response = array();
    switch ($this->type) {
    case 'select':
      //SELECT REQUEST
      $sql = "SELECT ".$this->select." FROM ".$this->from;
      if (!empty($this->where)) { $sql .= " WHERE ".$this->where; }
      if (!empty($this->groupby)) { $sql .= " ".$this->groupby; }
      if (!empty($this->orderby)) { $sql .= " ".$this->orderby; }
      if (!empty($this->limit)) { $sql .= " ".$this->limit; }
      $this->sqlRaw = $sql;
      $stmt = $this->Database->prepare($sql);
      if (false==$stmt) { $this->success = false; $this->errors[] = 'prepare() failed: ' . $this->Database->error; break; }

      if (!empty($this->whereRaw)) {
        $resultParams = $this->generateParams($this->whereRaw);
        $result = call_user_func_array(array($stmt, 'bind_param'), $resultParams);
        if (false==$result) { $this->success = false; $this->errors[] = 'bind_param() failed: ' . $this->Database->error; break; }
      }

      $result = $stmt->execute();
      if (false==$result) { $this->success = false; $this->errors[] = 'execute() failed: ' . $this->Database->error; break; }
      $result = $stmt->get_result();

      //Build the Array
      while ($row = $result->fetch_assoc()) {
        $response[] = $row;
      }

      $this->cleanUP($stmt);
      return $response;
    case 'insert':
      //INSERT REQUEST
      $sql = "INSERT INTO ".$this->insert."(".$this->into.") VALUES (".$this->buildPlaceHolders($this->intoRaw).")";
      $this->sqlRaw = $sql;
      $stmt = $this->Database->prepare($sql);
      if (false==$stmt) { $this->success = false; $this->errors[] = 'prepare() failed: ' . $this->Database->error; break; }

      $resultParams = $this->generateParams($this->intoRaw);
      $result = call_user_func_array(array($stmt, 'bind_param'), $resultParams);
      if (false==$result) { $this->success = false; $this->errors[] = 'bind_param() failed: ' . $this->Database->error; break; }

      $result = $stmt->execute();
      if (false==$result) { $this->success = false; $this->errors[] = 'execute() failed: ' . $this->Database->error; break; }
      $insertID = $this->Database->insert_id;

      $this->cleanUP($stmt);
      return $insertID;
    case 'update':
      //UPDATE REQUEST
      $sql = "UPDATE ".$this->update." SET ".$this->set;
      if (!empty($this->where)) { $sql .= " WHERE ".$this->where; }
      $this->sqlRaw = $sql;
      $stmt = $this->Database->prepare($sql);
      if (false==$stmt) { $this->success = false; $this->errors[] = 'prepare() failed: ' . $this->Database->error; break; }

      if (!empty($this->whereRaw)) {
        $resultParams = $this->generateParams(array_merge($this->setRaw,$this->whereRaw));
        $result = call_user_func_array(array($stmt, 'bind_param'), $resultParams);
        if (false==$result) { $this->success = false; $this->errors[] = 'bind_param() failed: ' . $this->Database->error; break; }
      }

      $result = $stmt->execute();
      if (false==$result) { $this->success = false; $this->errors[] = 'execute() failed: ' . $this->Database->error; break; }

      $this->cleanUP($stmt);
      break;
    case 'delete':
      //DELETE REQUEST
      $sql = "DELETE FROM ".$this->from;
      if (!empty($this->where)) { $sql .= " WHERE ".$this->where; }
      $this->sqlRaw = $sql;
      $stmt = $this->Database->prepare($sql);
      if (false==$stmt) { $this->success = false; $this->errors[] = 'prepare() failed: ' . $this->Database->error; break; }

      if (!empty($this->whereRaw)) {
        $resultParams = $this->generateParams($this->whereRaw);
        $result = call_user_func_array(array($stmt, 'bind_param'), $resultParams);
        if (false==$result) { $this->success = false; $this->errors[] = 'bind_param() failed: ' . $this->Database->error; break; }
      }

      $result = $stmt->execute();
      if (false==$result) { $this->success = false; $this->errors[] = 'execute() failed: ' . $this->Database->error; break; }

      $this->cleanUP($stmt);
      break;
    }
  }

  private function generateParams($input) {
    $values = array();
    foreach($input as $key => $value) {
        $values[$key] = &$input[$key];
    }
    $resultParams = array_merge($this->var,$values);
    return $resultParams;
  }

  private function cleanUP($stmt) {
    $stmt->close();
    $this->type = NULL;
    $this->insert = NULL;
    $this->update = NULL;
    $this->set = NULL;
    $this->setRaw = NULL;
    $this->select = NULL;
    $this->into = NULL;
    $this->intoRaw = NULL;
    $this->from = NULL;
    $this->where = NULL;
    $this->whereRaw = NULL;
    $this->var = NULL;
    $this->orderby = NULL;
    $this->groupby = NULL;
    $this->limit = NULL;
  }

  public function buildPlaceHolders($data) {
    $response = '';
    for ($i = 1; $i <= count($data); $i++) {
      if ($i == count($data)) {
        $response .= '?';
      } else {
        $response .= '?,';
      }
    }
    return $response;
  }

  public function getSuccess() {
    return $this->success;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getSQLRaw() {
    return $this->sqlRaw;
  }

  public function getWhereCount() {
    return count($this->whereRaw);
  }

  public function get() {
    return $this->Database;
  }

  public function unsetOrderby() {
    unset($this->orderby);
  }

}

?>
