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
       7 -> button 1
       6 -> button 4
       5 -> button 3
       3 -> button 2
     */
    public $debug;
    public $lightID;
    public $pihatPath;

    public function __construct()
    {

        $function = isset($_GET['function']) ? $_GET['function'] : false;
        $debug = isset($_GET['debug']) ? $_GET['debug'] : false;
        $inputLight = isset($_GET['id']) ? $_GET['id'] : false;

        $this->setDebug($debug);
        $this->setLightID($inputLight);
        $this->setPihatPath("/home/pi/scripts/pihat/pihat");


        if ($function == "off") {
            $output = $this->turnLightOff( $this->getLightID() );
            echo $output;
        }
        if ($function == "on") {
            $this->turnLightOn( $this->getLightID() );
        }

    }

    public function setLightID($lightID)
    {
        $this->lightID = $lightID;
    }

    public function getLightID()
    {
        return $this->lightID;
    }

    public function getPihatPath()
    {
        return $this->pihatPath;
    }

    public function setPihatPath($pihatPath)
    {
        $this->pihatPath = $pihatPath;
    }

    private function turnLightOff($inputLight)
    {
        if(!$inputLight){
            $pihatPath = $this->getPihatPath();
            $output = passthru('sudo '.$pihatPath.' --repeats=10 --id=0 --channel='.$inputLight.' --state=1');
            return $output;
        }
        return "Error: No light defined";
    }

    public function turnLightOn($inputLight)
    {
        if(!$inputLight){
            $pihatPath = $this->getPihatPath();
            $output = passthru('sudo '.$pihatPath.' --repeats=10 --id=0 --channel='.$inputLight.' --state=0');
            return $output;
        }
        return "Error: No light defined";
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