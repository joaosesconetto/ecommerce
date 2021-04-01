<?php 
// verificamos se a sessão foi iniciada.
session_start();
// o nome do nosso vendor esta no arquivo composer.json: "name": "hcodebr/ecommerce"
// Veja neste arquivo composer.json todas as rotas que são carregadas automaticamente.
require_once("vendor/autoload.php");

// Slim é a classe do microframework que instalamos para criação de rotas. Tivemos que criar as dependências do Slim com o Compuser. Esta regras de criação de rotas é que definirá na nossa URL nomes sugestivos para encontrar as nossas funcionalidades do nosso site. Isso ajuda até o google no rankeamento do site, para melhor identificação dos caminhos das etapas do nosso site. (Ver aula 90)
// Na aula 105 fizemos o front da tela de login. Na aula a 106 implementamos as regras do próprio login.
use \Slim\Slim;
use \Hcode\page;
use \Hcode\PageAdmin;
// abaixo criamos um namespace só para os nossos Model e vamos dizer aqui que usaremos a nossa class User.
use \Hcode\Model\User;
use \Hcode\Model\Category;

// app é uma variável à qual vamos definir cada uma das rotas
$app = new Slim();
// para simplificar a linha abaixo fizemos estas declarações na linha de cima.
// $app = new \Slim\Slim();

$app->config('debug', true);

// Segundo o professor as unicas duas coisas que vão realmente interessar ou que vai mudar efetivamente no nosso projeto, é o comando abaixo que é a rota que estamos chamando e dentro deste bloco que é a barra '/'
// Quando chamar a função get sem nenhuma função, ou seja sem nada que precede a barra '/', ou seja, que esteja sendo incluido a mais na nossa rota, o meu php executará somente os comandos declarados aqui dentro desta função. Reforçando, dentro desta função tem somente a criação do header, do conteúdo que esta no arauivo index.html e o footer que será criado e destruido pela  função destruct. Por fim a linha que manda todo este processo começar e o comando "$app->run();" que esta lá em baixo, ou seja, quando estiver tudo montado e carregado, esta linha do $app->run() executa o nosso php.
// get é o metodo que estamos utilizando do Slim para processar a nossa rota. Toda vez que digitamos na URL algum dado, o metódo que esta sendo utilizado é o metódo get.
// o nome da rota será passado em function e é o mesmo nome que estará em '/', igualzinho'(ver aula 90)
// a '/' siginifica que aqui entra o nome da rota principal que é o nome da pasta onde o nosso programa php esta executando.
// a rota abaixo monta o header, o conteúdo e o footer da minha página
$app->get('/', function() {
    
    // Depois de criar as dependências das nossas classes no no Git Bash Here, vamos testar abaixo a classe Sql por exemplo. Para isso inibimos a linha originalmente descrita abaixo:
	// echo "OK";

	// As linhas abaixo foram inibidas neste código, por que somente foram colocadas aqui para fazermos testes no inicio do projeto.
	// $sql = new Hcode\DB\Sql;

	// $results = $sql->select("SELECT * FROM tb_users");
	
	// echo json_encode($results);

	// Neste momento em que criamos o Page com construtor vazio diga-se de passagem, o sistema vai chamar o construct e vai adicionar o header na nossa tela
	$page = new page();

	// Quando chamar o setTPL passando o nome do nosso template do header, o sistema vai adicionar o conteudo da nossa página
	$page->setTpl("index");

	// Quando o sistema tiver passado pelos códigos acima, significa que acabou a execução, e nesse momento o php vai limpar a memória do nosso código e chamar a função detruct que vai incluir o footer na nossa página
});

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
// <form action="/admin/login" method="post">
// que diz que identifica o caminho e o method utilizado que é post, por iss a rota abaixo esta definica como colocamos ai...
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

// declarando a rota para acessar a tabela de usuários na template users.
// para execução desta rota o usuário deve esta logado no sistema.
// Esta primeira rota é para listar todos os usuários. CONSULTA
$app->get("/admin/users", function() {

	// o usuário tem que esta logado e ser do administrativo
	User::verifyLogin();

	// chamamos a seguir o metódo estático User::listAll
	// User é a nossa classe cujo arquivo coincidentemente também chama-se User e listAll e o metódo que se encontra lá na nossa classe User. 
	// abaixo receberemos o array com toda a lista de usuários.
	$users = User::listAll();

	$page = new PageAdmin();

	// abaixo estamos passando todo o nosso array com a lista de usuários "$users" para o template

	$page->setTpl("users", array(
		"users" => $users
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

// A difereça de uma rota pra outra rota é só o metódo. Se for acessado via get ele vai responder com html. Se for acessado via post ele vai responder com insert, ele espera receber estes dados via post e enviar para o banco de dados e salvar estes registros.
$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();	

	$page->setTpl("categories", [
		'categories'=>$categories
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


$app->run();

?>