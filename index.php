<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

  <title>Twitter Geo Trends Analysis</title>
  <link rel="stylesheet" type="text/css" href="/assets/css/semantic.min.css">

  <script src="/assets/jquery-2.2.0.min.js"></script>
  <script src="/assets/css/semantic.min.js"></script>
  <script src="/assets/main.js"></script>

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

    .header img.logo {
      width: 4em !important;
    }

    body .ui.form .error.message {
      display: block !important;
    }
  </style>
</head>
<body>

  <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAquu9CH7H7PLjIF3lcjFCU2VwmyvmgNfk&signed_in=true&libraries=visualization&callback=initMap">
  </script>

  <div class="ui large menu fixed">
    <a href="/" class="header item">
      <img class="logo" src="/assets/logo.png">
    </a>

    <div class="item">
        <div class="ui labeled icon top right pointing dropdown button" id="filter-trends-menu">
            <i class="map icon"></i>
            <span class="text">Select Trend</span>
            <div class="menu">
                <div class="ui search icon input">
                  <i class="search icon"></i>
                  <input type="text" name="search" placeholder="Search trends...">
                </div>
                <div class="divider"></div>
                <div class="header">
                  <i class="tags icon"></i>
                  Filter by trend
                </div>
            </div>
        </div>
    </div>

    <div class="item">
        <div class="ui primary button" id="add-keyword">Add Trend</div>
    </div>

    <div class="item">
        Attitude: &nbsp; &nbsp; <span id="stats-likes"></span><i class="thumbs outline up icon"></i>
        &nbsp; &nbsp;
        <span id="stats-dislikes"></span><i class="thumbs outline down icon"></i>
    </div>

    <a class="item" href="/reports.php">
        <i class="icon pie chart"></i> Reports
    </a>

    <div class="right menu">
      <a class="item about-app-link" href="#">
        <i class="icon help"></i> About
      </a>
    </div>
  </div>


  <div id="map"></div>

  <div class="ui active dimmer" id="loading-screen">
    <div class="ui large text loader">Loading</div>
  </div>

  <div class="ui modal about-app">
    <i class="close icon"></i>
    <div class="header">
      Twitter GeoTrends Analysis tool
    </div>
    <div class="image content">
      <div class="ui medium image">
        <img src="/assets/big-logo.png">
      </div>
      <div class="description">
        <div class="ui">
          <p>This is tool is aimed to allow deeper analysis of global trends on different topics and just to allow curious users to discover something interesting.</p>
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


  <div class="ui modal add-trend">
  <i class="close icon"></i>
  <div class="header">
    Add Trend
  </div>
  <div class="content">
    <form class="ui form">
      <div class="field">
        <label>Keyword</label>
        <input type="text" id="form-keyword" placeholder="Keyword">
      </div>
    </form>
  </div>
  <div class="actions">
    <div class="ui black deny button">
      Cancel
    </div>
    <div class="ui positive right labeled icon button">
      Add
      <i class="checkmark icon"></i>
    </div>
  </div>
</div>
</body>
</html>