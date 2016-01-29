<?php

/**
 * Created by PhpStorm.
 * User: macattack
 * Date: 30/12/15
 * Time: 23:52
 */
class lights
{
    /*
       0 -> ALL
       7 -> Row 1 /Vardagsrum
       6 ->
       5 -> Row 2 /Lilly
       3 ->
       2 -> Row 3 /Marcus
     */
    public $debug;
    public $lightChannel;
    public $state;
    public $pihatPath;
    public $stateText;
    public $jsonLampsFile;

    public function __construct()
    {
        date_default_timezone_set('Europe/Stockholm');
        $state = isset($_GET['state']) ? $_GET['state'] : false;
        $function = isset($_GET['function']) ? $_GET['function'] : false;
        $debug = isset($_GET['debug']) ? $_GET['debug'] : false;
        $inputLightChannel = isset($_GET['channel']) ? $_GET['channel'] : false;
        $jsonLampsFile = isset($_GET['lampspath']) ? $_GET['lampspath'] : "../public_files/lamps.json";

        $this->setDebug($debug);
        $this->setLightChannel($inputLightChannel);
        $this->setState($state);
        $this->setPihatPath("/home/pi/scripts/pihatgit/pihat");
        $this->setJsonLampsFilePath($jsonLampsFile);

        if ($function == "toggleLight") {
            $output = $this->toggleLight( $this->getLightChannel(), $this->getState() );
            echo $output;
            return;
        }
        if ( strtolower($function) == strtolower("toggleAllLights") ){
            $myLamps = array(7,5,2);
            $output = null;

            foreach ($myLamps as $lightChannel){
                $this->setLightChannel($lightChannel);
                $output .= $this->toggleLight($lightChannel, $this->getState());
            }
            echo $output;
            return;
        }
    }

    public function setState($state)
    {
        $this->state = $state;
        switch($state){
            case 0:
                $this->setStateText("off");
                break;
            case 1:
                $this->setStateText("on");
                break;
            default:
                $this->setStateText("off");
        }
    }

    public function setStateText($stateText)
    {
        $this->stateText = $stateText;
    }

