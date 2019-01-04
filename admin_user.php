<?php
use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Product;
use \Hcode\Tpl;

$app->get("/admin/users", function(){
	
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdm();

	$page->setTpl("users", array(
		"users"=>$users
	));
});

$app->get("/admin/users/create", function(){

	User::verifyLogin();

	$page = new PageAdm();

	$page->setTpl("users-create");

});

$app->post("/admin/users/create", function(){
	
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;
});

$app->get("/admin/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

$app->get("/admin/users/:iduser", function($iduser){
	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdm();

	$page->setTpl("users-update", array(
		"user"=>$user->getData()
	));
});

$app->post("/admin/users/:iduser", function($iduser){
	
	User::verifyLogin();	

	$user = new User();

	$user->get((int)$iduser);

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});


?>