<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function(){

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') {

		$pagination = Product::getPageSearch($search, $page);

	} else {

		$pagination = Product::getPage($page);

	}

	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x++)
	{

		array_push($pages, [
			'href'=>'/admin/products?'.http_build_query([
				'page'=>$x+1,
				'search'=>$search
			]),
			'text'=>$x+1
		]);

	}

	$page = new PageAdmin();

	$page->setTpl("products", [
		"products"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	]);

});
// abaixo apresentamos o formulário para edição
$app->get("/admin/products/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create");
		
});

// abaixo apresentamos o formulário para edição
$app->post("/admin/products/create", function(){

	// verifico se o usuário continua logado
	User::verifyLogin();

	// crio um novo produto
	$product = new Product();

	// pego os dados do meu post que foi preenchido
	$product->setData($_POST);

	$product->save();

	header("Location: /admin/products");
	exit;
		
});

// abaixo criamos a rota pra alteração do registro de produto
$app->get("/admin/products/:idproduct", function($idproduct){

	User::verifyLogin();
	
	// lista os produtos
	$product = new Product();

	$product->get((int)$idproduct);

	$page = new PageAdmin();

	$page->setTPL("products-update", [
		'product'=>$product->getValues()
	]);
});

// A rota abaixo permite ao usuário do nosso sistema fazer upload de imagens para o nosso arquivo.
$app->post("/admin/products/:idproduct", function($idproduct){

	User::verifyLogin();

	// lista os produtos
	$product = new Product();

	$product->get((int)$idproduct);

	// recebe o arquivo do campo tipo POST do meu site
	$product->setData($_POST);

	$product->save();

	// name é o nome do input lá no arquivo products-update.html 
	$product->setPhoto($_FILES["file"]);
	
	header("Location: /admin/products");
	exit;
});

// A rota abaixo permite a exclusão do registro de produtos.
$app->get("/admin/products/:idproduct/delete", function($idproduct){

	User::verifyLogin();

	// lista os produtos
	$product = new Product();

	$product->get((int)$idproduct);

	$product->delete();

	header("Location: /admin/products");
	exit;
	
});

?>