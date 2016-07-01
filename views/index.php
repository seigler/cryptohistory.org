<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
  <title>Embeddable Cryptocurrency Charts : Cryptohistory</title>
  <style>
    *, *:after, *:before { box-sizing: inherit; }
    html {
      background-image: linear-gradient(105deg, #403, #107);
      background-attachment: fixed;
    }
    body {
      font-family: sans-serif;
      line-height: 1.25;
      margin: 0 auto;
      box-sizing: border-box;
      color: white;
      max-width: 60em;
      min-height: 100vh;
      background-color: rgba(0,0,0,0.25);
      position: relative;
    }
    body:before {
      content: '';
      display: table;
    }
    a {
      color: white;
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
      border: 1px dashed black;
      font-size: 1.2em;
      background-color: rgba(0,0,0,0.75);
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
  <meta name="twitter:image" content="http://cryptohistory.org/charts/dark/dash-btc/24h/svg" />
</head>
<body>
  <header>
    <h1>Embeddable Cryptocurrency Charts</h1>
  </header>
  <main>
    <section>
      <h2>SVG and PNG charts with your favorite cryptocurrencies</h2>
      <figure>
        <img src="/charts/light/dash-btc/7d/svg" alt="Poloniex Dash/BTC price">
        <figcaption>7 Day Dash price in BTC <code>http://cryptohistory.org/charts/light/dash-btc/7d/svg</code></figcaption>
      </figure>
    </section>
    <section>
      <h2>Build your own chart:</h2>
      <p>The URL is flexible:<br>
        <code>http://cryptohistory.org/charts/{theme}/{currency}-btc/{timespan}/{format}</code></p>
      <p>Theme: <code>dark</code> or <code>light</code>. (More planned)</p>
      <p>Currency: anything active on Poloniex. Prices are all in bitcoin.</p>
      <p>Timespan: <code>7d</code> or <code>24h</code>. (More planned)</p>
      <p>Format: <code>svg</code> (best) or <code>png</code>.</p>
    </section>
    <section>
      <h2>Examples:</h2>
      <p>Ethereum 24h, dark SVG: <code><a href="/charts/dark/eth-btc/24h/svg" target="_blank">http://cryptohistory.org/charts/dark/eth-btc/24h/svg</a></code></p>
      <p>Litecoin 7d, light colored PNG: <code><a href="/charts/dark/ltc-btc/7d/png" target="_blank">http://cryptohistory.org/charts/dark/ltc-btc/7d/png</a></code></p>
      <p>Factom 7d, dark SVG: <code><a href="/charts/dark/fct-btc/24h/svg" target="_blank">http://cryptohistory.org/charts/dark/fct-btc/24h/svg</a></code></p>
    </section>
  </main>
  <footer>
    Made by <a href="https://joshua.seigler.net/">Joshua Seigler</a> using <a href="https://github.com/seigler/neat-charts">seigler/neat-charts</a>, <a href="http://altorouter.com/">AltoRouter</a>, and <a href="http://www.phpfastcache.com/">phpfastcache</a> with data from the <a href="https://poloniex.com/support/api/">Poloniex public API</a>.
  </footer>
</body>
</html>
