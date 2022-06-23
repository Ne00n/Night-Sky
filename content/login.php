<main class="form-signin">
  <form action="index.php?p=login" method="post">
    <h1 class="h3 mb-3 fw-normal">Night-Sky Monitoring</h1>

    <div class="form-floating">
      <input type="input" class="form-control" id="floatingInput" placeholder="Username">
      <label for="floatingInput">Username</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
      <label for="floatingPassword">Password</label>
    </div>
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

            $Login = new Login($DB);

            if (!$Login->check_blocked_ip($_SERVER['REMOTE_ADDR'])) {

              $Verify = new Verify($DB);
              $Verify->ValidateLogin($_POST['username'],$_POST['password']);
              if ($Verify->getLastError() == "") {
                $_SESSION['logged_in'] = 1;
                $_SESSION['user_id'] = $Verify->getUserID();
                header('Location: index.php?p=main');
              } else {
                echo '<div class="alert alert-danger" role="alert"><center>'.$Verify->getLastError().'</center></div>';
                $Login->addtoBlacklist($_SERVER['REMOTE_ADDR']);
              }

            } else {
              echo '<div class="alert alert-danger" role="alert"><center>IP Blocked</center></div>';
            }

          } else {
              echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
          }

        }

       ?>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    <div class="text-center pt-2"><a href="index.php?p=register">Register</a> - <a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></div>
  </form>
</main>