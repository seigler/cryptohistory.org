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
$router->map( 'GET', '/charts/[dark|light:theme]/[a:curA]-[btc:curB]/[a:duration]/[svg|png:format]', function($theme, $curA, $curB, $duration, $format) {
  require __DIR__ . '/views/chart.php';
  return renderChart(
    $theme,
    $curA,
    $curB,
    $duration,
    $format,
    800,
    250,
    12
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
try {
} catch (Exception $e) {
  header($_SERVER["SERVER_PROTOCOL"]." 500 Server Error", true, 500);
  echo '<h1>500 Server Error</h1><p>There was a problem generating this page</p>';
  return true;
}

?>
