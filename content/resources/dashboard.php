<?php

include 'navbar.php';

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-5 minutes', time());
$end = time();

$cpuLoad = $S->getUage('CPU',$start,$end);
$memoryUsage = $S->getUage('Memory',$start,$end);
$diskUsage = $S->getUage('Disk',$start,$end);
$networkUsage = $S->getUage('Network',$start,$end);

?>

<div class="col-md-6">
  <h3 class="text-left">CPU</h3>
  <div id="chart-cpu"></div>
</div>
<div class="col-md-6">
  <h3 class="text-left">Memory</h3>
  <div id="chart-memory"></div>
</div>
<div class="col-md-6">
  <h3 class="text-left">Disk</h3>
  <div id="chart-hdd"></div>
</div>
<div class="col-md-6">
  <h3 class="text-left">Network</h3>
  <div id="chart-net"></div>
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
  size: {
    height: 300
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
 size: {
   height: 300
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
       categories: [<?php echo implode(",", $cpuLoad['timestamp']); ?>],
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

var chart = c3.generate({
  bindto: '#chart-net',
  data: {
    columns: [
        ['TX', <?php echo implode(",", $networkUsage['TX']); ?>],
        ['RX', <?php echo implode(",", $networkUsage['RX']); ?>]
    ],
    types: {
        TX: 'area',
        RX: 'area'
    },
    groups: [['RX' , 'TX']]
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
        categories: [<?php echo implode(",", $networkUsage['timestamp']); ?>],
        tick: {
        width: 80,
            culling: {
                max: 7
            }
          }
      },
  y: {
      label: 'MBit/s'
  },
}
});
</script>
