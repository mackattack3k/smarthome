<?php
if (array_key_exists('bestmatch', $stationIdVal) && $stationIdKey['bestmatch'] == 'true') {
// 'custprod' exists and is 1
    echo "bestmatch exists :)";
    echo $stationIdVal."<br>";
}else{
    print_r($stationIdVal);
    echo "nope..";
}  

?>