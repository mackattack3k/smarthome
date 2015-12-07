<?php
/* API Keys */
if (!fileExists('apikeys.php')){
  return "No api keys";
} else {
  require_once('apikeys.php');
}

/* Global vars */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
//echo exec('whoami');

/*GET variables */
$getFunction    = (isset($_GET["function"]))? ($_GET["function"]) : '';
$getVar1        = (isset($_GET["var1"]))? ($_GET["var1"]) : '';
$getVar2        = (isset($_GET["var2"]))? ($_GET["var2"]) : '';
if ( isset($getFunction) ){
    if ( $getFunction == ''){
        $getFunction = 'resetthingy';
    }
    return $getFunction($getVar1, $getVar2);
}

/* Functions */

function resetthingy() {
    //This function is only here so that the getFunction works even if there is no function called
}

/* Lamps start */

function getLamps() {
    //echo 'Called function getLamps'; //Used for the debugging -- check if the function was called :)
    $lampsFileName = 'public_files/lamps.json';

    //Check if the file exists and output its contents
    if (fileExists($lampsFileName) === true ){
        //echo 'The lamps file exists, yay!';
        $getLampsJson   =   file_get_contents($lampsFileName);
        $lampsJson      =   json_decode($getLampsJson, true);
        return $lampsJson;
    }else{
        //This shouldnt happen because the fileExists function checks if the file exists... but just in case
        echo "Get lamps failed: 24";
    }
}

function editLamps($lamp , $newState) {
    $currentLamps   =   getLamps();

    //Check to see if a lamp is inputted
    if ($lamp){

        //Check to see if the new state of the lamp is set or not
        if ($newState != ''){

            //Check to see if the new state is On or Off - otherwise error
            if (strtolower($newState) == 'on' or strtolower($newState) == 'off' ){

                //Check to see if all the lamps should be changed
                if (strtolower($lamp) == 'all'){


                    //Loop through each lamp and put it to the value
                    foreach ($currentLamps['lamps'] as $key => $val) {
                        $currentLamps['lamps'][$key] = strtolower($newState);
                    }
                    //Write the new lamps.json file with all the lamps
                    $fp = fopen('public_files/lamps.json', 'w');
                    fwrite($fp, json_encode($currentLamps));
                    fclose($fp);

                    //Send the 433 mhz signal to the Pi
                    echo "Changed all the lamps to: ".strtolower($newState);

                }else{

                    //Turn only one lamp off or on
                    //Check to see if the lamp exist in the json file
                    if (array_key_exists($lamp, $currentLamps['lamps'])) {
                        $currentLamps['lamps'][$lamp] = $newState;

                        //Write to lamps.json with the change
                        $fp = fopen('public_files/lamps.json', 'w');
                        fwrite($fp, json_encode($currentLamps));
                        fclose($fp);

                        echo $lamp." is now set to: ".$newState;
                    }else{
                        echo $lamp." could not be found in the json file";
                    }
                }
            }else{
                echo "The new state must be set to ON or OFF";
            }
        }else{
            echo "The new state must be set to a value (ON/OFF)";
        }
    }else{
        echo "No lamp was selected";
    }
}

function htmlLamps(){
    //output the current lamps to html for the frontpage
    $currentLamps   =   getLamps();

    //Check if there actually is lamps =)
    if ( $currentLamps ){

        //Print a button element for each lamp
        foreach ($currentLamps['lamps'] as $key => $val) {

            //Check the state of the lamp and append the class Active if its on
            if ( strtolower($val) == 'on'){
                echo "<paper-button raised class='lights-yellow active' id='".$key."'>".$key."</paper-button>";
            }else{
                echo "<paper-button raised class='lights-yellow' id='".$key."'>".$key."</paper-button>";
            }
        }
    }else{
        echo "No lamps file was found";
    }
}

function fileExists($filename){
    if (!$filename){
        return 'No filename input...';
    }else{
        if (file_exists($filename)) {
            return true;
        } else {
            return false;
        }
    }

}

