<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">

      <?php

      $GR = new Group($DB,$Login);

      if (Page::startsWith($p,"group?remove=")) {

      $group_id = str_replace("group?remove=", "", $p);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $GR->setID($group_id);
            $GR->removeGroup();
            if ($GR->getLastError() == "") {
              echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$GR->getLastError().'</center></div>';
            }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } else {

        ?>

          <p>Are you sure, that you want to delete this Group?</p>

          <form class="form-horizontal" action="index.php?p=group?remove=<?= Page::escape($group_id) ?>" method="post">
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=group"><button class="btn btn-primary" type="button">No</button></a>
            </div>
          </form>

          <?php
        }
      }

     if (Page::startsWith($p,"group?edit=")) {

       $group_id = str_replace("group?edit=", "", $p);

       $GR->setID($group_id);
       $GR->getData();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $GR->editGroup($_POST['name']);
             if ($GR->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$GR->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } ?>

        <form class="form-horizontal" action="index.php?p=group?edit=<?php echo Page::escape($group_id); ?>" method="post">
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-pencil"></span>
               </div>
                <input value="<?php echo Page::escape($GR->getName()); ?>" type="text" class="form-control input-sm" name="name">
              </div>
            </div>
          </div>
          <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
          <div class="form-group">
              <button type="submit" name="confirm" class="btn btn-primary">Save</button>
          </div>
        </form>

  <?php }

      if ($p == "group?add") {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $GR->addGroup($_POST['name']);
             if ($GR->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$GR->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } ?>

        <form class="form-horizontal" action="index.php?p=group?add" method="post">
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
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

        <?php

        $USER_ID = $Login->getUserID();

        $query = "SELECT ID,Name FROM groups WHERE USER_ID = ? ";
        $stmt = $DB->GetConnection()->prepare($query);
        $stmt->bind_param('i', $USER_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
          echo '<tr>';
          echo '<td class="text-left">'.Page::escape($row['Name']).'</td>';
          echo '<td class="text-left col-md-3"><a href="index.php?p=group?edit='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-gear"></i></button></a>';
          echo '<a href="index.php?p=group?remove='.Page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
          echo '</tr>';
        } ?>

        </tbody>
      </table>
    </div>

      <div class="form-group">
        <a href="index.php?p=group?add"><button class="btn btn-primary" type="button">Add a Group</button></a>
      </div>

      </div>
    </div>
    <center><a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
  </div>
