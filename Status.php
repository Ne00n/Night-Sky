<?php

include 'content/header.html';
include 'content/config.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
}

spl_autoload_register('dat_loader');

$DB = new Database;
$DB->InitDB();

$Verify = array(); //create a dummy Verify, since we need to pass it but we dont access it

$SP = new StatusPage($DB,$Verify);

if (isset($_GET["token"])) {
  $token = $_GET["token"];
}

$servers = $SP->getServersbyToken($token);

echo '<body><div class="container">';

if (count($servers) != 1) {
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

                    foreach($servers as $key => $row)
                    {

                      if ($key !== "operational" && $key !== "name") {

                        echo '<div class="list-group-item">
                            <h4 class="list-group-item-heading">'.Page::escape($row['Name']).'
                              <span class="pull-right">
                                <div class="text-'.($row['Status'] == 1 ? "success" : "danger").'" id="fs-small">'.($row['Status'] == 1 ? "Operational" : "Not Operational").'</div>
                              </span>
                            </h4>
                        </div>';

                      }
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
