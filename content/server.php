<body>

<?php include 'content/navbar.php'; ?>

  <div class="col-lg-10 col-lg-offset-1 page-style">

    <div class="row">

        <?php

        if (isset($_POST['timestampStart']) && isset($_POST['timestampEnd']) ) {
          $start = $_POST['timestampStart'];
          $_SESSION["timestampStart"] = $_POST['timestampStart'];
          $end = $_POST['timestampEnd'];
          $_SESSION["timestampEnd"] = $_POST['timestampEnd'];
        } elseif (isset($_SESSION['timestampStart']) && isset($_SESSION['timestampEnd']) ) {
          $start = $_SESSION["timestampStart"];
          $end = $_SESSION["timestampEnd"];
        } else {
          $start = strtotime('-120 minutes', time());
          $end = time();
        }

        $serverID = str_replace("server=", "", $_GET["server"]);

        if (isset($_GET["resource"]) && $_GET["resource"] == 'dashboard') {
          include 'resources/dashboard.php';
        } elseif (isset($_GET["resource"]) && $_GET["resource"] == 'cpu') {
          include 'resources/cpu.php';
        } elseif (isset($_GET["resource"]) && $_GET["resource"] == 'memory') {
          include 'resources/memory.php';
        } elseif (isset($_GET["resource"]) && $_GET["resource"] == 'disk') {
          include 'resources/disk.php';
        } elseif (isset($_GET["resource"]) && $_GET["resource"] == 'network') {
          include 'resources/network.php';
        } elseif (isset($_GET["resource"]) && $_GET["resource"] == 'alerts') {
          include 'resources/alert.php';
        } else {
          include 'overview.php';
        }

        ?>

    </div>
    <center><a href="index.php?p=tos">Terms of Service</a> - <a href="index.php?p=privacy">Privacy</a></center>
  </div>
