<?php

use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Product;
use \Hcode\Tpl;


$app->get('/admin', function() {

	User::verifyLogin();
    
	$page = new PageAdm();

	$page->setTpl("index");

});

//INICIO LOGIN
$app->get('/admin/login', function(){

	$page = new PageAdm([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");
});

$app->post('/admin/login', function(){
	
	User::login($_POST["login"],$_POST["password"]);
	
	header("Location: /admin");
	exit;
});

$app->get('/admin/logout', function(){

	User::logout();
	header("Location: /admin/login");
	exit;
});

//INICIO ESQUECEU A SENHA
$app->get("/admin/forgot", function(){

	$page = new PageAdm(["header="=>false,"footer"=>false]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdm(["header="=>false,"footer"=>false]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){
	
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdm(["header"=>false,"footer"=>false]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));	
});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$user->setPassword($_POST["password"]);

	$page = new PageAdm(["header"=>false,"footer"=>false]);

	$page->setTpl("forgot-reset-success");

});

?>