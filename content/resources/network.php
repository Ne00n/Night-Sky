<?php

include 'navbar.php';

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-120 minutes', time());
$end = time();

$networkUsage = $S->getUage('Network',$start,$end);

?>

<div class="col-md-12">
  <h3 class="text-left">Total Network usage</h3>
  <div id="chart-net"></div>
</div>

<?php
  foreach ($networkUsage['nics'] as $key => $value) {
    echo '<div class="col-md-6"><h3 class="text-left">'.Page::escape($key).'</h3><div id="chart-'.Page::escape($key).'"></div></div>';
  }
?>


<script>
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
  height: 200
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

<?php

foreach ($networkUsage['nics'] as $key => $value) {
  echo "<script>
  var chart = c3.generate({
    bindto: '#chart-".Page::escape($key)."',
    data: {
      columns: [
          ['TX',".implode(',', $networkUsage['nic'][$key]['TX'])."],
          ['RX',".implode(',', $networkUsage['nic'][$key]['RX'])."]
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
    height: 200
  },
  axis: {
    x: {
          type: 'category',
          categories: [".implode(',', $networkUsage['timestamp'])."],
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
  </script>";
}

?>
