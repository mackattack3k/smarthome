<?php
echo "<html><head <meta charset='UTF-8'></head>";

function getDeparts($station){
    $station = isset($station) ? $station : 'Åmänningevägen';
    
    $stolpTidtabeller     =     "1z2g6LAZ4diBFyrPj86k0LaNFsHP0QEy";
    $searchTrip           =     "DubDBSMx349tzJyhg1MvwomgFu8fTtQP";

    if($station){
        //Convert the searched site to URL so the API can handle the name
        $stationNameURL           =   rawurlencode($station);
        echo "<h1>Du sökte på: ".$stationNameURL."</h1>";
        /*
        /   This wil get the names that match the entered site and the siteID
        */
        $findByName             =   "https://api.trafiklab.se/samtrafiken/resrobot/FindLocation.json?apiVersion=2.1&from=$stationNameURL&coordSys=RT90&key=$searchTrip";            
        $findByNameResult       =   file_get_contents($findByName);
        $findByNameResultJson   =   (json_decode($findByNameResult, true));
        $findByNameResultStops  =   $findByNameResultJson['findlocationresult']['from']['location'];

        //echo "<pre>";
       // print_r($findByNameResultStops);
       // echo "</pre>";
    }
    $stationID = '7421705';
    if($stationID){
    
        $findByStationID        = "https://api.trafiklab.se/samtrafiken/resrobotstops/GetDepartures.json?apiVersion=2.1&coordSys=RT90&locationId=$stationID&key=$stolpTidtabeller";
        $findByStationResult       =   file_get_contents($findByStationID);
        $findByStationResultJson   =   (json_decode($findByStationResult, true));
        
        echo "<pre>";
        print_r($findByStationResultJson);
        echo "</pre>";
    }
    
    
}

getDeparts('Gullmarsplan');

echo "</html>";

?>