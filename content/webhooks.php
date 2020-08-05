<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="row aligned-row">
          <div class="col-md-6 text-left">
            <h3>Webhoooks</h3>
          </div>
          <div class="col-md-6 text-right">
            <a href="index.php?p=webhook?add"><button class="btn btn-primary btn-xs"  type="button"><i class="fa fa-plus" aria-hidden="true"></i></button></a>
          </div>
        </div>
      <?php

      $WH = new WebHook($DB,$Login);

      if (Page::startsWith($p,"webhook?remove=")) {

      $webhookID = str_replace("webhook?remove=", "", $p);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $WH->setID($webhookID);
            $WH->removeHook();
            if ($WH->getLastError() == "") {
              echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$WH->getLastError().'</center></div>';
            }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } else {

        ?>

          <p>Are you sure, that you want to delete this WebHook?</p>

          <form class="form-horizontal" action="index.php?p=webhook?remove=<?= Page::escape($webhookID) ?>" method="post">
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=contact"><button class="btn btn-primary" type="button">No</button></a>
            </div>
          </form>

          <?php
        }
      }

     if (Page::startsWith($p,"webhook?edit=")) {

       $webhookID = str_replace("webhook?edit=", "", $p);

       $WH->setID($webhookID);
       $WH->getData();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $WH->editHook($_POST['name'],$_POST['method'],$_POST['urlDown'],$_POST['jsonDown'],$_POST['headersDown'],$_POST['urlUp'],$_POST['jsonUp'],$_POST['headersUp'],$_POST['groups']);
             if ($WH->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$WH->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        }
        $WH->getData();
         ?>

        <form class="form-horizontal" action="index.php?p=webhook?edit=<?php echo Page::escape($webhookID); ?>" method="post">
            <?php include 'pages/webhook.php'; ?>
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-primary">Update</button>
            </div>
          </form>

  <?php }

      if ($p == "webhook?add") {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $WH->addHook($_POST['name'],$_POST['method'],$_POST['urlDown'],$_POST['jsonDown'],$_POST['headersDown'],$_POST['urlUp'],$_POST['jsonUp'],$_POST['headersUp'],$_POST['groups']);
             if ($WH->getlastError() == "") {
               echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
               $_POST = array();
             } else {
               echo '<div class="alert alert-danger" role="alert"><center>'.$WH->getLastError().'</center></div>';
             }
          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        } ?>

        <form class="form-horizontal" action="index.php?p=webhook?add" method="post">
          <?php include 'pages/webhook.php'; ?>
          <div class="form-group">
              <button type="submit" name="confirm" class="btn btn-primary">Create</button>
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

            $query = "SELECT ID,Name FROM webhooks WHERE UserID = ? ";
            $stmt = $DB->GetConnection()->prepare($query);
            $stmt->bind_param('i', $USER_ID);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
              echo '<tr>';
              echo '<td class="text-left">'.Page::escape($row['Name']).'</td>';
              echo '<td class="text-left col-md-3"><a href="index.php?p=webhook?edit='.Page::escape($row['ID']).'"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-gear"></i></button></a>';
              echo '<a href="index.php?p=webhook?remove='.Page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
              echo '</tr>';
            } ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
