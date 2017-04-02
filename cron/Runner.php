<?php

  if (php_sapi_name() == 'cli') {

    include '../content/configs/config.php';
    include '../content/configs/regex.php';

    function dat_loader($class) {
        include '../class/' . $class . '.php';
    }

    spl_autoload_register('dat_loader');

    function fetchAll($DB,$i) {

      $checks = array();
      $checks_out = array();

      $query = "SELECT SLOT,ID,IP,PORT,USER_ID,NAME FROM checks WHERE ENABLED = 1 AND SLOT = ? ORDER by ID";
      $stmt = $DB->GetConnection()->prepare($query);
      $stmt->bind_param('i', $i);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
        $checks[] = $row;
      }

      foreach($checks as $row)
      {
        $emails = array();

        //Fetch all Contacts which are assigned to this Check
        $query = "SELECT emails.EMail FROM groups_checks INNER JOIN groups_emails ON groups_emails.GroupID=groups_checks.GroupID INNER JOIN emails ON emails.ID=groups_emails.EmailID WHERE groups_checks.CheckID = ? AND emails.Status = 1 AND emails.USER_ID = ? GROUP BY emails.EMail";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('ii', $row['ID'],$row['USER_ID']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row_emails = $result->fetch_assoc()) {
          $emails[] = $row_emails['EMail'];
        }

        //Here we need all details
        $checks_out[$row['SLOT']][$row['ID']] = array("IP" => $row['IP'],"PORT" => $row['PORT'],"EMAIL" => $emails,"NAME" => $row['NAME'],"USER_ID" => $row['USER_ID']);
      }

      return $checks_out;
    }

    $DB = new Database;
    $DB->InitDB();

    $R = new Remote($DB);
    $Remote = $R->getRemote();

    $Verify = new Verify($DB);

    $options = getopt("T:I:");

    $threadID = $options['T'];
    $i = $options['I'];

    $Checks = fetchAll($DB,$threadID);

    $Check_Thread = array_slice($Checks[$threadID], $i, $i +5, true);

    $CS = new CronjobServ($threadID,$i,$Check_Thread,$Remote);
    $CS->run();

  }

?>
