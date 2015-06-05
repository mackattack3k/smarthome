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
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="column column-1">
            <div id="time" class="content">
                <div class="header">
                    <div id="currenttime" class="super-duper-big-font">
                    </div>
                    <div id="currentday" class="big-font">
                    </div>
                    <div id="currentdate" class="big-font">
                    </div>                        
                </div>
            </div>
            <div id="weather" class="content">
                <div class="header">
                    35 grader<br/>
                    Sol hela dagen
                </div>
            </div>          
        </div>
        
        <div class="column column-2">
            <div id="news" class="content">
                <div class="header">
                    news
                </div>
                <div class="content-item">
                    Lycklig årstabo vann eurojackpott denna fredag.
                </div>
                <div class="content-item">
                    Lyckligaste människorna bor i Stockholm.
                </div>
                <div class="content-item">
                    IT-Killar är bäst på att koda.
                </div>
            </div>  
        </div>
        
        <div class="column column-3">
            <div id="lights" class="content">
                <div class="header">
                    lights
                </div>
                <div class="raised-item">
                    1
                </div>
                <div class="raised-item">
                    2
                </div>
                <div class="raised-item">
                    3
                </div>
                <div class="raised-item">
                    All on
                </div>
                <div class="raised-item">
                    All off
                </div>
            </div>  
        </div>

        
    </div>
    
    <script src="js/global.js"></script>
</body>
</html>