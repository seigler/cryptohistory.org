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
  $pair,
  $dataDuration = (7 * 24 * 60 * 60),
  $dataResolution = 1800,
  $format = 'svg',
  $color = '#000',
  $width = 800,
  $height = 200,
  $fontSize = 12
) {

  $result = CacheManager::get('poloniex-'.$pair.'-'.$dataDuration.'-'.$format);

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
      'lineColor'=>"#1C75BC", // Dash blue
      'labelColor'=>"#000",
      'smoothed'=>false,
      'fontSize'=>12
    ]);
    $result = $poloniexChart->render();

    if ($format == 'png') {
      $im = new Imagick();
      $im->setBackgroundColor(new ImagickPixel("transparent"));
      $im->readImageBlob($result);
      $im->setImageFormat("png32");
      $result = $im->getImageBlob();
      $im->clear();
      $im->destroy();
    }
    CacheManager::set('poloniex-'.$pair.'-'.$dataDuration.'-'.$format, $result, $dataDuration);
    $resultExpires = time() + $dataDuration;
  } else {
    $resultExpires = CacheManager::getInfo('poloniex-'.$pair.'-'.$dataDuration.'-'.$format)[ 'expired_time' ];
    $startTime = $resultExpires - $dataDuration;
  }

  header('Expires: '.gmdate("D, d M Y H:i:s", $resultExpires));
  if ($format == 'svg') {
    header('Content-type: image/svg+xml; charset=utf-8');
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $startTime) . '--' . gmdate('Y-m-d\THis+0') . '.svg"');
  } else if ($format == 'png') {
    header("Content-Type: image/png");
    header('Content-Disposition: inline; filename="Dash-chart-' . gmdate('Y-m-d\THis+0', $startTime) . '--' . gmdate('Y-m-d\THis+0') . '.png"');
  }
  echo $result;
}
