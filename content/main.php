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

        if (Page::startsWith($p,"main?enable=")) {

          $check_id = str_replace("main?enable=", "", $p);

          $M = new Main($DB,$Login);
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

          $M = new Main($DB,$Login);
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

          $M = new Main($DB,$Login);
          $M->setID($check_id);

          if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

            if ($_POST['Token'] == $_SESSION['Token']) {

              $M->updateCheck($_POST['ip'],$_POST['port'],$_POST['email'],$_POST['name']);

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
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-server"></span>
                 </div>
                 <input value="<?php echo Page::escape($M->getIP()); ?>" type="text" class="form-control input-sm" name="ip" placeholder="127.0.0.1"/>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-6 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-pencil"></span>
                 </div>
                  <input value="<?php echo Page::escape($M->getName()); ?>" type="text" class="form-control input-sm" name="name" placeholder="Tracer"/>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-circle-o"></span>
                 </div>
                  <input value="<?php echo Page::escape($M->getPort()); ?>" type="text" class="form-control input-sm" name="port" placeholder="80"/>
                </div>
              </div>
            </div>
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">

            <div class="form-group">
                  <div class="col-sm-5 col-sm-offset-2">
                    <div class="input-group">
                      <div class="input-group-addon">
                     <span class="fa fa-envelope"></span>
                      </div>
                      <select class="form-control input-sm" name="email">
                        <?php
                        $query = "SELECT ID,EMail FROM emails WHERE USER_ID = ? AND Status = 1 ORDER by id";
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
            </div>
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-primary">Save</button>
            </div>
          </form> <?php

        }

        if (Page::startsWith($p,"main?remove=")) {

          $check_id = str_replace("main?remove=", "", $p);

          if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

            if ($_POST['Token'] == $_SESSION['Token']) {

              $M = new Main($DB,$Login);
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

              $M = new Main($DB,$Login);
              $M->addCheck($_POST['ip'],$_POST['port'],$_POST['email'],$_POST['name']);

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
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-server"></span>
                 </div>
                 <input value="<?php if (isset($_POST['ip'])) {echo Page::escape($_POST['ip']);} ?>" type="text" class="form-control input-sm" name="ip" placeholder="127.0.0.1"/>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-6 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-pencil"></span>
                 </div>
                  <input value="<?php if (isset($_POST['name'])) {echo Page::escape($_POST['name']);} ?>" type="text" class="form-control input-sm" name="name" placeholder="Tracer"/>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-circle-o"></span>
                 </div>
                  <input value="<?php if (isset($_POST['port'])) {echo Page::escape($_POST['port']);} ?>" type="text" class="form-control input-sm" name="port" placeholder="80"/>
                </div>
              </div>
            </div>
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">

            <div class="form-group">
                  <div class="col-sm-5 col-sm-offset-2">
                    <div class="input-group">
                      <div class="input-group-addon">
                     <span class="fa fa-envelope"></span>
                      </div>
                      <select class="form-control input-sm" name="email">
                        <?php
                        $query = "SELECT ID,EMail FROM emails WHERE USER_ID = ? AND Status = 1 ORDER by id";
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
            </div>
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-primary">Save</button>
            </div>
          </form>

          <?php } ?>

        <div class="table-responsive table-hover">
        <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>IP</th>
            <th>Port</th>
            <th>Status</th>
            <th>Online</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $USER_ID = $Login->getUserID();

        $query = "SELECT ID,IP,PORT,ENABLED,ONLINE,NAME FROM checks WHERE USER_ID = ? ";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('i', $USER_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {

          echo '<tr>';
          echo '<td class="text-left">'.Page::escape($row['NAME']).'</td>';
          echo '<td class="text-left">'.Page::escape($row['IP']).'</td>';
          echo '<td class="text-left">'.Page::escape($row['PORT']).'</td>';
          echo '<td class="text-left">'.($row['ENABLED'] ? 'Enabled' : 'Disabled').'</td>';
          echo '<td class="text-left">'.($row['ONLINE'] ? 'Yes' : 'No').'</td>';
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

      <div class="form-group">
        <a href="index.php?p=main?add"><button class="btn btn-primary" type="button">Add Check</button></a>
    </div>
      </div>
    </div>

  </div>

  <?php
     } else { header('Location: index.php');}
   ?>
