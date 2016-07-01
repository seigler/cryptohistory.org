<?php
$chartData = [];

$start = 100 * (rand()/getRandMax())**3;
$volatility = rand()/getRandMax() + 0.01;
$velocity = (rand()/getRandMax() - 0.5);
$acceleration = 0.1 * (rand()/getRandMax())**2;

for ($n = 0, $current = $start; $n < 12; $n++) {
  $velocity *= 0.5;
  $velocity += $acceleration * 2 * (rand()/getRandMax() - 0.5);
  $current += $velocity;
  $chartData[$n] = $current;
}

$tempChart = new NeatCharts\LineChart($chartData,  [
  "width"=>700,
  "height"=>400,
  "lineColor"=>"#D00",
  "labelColor"=>"#777",
  "smoothed"=>true
]);
print $tempChart->render();
?>
