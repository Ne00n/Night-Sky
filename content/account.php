<?php

$Login = new Verify($DB);
if ($Login->isLoggedIN()) {

?>

<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-8 col-md-offset-2">

        <form class="form-horizontal" action="index.php?p=account" method="post">
          <div class="form-group">
            <h3>Password</h3>
          </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-key"></span>
                 </div>
                 <input type="password" class="form-control input-sm" name="old_password" placeholder="Old Password"/>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-key"></span>
                 </div>
                 <input type="password" class="form-control input-sm" name="new_password" placeholder="New Password"/>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-2">
                <div class="input-group">
                 <div class="input-group-addon">
                <span class="fa fa-key"></span>
                 </div>
                 <input type="password" class="form-control input-sm" name="new_password_2" placeholder="Repeat your Password"/>
                </div>
              </div>
            </div>
            <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>"\>

            <?php

            if (isset($_POST['confirm_password'])) {

              if ($_POST['Token'] == $_SESSION['Token']) {

                $U = new User($DB,$Login);
                $U->changePassword($_POST['old_password'],$_POST['new_password'],$_POST['new_password_2'],$Login);

                if ($U->getLastError() == "") {
                  echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
                } else {
                  echo '<div class="alert alert-danger" role="alert"><center>'.$U->getLastError().'</center></div>';
                }

              } else {

                  echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';

              }

            }
             ?>

            <div class="form-group">
                <button type="submit" name="confirm_password" class="btn btn-primary">Change Password</button>
            </div>
          </form>

      </div>
    </div>

  </div>

  <?php
     } else { header('Location: index.php');}
   ?>