function shellcommand(){
    shell_exec('sudo /tmp/maha/433Utils/RPi_utils/codesend 123');

}

/* Lamps end */

/* Busses start */

function getBusStop($station){
    //echo "Nu körs 'hitta stationsid från namn'";
    $station = isset($station) ? $station : 'Åmänningevägen';
    global $searchTripApiKey; //Get the api key from apikeys.php


    if($station){
        //Convert the searched site to URL so the API can handle the name
        $stationNameURL           =   rawurlencode($station);
        //echo "<h1>Du sökte på: ".$stationNameURL."</h1>";
        /*
        /   This will get the names that match the entered site and the siteID
        */
        $findByName             =   "https://api.trafiklab.se/samtrafiken/resrobot/FindLocation.json?apiVersion=2.1&from=$stationNameURL&coordSys=RT90&key=$searchTripApiKey";
        $findByNameResult       =   file_get_contents($findByName);
        $findByNameResultJson   =   (json_decode($findByNameResult, true));
        $findByNameResultStops  =   $findByNameResultJson['findlocationresult']['from']['location'];

        if (array_key_exists('bestmatch', $findByNameResultStops) && $findByNameResultStops['bestmatch'] == 'true') {
            /*echo "Station: ".$findByNameResultStops['displayname'];
            echo " med locationid: ".$findByNameResultStops['locationid']."<br><br>";
            */
            getBusTime($findByNameResultStops['locationid']);

        }else{
            //Check if it exists one layer deeper in the array
            foreach($findByNameResultStops as $stationIdKey => $stationIdVal){
                /*
                echo "<br>Station: ".$stationIdVal['displayname'];
                echo " med locationid: ".$stationIdVal['locationid'];
                */
                //Output station name to the stolpTidtabeller
                getBusTime($stationIdVal['locationid']);
            }
        }
        //echo "<pre>";
        //print_r($findByNameResultStops);
        //echo "</pre>";
    }
}

function getBusTime($busStop){
    //echo "<br>Nu körs travelplanner<br/>";
    //echo $busStop;
    $amanningevagen       =     '7453026'; //Åmänningevägen
    $arstaberg            =     '7424920'; //Årstaberg station
    global $stolpTidtabeller; //Get the api key from apikeys.php



    if($busStop){
        $findByStationID        = "https://api.trafiklab.se/samtrafiken/resrobotstops/GetDepartures.json?apiVersion=2.1&coordSys=RT90&locationId=$busStop&key=$stolpTidtabeller";
        $findByStationResult       =   @file_get_contents($findByStationID);
        $findByStationResultJson   =   (json_decode($findByStationResult, true));
        $busArr                    =    $findByStationResultJson['getdeparturesresult']['departuresegment'];
        $trafficTypesArr           =    array('buss' => 'bus', 'tåg' => 'train');

        $debug = 0;

        if($debug == 1){
            echo "<div class='debug' style='display:block;'><pre>";
            print_r($findByStationResultJson);
            echo "</div></pre>";
        }else{
            //echo "<div class='debug'><pre>";
            //print_r($findByStationResultJson);
            //echo "</div></pre>";
        }


        foreach($busArr as $busKey => $busVal){

            $getTrafficType            =    strtolower($busVal['segmentid']['mot']['#text']);
            //var_dump($busVal);

            //Check if the traffic type exists in the swedish translation array - trafficTypesArr
            if(isset($trafficTypesArr[$getTrafficType])){
                $trafficType    =    $trafficTypesArr[$getTrafficType];
            }else{
                $trafficType    =   'unkown';
            }

            //print_r($busVal);
            getTravelPlanner($busVal['departure']['location']['name'], $busVal['direction'], substr($busVal['departure']['datetime'], 0, 10), substr($busVal['departure']['datetime'], 11, 5));
            /*
            echo "
            <div class='traffic-result'><div class='traffic-time'>"
                .substr($busVal['departure']['datetime'],11,5).
                "</div><div class='traffic-destinations'><div class='traffic-main traffic-from'>"
                .$busVal['departure']['location']['name'].
                "</div><div class='traffic-main traffic-to'>"
                .$busVal['direction'].
                "</div></div><div class='traffic-type icon icon-".$trafficType." icon-2x'></div></div>";
            */

        }
    }


}

