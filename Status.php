<?php
include 'content/configs/config.php';
include 'content/configs/regex.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
}
spl_autoload_register('dat_loader');

$DB = new Database;
$DB->InitDB();
$SP = new StatusPage($DB,false);

if (isset($_GET["token"])) {
  $token = $_GET["token"];
}
$servers = $SP->getServersbyToken($token);

include 'content/header.html';
echo '<body><div class="container">';
?>
  <?php
    if ($servers == false) {
  ?>
  <div class="row">
    <div class="col-md-12 col-sm-offset-2">
        <div class="alert alert-danger">Status Page token invalid!</div>
    </div>
  </div>
  <?php
    }
    else if (empty($servers['name'])) {
  ?>
  <div class="row">
    <div class="col-md-12 col-sm-offset-2">
        <div class="alert alert-danger">Status Page was not found!</div>
    </div>
  </div>
  <?php
    }
    else {
  ?>
  <div class="row">
    <div class="col-md-12 col-sm-offset-2">
      <h3><?php echo Page::escape($servers['name']); ?></h3>
    </div>
  </div>
  <div class="row clearfix">
      <div class="col-md-8 column col-sm-offset-2">
          <div class="panel panel-<?php echo ($servers['operational'] == 1 ? "success" : "warning"); ?>">
            <div class="panel-heading">
              <h3 class="panel-title">
              <?php echo ($servers['operational'] == 1 ? "All Systems Operational" : "Not All Systems Operational"); ?>
              </h3>
            </div>
          </div>

          <div class="row clearfix">
              <div class="col-md-12 column">
                  <div class="list-group">

                    <?php

                    foreach($servers['servers'] as $server)
                    {
                        echo '<div class="list-group-item">
                            <h4 class="list-group-item-heading">'.Page::escape($server['Name']).'
                              <span class="pull-right">
                                <div class="text-'.($server['Status'] == 1 ? "success" : "danger").'" id="fs-small">'.($server['Status'] == 1 ? "Operational" : "Not Operational").'</div>
                              </span>
                            </h4>
                        </div>';
                    }

                    ?>

                  </div>
              </div>
          </div>
      </div>
  </div>

  <?php
  }
  ?>

  </div>

<?php include 'content/footer.html'; ?>
