<?php

/**
 * Created by PhpStorm.
 * User: macattack
 * Date: 20/12/15
 * Time: 00:49
 */
class publicTransport
{

    /*
     * Units
     */

    public $debug;
    public $inputStationID;
    public $allUnits = array();
    public $API_KEYS = array();

    public function __construct()
    {
        //TODO: Make this a function instead
        if (file_exists(__DIR__ . '/../../api_keys.php')) {
            global $GlobalAPI_Keys;
            require_once(__DIR__ . '/../../api_keys.php');
            $this->setAPIKEYS($GlobalAPI_Keys);
        } else {
            echo "Error: No api keys. Exiting!";
            return;
        }

        $function = isset($_GET['function']) ? $_GET['function'] : false;
        $debug = isset($_GET['debug']) ? $_GET['debug'] : false;
        $inputStationID = isset($_GET['stationid']) ? $_GET['stationid'] : false;

        $this->setDebug($debug);
        $this->setInputStationID($inputStationID);

        if ($function == "fullHTML") {
            $this->startHTMLAutomaticVersion();
        }

    }

    public function getAPIKEYS()
    {
        return $this->API_KEYS;
    }

    public function setAPIKEYS($API_KEYS)
    {
        $this->API_KEYS = $API_KEYS;
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

    public function setInputStationID($inputStationID)
    {
        $this->inputStationID = $inputStationID;
    }

    public function getInputStationID()
    {
        return $this->inputStationID;
    }

    private function startHTMLAutomaticVersion()
    {
        $stationID = $this->getInputStationID();
        if ( $stationID ){

            $stationData = $this->getDepartures($stationID);
            if ( $stationData ){
                $stationHTMLData = $this->convertDeparturesToHtml($stationData);
                echo $stationHTMLData;
                return true;
            }
            echo "Error: No data returned from Resrobot";
            return false;
        }
        echo "Error: startHTMLAutomaticVersion:: stationid is not set";
    }

    //TODO: remove this and use rawurlencode in getstationsfromSL
    function findStation($inputStationName)
    {
        if ($inputStationName == '') {
            echo "No station name entered";
            return;
        }

        echo "Finding station that matches: " . $inputStationName . "<br/>";
        $urlStationName = rawurlencode($inputStationName);
        //Get json result from SL
        $stations = getStationsFromSL($urlStationName);
    }

    function getStationsFromSL($stationName)
    {
        global $platsuppslagKey;
        $resrobotSearchKey = $this->getAPIKEYS()['resrobotSearchKey'];
        $resrobot= "https://api.resrobot.se/location.name?key=$resrobotSearchKey&input=$stationName&format=json";
        $findByName = "https://api.sl.se/api2/typeahead.json?key=$platsuppslagKey&searchstring=$stationName&stationsonly=TRUE&maxresults=5";
        $findByNameResult = file_get_contents($findByName);
        $findByNameResultJson = json_decode($findByNameResult, true);
        print_r($findByNameResultJson);
        $stations = $findByNameResultJson->ResponseData;

        //Check if there isnt any results for the station
        if (count($stations) < 1) {
            echo "No results";
            return;
        }
        foreach ($stations as $key => $station) {
            echo $station['Name'] . " - " . $station['SiteId'] . "<br/>";
        }
        return;


    }

    function getDepartures($inputSiteID)
    {
        $resrobotKey = $this->getAPIKEYS()['resrobotKey'];
        $output = null;

        /*
         * ULT = Tunnelbana, BLT = Buss, SLT = Tvärbana
         * Åmänningevägen = 7453026, Årstaberg station = 7424920, Gullmarsplan = 7421705
         *
         */

        if (!$inputSiteID) {
            $output = "Error: No site ID in getDepartures()";
            return $output;
        }
        $findByStationID = "https://api.resrobot.se/departureBoard?key=$resrobotKey&id=$inputSiteID&maxJourneys=10&format=json";

        $departsResultJson = @file_get_contents($findByStationID);
        $departsResultJsonArray = json_decode($departsResultJson, true);
        $departsResultJsonObj = json_decode($departsResultJson);

        if( empty( $departsResultJsonArray ) )
        {
            return false;
        }
        return $departsResultJsonObj;
    }

    private function convertDeparturesToHtml($departsResultJson){
        $output = null;
        if ( !$departsResultJson ){
            $output .= "Error: No data in convertDeparturesToHtml";
        }
        if ( $this->getDebug() == "true" ){
            $output .= print_r($departsResultJson);
        }

        $departsResult = $departsResultJson->Departure;

        $transportTypeTranslationArray = new stdClass();
        $transportTypeTranslationArray->ULT = 'train'; //Tunnelbana
        $transportTypeTranslationArray->SLT = 'train'; //Tvärbana
        $transportTypeTranslationArray->JLT = 'train'; //SJ
        $transportTypeTranslationArray->BLT = 'bus'; //Buss

        foreach ($departsResult as $departureArrayKey => $departureInfo) {
            //exit this departure loop if the bus is going to the same station as we are going from (Weird bug from api call)
            if ($departureInfo->Product->num == '') {
                continue;
            }

            $stops = $departureInfo->Stops->Stop;
            end($stops);         // move the internal pointer to the end of the array
            $lastStopObject = current($stops);

            $transportationCategory = $departureInfo->transportCategory;

            /*
            * Final values
            */

            $arrivalStopName = preg_replace('/\s\(.*\)?/', '', $departureInfo->direction);
            $arrivalTime = substr($lastStopObject->arrTime, 0, 5); //Cutting string since I only need HH:MM
            $departTime = substr($departureInfo->time, 0, 5);
            $departDate = $departureInfo->date;
            $lineFullName = $departureInfo->Product->name;
            $line = substr($lineFullName, 4, strlen($arrivalStopName));

            //Check if the transportation type exists in our translation array and then translate it
            if (isset($transportTypeTranslationArray->$transportationCategory)) {
                $translatedTransportType = $transportTypeTranslationArray->$transportationCategory;
            } else {
                $translatedTransportType = 'rocket purple-icon';
            }

            /*
            * Output the HTML
            */
            $output .=  "<div class='traffic-result $transportationCategory'>";
            $output .=  "<div class='traffic-first'>";
            $output .=  "<div class='icon icon-".strtolower($translatedTransportType)." icon-2x'></div>";
            $output .=  "<div class='traffic-line'>$line</div>";
            $output .=  "</div>";
            $output .=  "<div class='traffic-second'>";
            $output .=  "<div class='traffic-destination'>" . $arrivalStopName . "</div>";
            $output .=  "</div>";
            $output .=  "<div class='traffic-third'>";
            $output .=  "<div class='traffic-time departure-time' value='$departDate $departTime'>avgår $departTime</div>";
            $output .=  "<div class='traffic-time arrival-time'>framme $arrivalTime</div>";
            $output .=  "</div>";
            $output .=  "</div>";
        }
        return $output;
    }

}

new publicTransport();