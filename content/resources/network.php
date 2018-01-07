<?php

include 'navbar.php';

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-120 minutes', time());
$end = time();

$networkUsage = $S->getUage('Network',$start,$end);

?>

<div class="col-md-12">
  <h3 class="text-left">Network</h3>
  <div id="chart-net"></div>
</div>


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
