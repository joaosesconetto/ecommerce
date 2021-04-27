<?php

use \Hcode\PageAdmin;
use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

// A difereça de uma rota pra outra rota é só o metódo. Se for acessado via get ele vai responder com html. Se for acessado via post ele vai responder com insert, ele espera receber estes dados via post e enviar para o banco de dados e salvar estes registros.
$app->get("/admin/categories", function(){

	User::verifyLogin();

	// abaixo verificamos se a variável search existe, se existir traz ela mesmo, se não existir faz search em branco.
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	
	// echo $search;
	// se for definido na minha URL o número da página então page será igual a este número definido, se não page será igual a 1.
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') {

		$pagination = Category::getPageSearch($search, $page);

	} else {

		$pagination = Category::getPage($page);

	}


	// $users = User::getPage($page, 10); // Caso queira deixar o usuário informar a qtd de itens então devemos pegar via get e colocar no lugar do 10. Mas o que fizemso foi deixar o próprio método a definição desta quantidade

	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x++)
	{

			array_push($pages, [
				'href'=>'/admin/categories?'.http_build_query([
					'page'=>$x+1,
					'search'=>$search
				]),
				'text'=>$x+1
			]);
	}

	$page = new PageAdmin();	

	$page->setTpl("categories", [
		"categories" =>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	]);
});


// abaixo carregamos o arquivo html categories-create.html, que cria o item de menu categories.
$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();	

	$page->setTpl("categories-create");
});

// Cadastra a categoria
$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();	

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;
});

// Rota para exclusão do registro de categorias
// Notei que somente nos casos que estamos colhendo uma variável da URL é que informamos o nome deste atributo em function como esta abaixo function($idcategory)
$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	// Criando o método get para carrega o registro para ter certeza que ele ainda existe. 
	$category->get((int)$idcategory);
	// Criando o método delete que ainda também vamos criar.
	$category->delete();
	// redireciona para tela de categorias
	header("Location: /admin/categories");
	exit;
});

// Rota para alteração do registro de categorias - 1ª parte
// No caso da alteração do registro, será chamado uma view, uma tela. Por isso utilizamos o setTPL - template
// Notei que somente nos casos que estamos colhendo uma variável da URL é que informamos o nome deste atributo em function como esta abaixo function($idcategory)
$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();
	// temos que converter o $idcategory pra int, por que este idcategory esta vindo da URL, pois foi escolhido um registro para ser deletado pelo usuário. E tudo que vem via URL vem como string.
	$category->get((int)$idcategory);

	$page = new PageAdmin();	

	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);
});

// Rota para alteração do registro de categorias - 2ª parte
// No caso da alteração do registro, será chamado uma view, uma tela. Por isso utilizamos o setTPL - template
// Notei que somente nos casos que estamos colhendo uma variável da URL é que informamos o nome deste atributo em function como esta abaixo function($idcategory)
$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();
	// temos que converter o $idcategory pra int, por que este idcategory esta vindo da URL, pois foi escolhido um registro para ser deletado pelo usuário. E tudo que vem via URL vem como string.
	$category->get((int)$idcategory);

	// abaixo eu pego os dados que vem do formulário, pois os campos do meu formulário são exatamentes como é os do meu banco de dados, por isso que eu consigo passar direto para setData, estes campos do meu post $_POST.
	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;
});

$app->post("/categories/:idcategory", function($idcategory){
	
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>[]
	]);
});


$app->get("/admin/categories/:idcategory/products", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products", [
		'category'=>$category->getValues(),
		'productsRelated'=>$category->getProducts(),
		'productsNotRelated'=>$category->getProducts(false)
	]);
});

// criando a rotas para incluir produtos em determinada categoria
// :idcategory da página do html é recebido na variável $idcategory e
// :idproduct da página do html é recebido na variável $idpr0duct e
$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){

	User::verifyLogin();

	// utilizamos a classe Category para recuperar o id que foi passado na função com get.
	$category = new Category();
	// verificamos se a variável idcategory é mesmo númerico, por que pode ter vindo alguma sujeira da URL, para isso chamamos a classe get que esta declarada no arquivo Category.php
	$category->get((int)$idcategory);
	// não precisa do PageAdmin por que não vai ter tela
	// $page = new PageAdmin();

	$product = new Product();

	$product->get((int)$idproduct);

	// chamando o método addProduct para exe
	// devemos criar o método addProduct na classe category, pois $category acima esta sendo instânciado da classe Category()
	$category->addProduct($product);

	header("Location:/admin/categories/".$idcategory."/products");

	exit;

});

// criando a rotas para incluir produtos em determinada categoria
// :idcategory da página do html é recebido na variável $idcategory e
// :idproduct da página do html é recebido na variável $idpr0duct e
$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){

	User::verifyLogin();

	// utilizamos a classe Category para recuperar o id que foi passado na função com get.
	$category = new Category();
	// verificamos se a variável idcategory é mesmo númerico, por que pode ter vindo alguma sujeira da URL, para isso chamamos a classe get que esta declarada no arquivo Category.php
	$category->get((int)$idcategory);
	// não precisa do PageAdmin por que não vai ter tela
	// $page = new PageAdmin();

	$product = new Product();

	$product->get((int)$idproduct);

	// chamando o método removeProduct para exe
	// devemos criar o método removeProduct na classe category, pois $category acima esta sendo instânciado da classe Category()
	$category->removeProduct($product);
	header("Location: /admin/categories/".$idcategory."/products");
	exit;
	
});

?>