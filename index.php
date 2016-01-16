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
    <link rel="import" href="bower_components/paper-input/paper-input.html">
    <link rel="import" href="bower_components/paper-dropdown-menu/paper-dropdown-menu.html">
    <link rel="import" href="bower_components/paper-item/paper-item.html">
    <link rel="import" href="bower_components/paper-tooltip/paper-tooltip.html">
    <link rel="import" href="bower_components/paper-input/paper-textarea.html">
    <link rel="import" href="bower_components/paper-ripple/paper-ripple.html">
    <link rel="import" href="bower_components/paper-menu/paper-menu.html">
    <link rel="import" href="bower_components/paper-material/paper-material.html">
    <link rel="import" href="bower_components/paper-spinner/paper-spinner.html">
    <link rel="import" href="bower_components/iron-dropdown/iron-dropdown.html">
    <link rel="import" href="bower_components/iron-behaviors/iron-button-state.html">
    <link rel="import" href="bower_components/iron-dropdown/iron-dropdown.html">
    <link rel="import" href="bower_components/iron-icons/iron-icons.html">
    <link rel="import" href="bower_components/iron-icons/device-icons.html">
    <link rel="import" href="bower_components/iron-selector/iron-selector.html">
    <link rel="import" href="bower_components/iron-selector/iron-selection.html">

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
                    paper-textarea {
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
                    paper-spinner {
                        --paper-spinner-layer-1-color: #C2185B;
                        --paper-spinner-layer-2-color: #C2185B;
                        --paper-spinner-layer-3-color: #C2185B;
                        --paper-spinner-layer-4-color: #C2185B;
                    }
                    paper-spinner.loading-icon{
                        width: 100px;
                        height: 100px;
                    }
                    paper-ripple {
                        color: #C2185B;
                    }
                </style>
                <li><paper-input
                        id="stocks-input"
                        class="settings-input"
                        label="Aktier"
                        pattern="^([a-zA-Z]{2,8}){1}(,[a-zA-Z]{2,8})*$"
                        error-message="Invalid format (STOCK1,STOCK2,...)"
                        oninput="validate()">
                    </paper-input>
                    <paper-tooltip position="bottom" offset="10">tex AAPL,GOOG,MSFT</paper-tooltip>
                </li>
                <li><paper-input
                        disabled
                        id="numberofdepartures-input"
                        class="settings-input"
                        label="Antal avgångar"
                        pattern="^([4-9]|1([0-2]))$"
                        error-message="Enter a number between 4 and 12"
                        oninput="validate()">
                    </paper-input>

                </li>
                <li><paper-dropdown-menu
                        id="station-input"
                        class="settings-input"
                        label="Station">
                        <paper-menu
                            id="station-menu"
                            class="dropdown-content"
                            selected="Åmänningevägen,7453026"
                            attr-for-selected="data-value">
                            <paper-item data-value="Gullmarsplan,7421705">Gullmarsplan</paper-item>
                            <paper-item data-value="Sulvägen,7465488">Sulvägen</paper-item>
                            <paper-item data-value="Åmänningevägen,7453026">Åmänningevägen</paper-item>
                            <paper-item data-value="Årstaberg,7424920">Årstaberg</paper-item>
                        </paper-menu>
                    </paper-dropdown-menu>
                </li>
                <li><paper-input
                        id="latitude-input"
                        class="settings-input"
                        label="Latitud"
                        pattern="^(\-?\d+(\.\d+)?).\s*(\-?\d+(\.\d+)?)$"
                        oninput="validate()"></paper-input>
                </li>
                <li><paper-input
                        id="longitude-input"
                        class="settings-input"
                        label="Longitud"
                        pattern="^(\-?\d+(\.\d+)?).\s*(\-?\d+(\.\d+)?)$"
                        oninput="validate()"></paper-input></li>
                <paper-button raised id="auto-gps-button" class="lillypurple">
                    <iron-icon icon="device:gps-fixed"></iron-icon> Hämta koordinater
                </paper-button>
                <li><paper-input
                        id="timezone-input"
                        class="settings-input"
                        label="Tidzon"
                        pattern=".*"
                        oninput="validate()"></paper-input></li>
                <li><paper-toggle-button
                        class="hover no-select"
                        id="notifications-toggle">Notifieringar</paper-toggle-button></li>
                <li><paper-toggle-button
                        class="hover no-select"
                        id="debug-toggle">Debug</paper-toggle-button></li>
                <li><paper-input disabled label="Språk"></paper-input></li>
                <!--TODO: Create checkboxes to enable or disable columns/info-->
            </ul>
            <paper-material elevation="1" id="saving-settings-card" class="hidden">
                <div><paper-spinner active id="saving-settings-spinner"></paper-spinner></div>
                <iron-icon id="saving-settings-icon-success" icon="icons:check"></iron-icon>
                <iron-icon id="saving-settings-icon-error" icon="icons:error-outline"></iron-icon>
                <div id="saving-settings-text">Sparar inställningar</div>
            </paper-material>
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
                    <paper-spinner active id="weather-loading" class="loading-icon"></paper-spinner>
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
                        <div id="traffic-search-input" class="traffic-input">Åmänningevägen</div>
                    </div>
                    <paper-spinner active id="traffic-loading" class="loading-icon"></paper-spinner>
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
                <paper-spinner active id="stocks-loading" class="loading-icon" ></paper-spinner>
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

<script src="js/slideout.min.js"></script>
<script src="js/js-cookie/src/js.cookie.js"></script>
<script src="js/global.js"></script>
</body>
</html>