function getTravelPlanner($getOrigin, $getDestination, $date, $time){
    //echo "<br>Nu körs realtidsinfo 3<br>";
    //echo $getOrigin."<br>".$getDestination."<br>".$date."<br>".$time."<br>";
    global $realtidsinformation; //Get the api key from apikeys.php
    $origin                 =   rawurlencode($getOrigin);
    $destination            =   rawurlencode($getDestination);

    if($getDestination == 'Årstaberg station'){
      $destination = rawurlencode('Årstaberg');
    }

    if($origin && $destination){
        $findByStationID        = "http://api.sl.se/api2/TravelplannerV2/trip.json?key=".$realtidsinformation."&originId=".$origin."&destId=".$destination."&date=".$date."&time=".$time."&numTrips=1";
        $findByStationResult       =   @file_get_contents($findByStationID);
        $findByStationResultJson   =   (json_decode($findByStationResult, true));
        $trips                     =    $findByStationResultJson;

        $debug = 0;

        if($debug == 1){
            echo "<div class='debug' style='display:block;'><pre>";
            print_r($findByStationResultJson['TripList']['Trip'][0]);
            echo "</div></pre>";
        }else{
            //echo "<div class='debug'><pre>";
            //print_r($findByStationResultJson);
            //echo "</div></pre>";
        }
        //var_dump($trips);

        foreach($trips as $tripKey => $tripValue){

            $finalTrips     =   $tripValue['Trip']['LegList']['Leg'];
            $duration       =   $tripValue['Trip']['dur'];
            $trip             =   $tripValue['Trip'];
            $finalDestination = preg_replace('/( [(]terminalen)[)]/', '', $finalTrips['Destination']['name']);
            /*
            echo "<pre>";
            var_dump($trip);
            echo "</pre>";
            */
            //echo "<i class='icon icon-bus'></i>";


            echo "
                <div class='traffic-result'>
                  <div class='traffic-first'>
                    <div class='icon icon-".strtolower($finalTrips['type'])." icon-2x'></div>
                    <div class='traffic-line'>".$finalTrips['line']."</div>
                  </div>
                  <div class='traffic-second'>
                    <div class='traffic-destination'>".$finalDestination."</div>
                    <!--<div class='traffic-duration'>".$duration." minuter</div>-->
                  </div>
                  <div class='traffic-third'>
                    <div class='traffic-time departure-time' value='".$finalTrips['Origin']['date'].' '.$finalTrips['Origin']['time']."'>avgår ".$finalTrips['Origin']['time']."</div>
                    <div class='traffic-time arrival-time'>framme ".$finalTrips['Destination']['time']."</div>
                  </div>
                </div>
            ";

            /* -- Old way to get out the values from the travel planner... not working
            foreach($finalTrips as $finalTripsKey => $finalTripsValue){

                var_dump($finalTripsValue);
                echo "
                    <div class='traffic-result'><div class='traffic-time'>"
                    .$finalTripsValue['Origin']['time'].
                    " "
                    .$finalTripsValue['Destination']['time'].
                    "</div><div class='traffic-destinations'><div class='traffic-main traffic-from'>"
                    .$finalTripsValue['Origin']['name'].
                    "</div><div class='traffic-main traffic-to'>"
                    .$finalTripsValue['Destination']['name'].
                    "</div></div><div class='traffic-type icon icon-"
                    .$finalTripsValue['Origin']['type'].
                    " icon-2x'></div></div>";

            }
            */


            /*
            echo "
            <div class='traffic-result'><div class='traffic-time'>"
                .$tripValue['LegList']['Leg']['Origin']['time']
                .$tripValue['LegList']['Leg']['Destination']['time'].
                "</div><div class='traffic-destinations'><div class='traffic-main traffic-from'>"
                .$tripValue['LegList']['Leg']['Origin']['name'].
                "</div><div class='traffic-main traffic-to'>"
                .$tripValue['LegList']['Leg']['Destination']['name'].
                "</div></div><div class='traffic-type icon icon-"
                .$tripValue['LegList']['Leg']['Origin']['type'].
                " icon-2x'></div></div>";
            */
        }


    }


}

