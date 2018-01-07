<body>

<?php include 'content/navbar.php'; ?>

  <div class="container page-style">

    <div class="row">
      <div class="col-md-12 col-md-offset-0">

        <?php

        $serverID = str_replace("server=", "", $_GET["server"]);

        if (isset($_GET["resource"]) && $_GET["resource"] == 'dashboard') {
          include 'resources/dashboard.php';
        } elseif (isset($_GET["resource"]) && $_GET["resource"] == 'cpu') {
          include 'resources/cpu.php';
        } else {
          include 'overview.php';
        }

        ?>

      </div>
    </div>
    <center><a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
  </div>
