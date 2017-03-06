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

      $CT = new Contact($DB,$Login);

      if (Page::startsWith($p,"contact?key=")) {

        $k = str_replace("contact?key=", "", $p);

        if ($Login->checkEmailHash($k)) {

          $CT->enableContact($k);

          if ($CT->getlastError() == "") {
            echo '<div class="alert alert-success" role="alert"><center>Mail enabled</center></div>';
          } else {
            echo '<div class="alert alert-danger" role="alert"><center>'.$CT->getLastError().'</center></div>';
          }

        } else {
          echo '<div class="alert alert-danger" role="alert"><center>Invalid Key</center></div>';
        }

      }

      if (Page::startsWith($p,"contact?remove=")) {

      $contact_id = str_replace("contact?remove=", "", $p);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $CT->setID($contact_id);
            $CT->removeContact();
            if ($CT->getLastError() == "") {
              echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$CT->getLastError().'</center></div>';
            }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } else {

        ?>

          <p>Are you sure, that you want to delete this Contact?</p>

          <form class="form-horizontal" action="index.php?p=contact?remove=<?= Page::escape($contact_id) ?>" method="post">
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=contact"><button class="btn btn-primary" type="button">No</button></a>
            </div>
          </form>

          <?php
        }
      }

      if (Page::startsWith($p,"contact?edit=")) {

        $contact_id = str_replace("contact?edit=", "", $p);

        $CT->setID($contact_id);
        $CT->getData();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $CT->updateContact($_POST['email'],$_POST['groups']);
             if ($CT->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$CT->getLastError().'</center></div>';
             }

          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }
        } ?>

        <form class="form-horizontal" action="index.php?p=contact?edit=<?php echo Page::escape($contact_id); ?>" method="post">
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-envelope"></span>
               </div>
                <input value="<?php echo Page::escape($CT->getEmail()); ?>" type="text" class="form-control input-sm" name="email">
              </div>
            </div>
          </div>
          <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                  <div class="input-group">
                    <div class="input-group-addon">
                   <span class="fa fa-group"></span>
                    </div>
                    <select class="selectpicker form-control" data-size="3" name="groups[]" multiple>
                      <?php
                      $group_ids = array();

                      $query = "SELECT GroupID FROM groups_emails WHERE EmailID=?";
                      $stmt = $DB->GetConnection()->prepare($query);
                      $stmt->bind_param('i', $contact_id);
                      $stmt->execute();
                      $stmt->bind_result($db_group_id);
                      while ($stmt->fetch()) {
                           $group_ids[] = $db_group_id;
                      }

                      $query = "SELECT ID,Name FROM groups WHERE USER_ID=? ORDER BY ID";
                      $USER_ID = $Login->getUserID();
                      $stmt = $DB->GetConnection()->prepare($query);
                      $stmt->bind_param('i', $USER_ID);
                      $stmt->execute();
                      $stmt->bind_result($db_group_id, $db_group_name);
                      while ($stmt->fetch()) {
                           echo '<option '.(in_array($db_group_id,$group_ids) ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                      }
                      $stmt->close(); ?>
                    </select>
                   </div>
              </div>
          </div>
          <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
          <div class="form-group">
              <button type="submit" name="confirm" class="btn btn-primary">Save</button>
          </div>
        </form>

  <?php }

      if ($p == "contact?add") {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $CT->addContact($_POST['email'],$_POST['groups']);
             if ($CT->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$CT->getLastError().'</center></div>';
             }

          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }
        } ?>

        <form class="form-horizontal" action="index.php?p=contact?add" method="post">
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-envelope"></span>
               </div>
                <input value="<?php if(isset($_POST['email'])) {echo Page::escape($_POST['email']);} ?>" type="text" class="form-control input-sm" name="email" placeholder="alert@email.com">
              </div>
            </div>
          </div>
          <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                  <div class="input-group">
                    <div class="input-group-addon">
                   <span class="fa fa-group"></span>
                    </div>
                    <select class="selectpicker form-control" data-size="3" name="groups[]" multiple>
                      <?php
                      $query = "SELECT ID,Name FROM groups WHERE USER_ID=? GROUP BY ID";
                      $USER_ID = $Login->getUserID();
                      $stmt = $DB->GetConnection()->prepare($query);
                      $stmt->bind_param('i', $USER_ID);
                      $stmt->execute();
                      $stmt->bind_result($db_group_id, $db_group_name);
                      while ($stmt->fetch()) {
                           echo '<option '.($db_group_uniq ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                      }
                      $stmt->close(); ?>
                    </select>
                   </div>
              </div>
          </div>
          <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
          <div class="form-group">
              <button type="submit" name="confirm" class="btn btn-primary">Save</button>
          </div>
        </form>

  <?php } ?>

      <div class="table-responsive table-hover">
        <table class="table">
        <thead>
          <tr>
            <th>EMail</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $USER_ID = $Login->getUserID();

        $query = "SELECT ID,EMail,Status FROM emails WHERE USER_ID = ? ";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('i', $USER_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {

          echo '<tr>';
          echo '<td class="text-left">'.Page::escape($row['EMail']).'</td>';
          echo '<td class="text-left">'.($row['Status'] ? 'Enabled' : 'Disabled').'</td>';
          echo '<td class="text-left col-md-3"><a href="index.php?p=contact?edit='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-gear"></i></button></a>';
          echo '<a href="index.php?p=contact?remove='.Page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
          echo '</tr>';

        } ?>

        </tbody>
      </table>
    </div>

      <div class="form-group">
        <a href="index.php?p=contact?add"><button class="btn btn-primary" type="button">Add a Contact</button></a>
      </div>

      </div>
    </div>
    <center><a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
  </div>

  <?php
     } else { header('Location: index.php');}
   ?>
