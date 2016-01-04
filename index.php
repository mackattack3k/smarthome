<?php
//require_once 'php/weather.php';
require_once ('php/lights.php');

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
    <link rel="import" href="bower_components/paper-toggle-button/paper-toggle-button.css">
    <link rel="import" href="bower_components/paper-button/paper-button.html">
    <link rel="import" href="bower_components/paper-checkbox/paper-checkbox.html">
    <link rel="import" href="bower_components/paper-styles/paper-styles.html">
    <link rel="import" href="bower_components/paper-input/paper-input.html">
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
<nav id="settings">
    <h2>Settings</h2>
    <section class="settings-section">
        <!--<h3 class="settings-section-title">Docs</h3>-->
        <ul class="settings-section-list">
            <style is="custom-style">
                paper-input {
                    --paper-input-container-color: #C2185B;
                    --paper-input-container-focus-color: #C2185B;
                    --paper-input-container-invalid-color: rgba(194, 24, 91, 0.25);
                    --paper-input-container-input-color: black;
                }
                paper-toggle-button {
                    --paper-checkbox-checked-color: #C2185B;
                    --paper-checkbox-unchecked-background-color: white;
                    --paper-checkbox-checked-ink-color: #C2185B;
                    --paper-checkbox-unchecked-color: white;
                    --paper-checkbox-unchecked-ink-color: #C2185B;
                }
            </style>
            <li><paper-input
                    pattern="^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$"
                    label="Latitude"
                    error="Not a correct coordinate"
                    onfocusout="validate()"></paper-input></li>
            <li><paper-input label="Longitude"></paper-input></li>
            <li><paper-input label="Stocks"></paper-input></li>
            <li><label for="notifications-toggle">Notifications</label></li>
            <li><paper-toggle-button id="notifications-toggle">Nooooooots</paper-toggle-button></li>
            <li><paper-input disabled label="Language"></paper-input></li>
        </ul>
    </section>
</nav>

<div id="panel" class="column-container">
    <div class="slideout-button-container">
        <div class="slideout-toggle-button icon icon-bars"></div>
    </div>
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
                    <input type="text" id="traffic-search-input" class="traffic-input" placeholder="Sök på station" value="Årstaberg"></input>
                </div>
                <div id="traffic-loading" class="icon icon-refresh icon-4x icon-spin"></div>
                <div id="traffic-results">
                </div>
            </div>
            <div class="timestamp" id="traffic-last-updated"></div>
        </div>
    </div>

    <div class="column column-3">
            <div id="stocks" class="column-content">
                <div class="header">
                    Aktier
                </div>
                <div class="stock-items">
                </div>
                <div class="timestamp" id="stocks-last-updated">
                </div>
            </div>


            <div id="lights" class="column-content">
                <div class="header">
                    Belysning
                </div>
                <div class="items">
                    <?php
                    $lights = new lights();
                    $lights->setDebug(false);
                    $lights->setJsonLampsFilePath("public_files/lamps.json");
                    echo $lights->htmlLamps();

                    ?>
                    <paper-button raised class="lights-all" id="all-on">På</paper-button>
                    <paper-button raised class="lights-all" id="all-off">Av</paper-button>

                </div>

            </div>
            <!---
            <div id="speakers" class="column-content">
                <div class="header">
                    Speakers
                </div>
                <div class="items">
                    <paper-toggle-button disabled class="red" label="mute">Mute</paper-toggle-button>
                    <paper-checkbox disabled class="white" checked>Checkbox</paper-checkbox>

                </div>
            </div>
            --->
        </div>
    <div class="notifications-container"></div>
</div>

<script src="js/global.js"></script>
<script src="js/slideout.min.js"></script>
<script src="js/js-cookie/src/js.cookie.js"></script>
<script>
    var slideout = new Slideout({
        'panel': document.getElementById('panel'),
        'menu': document.getElementById('settings'),
        'padding': 260,
        'tolerance': 70
    });
    // Toggle button
    document.querySelector('.slideout-toggle-button').addEventListener('click', function() {
        slideout.toggle();
    });
    slideout.open();
</script>
</body>
</html>