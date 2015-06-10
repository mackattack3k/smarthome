<?php
$filename = 'lamps.json';

if (file_exists($filename)) {
    echo "The file $filename exists!!!!";
} else {
    echo "The file $filename does not exist";
}

$getLampsJson   =   file_get_contents('lamps.json');
$lampsJson      =   json_decode($getLampsJson, true);
var_dump($lampsJson);



?>