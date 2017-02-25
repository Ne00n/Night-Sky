<?php

$U = new User($DB);

?>

<div class="container col-sm-4 col-md-offset-4">
    <div class="row main">
      <?php

      if ($U->checkUserAmmount() === true) {
        echo '<h2 class="form-signin-heading"><center>Night Sky Monitoring - Register</center></h2>';
      } else {
        echo '<h2 class="form-signin-heading"><center>Currently out of Stock, but here is a Video</center></h2>';
      }

       ?>
      <div class="main-login main-center">
        <form class="form-horizontal" method="post" action="index.php?p=register">

          <?php

          if ($U->checkUserAmmount() === true) {

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

              if ($_POST['Token'] == $_SESSION['Token']) {

                $U->registerUser($_POST['username'],$_POST['email'],$_POST['password'],$_POST['password_confirm'],$_POST['code']);

                if ($U->getlastError() == "") {
                  $_POST = array();
                }

              } else {
                  echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
              }

            }

           ?>

          <div class="form-group">
            <label for="username" class="cols-sm-2 control-label">Username</label>
            <div class="cols-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
                <input type="text" value="<?php if(isset($_POST['username'])) {echo Page::escape($_POST['username']);} ?>" class="form-control" name="username" placeholder="Enter your Username"/>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="email" class="cols-sm-2 control-label">Your Email</label>
            <div class="cols-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
                <input type="text" value="<?php if(isset($_POST['email'])) {echo Page::escape($_POST['email']);} ?>" class="form-control" name="email" placeholder="Enter your Email"/>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="cols-sm-2 control-label">Password</label>
            <div class="cols-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Enter your Password"/>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="confirm" class="cols-sm-2 control-label">Confirm Password</label>
            <div class="cols-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                <input type="password" class="form-control" name="password_confirm" placeholder="Confirm your Password"/>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="confirm" class="cols-sm-2 control-label">Code</label>
            <div class="cols-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-qrcode fa-lg" aria-hidden="true"></i></span>
                <input type="text" value="<?php if(isset($_POST['code'])) {echo Page::escape($_POST['code']);} ?>" class="form-control" name="code" placeholder="Enter your Code"/>
              </div>
            </div>
          </div>
          <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">

          <?php

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if ($U->getlastError() == "" AND $U->getlastWarning() == "") {
                  echo '<div class="alert alert-success" role="alert"><center>Success, confirm your email to enable your Account.</center></div>';
                } elseif ($U->getlastWarning() != "") {
                  echo '<div class="alert alert-warning" role="alert"><center>'.$U->getlastWarning().'</center></div>';
                } else {
                  echo '<div class="alert alert-danger" role="alert"><center>'.$U->getLastError().'</center></div>';
                }

            }

          ?>

          <div class="form-group ">
            <button type="submit" class="btn btn-primary btn-lg btn-block login-button">Register</button>
          </div>

          <?php } else {
            echo '<center><iframe width="560" height="315" src="https://www.youtube.com/embed/X9otDixAtFw" frameborder="0" allowfullscreen></iframe><center>';
            } ?>

          <div class="login-register">
            <center><a href="index.php">Login</a> - <a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
          </div>
        </form>
      </div>
    </div>
  </div>
