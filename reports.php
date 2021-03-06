<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

  <title>Twitter GeoTrends Analytics - Reports</title>
  <link rel="stylesheet" type="text/css" href="/assets/css/semantic.min.css">

  <script src="/assets/jquery-2.2.0.min.js"></script>
  <script src="/assets/css/semantic.min.js"></script>
  <script src="/assets/tablesort.min.js"></script>
  <script src="/assets/main.js"></script>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
  </script>


  <style type="text/css">
    .ui.vertical.stripe {
      padding: 8em 0em;
    }
    .ui.vertical.stripe h3 {
      font-size: 2em;
    }
    .ui.vertical.stripe .button + h3,
    .ui.vertical.stripe p + h3 {
      margin-top: 3em;
    }
    .ui.vertical.stripe .floated.image {
      clear: both;
    }
    .ui.vertical.stripe p {
      font-size: 1.33em;
    }
    .ui.vertical.stripe .horizontal.divider {
      margin: 3em 0em;
    }

    #map {
      width: 100%;
      height: 100%;
      background: red;
    }

    .menu a.header {
      padding: 0 !important;
    }
    .header img.logo {
      width: 5.7em !important;
    }

    body .ui.form .error.message {
      display: block !important;
    }
  </style>
</head>
<body class="reports">
  <div class="ui large menu fixed">
    <a href="/" class="header item">
      <img class="logo" src="/assets/logo.png">
    </a>

    <a class="item" href="/">
        <i class="icon map"></i> Map
    </a>
    <a class="item active" href="/reports.php">
        <i class="icon pie chart"></i> Reports
    </a>

    <div class="right menu">
      <a class="item help-link" href="#">
        <i class="icon help"></i> Help
      </a>
      <a class="item about-app-link" href="#">
        <i class="icon idea"></i> About
      </a>
    </div>
  </div>


<!-- Page Contents -->
<div class="pusher">

  <div class="ui vertical stripe segment">
    <div class="ui stackable grid container">
      <div class="row">
        <div class="sixteen wide column">

          <h1 class="ui header">
            <div class="content"><i class="icon chart line"></i>Available Trends</div>
          </h1>

          <p>Select one or more trends to compare. This tool can be used to see where specific trends are more popular how do they compare to each other, etc. It is especially useful for competing brands like Audi and BMW or Apple and Android.</p>
          <form id="compare-trends-form">
            <div class="ui fluid action input">
              <select class="ui fluid search dropdown" multiple="" id="trends-list" name="trends[]"></select>
              <button class="ui teal button" id="compare-trends">Compare</button>
            </div>
          </form>

          <table class="ui sortable selectable celled table" id="comparision-table" style="display:none;">
            <thead>
              <tr>
                <th>Trend</th>
                <th>Mentions</th>
                <th>Positive</th>
                <th>Negative</th>
                <th>Top Countries</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>

          <div id="chart_div" style="width: 100%; height: 500px; display:none;"></div>
        </div>
      </div>
    </div>
  </div>
</div>


  <div class="ui modal about-app">
    <i class="close icon"></i>
    <div class="header">
      Twitter GeoTrends Analytics - About
    </div>
    <div class="image content">
      <div class="ui medium image">
        <img src="/assets/big-logo.png">
      </div>
      <div class="description">
        <div class="ui">
          <p>This tool is aimed to allow deeper analysis of global trends on different topics and just to allow curious users to discover something interesting.</p>
          <p>What is under the hood?</p>
          <ul>
            <li>Twitter Stream daemon</li>
            <li>Tweets parser on cron</li>
            <li>MySQL database collecting mentions, locations and attitudes data</li>
            <li><a href="http://thematicmapping.org/downloads/world_borders.php" target="_blank">World Boundaries database</a> for reverse geolocations</li>
          </ul>
          <p>Who are the developers?</p>
          <ul>
            <li><a href="https://www.linkedin.com/in/denisilyasov" target="_blank">Denis Ilyasov</a> - server configuration and testing</li>
            <li><a href="https://ua.linkedin.com/in/vadimilyasov" target="_blank">Vadim Ilyasov</a> - idea and development</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="actions">
      <div class="ui positive right labeled icon button">
        That's cool!
        <i class="checkmark icon"></i>
      </div>
    </div>
  </div>


  <div class="ui modal help">
    <i class="close icon"></i>
    <div class="header">
      Twitter GeoTrends Analytics - Help
    </div>
    <div class="image content">
      <div class="ui medium image">
        <img src="/assets/big-logo.png">
      </div>
      <div class="description">
        <div class="ui">
          <p>Application consists of two pages:</p>
          <ul>
            <li><a href="/">Map</a> page displays heatmap for select trend (keyword)</li>
            <li><a href="/reports.php">Reports</a> page allows users to see detailed report for selected trends, compare them, etc.</li>
          </ul>
          <p>On the map page, you can select Trend by clicking drop-down filter on the left side of the header. You can also add new trend by clicking "Add new" button. Please note, it takes a time to collect information for your trend, you should allow at least 30-60 minutes to see some results for most keywords.</p>
          <p>You can also see a trend attitude. We analyze tweets to see if they are positive or negative and collect that information also.</p>
          <p>On reports page, you can see a detailed table with the list of mentions for each trend, top 3 Countries, the number of positive and negative tweets accordingly.</p>
          <p>In addition, there is a nice trendline chart that displays popularity of each trend and allows you to compare them. With bigger timeline it could be useful to see the impact of advertisement campaigns for brands or new products.</p>
        </div>
      </div>
    </div>
    <div class="actions">
      <div class="ui positive right labeled icon button">
        Got it!
        <i class="checkmark icon"></i>
      </div>
    </div>
  </div>
</body>
</html>