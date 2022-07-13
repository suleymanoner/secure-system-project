<?php
define('SERVER_ROOT', realpath(dirname(__FILE__)));

error_reporting(0);
ini_set('display_errors', 0);

$arr = explode("/", $_GET['send']);
if ($arr[0] == 'swagger.json') {

    require (SERVER_ROOT . "/../vendor/autoload.php");
    $openapi = \OpenApi\scan([
        SERVER_ROOT . "/../api/models",
        SERVER_ROOT . "/../api/routes",
        SERVER_ROOT . "/doc_setup.php"
    ]);

    $base_url = "";
    if ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")) {
        $base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";
    } else {
        $base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    $base_url = "https://secure-project.herokuapp.com/api/";

    $openapi->servers[0]->url = str_replace("doc/swagger.json", "", $base_url);

  
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Content-Type: application/json');

    echo $openapi->toJson();
}
