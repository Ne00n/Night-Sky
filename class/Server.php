<?php

class Server {

  private $DB;
  private $Verify;
  private $error;
  private $id;
  private $name;
  private $group;
  private $token;

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
      $this->token = $token = bin2hex(random_bytes(20));

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

  public function getUage($type = 'cpu',$start = 0,$end = 0,$latest = false) {
    if ($latest == true) {
      $time = strtotime('-5 minutes', time());
      $response = array();

      $query = "SELECT * FROM servers".$type." WHERE timestamp >= ? AND serversTokenID = ?";
      $stmt = $this->DB->GetConnection()->prepare($query);
      $stmt->bind_param('ii', $time,$this->id);
      $stmt->execute();
      $result = $stmt->get_result();

      while ($row = $result->fetch_assoc()) {
        $response['timestamp'][] = $row['timestamp'];
        $response['data'][] = $row;
      }

      if ($type == 'CPU') {
        $cpuLoad = 0;
        if (!empty($response['data'])) {
          foreach ($response['data'] as $element) {
            $cpuLoad += $element['idle'];
          }
          $cpuLoad = abs(($cpuLoad / count($response['data'])) - 100);
        }
        return $cpuLoad;
      }

      if ($type == 'Memory') {
        $memoryUsage = 0;
        if (!empty($response['data'])) {
          foreach ($response['data'] as $element) {
            $memoryUsage += $element['percent'];
          }
          $memoryUsage = $memoryUsage / count($response['data']);
        }
        return $memoryUsage;
      }

      if ($type == 'Disk') {
        $diskUsage = 0;
        if (!empty($response['data'])) {
          foreach ($response['data'] as $element) {
            $diskUsage += $element['percent'];
          }
          $diskUsage = $diskUsage / count($response['data']);
        }
        return $diskUsage;
      }

      if ($type == 'Network') {
        $networkUsageTotal = 0; $networkUsage = array();
        if (!empty($response['data'])) {
          foreach ($response['data'] as $element) {
            if (isset($networkUsage[$element['nic']]['lastRX'])) {
              $networkUsage['RX'] += $element['bytesRX'] - $networkUsage[$element['nic']]['lastRX'];
              $networkUsage['TX'] += $element['bytesTX'] - $networkUsage[$element['nic']]['lastTX'];
            }
            $networkUsage[$element['nic']]['lastRX'] = $element['bytesRX'];
            $networkUsage[$element['nic']]['lastTX'] = $element['bytesTX'];
          }
          $networkUsage['RX'] = $networkUsage['RX'] / count($response['data']);
          $networkUsage['TX'] = $networkUsage['TX'] / count($response['data']);
          $networkUsage['RX'] = $networkUsage['RX'] / 125000;
          $networkUsage['TX'] = $networkUsage['TX'] / 125000;
          $networkUsageTotal = round(($networkUsage['RX'] + $networkUsage['TX']) / 60,2);
        }
        return $networkUsageTotal;
      }

      return $response;
    } elseif ($start != 0 and $end != 0) {

      $total = $end - $start;
      if ($total >= 8000) {
        $total = round($total / 86400);
        $gaps = $total * 4;
      } else {
        $gaps = 1;
      }
      $count = 0;

      $query = "SELECT * FROM servers".$type." WHERE serversTokenID = ? AND timestamp >= ? AND timestamp <= ?";
      $stmt = $this->DB->GetConnection()->prepare($query);
      $stmt->bind_param('iii', $this->id,$start,$end);
      $stmt->execute();
      $result = $stmt->get_result();

      $memoryArray = array('active','inactive','buffers','cached','shared','free','used','total','available');

      while ($row = $result->fetch_assoc()) {
        if ($count == $gaps) {
          $response['data'][] = $row;
          foreach ($row as $key => $element) {
            if (in_array($key, $memoryArray)) {
              $tmp = round($element / 1000000,2);
              if ($type == 'Disk') {
                $tmp = round($tmp / 1000,2);
              }
              $response[$key][] = $tmp;
            } elseif ($key == 'timestamp') {
              $response[$key][] = date("'Y-m-d H:i'",$element);
            } else {
              $response[$key][] = $element;
            }
          }
          $count = 0;
        }
        $count++;
      }

      if ($type == 'CPU') {
        $cpuLoad = array();
        foreach ($response['data'] as $element) {
          //For each Core
          $cpuLoad['load'][$element['core']][] = abs($element['idle'] - 100);
          $cpuLoad['system'][$element['core']][] = $element['system'];
          $cpuLoad['user'][$element['core']][] = $element['user'];
          $cpuLoad['nice'][$element['core']][] = $element['nice'];
          $cpuLoad['steal'][$element['core']][] = $element['steal'];
          $cpuLoad['iowait'][$element['core']][] = $element['iowait'];
          //For all Cores
          if (!isset($cpuLoad[$element['timestamp']]['idle']))    { $cpuLoad[$element['timestamp']]['idle'] = $element['idle'];      } else { $cpuLoad[$element['timestamp']]['idle'] += $element['idle']; }
          if (!isset($cpuLoad[$element['timestamp']]['systemA'])) { $cpuLoad[$element['timestamp']]['systemA'] = $element['system']; } else { $cpuLoad[$element['timestamp']]['systemA'] += $element['system']; }
          if (!isset($cpuLoad[$element['timestamp']]['userA']))   { $cpuLoad[$element['timestamp']]['userA'] = $element['user'];     } else { $cpuLoad[$element['timestamp']]['userA'] += $element['user']; }
          if (!isset($cpuLoad[$element['timestamp']]['niceA']))   { $cpuLoad[$element['timestamp']]['niceA'] = $element['nice'];     } else { $cpuLoad[$element['timestamp']]['niceA'] += $element['nice']; }
          if (!isset($cpuLoad[$element['timestamp']]['stealA']))  { $cpuLoad[$element['timestamp']]['stealA'] = $element['steal'];   } else { $cpuLoad[$element['timestamp']]['stealA'] += $element['steal']; }
          if (!isset($cpuLoad[$element['timestamp']]['iowaitA'])) { $cpuLoad[$element['timestamp']]['iowaitA'] = $element['iowait']; } else { $cpuLoad[$element['timestamp']]['iowaitA'] += $element['iowait']; }
          if (!isset($cpuLoad[$element['timestamp']]['cores']))   { $cpuLoad[$element['timestamp']]['cores'] = 1;                    } else { $cpuLoad[$element['timestamp']]['cores']++; }
        }

        foreach ($cpuLoad as $key => $load) {
          if (is_numeric($key)) {
            $cpuLoad['loadA'][] = abs(($cpuLoad[$key]['idle'] / $cpuLoad[$key]['cores']) - 100);
            $cpuLoad['systemA'][] = $cpuLoad[$key]['systemA'];
            $cpuLoad['userA'][] = $cpuLoad[$key]['userA'];
            $cpuLoad['niceA'][] = $cpuLoad[$key]['niceA'];
            $cpuLoad['stealA'][] = $cpuLoad[$key]['stealA'];
            $cpuLoad['iowaitA'][] = $cpuLoad[$key]['iowaitA'];
            $cpuLoad['timestamp'][] = date("'Y-m-d H:i'",$key);
          }
        }

        return $cpuLoad;
      }

      if ($type == 'Network') {
        $networkUsage = array();
        foreach ($response['data'] as $element) {
          if (isset($networkUsage[$element['nic']]['lastRX'])) {
            $timestamp = $element['timestamp'];
            if (!isset($networkUsage[$timestamp]['RX'])) {
              $networkUsage[$timestamp]['RX'] = ($element['bytesRX'] - $networkUsage[$element['nic']]['lastRX']) / 125000;
            } else {
              $networkUsage[$timestamp]['RX'] += ($element['bytesRX'] - $networkUsage[$element['nic']]['lastRX']) / 125000;
            }
            if (!isset($networkUsage[$timestamp]['TX'])) {
              $networkUsage[$timestamp]['TX'] = ($element['bytesTX'] - $networkUsage[$element['nic']]['lastTX']) / 125000;
            } else {
              $networkUsage[$timestamp]['TX'] += ($element['bytesTX'] - $networkUsage[$element['nic']]['lastTX']) / 125000;
            }
            $networkUsage['nic'][$element['nic']]['TX'][] = round((($element['bytesTX'] - $networkUsage[$element['nic']]['lastTX']) / 125000) / 60,2);
            $networkUsage['nic'][$element['nic']]['RX'][] = round((($element['bytesRX'] - $networkUsage[$element['nic']]['lastRX']) / 125000) / 60,2);
            $networkUsage['timestamp'][] = date("'Y-m-d H:i'",$element['timestamp']);
          }
          $networkUsage[$element['nic']]['lastRX'] = $element['bytesRX'];
          $networkUsage[$element['nic']]['lastTX'] = $element['bytesTX'];
          $networkUsage['nics'][$element['nic']] = 1;
        }

        foreach ($networkUsage as $element) {
          if (isset($element['RX'])) {
            $networkUsage['RX'][] = round($element['RX'] / 60,2);
            $networkUsage['TX'][] = round($element['TX'] / 60,2);
          }
        }
        return $networkUsage;
      }

      return $response;
    } else {
      return false;
    }
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

  public function getToken() {
    return $this->token;
  }

  public function getLastError() {
    return $this->error;
  }

}

?>
