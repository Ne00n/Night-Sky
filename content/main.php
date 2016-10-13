<?php

$Login = new Verify($DB);
if ($Login->isLoggedIN()) {

?>

<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">

        <?php

        if (page::startsWith($p,"main?remove=")) {

          $check_id = str_replace("main?remove=", "", $p);

          if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

            $M = new Main($DB,$Login);
            $M->setID($check_id);
            $M->removeCheck();
            if ($M->getLastError() == "") {
              echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$M->getLastError().'</center></div>';
            }

          } else {

          ?>

            <p>Are you sure, that you want to delete this Check?</p>

            <form class="form-horizontal" action="index.php?p=main?remove=<?= page::escape($check_id) ?>" method="post">
              <div class="form-group">
                  <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=main"><button class="btn btn-primary" type="button">No</button></a>
              </div>
            </form>


            <?php
          }

        }

        if ($p == "main?add") {

          if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

            $M = new Main($DB,$Login);
            $M->addCheck($_POST['ip'],$_POST['port'],$_POST['email']);

             if ($M->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$M->getLastError().'</center></div>';
             }

          } ?>

          <form class="form-horizontal" action="index.php?p=main?add" method="post">
            <div class="form-group">
              <label class="control-label col-sm-2">IP:</label>
              <div class="col-sm-8">
                <input type="text" class="form-control input-sm" name="ip" placeholder="127.0.0.1">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Port:</label>
              <div class="col-sm-4">
                <input type="text" class="form-control input-sm" name="port" placeholder="80">
              </div>
            </div>
            <div class="form-group">
                  <label class="control-label col-sm-2">Email:</label>
                  <div class="col-sm-4">
                    <select class="form-control input-sm" name="email">
                      <?php
                      $query = "SELECT ID,EMail FROM emails WHERE USER_ID = ? ORDER by id";
                      $USER_ID = $Login->getUserID();
                      $stmt = $DB->GetConnection()->prepare($query);
                      $stmt->bind_param('i', $USER_ID);
                      $stmt->execute();
                      $stmt->bind_result($db_ID, $db_EMail);
                      while ($stmt->fetch()) {
                           echo '<option value="'. Page::escape($db_ID) .'">'. Page::escape($db_EMail) .'</option>';
                      }
                      $stmt->close(); ?>
                    </select>
                  </div>
            </div>
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-primary">Save</button>
            </div>
          </form>

          <?php } ?>

        <table class="table">
        <thead>
          <tr>
            <th>IP</th>
            <th>Port</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $USER_ID = $Login->getUserID();

        $query = "SELECT ID,IP,PORT,ENABLED FROM checks WHERE USER_ID = ? ";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('i', $USER_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {

          echo '<tr>';
          echo '<td class="text-left">'.Page::escape($row['IP']).'</td>';
          echo '<td class="text-left">'.Page::escape($row['PORT']).'</td>';
          echo '<td class="text-left">'.($row['ENABLED'] ? 'Enabled' : 'Disabled').'</td>';
          echo '<td class="text-left"><a href="index.php?p=main?remove='.page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
          echo '</tr>';

        } ?>

        </tbody>
      </table>

      <div class="form-group">
        <a href="index.php?p=main?add"><button class="btn btn-primary" type="button">Add Check</button></a>
    </div>
      </div>
    </div>

  </div>

  <?php
     } else { header('Location: index.php');}
   ?>
