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
  $format = 'svg',
  $width = 800,
  $height = 200,
  $fontSize = 12
) {

  $durations = [
//    '30d'=> [
//      'duration' => 60 * 60 * 24 * 30,
//      'resolution' => 7200 // 2h
//    ],
    '7d'=> [
      'duration' => 60 * 60 * 24 * 7,
      'resolution' => 1800 // 30m
    ],
    '24h' => [
      'duration' => 60 * 60 * 24 * 1,
      'resolution' => 900 // 15m
    ]
  ];

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
      CacheManager::set('poloniex-json-'.$pair.'-'.$dataDuration, $poloniexJson, $dataDuration);
    }

    $chartData = [];

    foreach ($poloniexJson as $item) {
      $chartData[$item->date] = $item->weightedAverage;
    }

    $poloniexChart = new NeatCharts\LineChart($chartData, [
      'width'=>800,
      'height'=>200,
      'lineColor'=>($theme == 'dark' ? '#000' : '#fff'),
      'labelColor'=>($theme == 'dark' ? '#000' : '#fff'),
      'smoothed'=>false,
      'fontSize'=>12
    ]);
    $result = $poloniexChart->render();

    if ($format == 'png') {
      $im = new Imagick();
      $im->setBackgroundColor(new ImagickPixel('transparent'));
      $im->readImageBlob($result);
      $im->setImageFormat('png32');
      $result = $im->getImageBlob();
      $im->clear();
      $im->destroy();
    }
    CacheManager::set($chartCacheKey, $result);
    $resultExpires = time() + $dataDuration;
  } else {
    $resultExpires = CacheManager::getInfo($chartCacheKey)[ 'expired_time' ];
    $startTime = $resultExpires - $dataDuration;
  }

  header('Expires: '.gmdate('D, d M Y H:i:s', $resultExpires));
  if ($format == 'svg') {
    header('Content-type: image/svg+xml; charset=utf-8');
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $startTime) . '--' . gmdate('Y-m-d\THis+0') . '.svg"');
  } else if ($format == 'png') {
    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $startTime) . '--' . gmdate('Y-m-d\THis+0') . '.png"');
  }
  echo $result;
  return true;
}
