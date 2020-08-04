<body>

<?php include 'content/navbar.php'; ?>
  <div class="container page-style">
    <div class="col-md-10 col-md-offset-1">
      <div class="row">
        <div class="col-md-6">

        </div>
        <div class="col-md-6 text-right">
          <a href="index.php?p=main?add"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button></a>
        </div>
      </div>
        <?php

        $M = new Main($DB,$Login);
        if (Page::startsWith($p,"main?enable=")) {

          $check_id = str_replace("main?enable=", "", $p);

          $M->setID($check_id);
          $M->enable();
          if ($M->getLastError() == "") {
            echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
          } else {
            echo '<div class="alert alert-danger" role="alert"><center>'.$M->getLastError().'</center></div>';
          }

        }

        if (Page::startsWith($p,"main?disable=")) {

          $check_id = str_replace("main?disable=", "", $p);

          $M->setID($check_id);
          $M->disable();
          if ($M->getLastError() == "") {
            echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
          } else {
            echo '<div class="alert alert-danger" role="alert"><center>'.$M->getLastError().'</center></div>';
          }

        }

        if (Page::startsWith($p,"main?edit=")) {

          $check_id = str_replace("main?edit=", "", $p);

          $M->setID($check_id);
          $M->getData();

          if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

            if ($_POST['Token'] == $_SESSION['Token']) {

              $M->updateCheck($_POST['ip'],$_POST['port'],$_POST['email'],$_POST['name'],$_POST['interval'],$_POST['type'],$_POST['timeout'],$_POST['connectionTimeout'],$_POST['httpCodes'],$_POST['mtr']);

               if ($M->getlastError() == "") {
                 echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
                 $_POST = array();
               } else {
                 echo '<div class="alert alert-danger" role="alert"><center>'.$M->getLastError().'</center></div>';
               }

            } else {
                echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
            }

          }

          $M->getData();

        ?><form class="form-horizontal" action="index.php?p=main?edit=<?php echo Page::escape($check_id); ?>" method="post">
          <?php include 'pages/check.php'; ?>

            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-primary">Save</button>
            </div>
          </form> <?php

        }

        if (Page::startsWith($p,"main?remove=")) {

          $check_id = str_replace("main?remove=", "", $p);

          if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

            if ($_POST['Token'] == $_SESSION['Token']) {

              $M->setID($check_id);
              $M->removeCheck();
              if ($M->getLastError() == "") {
                echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
              } else {
                echo '<div class="alert alert-danger" role="alert"><center>'.$M->getLastError().'</center></div>';
              }

            } else {
                echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
            }

          } else {

          ?>

            <p>Are you sure, that you want to delete this Check?</p>

            <form class="form-horizontal" action="index.php?p=main?remove=<?= Page::escape($check_id) ?>" method="post">
              <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
              <div class="form-group">
                  <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=main"><button class="btn btn-primary" type="button">No</button></a>
              </div>
            </form>

            <?php
          }

        }

        if ($p == "main?add") {

          if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

            if ($_POST['Token'] == $_SESSION['Token']) {

              $M->addCheck($_POST['ip'],$_POST['port'],$_POST['email'],$_POST['name'],$_POST['interval'],$_POST['type'],$_POST['timeout'],$_POST['connectionTimeout'],$_POST['httpCodes'],$_POST['mtr']);

               if ($M->getlastError() == "") {
                 echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
                 $_POST = array();
               } else {
                 echo '<div class="alert alert-danger" role="alert"><center>'.$M->getLastError().'</center></div>';
               }

            } else {
                echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
            }

          } ?>

          <form class="form-horizontal" action="index.php?p=main?add" method="post">
            <?php include 'pages/check.php'; ?>
            <div class="form-group">
              <button type="submit" name="confirm" class="btn btn-primary">Done</button>
            </div>
          </form>

          <?php }
          $checks = $M->getChecks();
          ?>

        <div class="table-responsive table-hover">
          <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Target</th>
              <th>Port</th>
              <th>Status</th>
              <th>Online</th>
              <th>Lastrun</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>

          <?php

          foreach ($checks as $row) {

            echo '<tr class="'.($row['ONLINE'] ? 'success' : 'danger').'">';
            echo '<td class="text-left">'.Page::escape($row['NAME']).'</td>';
            echo '<td class="text-left">'.Page::escape($row['IP']).'</td>';
            echo '<td class="text-left">'.Page::escape($row['PORT']).'</td>';
            echo '<td class="text-left">'.($row['ENABLED'] ? 'Enabled' : 'Disabled').'</td>';
            echo '<td class="text-left">'.($row['ONLINE'] ? 'Yes' : 'No').'</td>';
            echo '<td class="text-left">'.Page::escape(date("d.m.Y H:i:s",$row['Lastrun'])).'</td>';
            echo '<td class="text-left col-md-3"><a href="index.php?p=main?edit='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-gear"></i></button></a>';
            if ($row['ENABLED'] === 1) {
              echo '<a href="index.php?p=main?disable='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-pause"></i></button></a>';
            } elseif ($row['ENABLED'] === 0) {
              echo '<a href="index.php?p=main?enable='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-play"></i></button></a>';
            }
            echo '<a href="index.php?p=history?id='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-history"></i></button></a>';
            echo '<a href="index.php?p=main?remove='.Page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
            echo '</tr>';

          } ?>

          </tbody>
          </table>
        </div>
      </div>
    </div>
