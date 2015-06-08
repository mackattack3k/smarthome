<?php


?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    
    <title>Smart home - Kebab edition</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:500,300,400|Open+Sans:300,400' rel='stylesheet' type='text/css'>
    <link rel="import" href="bower_components/paper-toggle-button/paper-toggle-button.html">
    <link rel="import" href="bower_components/paper-button/paper-button.html">
    <link rel="import" href="bower_components/paper-checkbox/paper-checkbox.html">
    <link rel="import" href="bower_components/paper-styles/paper-styles.html">
    <link rel="import" href="bower_components/paper-icon-button/paper-icon-button.html">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="bower_components/webcomponentsjs/webcomponents-lite.js"></script>
    
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
                    35 grader<br/>
                    Sol hela dagen
                </div>
            </div>          
        </div>
        
        <div class="column column-2">
            <div id="traffic" class="column-content">
                <div class="header">
                    traffic
                </div>
                <div class="items">
                
                </div>
            </div>  
        </div>
        
        <div class="column column-3">
            <div id="lights" class="column-content">
                <div class="header">
                    lights
                </div>
                <div class="items">
                    <paper-button tabindex="0" raised class="yellow active blue ripple">1</paper-button>
                    <paper-button raised class="yellow">2</paper-button>
                    <paper-button raised class="yellow">3</paper-button>
                    <paper-button disabled class="yellow">All on</paper-button>
                    <paper-button raised class="yellow">All off</paper-button>
 
                </div>

            </div>
            <div id="speakers" class="column-content">
                <div class="header">
                    speakers
                </div>
                <div class="items">
                    <paper-icon-button icon="menu"></paper-icon-button>
                    <paper-icon-button icon="menu"></paper-icon-button>
                    <paper-toggle-button class="red" label="mute">Mute</paper-toggle-button>
                    <paper-checkbox class="white" checked>Calcium</paper-checkbox>
                    
                </div>
            </div>
        </div>

        
    </div>
    
    <script src="js/global.js"></script>
</body>
</html>