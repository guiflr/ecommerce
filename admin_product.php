<?php

use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function(){

	User::verifyLogin();

	$search = (isset($_GET["search"])?$_GET["search"]:"");
	$page = (isset($_GET["page"])?(int)$_GET["page"]:1);

	if($search != ""){

		$pagination = Product::getPageSearch($search, $page,10);

	}else{

		$pagination = Product::getPage($page, 10);

	}

	$pages = [];

	for($i=0;$i<$pagination["pages"];$i++){

		array_push($pages, [
			"href"=>"/admin/products?".http_build_query([
				"page"=>$i+1,
				"search"=>$search
			]),
			"text"=>$i+1
		]);
	}

	$page = new PageAdm();

	$page->setTpl("products", array(
		"products"=>$pagination["data"],
		"search"=>$search,
		"pages"=>$pages
	));

});

$app->get("/admin/products/create", function(){

	User::verifyLogin();

	$page = new PageAdm();

	$page->setTpl("products-create");
});

$app->post("/admin/products/create", function(){

	User::verifyLogin();

	$products = new Product();

	$products->setData($_POST);

	$products->save();

	header("Location: /admin/products");
	exit;
});

$app->get("/admin/products/:idproduct", function($idproduct){

	User::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);
	
	$page = new PageAdm();

	$page->setTpl("products-update",[
		"product"=>$products->getData()
	]);
});

$app->post("/admin/products/:idproduct", function($idproduct){

	User::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);
	
	$products->setData($_POST);

	$products->save();

	if(!isset($_FILES["file"]))$products->setPhoto($_FILES["file"]);
		
	header("Location: /admin/products");
	exit;
});

$app->get("/admin/products/:idproduct/delete", function($idproduct){

	User::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);
	
	$products->delete();

	header("Location: /admin/products");
	exit;
})
?>