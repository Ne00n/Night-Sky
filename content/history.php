<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">

        <table class="table">
        <thead>
          <tr>
            <th>Time (CET)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $history_id = str_replace("history?id=", "", $p);

        $H = new History($DB,$Login);

        if ($H->checkHistoryID($history_id)) {

          $data = $H->getHistory($history_id);

          foreach ($data as $key => $element) {

            echo '<tr class="'.($element['Status'] ? 'success' : 'danger').'">';
            echo '<td class="text-left">'.Page::escape(date("d.m.Y H:i:s",$element['Timestamp'])).'</td>';
            echo '<td class="text-left">'.($element['Status'] ? 'Online' : 'Offline').'</td>';
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
