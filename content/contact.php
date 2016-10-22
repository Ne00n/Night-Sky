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

      if (page::startsWith($p,"contact?remove=")) {

      $contact_id = str_replace("contact?remove=", "", $p);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          $CT->setID($contact_id);
          $CT->removeContact();
          if ($CT->getLastError() == "") {
            echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
          } else {
            echo '<div class="alert alert-danger" role="alert"><center>'.$CT->getLastError().'</center></div>';
          }

        } else {

        ?>

          <p>Are you sure, that you want to delete this Contact?</p>

          <form class="form-horizontal" action="index.php?p=contact?remove=<?= page::escape($contact_id) ?>" method="post">
            <div class="form-group">
                <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=contact"><button class="btn btn-primary" type="button">No</button></a>
            </div>
          </form>


          <?php
        }

      }


      if ($p == "contact?add") {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

          $CT->addContact($_POST['email']);
           if ($CT->getlastError() == "") {
             echo '<div class="alert alert-success" role="alert"><center>Success</center></div>';
             $_POST = array();
           } else {
             echo '<div class="alert alert-danger" role="alert"><center>'.$CT->getLastError().'</center></div>';
           }

        } ?>

        <form class="form-horizontal" action="index.php?p=contact?add" method="post">
          <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
              <div class="input-group">
               <div class="input-group-addon">
              <span class="fa fa-envelope"></span>
               </div>
                <input value="<?php if(isset($_POST['email'])) {echo page::escape($_POST['email']);} ?>" type="text" class="form-control input-sm" name="email" placeholder="alert@email.com">
              </div>
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
          echo '<td class="text-left"><a href="index.php?p=contact?remove='.page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
          echo '</tr>';

        } ?>

        </tbody>
      </table>

      <div class="form-group">
        <a href="index.php?p=contact?add"><button class="btn btn-primary" type="button">Add Contact</button></a>
      </div>

      </div>
    </div>

  </div>

  <?php
     } else { header('Location: index.php');}
   ?>
