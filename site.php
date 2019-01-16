<?php

use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Addresses;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
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

$app->get("/products/:desurl", function($desurl){

	$product = new Product();

	$product->getFromDesURL($desurl);

	$page = new Page();
	$page->setTpl("product-detail", [
		"product"=>$product->getData(),
		"categories"=>$product->getCategories()
	]);
});

$app->get("/cart", function(){

	$cart = Cart::getFromSession();

	$page = new Page();

	if(count($cart->getProducts())>0){

		$data = $cart->getData();
		$products = $cart->getProducts();
		$error = Cart::getMsgError();
	}else{
		$data = "";
		$products = "";
		$error = "";
	}

	$page->setTpl("cart", [
		"cart"=>$data,
		"products"=>$products,
		"error"=>$error
	]);
});

$app->get("/cart/:idproduct/add", function($idproduct){

	$product = new Product();
	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$qtd = (isset($_GET["qtd"]))?(int)($_GET["qtd"]):1;

	for($i=0;$i<$qtd;$i++){

		$cart->addProduct($product);

	}

	header("Location: /cart");
	exit;
});

$app->get("/cart/:idproduct/minus", function($idproduct){

	$product = new Product();
	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();
	$cart->removeProduct($product);

	header("Location: /cart");
	exit;
});

$app->get("/cart/:idproduct/remove", function($idproduct){

	$product = new Product();
	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();
	$cart->removeProduct($product, true);

	header("Location: /cart");
	exit;
});

$app->post("/cart/freight", function(){

	$cart = Cart::getFromSession();
	$cart->setFreight($_POST["zipcode"]);
	header("Location: /cart");
	exit;
});

$app->get("/checkout", function(){

	User::verifyLogin(false);

	$cart = Cart::getFromSession();

	$address = new Addresses();
	$page = new Page();

	$page->setTpl("checkout", [
		"cart"=>$cart->getData(),
		"address"=>$address->getData()
	]);
});

$app->get("/login", function(){

	$page = new Page();

	$page->setTpl("login",[
		"error"=>User::getMsgError(),
		"errorRegister"=>User::getRegisterError(),
		"registerValues"=>(isset($_SESSION["registerValues"]))?$_SESSION["registerValues"]:["name"=>"","email"=>"","phone"=>""]
	]);
});

$app->post("/login", function(){

	try {
	
		User::Login($_POST["login"],$_POST["password"]);


	} catch (Exception $e) {
		
		User::setMsgError($e->getMessage());

	}

	header("Location: /checkout");
	exit;
});


$app->get("/logout", function(){

	User::logout();

	header("Location: /login");
	exit;
});

$app->post("/register", function(){

	$_SESSION["registerValues"] = $_POST;

	if(!isset($_POST["name"]) || $_POST["name"] == ""){

		User::setRegisterError("Preencha seu nome");
		header("Location: /login");
		exit;
	}

	if(!isset($_POST["email"]) || $_POST["email"] == ""){

		User::setRegisterError("Preencha seu email");
		header("Location: /login");
		exit;
	}
	if(!isset($_POST["email"]) || $_POST["email"] == ""){

		User::setRegisterError("Preencha seu email");
		header("Location: /login");
		exit;
	}

	if(User::emailExists($_POST["email"]) === true){

		User::setRegisterError("Este email já esta cadastrado");
		header("Location: /login");
		exit;

	}

	$user = new User();

	$user->setData([
		"inadmin"=>0,
		"desperson"=>$_POST["name"],
		"deslogin"=>$_POST["email"],
		"desemail"=>$_POST["email"],
		"nrphone"=>$_POST["phone"],
		"despassword"=>$_POST["password"]
	]);

	$user->save();

	header("Location: /login");
	exit;
});


?>