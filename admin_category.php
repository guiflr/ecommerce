<?php
use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Tpl;

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdm();

	$page->setTpl("categories", [
		"categories"=>$categories
	]);
});

$app->get("/admin/categories/create", function(){

	User::verifyLogin();
	
	$page = new PageAdm();

	$page->setTpl("categories-create");
});

$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdm();

	$page->setTpl("categories-update",[
		"category"=>$category->getData()
	]);
});

$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategory/products", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdm();

	$page->setTpl("categories-products", [
		'category'=>$category->getData(),
		'productsRelated'=>$category->getProduct(),
		'productsNotRelated'=>$category->getProduct(false)
	]);
});

$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory,$idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->addProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});

$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory,$idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->deleteProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});
?>