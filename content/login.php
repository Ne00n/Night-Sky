<div class="large-3 large-centered columns">
  <div class="login-box">
  <div class="row">
  <div class="large-12 columns">
    <form action="index.php?p=login" method="post">
       <div class="row">
         <div class="large-12 columns">
             <input type="text" name="username" placeholder="Username" />
         </div>
       </div>
      <div class="row">
         <div class="large-12 columns">
             <input type="password" name="password" placeholder="Password" />
         </div>
      </div>

      <?php

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

          $Verify = new Verify($DB);
          $Verify->ValidateLogin($_POST['username'],$_POST['password']);
          if ($Verify->getLastError() == "") {
            $_SESSION['logged_in'] = 1;
            $_SESSION['user_id'] = $Verify->getUserID();
            header('Location: index.php?p=main');
          } else {

          }

        }

       ?>

      <div class="row">
        <div class="large-12 large-centered columns">
          <input type="submit" class="button expand" value="Log In"/>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
</div>
