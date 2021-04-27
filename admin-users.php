<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// declarando a rota para acessar a tabela de usuários na template users.
// para execução desta rota o usuário deve esta logado no sistema.
// Esta primeira rota é para listar todos os usuários. CONSULTA
$app->get("/admin/users/:iduser/password", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-password", [
		"user"=>$user->getValues(),
		"msgError"=>User::getError(),
		"msgSuccess"=>User::getSuccess()
	]);

});

$app->post("/admin/users/:iduser/password", function($iduser){

	User::verifyLogin();

	if (!isset($_POST['despassword']) || $_POST['despassword']==='') {

		User::setError("Preencha a nova senha.");
		header("Location: /admin/users/$iduser/password");
		exit;

	}

	if (!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm']==='') {

		User::setError("Preencha a confirmação da nova senha.");
		header("Location: /admin/users/$iduser/password");
		exit;

	}

	if ($_POST['despassword'] !== $_POST['despassword-confirm']) {

		User::setError("Confirme corretamente as senhas.");
		header("Location: /admin/users/$iduser/password");
		exit;

	}

	$user = new User();

	$user->get((int)$iduser);

	$user->setPassword(User::getPasswordHash($_POST['despassword']));

	User::setSuccess("Senha alterada com sucesso.");

	header("Location: /admin/users/$iduser/password");
	exit;

});


$app->get("/admin/users", function() {

	// o usuário tem que esta logado e ser do administrativo
	User::verifyLogin();

	// abaixo verificamos se a variável search existe, se existir traz ela mesmo, se não existir faz search em branco.
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	// se for definido na minha URL o número da página então page será igual a este número definido, se não page será igual a 1.
	// echo 'usuários ==> '. $search;
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') {

		$pagination = User::getPageSearch($search, $page);

	} else {

		$pagination = User::getPage($page);

	}

	
	
	// $users = User::getPage($page, 10); // Caso queira deixar o usuário informar a qtd de itens então devemos pegar via get e colocar no lugar do 10. Mas o que fizemso foi deixar o próprio método a definição desta quantidade

	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x++)
	{

			array_push($pages, [
				'href'=>'/admin/users?'.http_build_query([
					'page'=>$x+1,
					'search'=>$search
				]),
				'text'=>$x+1
			]);
	}
	
	$page = new PageAdmin();

	// abaixo estamos passando todo o nosso array com a lista de usuários "$users" para o template

	$page->setTpl("users", array(
		"users" =>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	
	));

});


// As três proximas rotas abaixo preparam o caminho para após estas três rotas efetivarmos a chamada para efetivamente executar cada uma dessas três ações.
// Esta segunda rota é para incluir usuários. INCLUSÃO
$app->get("/admin/users/create", function() {

	// o usuário tem que esta logado e ser do administrativo
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

// Mudamos a rota delete lá de baixo, porque segundo o professor o Slim framework quando atribui a rota "/admin/users/:iduser/delete" vai assumir toda esta rota até o delete. Agora se colocar-mos abaixo do update a seguir por exemplo, quando o Slim framework ler esta rota, supondo que colocamos esta rota abaixo da rota update repito, ele vair ler somente até o caminho "/admin/users/:iduser" e vai desconsiderar o delete. Então temos que tomar cuidado com a declaração dessas rotas, pelo que eu entedi, devemos colocar as rotas com mesmos caminhos iniciais maiores no inicio das declarações de rotas.
// Ainda não foi utilizado o metódo delete no inicio desta linha como estava antes "$app->delete" segundo o professo, para o metódo Slim receber o metódo delete, vamos ter que mandar via post e vamos ter que enviar mais um campo chamado _metode escrito delete pra ele entender que estamos chamando realmente o metódo delete. Disse ainda que na maioria dos servidores web o metódo delete vem desabilitado.
// a rota a seguir faz a EXCLUSÃO do registro da tabela de usuários
// Notei que somente nos casos que estamos colhendo uma variável da URL é que informamos o nome deste atributo em function como esta abaixo function($iduser)
$app->get("/admin/users/:iduser/delete", function($iduser) {

// para fazer a exclusão o usuário precisa também esta logado.
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

// Esta terceira rota é para fazer alterações na tabela de usuários. UPDATE
// a função que a rota abaixo esta indicando vai devolver o id do usuário que foi alterado.
// :iduser é definido como um padrão. O valor que vier no :iduser este chamado vai atribuir a variável $iduser.
// A difereça de uma rota pra outra rota é só o metódo. Se for acessado via get ele vai responder com html. Se for acessado via post ele vai responder com insert, ele espera receber estes dados via post e enviar para o banco de dados e salvar estes registros.
// Notei que somente nos casos que estamos colhendo uma variável da URL é que informamos o nome deste atributo em function como esta abaixo function($iduser)
$app->get("/admin/users/:iduser", function($iduser) {

	// o usuário tem que esta logado e ser do administrativo
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

	$page = new PageAdmin();

	$page->setTpl("users-upadate");

});

// AS ROTAS A SEGUIR EXECUTAM DEFINITIVAMENTE O QUE FOI PREPARADO NAS TRÊS ROTAS ACIMA
// A difereça de uma rota pra outra rota é só o metódo. Se for acessado via get ele vai responder com html. Se for acessado via post ele vai responder com insert, ele espera receber estes dados via post e enviar para o banco de dados e salvar estes registros.
// a rota a seguir faz o INSERT
$app->post("/admin/users/create", function() {

// para fazer o insert o usuário precisa também esta logado.
	User::verifyLogin();

	$user = new User();
	// se o campo ou check box foi marcado la no noss post atribuimos, representado por interrogação,  ao valor de 1, se não foi definido atribuimos o valor 0
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	// Até então estavamos em outra rota, num arquivo post, num acesso via post, então vamos mandar de volta pra barra users pra visualizar na tabela que inseriu realmente os dados do novo usuário.
	header("Location: /admin/users");
	exit;
	
});

// a rota a seguir faz o UPDATE
// Notei que somente nos casos que estamos colhendo uma variável da URL é que informamos o nome deste atributo em function como esta abaixo function($iduser)
$app->post("/admin/users/:iduser", function($iduser) {

// para fazer o update o usuário precisa também esta logado.
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
});

?>