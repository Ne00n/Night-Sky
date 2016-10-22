<?php

$Login = new Verify($DB);
if ($Login->isLoggedIN()) {

?>

<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">

        <table class="table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $history_id = str_replace("history?id=", "", $p);

        if ($Login->checkHistoryID($history_id)) {

          $H = new History($DB);
          $data = $H->getHistory($Login->getUserID(),$history_id);

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
      </div>
    </div>

  </div>

  <?php
     } else { header('Location: index.php');}
   ?>
