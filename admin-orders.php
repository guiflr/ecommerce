<?php

use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;

$app->get("/admin/orders/:idorder/delete", function($idorder){

	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	$order->delete();

	header("Location: /admin/orders");
	exit;
});

$app->get("/admin/orders/:idorder/status", function($idorder){

	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	$page = new PageAdm();

	$page->setTpl("order-status", [
		"order"=>$order->getData(),
		"status"=>OrderStatus::listAll(),
		"msgSuccess"=>Order::getMsgSuccess(),
		"msgError"=>Order::getMsgError()
	]);
});

$app->post("/admin/orders/:idorder/status", function($idorder){

	User::verifyLogin();

	if(!isset($_POST["idstatus"]) || !(int)$_POST["idstatus"] < 0){

		Order::setMsgError("Informe o status atual.");
		header("Location: /admin/orders/".$idorder."/status");
		exit;
	}

	$order = new Order();

	$order->get((int)$idorder);

	$order->setidstatus((int)$_POST["idstatus"]);

	$order->save();

	Order::setMsgSuccess("Status alterado.");
	header("Location: /admin/orders/".$idorder."/status");
	exit;
});

$app->get("/admin/orders/:idorder", function($idorder){

	$order = new Order();

	$order->get((int)$idorder);

	$cart = $order->getCart();

	$page = new PageAdm();

	$page->setTpl("order", [
		"order"=>$order->getData(),
		"cart"=>$cart->getData(),
		"products"=>$cart->getProducts()

	]);
});

$app->get("/admin/orders", function(){

	User::verifyLogin();

	$page = new PageAdm();

	$page->setTpl("orders", [
		"orders"=>Order::listAll()
	]);
});


?>