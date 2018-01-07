<?php

  class API {

    private $DB;
    private $id;

    public function __construct($DB) {
      $this->DB = $DB;
    }

    public function validateArray($array,&$threshold) {
      $threshold++;
      if ($threshold > 10) { $this->memeCode('413',true);  }
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

    public function insertData($data) {
      $timestamp = time();
      //Insert CPU Data
      $core = 0; $user;$nice;$system;$idle;$iowait;$irq;$softirq;$steal;$guest;$guestNice;
      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO serversCPU(serversTokenID,core,user,nice,system,idle,iowait,irq,softirq,steal,guest,guest_nice,timestamp) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param('iiddddddddddd',$this->id,$core,$user,$nice,$system,$idle,$iowait,$irq,$softirq,$steal,$guest,$guestNice,$timestamp);
      $this->DB->GetConnection()->query("START TRANSACTION");

      foreach ($data['cpu'] as $row) {
        $core = $core;
        $user = $row[0];
        $nice = $row[1];
        $system = $row[2];
        $idle = $row[3];
        $iowait = $row[4];
        $irq = $row[5];
        $softirq = $row[6];
        $steal = $row[7];
        $guest = $row[8];
        $guestNice = $row[9];
        $stmt->execute();
        $core++;
      }

      $stmt->close();
      $this->DB->GetConnection()->query("COMMIT");
      //Insert Network Data
      $nic;$bytesTX;$bytesRX;$packetsTX;$packetsRX;$errorTX;$errorRX;$droppedTX;$droppedRX;
      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO serversNetwork(serversTokenID,nic,bytesTX,bytesRX,packetsTX,packetsRX,errorTX,errorRX,droppedTX,droppedRX,timestamp) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param('isiiiiiiiii',$this->id,$nic,$bytesTX,$bytesRX,$packetsTX,$packetsRX,$errorRX,$errorTX,$droppedTX,$droppedRX,$timestamp);
      $this->DB->GetConnection()->query("START TRANSACTION");

      foreach ($data['network'] as $key => $row) {
        $nic = $key;
        $bytesTX = $row[0];
        $bytesRX = $row[1];
        $packetsTX = $row[2];
        $packetsRX = $row[3];
        $errorRX = $row[4];
        $errorTX = $row[5];
        $droppedTX = $row[6];
        $droppedRX = $row[7];
        $stmt->execute();
      }

      $stmt->close();
      $this->DB->GetConnection()->query("COMMIT");
      //Insert Memory Data
      $memory = $data['memory'];
      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO serversMemory(serversTokenID,total,available,percent,used,free,active,inactive,buffers,cached,shared,timestamp) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param('iiidiiiiiiii',$this->id, $memory[0],$memory[1],$memory[2],$memory[3],$memory[4],$memory[5],$memory[6],$memory[7],$memory[8],$memory[9],$timestamp);
      $stmt->execute();
      $stmt->close();
      //Insert Swap Data
      $swap = $data['swap'];
      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO serversSwap(serversTokenID,total,used,free,percent,sinTX,sinRX,timestamp) VALUES (?,?,?,?,?,?,?,?)");
      $stmt->bind_param('iiiidiii',$this->id, $swap[0],$swap[1],$swap[2],$swap[3],$swap[4],$swap[5],$timestamp);
      $stmt->execute();
      $stmt->close();
      //Insert Disk Data
      $disk = $data['disk'];
      $mount = '/';
      $stmt = $this->DB->GetConnection()->prepare("INSERT INTO serversDisk(serversTokenID,mount,total,used,free,percent,timestamp) VALUES (?,?,?,?,?,?,?)");
      $stmt->bind_param('isiiidi',$this->id,$mount,$disk[0],$disk[1],$disk[2],$disk[3],$timestamp);
      $stmt->execute();
      $stmt->close();
    }

    public function tokenExist($token) {
      $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM serversToken WHERE Token = ? LIMIT 1");
      $stmt->bind_param('i', $token);
      $rc = $stmt->execute();
      $stmt->bind_result($dbID);
      $stmt->fetch();
      $stmt->close();

      if ($dbID != "") {
        $this->id = $dbID;
      } else {
        $this->memeCode('401',true);
      }
    }
  }

?>
