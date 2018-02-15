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

function getThemeVariable($variable, $defaults) {
  if (!array_key_exists($variable, $defaults)) {
    return null;
  }
  if (array_key_exists($variable, $_GET)) {
    $toReturn = htmlspecialchars($_GET[$variable]);
    if (1 === preg_match('/^[a-fA-F0-9]{3,6}/', $toReturn)) {
      //this is an HTML color
      return '#' . $toReturn;
    } else {
      return $toReturn;
    }
  } else {
    return $defaults[$variable];
  }
}

function renderChart(
  $theme,
  $currencyA,
  $currencyB,
  $duration,
  $format = 'svg'
) {

  $currencyA = strtoupper($currencyA);
  $currencyB = strtoupper($currencyB);

  $themes = [
    'light'=>[
      'lineColor'=>'#FFF',
      'markerColor'=>'#FFF',
      'labelColor'=>'#FFF',
      'width'=>800,
      'height'=>250,
      'smoothed'=>false,
      'yAxisEnabled'=>true,
      'xAxisEnabled'=>true,
      'fontSize'=>12,
      'shadow'=>'#000'
    ],
    'dark'=>[
      'lineColor'=>'#000',
      'markerColor'=>'#000',
      'labelColor'=>'#000',
      'width'=>800,
      'height'=>250,
      'smoothed'=>false,
      'yAxisEnabled'=>true,
      'xAxisEnabled'=>true,
      'fontSize'=>12,
      'shadow'=>'#FFF'
    ],
    'sparkline'=>[
      'lineColor'=>'#000',
      'markerColor'=>'#F00',
      'width'=>100,
      'height'=>20,
      'fontSize'=>4,
      'yAxisEnabled'=>false,
      'xAxisEnabled'=>false
    ],
    'candlestick'=>[
      'width'=>800,
      'height'=>250,
      'barColor'=>'#000',
      'risingColor'=>'#0D0',
      'fallingColor'=>'#D00',
      'labelColor'=>'#000',
      'fontSize'=>15,
      'yAxisEnabled'=>true,
      'xAxisEnabled'=>true,
      'shadow'=>'#FFF'
    ],
    'filled'=>[
      'width'=>800,
      'height'=>250,
      'lineColor'=>'#000',
      'labelColor'=>'#000',
      'smoothed'=>true,
      'fontSize'=>15,
      'yAxisEnabled'=>true,
      'xAxisEnabled'=>false,
      'yAxisZero'=>true,
      'filled'=>true
    ]
  ];

  if (!array_key_exists($theme, $themes)) {
    return false;
  }

  $durations = [
    /* day, hour, minute */
    '1y'=> [
      'resolution' => 'day',
      'limit' => 365,
      'aggregate' => 7,
      'cacheTimeSeconds' => 7 * 24 * 60 * 60
    ],
    '30d'=> [
      'resolution' => 'day',
      'limit' => 30,
      'aggregate' => 1,
      'cacheTimeSeconds' => 24 * 60 * 60
    ],
    '7d'=> [
      'resolution' => 'hour',
      'limit' => 7 * 24,
      'aggregate' => 4,
      'cacheTimeSeconds' => 4 * 24 * 60 * 60
    ],
    '24h' => [
      'resolution' => 'minute',
      'limit' => 24 * 60,
      'aggregate' => 15,
      'cacheTimeSeconds' => 15 * 60
    ]
  ];

  if (array_key_exists($duration, $durations)) {
    $resolution = $durations[$duration]['resolution'];
    $limit = $durations[$duration]['limit'];
    $aggregate = $durations[$duration]['aggregate'];
    $cacheTimeSeconds = $durations[$duration]['cacheTimeSeconds'];
  } else {
    return false;
  }

  // $supportedCurrencies = CacheManager::get('cryptocompare-supported-currencies');
  // if (is_null($supportedCurrencies)) {
  //   $supportedCurrenciesJson = getJson('https://min-api.cryptocompare.com/data/all/coinlist');
  //   $supportedCurrencies = [
  //     'USD',
  //     'EUR',

  //   ];
  //   foreach ($supportedCurrenciesJson->Data as $key => $value) {
  //     if ($value->IsTrading == 1) {
  //       $supportedCurrencies[] = $key;
  //     }
  //   }
  //   CacheManager::set('cryptocompare-supported-currencies', $supportedCurrencies, 60 * 60 * 24); // asking once a day seems reasonable
  // }

  // $currencyA = strtoupper($currencyA);
  // $currencyB = strtoupper($currencyB);

  // if (!in_array($currencyA, $supportedCurrencies) || !in_array($currencyB, $supportedCurrencies)) {
  //   return false;
  // }

  $chartCacheKey = 'cryptocompare-'.$theme.'-'.$currencyA.'-'.$currencyB.'-'.$duration.'-'.$format;

  $result = null;//CacheManager::get($chartCacheKey);

  if (is_null($result)) {
    $dataCacheKey = 'cryptocompare-'.$currencyA.'-'.$currencyB.'-'.$duration;
    $cryptocompareJson = CacheManager::get($dataCacheKey);

    if(is_null($cryptocompareJson)) {
      $cryptocompareJson = getJson('https://min-api.cryptocompare.com/data/histo'.
        "$resolution?fsym=$currencyA&tsym=$currencyB&limit=$limit&aggregate=$aggregate");

      if ($cryptocompareJson->Response == 'Error') {
        CacheManager::set($dataCacheKey, $cryptocompareJson, 60);
        return false;
      } else {
        // Write to cache for next time
        // Expires either in a minute, or 60s after the next data point is supposed to be available
        $cacheTimeSeconds = max(60, $cryptocompareJson->TimeTo + $cacheTimeSeconds - time() + 60);
        CacheManager::set($dataCacheKey, $cryptocompareJson, $cacheTimeSeconds);
      }
    } else {
      $cacheTimeSeconds = max(60, $cryptocompareJson->TimeTo + $cacheTimeSeconds - time());
    }

    if ($format == 'svg') {
      $chartOptions = array_replace($themes[$theme], [
        'lineColor'=>'@lineColor',
        'markerColor'=>'@markerColor',
        'risingColor'=>'@risingColor',
        'fallingColor'=>'@fallingColor'
      ]);
    } else {
      $chartOptions = $themes[$theme];
    }

    if ($theme == 'candlestick') {
      $chartData = $cryptocompareJson->Data;
      foreach ($chartData as $item) {
        $item->date = $item->time;
      }
      $neatChart = new NeatCharts\CandlestickChart($chartData, $chartOptions);
    } else {
      $chartData = [];
      foreach ($cryptocompareJson->Data as $item) {
        $chartData[$item->time] = ($item->high + $item->low + $item->close) / 3;
      }
      $neatChart = new NeatCharts\LineChart($chartData, $chartOptions);
    }
    $result = '<?xml version="1.0" standalone="no"?>' . PHP_EOL;
    $result .= $neatChart->render();

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
    $resultExpires = CacheManager::getInfo($chartCacheKey)['expired_time'];
    // TODO cache an object that has the data and a when-expired timestamp to avoid this cache-info lookup
  }

  header('Expires: '.gmdate(DateTime::RFC1123, $resultExpires));
  if ($format == 'png') {
    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $resultExpires) . '--' . gmdate('Y-m-d\THis+0') . '.png"');
    echo $result;
  } else if ($format == 'svg') {
    header('Content-type: image/svg+xml; charset=utf-8');
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $resultExpires) . '--' . gmdate('Y-m-d\THis+0') . '.svg"');
    $result = str_replace([
      '@lineColor',
      '@markerColor',
      '@risingColor',
      '@fallingColor'
    ], [
      getThemeVariable('lineColor', $themes[$theme]),
      getThemeVariable('markerColor', $themes[$theme]),
      getThemeVariable('risingColor', $themes[$theme]),
      getThemeVariable('fallingColor', $themes[$theme])
    ], $result);
    echo $result;
  } else {
    return false;
  }
  return true;
}
