<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

// A rota criada abaixo é para identificar os caminhos que a classe PageAdmin utilizará:
// O primeiro parametro é o link da nossa administração. Por padrão sempre é admin. Só que num projeto mais seguro, devemos utilizar outro nome para evitar invasão com um nome tam padrão como este.
// Esta rota diz que para chamarmos as funções da classe PageAdmin devemos informar na linha da URL o comando: http://www.hcodecommerce.com.br/admin
// a rota abaixo verifica se o usuário esta logado ou não.
$app->get('/admin', function() {
    
    // abaixo criamos um metodo estático para verificar se o usuário esta logado ou não. Este metódo estático esta sendo criado lá dentro da classe User.php
    // User é uma classe que criamos como este mesmo nome é claro: User.php
    User::verifyLogin();

	// A classe é a nossa classe PageAdmin onde serão procurados os templates corretos.
	$page = new PageAdmin();

	// Quando chamar o setTPL passando o nome do nosso template do header, o sistema vai adicionar o conteudo da nossa página
	$page->setTpl("index");

	// Quando o sistema tiver passado pelos códigos acima, significa que acabou a execução, e nesse momento o php vai limpar a memória do nosso código e chamar a função detruct que vai incluir o footer na nossa página
});

// a rota abaixo cria o template da tela de login
$app->get('/admin/login', function(){
	// observe que como o login não tem header e nem footer padrão, então precisamos de desabilitar para a tela de login, estes html que criam o header e o footer e que estão na minha classe PageAdmin que consequentemente é uma extenção da classe page.
	// Para isso vamos passar uma variável que no caso aqui é um array, para desabiligar o __construct e o __destruct.
	// Vamos ter que ir lá no page que é uma extenção da classe PageAdmin para instruir o recebimento desta variável array com estes dois parametros.
	// abaixo criamos a nossa nova página "metódo construtor"
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

		// chamando o template que acabamos de criar agora que é o "login"
		$page->setTpl("login");

});
// a rota abaixo cria o template da para validação de login
// lá no arquivo login.html da pasta admin, esta declarado a seguinte linha:
// <form action="/admin/login" method="post"> que diz que identifica o caminho e o method utilizado que é post, por isso a rota abaixo esta definida como colocamos ai...
$app->post("/admin/login", function() {
// abaixo declaramos um metódo estático chamado login. Este metódo vai receber o post de login e o poste da senha.
// as definições do metódo $_POST que esta sendo referenciado aqui, consta como entrada de dados no código (<form action="/admin/login" method="post">) do arquivo html login.htm.
	User::login($_POST["login"], $_POST["password"]);
// se não estourar um exception ou qualquer erro na entrada da senha, ele vai passar aqui com sucesso e então podemos redirecionar pra nossa home page da nossa administração.
	header("Location: /admin");
	exit;

});

// Criando uma rota para executação do metódo logout. Depois de criado esta rota, alteramos o    caminho dentro do arquivo header.htm. para constar a rota "/admin/logout", para isso substituimos onde a linha "<a href="#" class="btn btn-default btn-flat">Sign out</a>"
// por esta: "<a href="/admin/logout" class="btn btn-default btn-flat">Sign out</a>"
// Este arquivo esta dentro da pasta "C:\ecommerce\views\admin"  
$app->get('/admin/logout', function() {

	User::logout();

	header("Location: /admin/login");
	exit;
});

// A rota abaixo /admin/forgot ela é enchergada por que também na declaração "use \Hcode\PageAdmin;" que esta dentro do nosso vendor: "C:\ecommerce\vendor\hcodebr\php-classes\src" declarado no composer.json. consta a seguinte declaração: "public function __construct($opts = array(), $tpl_dir = "/views/admin/") onde consta o nosso arquivo forgot "
$app->get("/admin/forgot", function(){

	// observe que como o login não tem header e nem footer padrão, então precisamos de desabilitar para a tela de login, estes html que criam o header e o footer e que estão na minha classe PageAdmin que consequentemente é uma extenção da classe page.
	// Para isso vamos passar uma variável que no caso aqui é um array, para desabiligar o __construct e o __destruct.
	// Vamos ter que ir lá no page que é uma extenção da classe PageAdmin para instruir o recebimento desta variável array com estes dois parametros.
	// abaixo criamos a nossa nova página "metódo construtor"
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	// chamando o template que (setTPL seta o template) acabamos de criar agora que é o "forget"
	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function() {

// abaixo criamos um metódo que saiba fazer todas as verificações de email que é o metódo getForgot. Criamos este metódo lá na classe usuario
	$user = User::getForgot($_POST["email"]);

	// Depois de executado o forgot acima, vamos enviar um email para o usuário informado que o email foi enviado com sucesso...
	header("Location: /admin/forgot/sent");
	exit;
});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
		// Agora vamos colocar o link lá na tela /admin/forgot/sent, lá no template. 
		$page->setTpl("forgot-sent");
});
	// Até este momento tem que esta enviando o email....