/* Busses end */

/* Busses 1.1 start */

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
  $findByName             =   "https://api.trafiklab.se/samtrafiken/resrobot/FindLocation.json?apiVersion=2.1&from=$stationName&coordSys=RT90&key=$searchTripApiKey";
  $findByNameResult       =   file_get_contents($findByName);
  $findByNameResultJson   =   json_decode($findByNameResult, true);
  $SLresult               =   $findByNameResultJson['findlocationresult']['from'];

  //Check if there isnt any results for the station
  if (count($SLresult) < 1) {
    echo "No results";
    return;
  }

  $station = $SLresult['location'];
  /*echo "<pre>";
  print_r(($station));
  echo "</pre>";
  */

  //Return the results
  if ( !array_key_exists(0, $station) ) {
    echo $station['displayname']." - ".$station['locationid'];
    return;
  }
  foreach ($station as $key => $stationInfo) {
    echo $stationInfo['displayname']." - ".$stationInfo['locationid']."<br/>";
  }
  return;


}

/* Busses 1.1 end */

/* Weather start */

function getWeather($type){
  /* Establish some global variables for the weather functions */
  global $weatherApiKey; //Get the api key from apikeys.php
  date_default_timezone_set('Europe/Stockholm');
  $lat            =   "59.298604";
  $lon            =   "18.047111";
  $units          =   "metric";
  $lang           =   "se";
  $days           =   "3";

  $debug          = 'false';

  $weatherstring  =   "http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=$units&lang=$lang&APPID=$weatherApiKey";
  $weatherstringResult       =   @file_get_contents($weatherstring);
  $weatherstringResultJson   =   (json_decode($weatherstringResult, true));
  //file_put_contents('weather.json', json_encode($weatherstringResult));

  //Get forecast - not used
  $forecaststring    =   "http://api.openweathermap.org/data/2.5/find?lat=$lat&lon=$lon&cnt=$days&units=$units&lang=$lang&APPID=$weatherApiKey";
  //$forecaststringResult       =   file_get_contents($forecaststring);
  //$forecaststringJson   =   (json_decode($forecaststringResult, true));

  if($debug == 'true'){
      echo "<pre>";
      print_r($weatherstringResultJson);
      //print_r($forecaststringJson);
      echo "</pre>";
  }

  //Check if its day or night
  $now = time();
  $gmt = new DateTimeZone('Europe/Stockholm');
  $timeInStockholm = new DateTime('now', $gmt);
  $gmtOffset = $gmt->getOffset( $timeInStockholm )/3600;

  $sunDown = date_sunset($now, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90+(50/60), $gmtOffset);
  $sunRise = date_sunrise($now, SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90+(50/60), $gmtOffset);

  if ($now > $sunRise && $now < $sunDown) {
    $dayOrNight = 'day';
  } else {
    $dayOrNight = 'night';
  }


  $icon   = $weatherstringResultJson['weather']['0']['id'];
  $temp   = round($weatherstringResultJson['main']['temp'], 1);
  $desc   = $weatherstringResultJson['weather']['0']['description'];

  if($type){
    if ($type == 'icon') {
      echo "<div class='weather-icon wi wi-owm-".$dayOrNight."-".$icon."'></div>";
    }elseif ($type == 'temp') {
      echo "<div class='weather-temp'>".$temp."°</div>";
    }elseif ($type == 'desc') {
      echo "<div class='weather-desc'>".$desc."</div>";
    }else {
      echo "Didnt find type: ".$type;
    }
  }else {
    echo "No weather type defined";
  }

}


/* Weather end */

?>