    public function getStateText()
    {
        return $this->stateText;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setLightChannel($lightChannel)
    {
        $this->lightChannel = $lightChannel;
    }

    public function getLightChannel()
    {
        return $this->lightChannel;
    }

    public function getPihatPath()
    {
        return $this->pihatPath;
    }

    public function setPihatPath($pihatPath)
    {
        $this->pihatPath = $pihatPath;
    }

    public function setJsonLampsFilePath($jsonLampsFile)
    {
        $this->jsonLampsFile = $jsonLampsFile;
    }

    public function getJsonLampsFilePath()
    {
        return $this->jsonLampsFile;
    }

    private function toggleLight($inputLightChannel, $state)
    {
        if ($state === false){
            return "Error: no new state defined";
        }
        if($inputLightChannel){
            $pihatPath = $this->getPihatPath();
            $pihatCommand = "sudo $pihatPath --repeats=3 --id=0 --channel='$inputLightChannel' --state=$state";

            exec("$pihatCommand 2>&1", $output, $return_var);

            if (is_array($output)){
                echo $output[0]; //We got an output... yay!
            }
            $changeLampStateResponse = $this->setLampState();

            return $changeLampStateResponse;
        }
        return "Error: No channel defined in toggleLight()";
    }

    public function setLampState()
    {
        $errorOutput = null;
        $idOfLampsEdited = [];
        $channelOfLampsEdited = [];
        $currentLamps   =   $this->getLamps();
        $inputLightChannel = $this->getLightChannel();
        $newState = $this->getState();
        $jsonLampsFile = $this->getJsonLampsFilePath();

        if (!$currentLamps){
            $errorOutput .= "Error: Couldn't get current lamps. Is the path to lamps.json correct?";
            $errorOutput .= "<br/>Path: ".$jsonLampsFile;
        }
        if (!$inputLightChannel){
            $errorOutput .= "<br/>Error: No channel defined in setLampState()";
        }
        if ($newState === false || !$this->isLampStateValid($newState) ){
            $errorOutput .= "<br/>Error: Invalid state in setLampState()";
        }
        if ($errorOutput != null){
            return $errorOutput;
        }

        //var_dump($currentLamps);

        if (strtolower($inputLightChannel) == 'all'){
            //Loop through each lamp and put it to the value
            foreach ($currentLamps['lamps'] as $key => $val) {
                $currentLamps['lamps'][$key] = strtolower($newState);
            }
            //Write the new lamps.json file with all the lamps
            $fp = fopen($jsonLampsFile, 'w');
            fwrite($fp, json_encode($currentLamps));
            fclose($fp);

            //Send the 433 mhz signal to the Pi
            return "Changed all the lamps to: ".strtolower($newState);

        }else{
            //Turn only one lamp off or on
            foreach ($currentLamps['lamps'] as $currentKey => $lamp){
                foreach ($lamp as $currentLampKey => $lampValue){
                    if ($lampValue['channel'] == $inputLightChannel){
                        $channel = $lampValue['channel'];
                        $id = $lampValue['id'];

                        $currentLamps['lamps'][$currentKey][$currentLampKey]['state'] = $newState;
                        $currentLamps['lamps'][$currentKey][$currentLampKey]['edited'] = new DateTime();

                        $fp = fopen($jsonLampsFile, 'w');
                        fwrite($fp, json_encode($currentLamps));
                        fclose($fp);

                        $isLampsEdited = true;
                        array_push($idOfLampsEdited, $id);
                        array_push($channelOfLampsEdited, $channel);
                    }
                }
            }
            if (!$isLampsEdited){
                return "Error: No lamp was changed, did you enter correct channel?";
            }
            $stringOfLampIDs = implode($idOfLampsEdited, ",");
            $stringOfLampChannels = implode($channelOfLampsEdited, ",");
            return "Lamp(s) with id($stringOfLampIDs) and channel($stringOfLampChannels) edited";
        }
    }

    private function isLampStateValid($state)
    {
        switch($state){
            case "0":
            case "1":
                return true;
                break;
            default:
                return false;
        }
    }

    public function getLamps() {
        //echo 'Called function getLamps'; //Used for the debugging -- check if the function was called :)
        $lampsFileName = $this->getJsonLampsFilePath();

        //Check if the file exists and output its contents
        if (file_exists($lampsFileName) === true ){
            //echo 'The lamps file exists, yay!';
            $getLampsJson   =   file_get_contents($lampsFileName);
            $lampsJson      =   json_decode($getLampsJson, true);
            return $lampsJson;
        }else{
            return false;
        }
    }

    public function htmlLamps(){
        //output the current lamps to html for the frontpage
        $currentLamps   =   $this->getLamps();
        $output = null;
        //Check if there actually is lamps =)
        if ( $currentLamps ){
            foreach ($currentLamps['lamps'] as $currentKey => $lamp){
                foreach ($lamp as $currentLampKey => $lampValue){
                    $channel = $lampValue['channel'];
                    $id = $lampValue['id'];
                    $state = $lampValue['state'];
                    $name = $lampValue['name'];

                    if ($state == "1"){
                        $output .= $this->newLampButton("active", $channel, $name);
                    }else{
                        $output .= $this->newLampButton("", $channel, $name);
                    }
                }
            }
        }else{
            $output .= "Error: No lamp was set in htmlLamps()";
        }
        return $output;
    }

    private function newLampButton($properties = "", $channel, $name)
    {
        $button = "<paper-button raised class='lights-purple $properties' data-channel='$channel'>".$name."</paper-button>";
        return $button;
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

    public function getDebug()
    {
        return $this->debug;
    }

}
new lights();