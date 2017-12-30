<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">

      <?php

      $SP = new StatusPage($DB,$Login);

      if (Page::startsWith($p,"status-page?remove=")) {

      $status_id = str_replace("status-page?remove=", "", $p);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $SP->setID($status_id);
            $SP->removePage();
            if ($SP->getLastError() == "") {
              echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$SP->getLastError().'</center></div>';
            }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } else {

        ?>

          <p>Are you sure, that you want to delete this Status Page?</p>

          <form class="form-horizontal" action="index.php?p=status-page?remove=<?= Page::escape($status_id) ?>" method="post">
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=contact"><button class="btn btn-primary" type="button">No</button></a>
            </div>
          </form>

          <?php
        }
      }

     if (Page::startsWith($p,"status-page?edit=")) {

       $status_id = str_replace("status-page?edit=", "", $p);

       $SP->setID($status_id);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $SP->editPage($_POST['name'],$_POST['groups']);
             if ($SP->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$SP->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        }
        $SP->getData();
         ?>

        <form class="form-horizontal" action="index.php?p=status-page?edit=<?php echo Page::escape($status_id); ?>" method="post">
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-pencil"></span>
               </div>
                <input value="<?php echo Page::escape($SP->getName()); ?>" type="text" class="form-control input-sm" name="name">
              </div>
            </div>
          </div>
          <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                  <div class="input-group">
                    <div class="input-group-addon">
                   <span class="fa fa-group"></span>
                    </div>
                    <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="groups">
                      <?php
                      $query = "SELECT ID,Name FROM groups WHERE USER_ID=? GROUP BY ID";
                      $USER_ID = $Login->getUserID();
                      $stmt = $DB->GetConnection()->prepare($query);
                      $stmt->bind_param('i', $USER_ID);
                      $stmt->execute();
                      $stmt->bind_result($db_group_id, $db_group_name);
                      while ($stmt->fetch()) {
                           echo '<option '.($db_group_id == $SP->getGroupID() ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
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

      if ($p == "status-page?add") {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $SP->addPage($_POST['name'],$_POST['groups']);
             if ($SP->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$SP->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } ?>

        <form class="form-horizontal" action="index.php?p=status-page?add" method="post">
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-pencil"></span>
               </div>
                <input value="<?php if(isset($_POST['name'])) {echo Page::escape($_POST['name']);} ?>" type="text" class="form-control input-sm" name="name" placeholder="Bastion">
              </div>
            </div>
          </div>
          <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                  <div class="input-group">
                    <div class="input-group-addon">
                   <span class="fa fa-group"></span>
                    </div>
                    <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="groups">
                      <?php
                      $query = "SELECT ID,Name FROM groups WHERE USER_ID=? GROUP BY ID";
                      $USER_ID = $Login->getUserID();
                      $stmt = $DB->GetConnection()->prepare($query);
                      $stmt->bind_param('i', $USER_ID);
                      $stmt->execute();
                      $stmt->bind_result($db_group_id, $db_group_name);
                      while ($stmt->fetch()) {
                           echo '<option '.($SP->getGroupID() == $db_group_id ? "selected" : "").' value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
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
            <th>Name</th>
            <th>Url</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $USER_ID = $Login->getUserID();

        $query = "SELECT ID,Name,Token FROM status_pages WHERE UserID = ? ";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('i', $USER_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
          echo '<tr>';
          echo '<td class="text-left">'.Page::escape($row['Name']).'</td>';
          echo '<td class="text-left"><a href="https://'.Page::escape(_Domain).'/Status.php?token='.Page::escape($row['Token']).'" target="_blank" >Click</a></td>';
          echo '<td class="text-left col-md-3"><a href="index.php?p=status-page?edit='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-gear"></i></button></a>';
          echo '<a href="index.php?p=status-page?remove='.Page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
          echo '</tr>';
        } ?>

        </tbody>
      </table>
    </div>

      <div class="form-group">
        <a href="index.php?p=status-page?add"><button class="btn btn-primary" type="button">Add a Status Page</button></a>
      </div>

      </div>
    </div>
    <center><a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
  </div>
