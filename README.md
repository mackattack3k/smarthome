# Smarthome
This project started as a simple weather website. It is now more of a dashboard for with information regarding my home and also controlling lights.

# Configs
Most of the settings is done in the slide out menu to the left.

All the modules on the page are controlled via the checkboxes.

Some of the settings are in the code such as:

1. Weather fetch interval. ``` setInterval('getWeather()', timeInSecondsGoesHere);  Global.js:473```
2. Stocks fetch interval. ``` setInterval('getStocks()', timeInSecondsGoesHere);  Global.js:476```
3. Available stations. ``` "..data-value=Name,ID**"  Index.php:144-147```

**ID, 
The ID for the station is available from resrobot api. I will probably create som sort of search and save functionallity for this in the future.

## Requirements
You need som sort of PHP server. I would suggest Nginx or lighttpd.

###### Plugins:
1. [Pihat](https://github.com/txt3rob/RPI-Control). Install at /home/pi/pihat. This is for controlling the lights via 433MHz.

###### API keys:
1. [Openweathermap](http://openweathermap.org/api) only needed for more accuarcy.
2. [Resrobot](http://Openweathermap.org) for public transport (Sweden only)

## API keys
You need to place a file with api keys in your root directory.

The file name must be named api_keys.php and have the following format:
```
<?php
$resrobotKey    =   'keyHere';
$weatherApiKey  =   'keyHere';
?>
```
