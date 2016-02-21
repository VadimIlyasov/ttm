<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

  <title>Twitter Geo Trends Analysis - Reports</title>
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

    body .ui.form .error.message {
      display: block !important;
    }
  </style>
</head>
<body class="reports">
  <div class="ui large menu fixed">
    <a class="item" href="/">
      <i class="world icon large blue"></i>
    </a>
    

    <a class="item" href="/">
        <i class="icon map"></i> Map
    </a>
    <a class="item active" href="/reports.php">
        <i class="icon pie chart"></i> Reports
    </a>

    <div class="right menu">
      <a class="item about-app-link" href="#">
        <i class="icon help"></i> About
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

          <p>Select trends to compare. Click on any to view details.</p>
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

          <div id="chart_div" style="width: 100%; height: 500px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>


  <div class="ui modal about-app">
    <i class="close icon"></i>
    <div class="header">
      Twitter GeoTrends Analysis tool
    </div>
    <div class="image content">
      <div class="ui medium image">
      </div>
      <div class="description">
        <div class="ui header">This is tool is aimed to allow analyst and just curious users to find out global trends on different topics.</div>
      </div>
    </div>
    <div class="actions">
      <div class="ui positive right labeled icon button">
        That's cool!
        <i class="checkmark icon"></i>
      </div>
    </div>
  </div>
</body>
</html>