<?php

/**
 * Class Weather
 */
class Weather
{

    public $lat;

    /*
     * Units
     */
    public $lon;
    public $measurements;
    public $lang;
    public $debug;
    public $allUnits = array();
    public $API_KEYS = array();

    public function __construct()
    {
        $htmlCall = isset($_GET['htmlCall']) ? $_GET['htmlCall'] : false;
        $debug = isset($_GET['debug']) ? $_GET['debug'] : false;

        $this->setDebug($debug);

        if (file_exists(__DIR__ . '/../../api_keys.php')) {
            global $GlobalAPI_Keys;
            require_once(__DIR__ . '/../../api_keys.php');
            $this->setAPIKEYS($GlobalAPI_Keys);
        } else {
            return "Error: No api keys. Exiting!";
        }

        if ($htmlCall == "true") {
            //echo "starting html version";
            $htmlOutput = $this->startHTMLAutomaticVersion();
            //echo $htmlOutput; //TODO: remove this from prod
            return $htmlOutput;
        }

    }

    private function startHTMLAutomaticVersion()
    {
        /*
         * Get the variables
         */

        $lat = isset($_GET['lat']) ? $_GET['lat'] : "59.298604";
        $lon = isset($_GET['lon']) ? $_GET['lon'] : "18.047111";
        $measurements = isset($_GET['measurements']) ? $_GET['measurements'] : 'metric';
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'se';


        /*
         * Set the variables
         */

        $this->setAllUnits($lat, $lon, $measurements, $lang);

        /*
         * Get current weather
         */

        $currentWeatherData = $this->getCurrentWeather();
        $currentWeatherHTML = $this->getCurrentWeatherHtml($currentWeatherData);

        $htmlOutput = $currentWeatherHTML;

        /*
         * Get coming weather
         */

        $comingWeatherData = $this->getWeatherComingDays();
        $htmlOutput .= $comingWeatherData;

        return $htmlOutput;

    }

    public function getCurrentWeather()
    {
        /* Establish some global variables for the weather functions */
        $weatherApiKey = $this->getAPIKEYS()['weatherApiKey'];
        date_default_timezone_set('Europe/Stockholm');
        $lat = $this->getLat();
        $lon = $this->getLon();
        $units = $this->getMeasurements();
        $lang = $this->getLang();

        $debug = $this->getDebug();

        $weatherString = "http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=$units&lang=$lang&APPID=$weatherApiKey";
        $weatherstringResult = file_get_contents($weatherString);
        $weatherstringResultJson = (json_decode($weatherstringResult, true));
        //file_put_contents('weather.json', json_encode($weatherstringResult));

        if ($debug == "true") {
            echo "<pre>";
            print_r($weatherstringResultJson);
            //print_r($forecaststringJson);
            echo "</pre>";
        }

        return $weatherstringResultJson;


    }

    public function getAPIKEYS()
    {
        return $this->API_KEYS;
    }

    public function setAPIKEYS($API_KEYS)
    {
        $this->API_KEYS = $API_KEYS;
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
                echo "<br/>Setting debug to True<br/>";
                break;
            case "on":
                $this->setDebug("true");
                break;
            default:
                $this->debug = "false";
        }

    }

    public function getCurrentWeatherHtml($weather)
    {
        $lat = $this->getLat();
        $lon = $this->getLon();

        $dayOrNight = $this->getDayOrNight($lat, $lon);


        $icon = $weather['weather']['0']['id'];
        $temp = round($weather['main']['temp'], 1);
        $desc = strtolower($weather['weather']['0']['description']);


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
        $weatherApiKey = $this->getAPIKEYS()['weatherApiKey'];
        date_default_timezone_set('Europe/Stockholm');
        $lat = $this->getLat();
        $lon = $this->getLon();
        $units = $this->getMeasurements();
        $lang = $this->getLang();
        $debug = $this->getDebug();
        $output = null;

        //Get forecast from openweathermap
        $forecaststring = "http://api.openweathermap.org/data/2.5/forecast/daily?lat=$lat&lon=$lon&cnt=$days&units=$units&lang=$lang&APPID=$weatherApiKey";
        //echo $forecaststring;
        $forecaststringResult = file_get_contents($forecaststring);
        $forecaststringJson = (json_decode($forecaststringResult));
        $forecastDay = $forecaststringJson->list;

        //header('Content-Type: application/json');
        //print_r($forecaststringJson->list);
        //print_r($forecaststringJson);


        foreach ($forecastDay as $dayType => $dayInfo) {

            $dayTemp = round($dayInfo->temp->day, 1);
            $description = strtolower($dayInfo->weather{0}->description);
            $icon = $dayInfo->weather{0}->id;

            $output .= "<div id='weather-'>";
            $output .= "<div class='weather-coming-icon'>";
            $output .= "<div class='weather-icon wi wi-owm-day-" . $icon . "'></div></div>";
            $output .= "<div class='weather-coming-details'>";
            $output .= "<div class='weather-temp'>" . $dayTemp . "°</div>";
            $output .= "<div class='weather-desc'>" . $description . "</div></div></div>";

        }
        return $output;


    }

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
    public function setAllUnits($newLat, $newLon, $newMeasurements, $newLang, $newDebug = "false")
    {
        $this->setLat($newLat);
        $this->setLon($newLon);
        $this->setMeasurements($newMeasurements);
        $this->setLang($newLang);

        if ($newDebug != $this->getDebug()) {
            $this->setDebug($newDebug);
        }

        $this->allUnits = [
            "lat" => $this->getLat(),
            "lon" => $this->getLon(),
            "measurements" => $this->getMeasurements(),
            "lang" => $this->getLang(),
            "debug" => $this->getDebug(),
        ];
    }

}

new Weather();