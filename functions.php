<?php
/* Global vars */

error_reporting(E_ALL);
ini_set('display_errors', 1);
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

?>