<?php

include 'navbar.php';

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-120 minutes', time());
$end = time();

$memoryUsage = $S->getUage('Memory',$start,$end);

?>

<div class="col-md-12">
  <h3 class="text-left">Memory</h3>
  <div id="chart-memory"></div>
</div>

<script>
var chart = c3.generate({
  bindto: '#chart-memory',
  data: {
    columns: [
        ['Free', <?php echo implode(",", $memoryUsage['free']); ?>],
        ['Cached', <?php echo implode(",", $memoryUsage['cached']); ?>],
        ['Buffer', <?php echo implode(",", $memoryUsage['buffers']); ?>],
        ['Used', <?php echo implode(",", $memoryUsage['used']); ?>],
        ['Active', <?php echo implode(",", $memoryUsage['active']); ?>],
        ['Inactive', <?php echo implode(",", $memoryUsage['inactive']); ?>]
    ],
    types: {
        Free: 'area',
        Cached: 'area',
        Buffer: 'area',
        Used: 'area',
    },
    groups: [['Free', 'Cached','Buffer','Used']]
},
point: {
     show: false
 },
 size: {
   height: 300
 },
axis: {
  x: {
        type: 'category',
        categories: [<?php echo implode(",", $memoryUsage['timestamp']); ?>],
        tick: {
        width: 80,
            culling: {
                max: 7
            }
          }
      },
  y: {
      label: 'MB'
  },
}
});
</script>
