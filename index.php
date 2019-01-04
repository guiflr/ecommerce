<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once("site.php");
require_once("admin.php");
require_once("admin_user.php");
require_once("admin_category.php");
require_once("admin_product.php");

$app->run();

 ?>