// criando a rota para quando o usuário clicar no link de recuperação da senha.
// A rota abaixo /admin/forgot/reset ela é enchergada por que também na declaração "use \Hcode\PageAdmin;" que esta dentro do nosso vendor: "C:\ecommerce\vendor\hcodebr\php-classes\src" declarado no composer.json. consta a seguinte declaração: "public function __construct($opts = array(), $tpl_dir = "/views/admin/") onde consta o nosso arquivo forgot "
$app->get("/admin/forgot/reset", function() {
	// temos que saber à quem pertence este código que alguem esta tentando recuperar...
	// vamos buscar o metódo estático que criaremos em seguinda à declaração desta chamada aqui, lá na classe User.php para válidar este código.
	// $_GET["code"] é o código que esta vindo via get lá do arquivo forgot-reset.html
	$user = User::validForgotDecrypt($_GET["code"]);

	
	// echo "Passei aqui n rota $app->get(admin forgot reset, function() em admin.php ===> " . "<br>"; 
	var_dump($user);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

// abaixo devolvemos para o usuário atraves do html forgot-reset o nome e o mesmo código criptografado, por que ele vai precisar válidar novamente na proxima página.
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

// Abaixo o usuário envia a nova senha pelo método post como esta no arquivo forgot-reset.html "<form  action="/admin/forgot/reset" method="post">"
$app->post("/admin/forgot/reset", function(){


	// abaixo vamos verificar novamente se o código criptografado continua válido, se não houve nenhuma invasão de outro usuário mal feitor. Só que agora em vez de receber este código via $_GET, vamos receber via $_POST
	// $user = User::validForgotDecrypt($_GET["code"]);
	$forgot = User::validForgotDecrypt($_POST["code"]);

	// Agora vamos criar um método estático para confirmar no banco de dados que esta senha já foi utilizada e que não poderá ser utilizada mais uma vez, nem se for no período de uma hora como é a nossa regra de reinicialização de senha.
	// oberser que dentro da variável $user temos um array retornado da função de validação acima. Dentro deste array temos uma chave com o idrecovery que estamos passando abaixo para o método estático setForgotUsed.
	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	// get é um método que declaramos lá na classe User. Estamos passando a váriavel $forgot para este método como inteiro e que esta variável esta no array na posição iduser. Pra carregar os dados do usuário. Assim temos os dados do usuário.
	$user->get((int)$forgot["iduser"]);

	// echo "Passei aqui n rota $app->post(/admin/forgot/reset, function() em admin.php ===> " . "<BR>";
	// vardump($user);
	// vamos criar um método para criptografar a nova senha informada pelo usuário.
	// password_hash é um método nativo no PHP.
	// primeiro parâmetro é a senha
	// segundo parâmetro é o modo da criptografia. O mode de criptrografia "PASSWORD_BCRYPT" é o mode default por isso colocamos a constante "PASSWORD_DEFAULT"
	// terceiro o o cost => 12 por exemplo que é o nível de segurança para geração da senha. quanto mais auto mais segura, mas temos que tomar cuidado, pois um número muito alto, pode travar o nosso sistema se muitos usuários estiverem solicitando senha ao mesmo tempo. 12 é um númro bom de segurança.
	// $2y$12$MqsYSphTbNqGcaBZhtHvEuxaTkzVn3n12uszoK6MACXINN.K6sHpq
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	// Abaixo criamos um método setPassword que vai gerar um rash e gravar no banco de dados.
	$user->setPassword($password);

	// a seguir vamos avisar o usuário que a senha dele foi trocada com sucesso...
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

// como o html forgot-reset-success não requer nenhuma variável, podemos retirar o array abaixo:
	// $page->setTpl("forgot-reset-success", array(
	// 	"name"=>$user["desperson"],
	// 	"code"=>$_GET["code"]
	// ));
	$page->setTpl("forgot-reset-success");

});

?>