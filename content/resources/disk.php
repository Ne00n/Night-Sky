<?php

include 'navbar.php';

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-120 minutes', time());
$end = time();

$diskUsage = $S->getUage('Disk',$start,$end);

?>

<div class="col-md-12">
  <h3 class="text-left">Disk</h3>
  <div id="chart-hdd"></div>
</div>

<script>
var chart = c3.generate({
  bindto: '#chart-hdd',
  data: {
    columns: [
        ['Used', <?php echo implode(",", $diskUsage['used']); ?>],
        ['Total', <?php echo implode(",", $diskUsage['total']); ?>]
    ],
    types: {
        Used: 'area',
        Free: 'area'
    },
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
       categories: [<?php echo implode(",", $diskUsage['timestamp']); ?>],
       tick: {
       width: 80,
           culling: {
               max: 7
           }
         }
     },
     y: {
      label: 'GB'
  },
 }
});
</script>
