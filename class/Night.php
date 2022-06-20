<?php

class Night {

  private $Lake;
  private $DB;
  private $test = false;

  public function __construct($test = false) {
    $this->test = $test;
    if ($test == false) {
      include '../content/configs/config.php';
      include '../content/configs/regex.php';
    }

    $this->DB = new Database;
    $this->DB->InitDB();
    $this->Lake = new Lake(_db_host,_db_user,_db_password,_db_database);
  }

  public function run() {
    $Checks = $this->fetchAll($this->DB);

    for ($i_out = 1; $i_out <= 6; $i_out++) {

      $connection = false;
      $probes = array('google.com', 'wikipedia.org', 'reddit.com');
      //should be randomized
      shuffle($probes);
      foreach($probes as $probe) {
        if(Page::check_page($probe)) {
          $connection = true;
          break;
        }
      }

      if ($connection) {
        echo "Connected to the Internet\n";

        $start = microtime(true);
        for ($i = 1; $i <= 10; $i++) {
            if (isset($Checks[$i])) {
              echo("Night Base\n");
              $CB = new CronjobBase($i,$Checks,$i_out);
              $CB->run($this->test);
            } else {
              printf("Night Base No Job\n",$i);
            }
            sleep(1);
        }
        echo microtime(true) - $start . "\n";
        echo "10 Slots done, next round"."\n";

      }

    }
    return true;
  }

  private function fetchAll() {

    $Checks = array();

    $query = "SELECT SLOT,ID,IP,PORT,USER_ID,NAME FROM checks WHERE ENABLED = 1 ORDER by ID";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $Checks[$row['SLOT']][$row['ID']] = array("NAME" => $row['NAME'],"USER_ID" => $row['USER_ID']); //Just for counting how much are in there, the Data gets pulled again later so we dont need all data, rest removed.
    }
    return $Checks;
  }

  public function checkStuckThreads() {
    $currentTime = time() - 300;
    $this->Lake->DELETE()->FROM('threads')->WHERE(array('THREAD_LOCK_TIME' => $currentTime),'<')->AND()->WHERE(array('THREAD_LOCK' => 1))->VAR('ii')->DONE();
  }

}

?>
