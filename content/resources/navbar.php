<div class="col-md-12">
  <ul class="nav nav-tabs">
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'dashboard' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=dashboard&server=<?php echo Page::escape($serverID); ?>">Dashboard</a>
     </li>
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'cpu' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=cpu&server=<?php echo Page::escape($serverID); ?>">CPU</a>
     </li>
     <li role="presentation">
        <a href="#">Memory</a>
     </li>
     <li role="presentation">
        <a href="#">Disk</a>
     </li>
     <li role="presentation">
        <a href="#">Network</a>
     </li>
     <li role="presentation">
        <a href="#">Alerts</a>
     </li>
  </ul>
</div>
