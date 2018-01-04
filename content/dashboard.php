<?php

$S = new Server($DB,$Login);
$serverID = str_replace("server=", "", $_GET["server"]);

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-200 minutes', time());
$end = time();

$cpuRaw = $S->getUage('CPU',$start,$end); $cpuLoad = array();
foreach ($cpuRaw as $element) {
  $cpuLoad[$element['timestamp']]['idle'] += $element['idle'];
  $cpuLoad[$element['timestamp']]['cores']++;
}

foreach ($cpuLoad as $key => $load) {
  $cpuLoad['idle'][] = abs(($cpuLoad[$key]['idle'] / $cpuLoad[$key]['cores']) - 100);
  $cpuLoad['timestamp'][] = date("'H:i'",$key);
}

?>

<div class="col-md-6">
  <canvas id="line-chart"></canvas>
</div>
<div class="col-md-6">
  <canvas id="line-chart2"></canvas>
</div>

<script>
// Bar chart
new Chart(document.getElementById("line-chart"), {
  type: 'line',
  data: {
    labels: [<?php echo implode(',',$cpuLoad['timestamp']); ?>],
    datasets: [{
        data: [<?php echo implode(',',$cpuLoad['idle']); ?>],
        label: "CPU Usage",
        borderColor: "#3e95cd",
        fill: false,
        radius: 0,
      }
    ]
  },
  options: {
    title: {
      display: true,
      text: 'CPU Usage in %'
    },
				tooltips: {
					mode: 'index',
				},
				hover: {
					mode: 'index'
				},
				scales: {
					xAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'Time'
						}
					}],
					yAxes: [{
						stacked: true,
						scaleLabel: {
							display: true,
							labelString: '%'
						}
					}]
				}
  }
});
</script>

<script>
// Bar chart
new Chart(document.getElementById("line-chart2"), {
  type: 'line',
  data: {
    labels: [<?php echo implode(',',$cpuLoad['timestamp']); ?>],
    datasets: [{
        data: [<?php echo implode(',',$cpuLoad['idle']); ?>],
        label: "CPU Usage",
        borderColor: "#3e95cd",
        fill: false,
        radius: 0,
      }
    ]
  },
  options: {
    title: {
      display: true,
      text: 'CPU Usage in %'
    },
				tooltips: {
					mode: 'index',
				},
				hover: {
					mode: 'index'
				},
				scales: {
					xAxes: [{
						scaleLabel: {
							display: true,
							labelString: 'Time'
						}
					}],
					yAxes: [{
						stacked: true,
						scaleLabel: {
							display: true,
							labelString: '%'
						}
					}]
				}
  }
});
</script>
