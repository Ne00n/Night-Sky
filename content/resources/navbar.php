<div class="col-md-12">
  <ul class="nav nav-tabs">
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'dashboard' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=dashboard&server=<?php echo Page::escape($serverID); ?>">Dashboard</a>
     </li>
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'cpu' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=cpu&server=<?php echo Page::escape($serverID); ?>">CPU</a>
     </li>
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'memory' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=memory&server=<?php echo Page::escape($serverID); ?>">Memory</a>
     </li>
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'disk' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=disk&server=<?php echo Page::escape($serverID); ?>">Disk</a>
     </li>
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'network' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=network&server=<?php echo Page::escape($serverID); ?>">Network</a>
     </li>
     <li role="presentation" class="<?php echo ($_GET["resource"] == 'alerts' ? 'active' : ''); ?>">
       <a href="index.php?p=server&resource=alerts&server=<?php echo Page::escape($serverID); ?>">Alerts</a>
     </li>
  </ul>
</div>