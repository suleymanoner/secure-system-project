<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

require_once dirname(__FILE__).'/../vendor/autoload.php';
require_once dirname(__FILE__)."/dao/BaseDao.php";
require_once dirname(__FILE__)."/dao/UserDao.php";

require_once dirname(__FILE__)."/services/UserService.php";

Flight::register('userService', 'UserService');

require_once dirname(__FILE__)."/routes/route.php";

Flight::set('flight.log_errors', TRUE);

Flight::start();
