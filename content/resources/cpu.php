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
        ['CPU Load', <?php echo implode(",", $cpuLoad['idle']); ?>],
    ]
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
          ['CPU Load',".implode(',',$cpuLoad['load'][$i])."],
      ]
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
