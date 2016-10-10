<?php

include 'content/header.html';

date_default_timezone_set('Europe/Amsterdam');
session_set_cookie_params(0,'/','.'._Domain,true,true);
session_start();

if (isset($_GET["page"])) {
  $page = $_GET["page"];
}

if(!isset($page)) {
  $page="login";
}

?>

<body>

  <div class="container">
    <div class="row">
      <div class="one-half column" style="margin-top: 25%">
        <h4>Night Sky Monitoring</h4>
        <form action="index.php?page=login" method="post">
          <div class="row">
            <div class="seven columns">
              <label for="email">Username</label>
              <input type="text" name="username" placeholder="User" required/>

              <label for="password">Password</label>
              <input type="password" name="password" required/>

              <button type="submit" class="button-primary">Sign In</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php

include 'content/footer.html';

?>
