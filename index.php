<?php 

require_once("vendor/autoload.php");

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$db = new Hcode\DB\Sql();

	$res = $db->Select("SELECT * FROM tb_users");

	echo json_encode($res);

});

$app->run();

 ?>