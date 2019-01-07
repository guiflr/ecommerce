<?php

use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Tpl;


$app->config('debug', true);

$app->get('/', function() {
    
	$products = Product::listAll();

	$page = new Page();

	$page->setTpl("index", [
		"products"=>Product::checkList($products)
	]);

});

//categorias dentro do site
$app->get("/categories/:idcategory", function($idcategory){

	$page = (isset($_GET['page']))?(int)$_GET['page']:1;
	
	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page);

	$pages = [];

	for ($i=1; $i <= $pagination['page'] ; $i++) { 
		
		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i
		]);
	}

	$pageSite = new Page();

	$pageSite->setTpl("category", [
		'category'=>$category->getData(),
		'products'=>$pagination["data"],
		'pages'=>$pages
	]);
});



?>