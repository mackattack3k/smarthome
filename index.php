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
    <link rel="import" href="bower_components/paper-button/paper-button.html">
    <link rel="import" href="bower_components/paper-checkbox/paper-checkbox.html">
    <link rel="import" href="bower_components/paper-styles/paper-styles.html">
    <link rel="import" href="bower_components/paper-input/paper-input.html">
    <link rel="import" href="bower_components/paper-dropdown-menu/paper-dropdown-menu.html">
    <link rel="import" href="bower_components/paper-listbox/paper-listbox.html">
    <link rel="import" href="bower_components/paper-item/paper-item.html">
    <link rel="import" href="bower_components/paper-tooltip/paper-tooltip.html">
    <link rel="import" href="bower_components/iron-a11y-keys-behavior/iron-a11y-keys-behavior.html">
    <link rel="import" href="bower_components/iron-dropdown/iron-dropdown.html">
    <link rel="import" href="bower_components/iron-behaviors/iron-control-state.html">
    <link rel="import" href="bower_components/iron-behaviors/iron-button-state.html">
    <link rel="import" href="bower_components/iron-dropdown/iron-dropdown.html">



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
<nav id="slide-menu">
    <div id="settings">
        <div class="settings-header">Settings</div>
        <section class="settings-section">
            <ul class="settings-section-list">
                <style is="custom-style">
                    paper-input {
                        --paper-input-container-color: #C2185B;
                        --paper-input-container-focus-color: #C2185B;
                        --paper-input-container-invalid-color: rgba(194, 24, 91, 0.25);
                        --paper-input-container-input-color: black;
                    }
                    paper-toggle-button {
                        --paper-toggle-button-checked-bar-color: #C2185B;
                        --paper-toggle-button-checked-ink-color: white;
                        --paper-toggle-button-checked-button-color: #C2185B;
                        --paper-toggle-button-label-color: #C2185B;
                        margin-top: 20px;
                    }
                    paper-dropdown-menu {
                        --paper-input-container-color: #C2185B;
                        --paper-input-container-focus-color: #C2185B;

                    }
                </style>
                <li><paper-input
                        label="Stocks"
                        pattern="^([a-zA-Z]{2,6})+(,[a-zA-Z]{2,6})*$"
                        onfocusout="validate()">
                    </paper-input>
                    <paper-tooltip position="bottom" offset="10">eg AAPL,GOOG,MSFT</paper-tooltip>
                </li>
                <li><paper-input
                        label="Number of departures"
                        pattern="^\d*$"
                        onfocusout="validate()"></paper-input></li>
                <li><paper-dropdown-menu label="Station">
                        <paper-menu class="dropdown-content">
                            <paper-item>Gullmarsplan</paper-item>
                            <paper-item>Sulvägen</paper-item>
                            <paper-item>Årstaberg</paper-item>
                            <paper-item>Åmänningevägen</paper-item>
                        </paper-menu>
                    </paper-dropdown-menu></li>
                <li><paper-input
                        label="Latitude"
                        pattern="^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$"
                        onfocusout="validate()"></paper-input></li>
                <li><paper-input
                        label="Longitude"
                        pattern="^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$"
                        onfocusout="validate()"></paper-input></li>
                <li><paper-input
                        label="Timezone"
                        pattern="^\d*$"
                        onfocusout="validate()"></paper-input></li>
                <li><paper-toggle-button
                        class="hover no-select"
                        id="notifications-toggle">Notifications</paper-toggle-button></li>
                <li><paper-toggle-button
                        class="hover no-select"
                        id="debug-toggle">Debug</paper-toggle-button></li>
                <li><paper-input disabled label="Language"></paper-input></li>
                <!--TODO: Create checkboxes to enable or disable columns/info-->
            </ul>
        </section>
    </div>
</nav>

<div id="panel">
    <div class="slideout-button-container">
        <div class="slideout-toggle-button icon icon-bars"></div>
    </div>
    <div class="column-container">

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
            <div id="weather" class="column-content second-content">
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
                    <!--TODO: Change this to paper-spinner-->
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


            <div id="lights" class="column-content second-content">
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
</div>

<script src="js/global.js"></script>
<script src="js/slideout.min.js"></script>
<script src="js/js-cookie/src/js.cookie.js"></script>
<script>
    var slideout = new Slideout({
        'panel': document.getElementById('panel'),
        'menu': document.getElementById('slide-menu'),
        'padding': 260,
        'tolerance': 70
    });
    // Toggle button
    document.querySelector('.slideout-toggle-button').addEventListener('click', function() {
        slideout.toggle();
    });
    //slideout.open();
</script>
</body>
</html>