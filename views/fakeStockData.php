<?php

$chartData = [];

$offset = 100 * (rand()/getRandMax())**4;
$scale = 100 * (rand()/getRandMax())**2;
$volatility = 0.5 * (rand()/getRandMax())**3;

for ($n = 0, $current = $offset + 0.5 * $scale; $n < 96; $n++) {
  $current -= $offset;
  $current *= 1 + $volatility * (rand()/getRandMax() - 0.5);
  $current += $offset;
  $chartData[$n] = $current;
}

$stockChart = new NeatCharts\LineChart($chartData, [
  "width"=>500,
  "height"=>150,
  "fontSize"=>10
]);
print $stockChart->render();

?>
