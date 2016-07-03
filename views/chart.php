<?php
use phpFastCache\CacheManager;

function getJson($url) {
  if (empty($url)) {
    trigger_error('Missing or empty JSON url');
  }
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  // http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
//  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
//  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//  curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/VeriSignClass3PublicPrimaryCertificationAuthority-G5.pem");

  $result = curl_exec($ch);
  $result = json_decode($result) or trigger_error('Couldn\'t parse JSON');
  return $result;
}

function renderChart(
  $theme,
  $currencyA,
  $currencyB,
  $duration,
  $format = 'svg'
) {

  $durations = [
    '1y'=> [
      'duration' => 60 * 60 * 24 * 365,
      'resolution' => 86400 // 1d
    ],
    '30d'=> [
      'duration' => 60 * 60 * 24 * 30,
      'resolution' => 7200 // 2h
    ],
    '7d'=> [
      'duration' => 60 * 60 * 24 * 7,
      'resolution' => 1800 // 30m
    ],
    '24h' => [
      'duration' => 60 * 60 * 24 * 1,
      'resolution' => 300 // 15m
    ]
  ];

  $themes = [
    'light'=>[
      'lineColor'=>'#FFF',
      'markerColor'=>'#FFF',
      'labelColor'=>'#FFF',
      'width'=>800,
      'height'=>250,
      'smoothed'=>false,
      'fontSize'=>12
    ],
    'dark'=>[
      'lineColor'=>'#000',
      'markerColor'=>'#000',
      'labelColor'=>'#000',
      'width'=>800,
      'height'=>250,
      'smoothed'=>false,
      'fontSize'=>12
    ],
    'sparkline'=>[
      'lineColor'=>'#000',
      'markerColor'=>'#F00',
      'width'=>100,
      'height'=>20,
      'fontSize'=>2,
      'yAxisEnabled'=>false,
      'xAxisEnabled'=>false
    ]
  ];

  if (!array_key_exists($theme, $themes)) {
    return false;
  }

  if (array_key_exists($duration, $durations)) {
    $dataDuration = $durations[$duration]['duration'];
    $dataResolution = $durations[$duration]['resolution'];
  } else {
    return false;
  }

  $supportedCurrencies = CacheManager::get('poloniex-supported-currencies');
  if (is_null($supportedCurrencies)) {
    $supportedCurrenciesJson = getJson('https://poloniex.com/public?command=returnCurrencies');
    foreach ($supportedCurrenciesJson as $key => $value) {
      if ($value->delisted == 0) {
        $supportedCurrencies[] = strtolower($key);
      }
    }
    CacheManager::set('poloniex-supported-currencies', $supportedCurrencies, 60 * 60 * 24 * 7); // asking once a week doesn't seem like too much
  }
  if ($currencyB != 'btc' || !in_array($currencyA, $supportedCurrencies)) {
    return false;
  }

  $pair = strtoupper($currencyB . '_' . $currencyA); // poloniex you strange

  $chartCacheKey = 'poloniex-'.$theme.'-'.$pair.'-'.$dataDuration.'-'.$format;

  $result = CacheManager::get($chartCacheKey);

  if (is_null($result)) {
    $startTime = time() - $dataDuration;
    $poloniexUrl = 'https://poloniex.com/public?command=returnChartData&currencyPair=' . $pair . '&start=' . $startTime . '&end=9999999999&period=' . $dataResolution;

    $poloniexJson = CacheManager::get('poloniex-json-'.$pair.'-'.$dataDuration);

    if(is_null($poloniexJson)) {
      $poloniexJson = getJson($poloniexUrl);
      // Write to cache for next time
      // Expires either in a minute, or 60s after the next data point is supposed to be available
      $cacheTimeSeconds = max(60, end($poloniexJson)->date + $dataResolution - time() + 60);
      CacheManager::set('poloniex-json-'.$pair.'-'.$dataDuration, $poloniexJson, $cacheTimeSeconds);
    } else {
      $cacheTimeSeconds = max(60, end($poloniexJson)->date + $dataResolution - time() + 60);
    }

    $chartData = [];

    foreach ($poloniexJson as $item) {
      $chartData[$item->date] = $item->weightedAverage;
    }

    if ($format == 'svg') {
      $chartOptions = array_replace($themes[$theme], ['lineColor'=>'@lineColor', 'markerColor'=>'@markerColor']);
    }

    $poloniexChart = new NeatCharts\LineChart($chartData, $chartOptions);
    $result = '<?xml version="1.0" standalone="no"?>' . PHP_EOL;
    $result .= $poloniexChart->render();

    if ($format == 'png') {
      $im = new Imagick();
      $im->setBackgroundColor(new ImagickPixel('transparent'));
      $im->readImageBlob($result);
      $im->setImageFormat('png32');
      $result = $im->getImageBlob();
      $im->clear();
      $im->destroy();
    }
    CacheManager::set($chartCacheKey, $result, $cacheTimeSeconds);
    $resultExpires = time() + $cacheTimeSeconds;
  } else {
    $resultExpires = CacheManager::getInfo($chartCacheKey)[ 'expired_time' ];
    // TODO cache an object that has the data and a when-expired timestamp to avoid this cache-info lookup
    $startTime = $resultExpires - $dataDuration;
  }

  header('Expires: '.gmdate(DateTime::RFC1123, $resultExpires));
  if ($format == 'png') {
    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $startTime) . '--' . gmdate('Y-m-d\THis+0') . '.png"');
    echo $result;
  } else if ($format == 'svg') {
    header('Content-type: image/svg+xml; charset=utf-8');
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $startTime) . '--' . gmdate('Y-m-d\THis+0') . '.svg"');

    if (array_key_exists('lineColor', $_GET)) {
      $lineColor = htmlspecialchars($_GET['lineColor']);
      if (1 === preg_match('/^[a-fA-F0-9]{3,6}/', $lineColor)) {
        //this is an HTML color
        $lineColor = '#' . $lineColor;
      }
    } else {
      $lineColor = $themes[$theme]['lineColor'];
    }

    if (array_key_exists('markerColor', $_GET)) {
      $markerColor = htmlspecialchars($_GET['markerColor']);
      if (1 === preg_match('/^[a-fA-F0-9]{3,6}/', $markerColor)) {
        //this is an HTML color
        $markerColor = '#' . $markerColor;
      }
    } else {
      $markerColor = $themes[$theme]['markerColor'];
    }

    echo str_replace(['@lineColor', '@markerColor'], [$lineColor, $markerColor], $result);
  } else {
    return false;
  }
  return true;
}
