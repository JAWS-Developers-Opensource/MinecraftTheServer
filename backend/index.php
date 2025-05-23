<?php
# Include


//Status
//die;

include "api.php";
require_once ("conf/config.php");

# Header
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');

# Init api
$API = new API();


$API->Boot();

$API->InitPath();