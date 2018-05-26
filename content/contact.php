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
        $CT->getData(); //Here we need to pull twice

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            if (isset($_POST['groups'])) {
              $CT->updateContact($_POST['email'],$_POST['groups']);
            } else {
              $CT->updateContact($_POST['email']);
            }

             if ($CT->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$CT->getLastError().'</center></div>';
             }

          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }
        }

        $CT->getData();

        ?>

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
                    <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="groups[]" multiple>
                      <?php
                      $group_ids = array();
                      $results = $Lake->SELECT(array('GroupID'))->FROM('groups_emails')->WHERE(array('EmailID' => $contact_id))->VAR('i')->DONE();
                      foreach ($results as $row) {
                           $group_ids[] = $row['GroupID'];
                      }

                      $USER_ID = $Login->getUserID();
                      $results = $Lake->SELECT(array('ID,Name'))->FROM('groups')->WHERE(array('USER_ID' => $USER_ID))->ORDERBY('ID')->VAR('i')->DONE();
                      foreach ($results as $row) {
                           echo '<option '.(in_array($row['ID'],$group_ids) ? "selected" : "").' value="'. Page::escape($row['ID']) .'">'. Page::escape($row['Name']) .'</option>';
                      } ?>
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

            if (isset($_POST['groups'])) {
              $CT->addContact($_POST['email'],$_POST['groups']);
            } else {
              $CT->addContact($_POST['email']);
            }

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
                    <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="groups[]" multiple>
                      <?php
                      $USER_ID = $Login->getUserID();
                      $results = $Lake->SELECT(array('ID,Name'))->FROM('groups')->WHERE(array('USER_ID' => $USER_ID))->GROUPBY('ID')->VAR('i')->DONE();
                      foreach ($results as $row) {
                           echo '<option value="'. Page::escape($row['ID']) .'">'. Page::escape($row['Name']) .'</option>';
                      } ?>
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
        $results = $Lake->SELECT(array('ID,EMail,Status'))->FROM('emails')->WHERE(array('USER_ID' => $USER_ID))->VAR('i')->DONE();
        foreach ($results as $row) {
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
