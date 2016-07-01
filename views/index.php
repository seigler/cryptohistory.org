<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
  <title>Embeddable Cryptocurrency Charts : Cryptohistory</title>
  <style>
    main {
      margin: 0 auto;
    }
    section {
      display: inline-block;
      width: auto;
      max-width: 800px;
      padding: 10px;
      vertical-align: top;
    }
  </style>
</head>

<body>
  <header>
    <h1>Embeddable Cryptocurrency Charts</h1>
  </header>
  <main>
    <section>
      <h2>Poloniex Dash/BTC Price</h2>
      <figure>
        <img src="/charts/dark/dash-btc/7d/svg" alt="Poloniex Dash/BTC price">
        <figcaption>7 Day Dash price in BTC <code>http://cryptohistory.org/charts/dark/dash-btc/7d/svg</code></figcaption>
      </figure>
    </section>
    <section>
      <h2>Build your own chart:</h2>
      <p>The URL is flexible: <code>http://cryptohistory.org/charts/{theme}/{main-currency}-{chart-currency}/{timespan}/{format}</code>.</p>
      <p>Theme: <code>dark</code> or <code>light</code>.</p>
      <p>Main currency: anything on Poloniex.</p>
      <p>Chart currency: any of the chart currencies on Poloniex, like BTC or USDT.</p>
      <p>Timespan: <code>7d</code> or <code>24h</code>. More options coming soon.</p>
      <p>Format: <code>svg</code> (best) or <code>png</code>.</p>
    </section>
  </main>

</body>
</html>
