<div class="container">

   <h2 class="form-signin-heading"><center>Night Sky Monitoring</center></h2>
  <form class="form-signin" action="index.php?p=login" method="post">
    <label class="sr-only">Username</label>
    <input type="text" class="form-control" placeholder="Username" name="username" required>
    <label class="sr-only">Password</label>
    <input type="password" class="form-control" placeholder="Password" name="password" required>
    <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
      <?php

        if (isset($k)) {

          $Verify = new Verify($DB);
          if ($Verify->checkHash($k)) {

            $U = new User($DB);
            $U->enableUser($k);

            if ($U->getlastError() == "") {
              echo '<div class="alert alert-success" role="alert"><center>Account enabled</center></div>';
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$Verify->getLastError().'</center></div>';
            }

          } else {
            echo '<div class="alert alert-danger" role="alert"><center>Invalid Key</center></div>';
          }

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

          if ($_POST['Token'] == $_SESSION['Token']) {

            $Verify = new Verify($DB);
            $Verify->ValidateLogin($_POST['username'],$_POST['password']);
            if ($Verify->getLastError() == "") {
              $_SESSION['logged_in'] = 1;
              $_SESSION['user_id'] = $Verify->getUserID();
              header('Location: index.php?p=main');
            } else {
              echo '<div class="alert alert-danger" role="alert"><center>'.$Verify->getLastError().'</center></div>';
            }

          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        }

       ?>
    <button type="submit" class="btn btn-lg btn-primary btn-block">Sign in</button>
  </form>
  <center><a href="index.php?p=register">Register</a> - <a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>

</div>
