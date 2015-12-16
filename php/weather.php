<?php

/**
 * Class Weather
 */
class Weather
{

    /*
     * Units
     */
    public $lat;
    public $lon;
    public $measurements;
    public $lang;
    public $debug;
    public $allUnits = array();

    public function getAllUnits()
    {
        return $this->allUnits;
    }

    /**
     * @param $newLat
     * @param $newLon
     * @param $newMeasurements
     * @param $newLang
     * @param string $newDebug
     */
    public function setAllUnits($newLat, $newLon, $newMeasurements, $newLang, $newDebug = "off")
    {
        $this->setLat($newLat);
        $this->setLon($newLon);
        $this->setMeasurements($newMeasurements);
        $this->setLang($newLang);
        $this->setDebug($newDebug);

        $this->allUnits = [
            "lat" => $this->getLat(),
            "lon" => $this->getLon(),
            "measurements" => $this->getMeasurements(),
            "lang" => $this->getLang(),
            "debug" => $this->getDebug(),
        ];
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    public function getLon()
    {
        return $this->lon;
    }

    public function setLon($lon)
    {
        $this->lon = $lon;
    }

    public function getMeasurements()
    {
        return $this->measurements;
    }

    public function setMeasurements($measurements)
    {
        $this->measurements = $measurements;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function setDebug($debug)
    {
        switch ($debug) {
            case "true":
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                $this->debug = "true";
                break;
            case "on":
                $this->setDebug("true");
                break;
            default:
                $this->debug = "false";
        }

    }

    public function getWeatherToday()
    {
        /* Establish some global variables for the weather functions */
        global $weatherApiKey; //Get the api key from apikeys.php
        $weatherApiKey = "d99fe917166e9602ed87f6ca3d629469";
        date_default_timezone_set('Europe/Stockholm');
        $lat = $this->getLat();
        $lon = $this->getLon();
        $units = $this->getMeasurements();
        $lang = $this->getLang();

        $debug = $this->getDebug();

        $weatherstring = "http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=$units&lang=$lang&APPID=$weatherApiKey";
        $weatherstringResult = @file_get_contents($weatherstring);
        $weatherstringResultJson = (json_decode($weatherstringResult, true));
        //file_put_contents('weather.json', json_encode($weatherstringResult));

        if ($debug == "true") {
            echo "<pre>";
            print_r($weatherstringResultJson);
            //print_r($forecaststringJson);
            echo "</pre>";
        }
        //echo $this->getDebug();

        //Check if its day or night
        $dayOrNight = $this->getDayOrNight($lat, $lon);


        $icon = $weatherstringResultJson['weather']['0']['id'];
        $temp = round($weatherstringResultJson['main']['temp'], 1);
        $desc = strtolower($weatherstringResultJson['weather']['0']['description']);


        $output = "<div id=\"weather-current-icon\"><div class='weather-icon wi wi-owm-" . $dayOrNight . "-" . $icon . "'></div></div>";
        $output .= "<div class=\"weather-current-details\"><div class='weather-temp'>" . $temp . "°</div>";
        $output .= "<div class='weather-desc'>" . $desc . "</div></div>";

        return $output;

    }

    private function getDayOrNight($lat, $lon)
    {
        $now = time();
        $gmt = new DateTimeZone('Europe/Stockholm');
        $timeInStockholm = new DateTime('now', $gmt);
        $gmtOffset = $gmt->getOffset($timeInStockholm) / 3600;

        $sunDown = date_sunset($now, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90 + (50 / 60), $gmtOffset);
        $sunRise = date_sunrise($now, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90 + (50 / 60), $gmtOffset);

        if ($now > $sunRise && $now < $sunDown) {
            return 'day';
        } else {
            return 'night';
        }
    }

    function getWeatherComingDays($days = '3')
    {
        if (!fileExists('apikeys.php')) {
            echo "Error (functions.php->getWeatherforecast): apikeys.php doesnt exist";
            return;
        }
        global $weatherApiKey; //Get the api key from apikeys.php
        date_default_timezone_set('Europe/Stockholm');
        $lat = "59.298604";
        $lon = "18.047111";
        $units = "metric";
        $lang = "se";
        $debug = "false";

        //Get forecast from openweathermap
        $forecaststring = "http://api.openweathermap.org/data/2.5/forecast/daily?lat=$lat&lon=$lon&cnt=$days&units=$units&lang=$lang&APPID=$weatherApiKey";
        //echo $forecaststring;
        $forecaststringResult = @file_get_contents($forecaststring);
        $forecaststringJson = (json_decode($forecaststringResult));
        $forecastDay = $forecaststringJson->list;

        //header('Content-Type: application/json');
        //print_r($forecaststringJson->list);
        //print_r($forecaststringJson);
        foreach ($forecastDay as $dayType => $dayInfo) {

            $dayTemp = round($dayInfo->temp->day, 1);
            $description = strtolower($dayInfo->weather{0}->description);
            $icon = $dayInfo->weather{0}->id;

            //var_dump($description);

            
            echo "<div id='weather-'>";
            echo "<div class='weather-coming-icon'>";
            echo "<div class='weather-icon wi wi-owm-day-" . $icon . "'></div></div>";
            echo "<div class='weather-coming-details'>";
            echo "<div class='weather-temp'>" . $dayTemp . "°</div>";
            echo "<div class='weather-desc'>" . $description . "</div></div></div>";


        }


    }


}
