<?php

include 'navbar.php';

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-120 minutes', time());
$end = time();

$cpuLoad = $S->getUage('CPU',$start,$end);
?>

<div class="col-md-12">
  <h3 class="text-left">Total CPU usage</h3>
  <div id="chart-cpu"></div>
</div>

<?php
  for ($i = 0; $i <= count($cpuLoad['load']) -1; $i++) {
    echo '<div class="col-md-6"><h3 class="text-left">CPU '.Page::escape($i).'</h3><div id="chart-cpu'.Page::escape($i).'"></div></div>';
  }
?>

<script>
var chart = c3.generate({
  bindto: '#chart-cpu',
  data: {
    columns: [
        ['User', <?php echo implode(",", $cpuLoad['userA']); ?>],
        ['System', <?php echo implode(",", $cpuLoad['systemA']); ?>],
        ['Nice', <?php echo implode(",", $cpuLoad['niceA']); ?>],
        ['Steal', <?php echo implode(",", $cpuLoad['stealA']); ?>],
        ['IOWait', <?php echo implode(",", $cpuLoad['iowaitA']); ?>]
    ],
    types: {
        System: 'area',
        User: 'area',
        Nice: 'area',
        Steal: 'area',
        IOWait: 'area',
    },
    groups: [['System', 'User','Nice','Steal','IOWait']]
  },
  point: {
     show: false
  },
  size: {
    height: 200
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
</script>

<?php

for ($i = 0; $i <= count($cpuLoad['load']) -1; $i++) {
  echo "<script>
  var chart = c3.generate({
    bindto: '#chart-cpu".Page::escape($i)."',
    data: {
      columns: [
          ['System',".implode(',',$cpuLoad['system'][$i])."],
          ['User',".implode(',',$cpuLoad['user'][$i])."],
          ['Nice',".implode(',',$cpuLoad['nice'][$i])."],
          ['Steal',".implode(',',$cpuLoad['steal'][$i])."],
          ['IOWait',".implode(',',$cpuLoad['iowait'][$i])."]
      ],
      types: {
          System: 'area',
          User: 'area',
          Nice: 'area',
          Steal: 'area',
          IOWait: 'area',
      },
      groups: [['System', 'User','Nice','Steal','IOWait']]
    },
    point: {
       show: false
    },
    size: {
      height: 200
    },
    axis: {
    x: {
          type: 'category',
          categories: [".implode(',',$cpuLoad['timestamp'])."],
          tick: {
          width: 70,
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
  </script>";
}

?>
