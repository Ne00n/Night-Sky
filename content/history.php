<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">

        <table class="table">
        <thead>
          <tr>
            <th>Start</th>
            <th>End</th>
            <th>Downtime</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $history_id = str_replace("history?id=", "", $p);

        $H = new History($DB,$Login);

        if ($H->checkHistoryID($history_id)) {

          $data = $H->getHistory($history_id);
          if ($data[count($data) -1]['Status'] == 1) { unset($data[count($data) -1]); }
          $entries = array();
          for ($i = 0; $i <= count($data) -1; $i++) {
            if ($i == 0 && $data[$i]['Status'] == 0) {
              $entries[] = array('start' => 'Outage since '.date("d.m.Y H:i:s",$data[$i]['Timestamp']),'end' => 'TBA','downtime' => 'TBA');
            } else {
              if ($data[$i]['Status'] == 1) {
                $downtime = $data[$i]['Timestamp'] - $data[$i +1]['Timestamp'];
                if ($downtime > 60) { $downtime = round($downtime / 60,1).' minute(s)'; } else { $downtime = $downtime.' seconds'; }
                $entries[] = array('start' => 'Outage '.date("d.m.Y H:i:s",$data[$i +1]['Timestamp']),'end' => date("d.m.Y H:i:s",$data[$i]['Timestamp']),'downtime' => $downtime);
              }
            }
          }

          foreach ($entries as $entry) {
            echo '<tr class="'.($entry['downtime'] == 'TBA' ? 'danger' : 'success').'">';
            echo '<td class="text-left">'.Page::escape($entry['start']).'</td>';
            echo '<td class="text-left">'.Page::escape($entry['end']).'</td>';
            echo '<td class="text-left">'.Page::escape($entry['downtime']).'</td>';
            echo '</tr>';
          }

        }

        ?>

        </tbody>
      </table>
      <div class="form-group">
        <a href="index.php?p=main"><button class="btn btn-primary" type="button">Go back</button></a>
      </div>
      </div>
    </div>
    <center><a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
  </div>
