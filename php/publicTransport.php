<?php

/**
 * Created by PhpStorm.
 * User: macattack
 * Date: 20/12/15
 * Time: 00:49
 */
class publicTransport
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
        $function = isset($_GET['function']) ? $_GET['function'] : false;
        $debug = isset($_GET['debug']) ? $_GET['debug'] : false;

        $this->setDebug($debug);

        if (file_exists(__DIR__ . '/../../api_keys.php')) {
            global $GlobalAPI_Keys;
            require_once(__DIR__ . '/../../api_keys.php');
            $this->setAPIKEYS($GlobalAPI_Keys);
        } else {
            echo "Error: No api keys. Exiting!";
            return;
        }

        if ($function == "fullHTML") {
            //echo "starting html version";
            $htmlOutput = $this->startHTMLAutomaticVersion();
            echo $htmlOutput;
            return;
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

    function findStation($inputStationName){
        if ($inputStationName == '') {
            echo "No station name entered";
            return;
        }

        echo "Finding station that matches: ".$inputStationName."<br/>";
        $urlStationName = rawurlencode($inputStationName);
        //Get json result from SL
        $stations = getStationsFromSL($urlStationName);
    }

    function getStationsFromSL($stationName){
        global $searchTripApiKey;
        global $platsuppslagKey;
        //$findByName             =   "https://api.trafiklab.se/samtrafiken/resrobot/FindLocation.json?apiVersion=2.1&from=$stationName&coordSys=RT90&key=$searchTripApiKey";
        $findByName             =   "https://api.sl.se/api2/typeahead.json?key=$platsuppslagKey&searchstring=$stationName&stationsonly=TRUE&maxresults=5";
        $findByNameResult       =   file_get_contents($findByName);
        $findByNameResultJson   =   json_decode($findByNameResult, true);
        print_r($findByNameResultJson);
        $stations               =   $findByNameResultJson->ResponseData;

        //Check if there isnt any results for the station
        if (count($stations) < 1) {
            echo "No results";
            return;
        }
        /*echo "<pre>";
        print_r(($station));
        echo "</pre>";
        */

        //Return the results
        /*if ( !array_key_exists(0, $stations) ) {
          echo $stations['Name']." - ".$stations['SiteId'];
          return;
        }
        */
        foreach ($stations as $key => $station) {
            echo $station['Name']." - ".$station['SiteId']."<br/>";
        }
        return;


    }

    function getDepartures($inputSiteID){
        //echo $getOrigin."<br>".$getDestination."<br>".$date."<br>".$time."<br>";
        global $realtidsInformation3; //Get the api key from apikeys.php
        global $resrobotKey;
        $amanningevagen       =     '7453026'; //Åmänningevägen
        $arstaberg            =     '7424920'; //Årstaberg station
        $gullmarsplan         =     '7421705'; //Gullmarsplan
        /*
        * ULT = Tunnelbana, BLT = Buss, SLT = Tvärbana
        */

        if($inputSiteID){
            $findByStationIDOLD        = "https://api.sl.se/api2/realtimedepartures.json?key=".$realtidsInformation3."&siteid=".$inputSiteID."&timewindow=30";
            $findByStationID       = "https://api.resrobot.se/departureBoard?key=".$resrobotKey."&id=7453026&maxJourneys=10&format=json";
            $debug = 0;
            $transportTypeTranslationArray = new stdClass();
            $transportTypeTranslationArray->ULT = 'train'; //Tunnelbana
            $transportTypeTranslationArray->SLT = 'train'; //Tvärbana
            $transportTypeTranslationArray->BLT = 'bus'; //Buss

            $departsResultJson = json_decode(@file_get_contents($findByStationID));
            $departsResult = $departsResultJson->Departure;

            foreach ($departsResult as $departureArrayKey => $departureInfo) {
                //var_dump($departureInfo);
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
                $arrivalTime = substr($lastStopObject->arrTime, 0,5); //Cutting string since I only need HH:MM
                $departTime = substr($departureInfo->time, 0,5);
                $departDate = $departureInfo->date;
                $lineFullName = $departureInfo->Product->name;
                $line = substr($lineFullName, 4, strlen($arrivalStopName));

                //Check if the transportation type exists in our translation array and then translate it
                if(isset($transportTypeTranslationArray->$transportationCategory)){
                    $translatedTransportType = $transportTypeTranslationArray->$transportationCategory;
                } else {
                    $translatedTransportType = 'rocket';
                }

                /*
                * Output the HTML
                */
                echo "
              <div class='traffic-result'>
                <div class='traffic-first'>
                  <div class='icon icon-".strtolower($translatedTransportType)." icon-2x'></div>
                  <div class='traffic-line'>".$line."</div>
                </div>
                <div class='traffic-second'>
                  <div class='traffic-destination'>".$arrivalStopName."</div>
                </div>
                <div class='traffic-third'>
                  <div class='traffic-time departure-time' value='".$departDate." ".$departTime."'>avgår ".$departTime."</div>
                  <div class='traffic-time arrival-time'>framme ".$arrivalTime."</div>
                </div>
              </div>
          ";


            }




        }

    }

}

new publicTransport();