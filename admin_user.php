<?php
use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Product;
use \Hcode\Tpl;

$app->get("/admin/users", function(){
	
	User::verifyLogin();

	$search = (isset($_GET["search"])?$_GET["search"]:"");
	$page = (isset($_GET["page"])?(int)$_GET["page"]:1);

	if($search != ""){

		$pagination = User::getPageSearch($search, $page,10);

	}else{

		$pagination = User::getPage($page, 10);

	}

	$pages = [];

	for($i=0;$i<$pagination["pages"];$i++){

		array_push($pages, [
			"href"=>"/admin/users?".http_build_query([
				"page"=>$i+1,
				"search"=>$search
			]),
			"text"=>$i+1
		]);
	}

	$page = new PageAdm();

	$page->setTpl("users", array(
		"users"=>$pagination["data"],
		"search"=>$search,
		"pages"=>$pages
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

	define('SECRET_IV', pack('a16','senha'));
	define('SECRET', pack('a16','senha'));

	$data = $_POST["despassword"];

	$_POST["despassword"] = openssl_encrypt(
	json_encode($data),
	'AES-128-CBC',
	SECRET,
	0,
	SECRET_IV
	);
	
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