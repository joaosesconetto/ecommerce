<?php 
// como nosso arquivo de rotas estava ficando muito grande, o professor resolveu quebrar os arquivos php em arquivos distintos para cada tipo de assunto. veja os novos arquivos criados (admin.php, admin-categories.php, admin-users.php e site.php)

// verificamos se a sessão foi iniciada.
session_start();

// $versaoPHP = phpversion () . "<br>";
// echo $versaoPHP;

// o nome do nosso vendor esta no arquivo composer.json: "name": "hcodebr/ecommerce"
// Veja neste arquivo composer.json todas as rotas que são carregadas automaticamente.
require_once("vendor/autoload.php");

// Slim é a classe do microframework que instalamos para criação de rotas. Tivemos que criar as dependências do Slim com o Compuser. Esta regras de criação de rotas é que definirá na nossa URL nomes sugestivos para encontrar as nossas funcionalidades do nosso site. Isso ajuda até o google no rankeamento do site, para melhor identificação dos caminhos das etapas do nosso site. (Ver aula 90)
// Na aula 105 fizemos o front da tela de login. Na aula a 106 implementamos as regras do próprio login.
use \Slim\Slim;
// app é uma variável à qual vamos definir cada uma das rotas
$app = new Slim();

$app->config('debug', true);

require_once("functions.php");
require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");
require_once("admin-orders.php");

$app->run();

?>