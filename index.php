<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require_once 'vendor/autoload.php';

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
$router->map( 'GET', '/charts/[dark|light|sparkline|candlestick|filled:theme]/[a:curA]-[a:curB]/[a:duration]/[svg|png:format]', function($theme, $curA, $curB, $duration, $format) {
  require __DIR__ . '/views/chart.php';
  return renderChart(
    $theme,
    $curA,
    $curB,
    $duration,
    $format
  );
});

// match current request url
$match = $router->match();

// call closure or throw 404 status
if( !$match || !is_callable( $match['target'] ) || false === call_user_func_array( $match['target'], $match['params'] )) {
  // no route was matched
  header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
  echo '<h1>404 Not Found</h1><p>Page not found</p>';
}

?>
