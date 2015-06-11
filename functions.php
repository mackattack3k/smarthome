<?php
/* Global vars */

error_reporting(E_ALL);
ini_set('display_errors', 1);
//echo exec('whoami'); 

/*GET variables */
$getFunction    = (isset($_GET["function"]))? ($_GET["function"]) : '';
$getVar1        = (isset($_GET["var1"]))? ($_GET["var1"]) : '';
$getVar2        = (isset($_GET["var2"]))? ($_GET["var2"]) : '';
return $getFunction($getVar1, $getVar2);

/* */

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
    $currentLamps  =   getLamps();
    print_r($currentLamps);
    
    //Check to see if a lamp is inputted
    if ($lamp){
        //print_r(getLamps()); --debugging
        
        //Check to see if all the lamps should be changed
        if (strtolower($lamp) == 'all'){
            
            //Loop through each lamp and put it to the value
            foreach ($currentLamps['lamps'] as $key => $val) {
                $currentLamps['lamps'][$key] = $newState;
            }
            echo "<br>New array:<br>";
            print_r($currentLamps);
            file_put_contents('lamps.jsons', print_r($currentLamps, true));
            //Send the 433 mhz signal to the Pi
            
            
        }else{
            //Turn only one lamp off or on
        }
        
    }else{
        echo "No lamp was selected";
    }
    
}

function htmlLamps(){
    //output the current lamps to html for the frontpage
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



?>