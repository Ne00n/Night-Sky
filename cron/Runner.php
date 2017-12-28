<?php

  if (php_sapi_name() == 'cli') {

    include '../content/configs/config.php';
    include '../content/configs/regex.php';

    function dat_loader($class) {
        include '../class/' . $class . '.php';
    }

    spl_autoload_register('dat_loader');

    function fetchAll($DB,$threadID,$i) {

      $checks = array();
      $checks_out = array();

      $iMax = $i +5;

      $query = "SELECT SLOT,ID,IP,PORT,USER_ID,NAME,Check_Interval FROM checks WHERE ENABLED = 1 AND SLOT = ? ORDER by ID LIMIT ?,?";
      $stmt = $DB->GetConnection()->prepare($query);
      $stmt->bind_param('iii', $threadID,$i,$iMax);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
        $checks[] = $row;
      }

      foreach($checks as $row)
      {
        $emails = array();
        $webhooks = array();

        //Fetch all Contacts which are assigned to this Check
        $query = "SELECT emails.EMail FROM groups_checks INNER JOIN groups_emails ON groups_emails.GroupID=groups_checks.GroupID INNER JOIN emails ON emails.ID=groups_emails.EmailID WHERE groups_checks.CheckID = ? AND emails.Status = 1 AND emails.USER_ID = ? GROUP BY emails.EMail";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('ii', $row['ID'],$row['USER_ID']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row_emails = $result->fetch_assoc()) {
          $emails[] = $row_emails['EMail'];
        }

        //Fetch all Webhooks which are assigned to this Check
        $query = "SELECT webhooks.ID,webhooks.urlDown,webhooks.jsonDown,webhooks.headersDown,webhooks.urlUp,webhooks.jsonUp,webhooks.headersUp,webhooks.Method FROM groups_checks INNER JOIN webhooks ON groups_checks.GroupID = webhooks.GroupID WHERE groups_checks.CheckID = ? AND webhooks.UserID = ?";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('ii', $row['ID'],$row['USER_ID']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row_webhooks = $result->fetch_assoc()) {
          $webhooks[$row_webhooks['ID']] = array('urlDown' => $row_webhooks['urlDown'],'jsonDown' => $row_webhooks['jsonDown'],'headersDown' => $row_webhooks['headersDown'],'urlUp' => $row_webhooks['urlUp'],'jsonUp' => $row_webhooks['jsonUp'],'headersUp' => $row_webhooks['headersUp'],'method' => $row_webhooks['Method']);
        }

        //Here we need all details
        $checks_out[$row['ID']] = array("IP" => $row['IP'],"PORT" => $row['PORT'],"EMAIL" => $emails,"WEBHOOK" => $webhooks,"NAME" => $row['NAME'],"USER_ID" => $row['USER_ID'],"INTERVAL" => $row['Check_Interval']);
      }

      return $checks_out;
    }

    $DB = new Database;
    $DB->InitDB();

    $R = new Remote($DB);
    $Remote = $R->getRemote();

    $Verify = new Verify($DB);

    $options = getopt("T:I:W:");

    $threadID = $options['T'];
    $time = $options['W'];
    $i = $options['I'];

    $Checks = fetchAll($DB,$threadID,$i);

    $CS = new CronjobServ($threadID,$i,$Checks,$Remote,$time);
    $CS->run();

  }

?>
