<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
require_once 'vendor/seigler/neat-charts/src/NeatCharts/NeatChart.php';
require_once 'vendor/seigler/neat-charts/src/NeatCharts/LineChart.php';

use phpFastCache\CacheManager;

CacheManager::setup([
  'storage' => 'files',
  'path' => sys_get_temp_dir().'/cryptohistory-cache/'
]);

$router = new AltoRouter();

$router->map('GET', '/', function() {
  require __DIR__ . '/views/index.php';
});

// map cryptocurrency stuff
$router->map( 'GET', '/charts/[dark|light:theme]/[a:curA]-[a:curB]/[7d|24h:duration]/[svg|png:format]', function($theme, $curA, $curB, $duration, $format) {
  require __DIR__ . '/views/chart.php';
  renderChart(
    $theme,
    strtoupper($curB.'_'.$curA),
    60 * 60 * 24 * ($duration == '7d' ? 7 : 1),
    ($duration == '7d' ? 1800 : 300),
    $format,
    800,
    200,
    12
  );
  return true;
});

// match current request url
$match = $router->match();

// call closure or throw 404 status
if( !$match || !is_callable( $match['target'] ) || false === call_user_func_array( $match['target'], $match['params'] )) {
  // no route was matched
  header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
  echo '<h1>404 Not Found</h1><p>Page not found</p>';
}
try {
} catch (Exception $e) {
  header($_SERVER["SERVER_PROTOCOL"]." 500 Server Error", true, 500);
  echo 'There was some problem generating that for you.';
  return true;
}

?>
