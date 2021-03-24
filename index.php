<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use Hcode\Page;

$app = new Slim();
// para simplificar a linha abaixo fizemos estas declarações na linha de cima.
// $app = new \Slim\Slim();

$app->config('debug', true);

// Segundo o professor as unicas duas coisas que vão realmente interessar ou que vai mudae efetivamente no nosso projeto, é o comando abaixo que é a rota que estamos chamando e dentro deste bloco que é a barra '/'
// Quando chamar a função get sem nenhuma função, ou seja sem nada que precede a barra '/', ou seja, que esteja sendo incluido a mais na nossa rota, o meu php executará somente os comandos declarados aqui dentro desta função. Reforçando, dentro desta função tem somente a criação do header, do conteúdo e o footer que esta na função destruct, que a própria função destruct fará a criação deste footer. Por fim a linha que manda todo este processo começar o o comando "$app->run();" que esta lá em baixo, ou seja, quando estiver tudo montado e carregado, esta linha do $app->run() executa o nosso php.
$app->get('/', function() {
    
    // Depois de criar as dependências das nossas classes no no Git Bash Here, vamos testar abaixo a classe Sql por exemplo. Para isso inibimos a linha originalmente descrita abaixo:
	// echo "OK";

	// As linhas abaixo foram inibidas neste código, por que somente foram colocadas aqui para fazermos testes no inicio do projeto.
	// $sql = new Hcode\DB\Sql;

	// $results = $sql->select("SELECT * FROM tb_users");
	
	// echo json_encode($results);

	// Neste momento em que criamos o Page com construtor vazio diga-se de passagem, o sistema vai chamar o constructer e vai adicionar o header na nossa tela
	$page = new Page();

	// Quando chamar o setTPL passando o nome do nosso template do header, o sistema vai adicionar o conteudo da nossa página
	$page->setTpl("index");

	// Quando o sistema tiver passado pelos códigos acima, significa que acabou a execução, e nesse momento o php vai limpar a memória do nosso código e chamar a função detruct que vai incluir o footer na nossa página
});

$app->run();

 ?>