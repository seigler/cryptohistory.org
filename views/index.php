<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
  <title>Embeddable Cryptocurrency Charts : Cryptohistory</title>
  <style>
    *, *:after, *:before { box-sizing: inherit; }
    html {
      background-color: #ffd;
    }
    body {
      font-family: sans-serif;
      line-height: 1.25;
      margin: 0 auto;
      box-sizing: border-box;
      color: black;
      max-width: 60em;
      min-height: 100vh;
      position: relative;
    }
    body:before {
      content: '';
      display: table;
    }
    header {
      padding: 0 2rem;
    }
    main {
      padding: 0 2rem 8rem;
    }
    section {
      display: block;
      margin: 1rem 0;
    }
    section + section {
      margin-top: 2rem;
    }
    figure {
      margin-left: 0;
      margin-right: 0;
    }
    code {
      display: inline-block;
      padding: 0.1em;
      border: 1px dashed gray;
      font-size: 1.2em;
      background-color: rgba(255,255,255,0.75);
      background-clip: padding-box;
    }
    img {
      max-width: 100%;
    }
    footer {
      position: absolute;
      width: 100%;
      bottom: 0;
      padding: 0 2rem 2rem;
    }
  </style>
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:site" content="@seiglerj" />
  <meta name="twitter:title" content="Cryptohistory : Charts" />
  <meta name="twitter:description" content="Embeddable SVG and PNG cryptocurrency charts" />
  <meta name="twitter:image" content="https://cryptohistory.org/charts/dark/dash-btc/24h/svg" />
</head>
<body>
  <header>
    <h1>Embeddable Cryptocurrency Charts</h1>
  </header>
  <main>
    <section>
      <h2>Transparent SVG and PNG charts with your favorite cryptocurrencies</h2>
      <figure>
        <img src="/charts/candlestick/dash-usd/7d/svg" alt="Dash/USD price">
        <figcaption>7 day Dash price candlesticks in USD <code><a href="/charts/candlestick/dash-usdt/7d/svg">https://cryptohistory.org/charts/candlestick/dash-usd/7d/svg</a></code></figcaption>
      </figure>
      <p>Sparklines too! ETH/BTC 7 days: <img src="/charts/sparkline/eth-btc/7d/svg" alt="ETH 7d chart" style="vertical-align: bottom;">
        <code>&lt;img src=&quot;https://cryptohistory.org/charts/sparkline/eth-btc/7d/svg&quot; alt=&quot;ETH/BTC 7d chart&quot; style=&quot;vertical-align: bottom;&quot;&gt;</code>
      </p>
    </section>
    <section>
      <h2>Build your own chart:</h2>
      <p>The URL is flexible:<br>
        <code>https://cryptohistory.org/charts/{theme}/{currencyA}-{currencyB}/{timespan}/{format}</code></p>
      <p>Theme: <code>dark</code>, <code>light</code>, or <code>sparkline</code>.</p>
      <p>Currency: anything supported by CryptoCompare.com, including fiat.</p>
      <p>Timespan: <code>1y</code>, <code>30d</code>, <code>7d</code>, or <code>24h</code>.</p>
      <p>Format: <code>svg</code> (best) or <code>png</code>.</p>
      <p>If you use svg format, you can control some of the colors with GET parameters: <code>lineColor</code>, <code>markerColor</code>, <code>risingColor</code>, <code>fallingColor</code>. Some examples: <code><a href="/charts/dark/maid-btc/7d/svg?lineColor=5593D7&markerColor=29578A">https://cryptohistory.com/charts/dark/maid-btc/7d/svg?lineColor=5593D7&amp;markerColor=29578A</a></code> <code><a href="/charts/candlestick/fct-btc/7d/svg?risingColor=FE8534&fallingColor=00BAE9">https://cryptohistory.org/charts/candlestick/fct-btc/7d/svg?risingColor=FE8534&amp;fallingColor=00BAE9</a></code>
      </p>
    </section>
    <section>
      <h2>Examples:</h2>
      <p>Ethereum/Bitcoin 24h, dark SVG: <code><a href="/charts/dark/eth-btc/24h/svg" target="_blank">https://cryptohistory.org/charts/dark/eth-btc/24h/svg</a></code></p>
      <p>Litecoin/Bitcoin 30d, light PNG: <code><a href="/charts/light/ltc-btc/30d/png" target="_blank">https://cryptohistory.org/charts/light/ltc-btc/30d/png</a></code></p>
      <p>Doge/USD 1y, dark SVG, yellow line: <code><a href="/charts/dark/doge-usd/1y/svg?lineColor=BB9F32" target="_blank">https://cryptohistory.org/charts/dark/doge-usd/1y/svg?lineColor=BB9F32</a></code></p>
    </section>
  </main>
  <footer>
    Made by <a href="https://joshua.seigler.net/">Joshua Seigler</a> using <a href="https://github.com/seigler/neat-charts">seigler/neat-charts</a>, <a href="http://altorouter.com/">AltoRouter</a>, and <a href="http://www.phpfastcache.com/">phpfastcache</a> with data from the <a href="https://www.cryptocompare.com/api/">CryptoCompare public API</a>.
  </footer>
</body>
</html>
