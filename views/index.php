<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
  <title>Embeddable Cryptocurrency Charts : Cryptohistory</title>
  <style>
    *, *:after, *:before { box-sizing: inherit; }
    body {
      font-family: sans-serif;
      background-color: #ffe;
      line-height: 1.25rem;
      margin: 0;
      box-sizing: border-box;
    }
    header {
      padding: 0 2rem;
    }
    main {
      margin: 0 auto 0 0;
      padding: 0 2rem 4rem;
      max-width: 70em;
    }
    section {
      display: block;
      margin: 1rem 0;
    }
    figure {
      margin-left: 0;
      margin-right: 0;
    }
    code {
      display: inline-block;
      padding: 0.1em;
      margin: -0.1em;
      border: 1px dashed rgba(0,0,0,0.2);
      font-size: 1.2em;
      line-height: 1.25rem;
      background-color: white;
    }
    footer {
      position: absolute;
      width: 100%;
      bottom: 0;
      padding: 0 2rem 2rem;
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
      <p>The URL is flexible:<br>
        <code>http://cryptohistory.org/charts/{theme}/{currency}-btc/{timespan}/{format}</code>.</p>
      <p>Theme: <code>dark</code> or <code>light</code>. (More planned)</p>
      <p>Currency: anything active on Poloniex. Prices are all in bitcoin.</p>
      <p>Timespan: <code>30d</code>, <code>7d</code>, or <code>24h</code>. (More planned)</p>
      <p>Format: <code>svg</code> (best) or <code>png</code>.</p>
    </section>
  </main>
  <footer>
    Made by <a href="https://joshua.seigler.net/">Joshua Seigler</a> using <a href="https://github.com/seigler/neat-charts">seigler/neat-charts</a>, <a href="http://altorouter.com/">AltoRouter</a>, and <a href="http://www.phpfastcache.com/">phpfastcache</a> with data from the <a href="https://poloniex.com/support/api/">Poloniex public API</a>.
  </footer>
</body>
</html>
