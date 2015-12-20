<?php
//require_once 'php/weather.php';

?>

<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta name="msapplication-TileColor" content="#ffc40d">
    <meta name="theme-color" content="#ffffff">

    <title>Smart home - Kebab edition</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/weather-icons.css" charset="utf-8">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

    <!-- Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500|Open+Sans:300,400,600' rel='stylesheet'
          type='text/css'>

    <!-- Polymer -->
    <link rel="import" href="bower_components/paper-toggle-button/paper-toggle-button.html">
    <link rel="import" href="bower_components/paper-button/paper-button.html">
    <link rel="import" href="bower_components/paper-checkbox/paper-checkbox.html">
    <link rel="import" href="bower_components/paper-styles/paper-styles.html">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-touch-icon-120x120.png">
    <link rel="icon" type="image/png" href="favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="favicons/favicon-16x16.png" sizes="16x16">

    <link rel="manifest" href="/manifest.json">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="bower_components/webcomponentsjs/webcomponents-lite.js"></script>
    <script src="https://use.fonticons.com/03ff41d1.js"></script>
    <script src="js/xdomainajax.js" charset="utf-8"></script>

</head>

<body>
<div class="container">
    <div class="column column-1">
        <div id="time" class="column-content">
            <div class="header">
                <div id="currenttime" class="super-duper-big-font">
                </div>
                <div id="currentday" class="big-font">
                </div>
                <div id="currentdate" class="big-font">
                </div>
            </div>
        </div>
        <div id="weather" class="column-content">
            <div class="header">
                Väder
            </div>
            <div class="weather-items">
                <div id="weather-loading" class="icon icon-refresh icon-4x icon-spin"></div>
                <div id="weather-data">

                </div>
            </div>
            <div class="timestamp" id="weather-last-updated"></div>

        </div>
    </div>

    <div class="column column-2">
        <div id="traffic" class="column-content">
            <div class="traffic-refresh-manually icon icon-refresh icon-2x" id="refresh-traffic">
            </div>
            <div class="header">
                Trafik
            </div>
            <div class="traffic-items">
                <div id="traffic-search">
                    <input type="text" id="traffic-search-input" class="traffic-input" placeholder="Sök på station" value="Åmänningevägen,"></input>
                </div>
                <div id="traffic-loading" class="icon icon-refresh icon-4x icon-spin"></div>
                <div id="traffic-results">
                </div>

            </div>
            <div class="timestamp" id="traffic-last-updated"></div>
        </div>
    </div>
    <!---
        <div class="column column-3">
            <div id="lights" class="column-content">
                <div class="header">
                    Lights
                </div>
                <div class="items">
                    <?php //echo htmlLamps(); ?>
                    <paper-button disabled class="lights-all" id="all-on">All on</paper-button>
                    <paper-button raised class="lights-all" id="all-off">All off</paper-button>

                </div>

            </div>
            <div id="speakers" class="column-content">
                <div class="header">
                    Speakers
                </div>
                <div class="items">
                    <paper-toggle-button disabled class="red" label="mute">Mute</paper-toggle-button>
                    <paper-checkbox disabled class="white" checked>Checkbox</paper-checkbox>

                </div>
            </div>
        </div>
      --->

</div>
<div class="notifications-container"></div>
<script src="js/global.js"></script>
</body>
</html>