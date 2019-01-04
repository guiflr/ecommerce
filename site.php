<?php

use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Product;
use \Hcode\Tpl;


$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

?>