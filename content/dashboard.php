<?php

$S = new Server($DB,$Login);
$serverID = str_replace("server=", "", $_GET["server"]);

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-200 minutes', time());
$end = time();

$cpuLoad = $S->getUage('CPU',$start,$end);
$memoryUsage = $S->getUage('Memory',$start,$end);

?>

<div class="col-md-6">
  <div id="chart-cpu"></div>
</div>
<div class="col-md-6">
  <div id="chart-memory"></div>
</div>


<script>
var chart = c3.generate({
  bindto: '#chart-cpu',
  data: {
    columns: [
        ['CPU Load', <?php echo implode(",", $cpuLoad['idle']); ?>],
    ]
  },
  point: {
     show: false
  },
  axis: {
  x: {
        type: 'category',
        categories: [<?php echo implode(",", $cpuLoad['timestamp']); ?>],
        tick: {
        width: 80,
            culling: {
                max: 7
            }
          }
      },
  y: {
      label: '%'
  },
}
});

var chart = c3.generate({
  bindto: '#chart-memory',
  data: {
    columns: [
        ['Free', <?php echo implode(",", $memoryUsage['free']); ?>],
        ['Cached', <?php echo implode(",", $memoryUsage['cached']); ?>],
        ['Buffer', <?php echo implode(",", $memoryUsage['buffer']); ?>],
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
axis: {
  x: {
        type: 'category',
        categories: [<?php echo implode(",", $cpuLoad['timestamp']); ?>],
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
