<?php

/**
 * Created by PhpStorm.
 * User: macattack
 * Date: 21/12/15
 * Time: 19:04
 */
class stocks
{
    /*
         * Units
         */

    public $debug;
    public $allUnits = array();
    public $API_KEYS = array();
    public $stocksYaoo;

    public function __construct()
    {
        date_default_timezone_set('Europe/Stockholm');
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
        $inputStocksCSV = isset($_GET['stocks']) ? $_GET['stocks'] : false;

        $this->setDebug($debug);


        if ($function == "html") {
            $this->setStocksYahooFormat($inputStocksCSV);
            $stocksData = $this->getStocksFromYahoo();
            $htmlStocks = $this->convertStocksToHtml($stocksData);

            echo $htmlStocks;
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

    public function getStocksFromYahoo()
    {
        $output = null;

        $stocksURL = "http://query.yahooapis.com/v1/public/yql?";
        $query = "q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in";
        $stocks = $this->getStocksYahooFormat();
        $params = "&format=json&env=http://datatables.org/alltables.env";

        if ($this->getDebug() == "true"){
            //var_dump("Time now and url for resrobot==".$todaysDate." ".$time."<br/>".$findByStationID);
        }

        $stocksResponse = @file_get_contents($stocksURL.$query.$stocks.$params);
        $stocksJsonArray = json_decode($stocksResponse, true);
        $stocksJsonObject = json_decode($stocksResponse);

        if( empty( $stocksJsonArray ) )
        {
            return false;
        }
        return $stocksJsonObject;
    }

    public function setStocksYahooFormat($inputStocksCSV)
    {
        if ( !$inputStocksCSV ){
            echo "Error: No stocks defined (Ex: AAPL,TSLA)";
            return false;
        }
        $stocksArray = explode(',', $inputStocksCSV);
        $output = "(";
        foreach ($stocksArray as $key => $stock ){
            if ($key == count($stocksArray)-1){
                $output .= "\"$stock\"";
                continue;
            }
            $output .= "\"$stock\",";
        }
        $output .= ")";

        $this->stocksYaoo = $output;
    }

    public function getStocksYahooFormat()
    {
        return $this->stocksYaoo;
    }

    public function convertStocksToHtml($stocksData)
    {
        if (!$stocksData){
            return "Error: no stocks data";
        }
        $output = null;
        $stocks = $stocksData->query->results->quote;

        if (is_object($stocks)){
            //TODO: Better handling of this. Its for when we only check one stock and doesn't need a foreach.
            $symbol = $stocks->Symbol;
            $askPrice = $stocks->Ask;
            $changeInPercent = $stocks->PercentChange;
            $change = $stocks->Change;
            $changeType = substr($change,0,1);
            switch($changeType){
                case "+":
                    $convertedChangeType = "positive";
                    break;
                case "-":
                    $convertedChangeType = "negative";
                    break;
                default:
                    $convertedChangeType = "unchanged";
            }

            $output .= "<div class='stock-item ".$convertedChangeType."'>";
            $output .= "<div class='stock-symbol'>".$symbol."</div>";
            $output .= "<div class='stock-ask'>".$askPrice."$</div>";
            $output .= "<div class='stock-change'>".$change."</div>";
            $output .= "<div class='stock-change-percent'>".$changeInPercent."</div>";
            $output .= "</div>";
        }else{
            foreach ($stocks as $key => $stock){
                $symbol = $stock->Symbol;
                $askPrice = round($stock->Ask, 1);
                $changeInPercent = $stock->PercentChange;
                $change = $stock->Change;
                $changeType = substr($change,0,1);
                switch($changeType){
                    case "+":
                        $convertedChangeType = "positive";
                        break;
                    case "-":
                        $convertedChangeType = "negative";
                        break;
                    default:
                        $convertedChangeType = "unchanged";
                }

                $output .= "<div class='stock-item ".$convertedChangeType."'>";
                $output .= "<div class='stock-symbol'>".$symbol."</div>";
                $output .= "<div class='stock-ask'>".$askPrice."$</div>";
                $output .= "<div class='stock-change'>".$change."$</div>";
                $output .= "<div class='stock-change-percent'>".$changeInPercent."</div>";
                $output .= "</div>";
            }
        }
        return $output;
    }

}

new Stocks();