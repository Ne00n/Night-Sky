<?php

$S = new Server($DB,$Login);
$serverID = str_replace("server=", "", $_GET["server"]);

$S = new Server($DB,$Login);
$S->setID($serverID);

$start = strtotime('-200 minutes', time());
$end = time();

$cpuLoad = $S->getUage('CPU',$start,$end);

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
							display: true
						}, ticks: {
              autoSkip: true,
              maxTicksLimit: 6,
              maxRotation: 0
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
      text: 'CPU Usage'
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
