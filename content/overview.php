<?php

if (Page::startsWith($p,"server?remove=")) {

  $serverID = str_replace("server?remove=", "", $p);

  if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

    if ($_POST['Token'] == $_SESSION['Token']) {

      $S = new Server($DB,$Login);
      $S->setID($serverID);
      $S->removeServer();
      if ($S->getLastError() == "") {
        echo '<div class="alert alert-success" role="alert"><center>Success.</center></div>';
      } else {
        echo '<div class="alert alert-danger" role="alert"><center>'.$S->getLastError().'</center></div>';
      }

    } else {
        echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
    }

  } else {

  ?>

    <p>Are you sure, that you want to delete this Check?</p>

    <form class="form-horizontal" action="index.php?p=server?remove=<?= Page::escape($serverID) ?>" method="post">
      <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
      <div class="form-group">
          <button type="submit" name="confirm" class="btn btn-danger">Yes</button><a href="index.php?p=main"><button class="btn btn-primary" type="button">No</button></a>
      </div>
    </form>

    <?php
  }

}

if ($p == "server?add") {

  if ($_SERVER['REQUEST_METHOD'] == 'POST' AND isset($_POST['confirm'])) {

    if ($_POST['Token'] == $_SESSION['Token']) {

      $S = new Server($DB,$Login);
      $S->addServer($_POST['name'],$_POST['groups']);

       if ($S->getlastError() == "") {
         echo '<div class="alert alert-success" role="alert"><center>Success.<br>To install the agent please run this command:<br><pre>wget https://raw.githubusercontent.com/Ne00n/Night-Sky-PRM/master/install.sh && bash install.sh '. $S->getToken().' '._Domain.'</pre></center></div>';
         $_POST = array();
       } else {
         echo '<div class="alert alert-danger" role="alert"><center>'.$S->getLastError().'</center></div>';
       }

    } else {
        echo '<div class="alert alert-danger" role="alert"><center>Token Verification Failed</center></div>';
    }

  } ?>

  <form class="form-horizontal" action="index.php?p=server?add" method="post">
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="input-group">
         <div class="input-group-addon">
        <span class="fa fa-pencil"></span>
         </div>
          <input value="<?php if (isset($_POST['name'])) {echo Page::escape($_POST['name']);} ?>" type="text" class="form-control input-sm" name="name" placeholder="Bastion"/>
        </div>
      </div>
    </div>
    <input type="hidden" name ="Token" value="<?php echo Page::escape($_SESSION['Token']); ?>">
    <div class="form-group">
          <div class="col-sm-8 col-sm-offset-2">
            <div class="input-group">
              <div class="input-group-addon">
             <span class="fa fa-group"></span>
              </div>
              <select class="selectpicker form-control input-sm" data-size="3" data-style="btn-default btn-sm" name="groups">
                <?php
                $query = "SELECT ID,Name FROM groups WHERE USER_ID=? GROUP BY ID";
                $USER_ID = $Login->getUserID();
                $stmt = $DB->GetConnection()->prepare($query);
                $stmt->bind_param('i', $USER_ID);
                $stmt->execute();
                $stmt->bind_result($db_group_id, $db_group_name);
                while ($stmt->fetch()) {
                     echo '<option value="'. Page::escape($db_group_id) .'">'. Page::escape($db_group_name) .'</option>';
                }
                $stmt->close(); ?>
              </select>
             </div>
        </div>
    </div>
    <div class="form-group">
        <button type="submit" name="confirm" class="btn btn-primary">Save</button>
    </div>
  </form>

  <?php } ?>

<div class="table-responsive table-hover">
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>CPU</th>
        <th>Memory</th>
        <th>Disk</th>
        <th>Network</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>

    <?php

    $USER_ID = $Login->getUserID();
    $S = new Server($DB,$Login);

    $query = "SELECT ID,UserID,Name FROM serversToken WHERE UserID = ? ";
    $stmt = $DB->GetConnection()->prepare($query);
    $stmt->bind_param('i', $USER_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $S->setID($row['ID']);
      $cpuLoad = $S->getUage('CPU','0','0',true);
      $memoryUsage = $S->getUage('Memory','0','0',true);
      $diskUsage = $S->getUage('Disk','0','0',true);
      $networkUsageTotal = $S->getUage('Network','0','0',true);
      echo '<tr>';
      echo '<td class="text-left"><a href="index.php?p=server&resource=dashboard&server='.$row['ID'].'">'.Page::escape($row['Name']).'</a></td>';
      echo '<td class="text-left">'.Page::escape($cpuLoad).'%</td>';
      echo '<td class="text-left">'.Page::escape($memoryUsage).'%</td>';
      echo '<td class="text-left">'.Page::escape($diskUsage).'%</td>';
      echo '<td class="text-left">'.Page::escape($networkUsageTotal).'Mbit</td>';
      echo '<td class="text-left col-md-3">';
      echo '<a href="index.php?p=server?remove='.Page::escape($row['ID']).'"><button class="btn btn-danger btn-xs" type="button"><i class="fa fa-times"></i></button></a></td>';
      echo '</tr>';

    } ?>

    </tbody>
  </table>
</div>

<div class="form-group">
<a href="index.php?p=server?add"><button class="btn btn-primary" type="button">Add a Server</button></a>
</div>
