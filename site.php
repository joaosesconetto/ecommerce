<?php 

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;

// Segundo o professor as unicas duas coisas que vão realmente interessar ou que vai mudar efetivamente no nosso projeto, é o comando abaixo que é a rota que estamos chamando e dentro deste bloco que é a barra '/'
// Quando chamar a função get sem nenhuma função, ou seja sem nada que precede a barra '/', ou seja, que esteja sendo incluido a mais na nossa rota, o meu php executará somente os comandos declarados aqui dentro desta função. Reforçando, dentro desta função tem somente a criação do header, do conteúdo que esta no arquivo index.html e o footer que será criado e destruido pela  função destruct. Por fim a linha que manda todo este processo começar é o comando "$app->run();" que esta lá em baixo, ou seja, quando estiver tudo montado e carregado, esta linha do $app->run() executa o nosso php.
// get é o metodo que estamos utilizando do Slim para processar a nossa rota. Toda vez que digitamos na URL algum dado, o método que esta sendo utilizado é o método get.
// o nome da rota será passado em function e é o mesmo nome que estará em '/', igualzinho'(ver aula 90)
// a '/' siginifica que aqui entra o nome da rota principal que é o nome da pasta onde o nosso programa php esta executando.
// a rota abaixo monta o header, o conteúdo e o footer da minha página
$app->get('/', function() {
    
    // listanto os produtos que estão no banco de dados...
    $products = Product::listAll();
	// var_dump($products);
    // Neste momento em que criamos o Page com construtor vazio diga-se de passagem, o sistema vai chamar o construct e vai adicionar o header na nossa tela
	$page = new Page();

// fazendo o foreach que esta dentro do setTpl
	$page->setTpl("index", [
		// abaixo estou incluindo a foto quando chamo Product::checkList($products)
		'products'=>Product::checkList($products)
	]);

});

// abaixo vamos criar uma rota para quando clicar em uma determinada categoria, aparecer os produtos daquela categoria.
// o :idcategory como esta na linha da URL, por que foi solicitado esta categoria pelo usuário, então já pegamos da linha da URL como esta abaixo, este código que aparece lá na URL.
// Notei que somente nos casos que estamos colhendo uma variável da URL é que informamos o nome deste atributo em function como esta abaixo function($idcategory)
$app->get("/categories/:idcategory", function($idcategory){

// abaixo verifico se foi passado algum número de página na minha URL "$_GET". se foi definida verifico se foi realmente um número que foi passado (int). Se não foi passado nenhum número na minha URL então minha página será de uma página só ": 1;"
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
// utilizamos a classe Category para recuperar o id que foi passado na função com get.
	$category = new Category();
// verificamos se a variável idcategory é mesmo númerico, por que pode ter vindo alguma sujeira da URL, para isso chamamos a classe get que esta declarada no arquivo Category.php
	$category->get((int)$idcategory);
// o primeiro parametro passado para a função getProductsPage será o número de páginas definidos acima em $page. O segundo parametro não preciso passar, por que será o padrão mesmo, quantos itens eu quero por página.
	$pagination = $category->getProductsPage($page);

	$pages = [];
// montando um array "$pages" que vai acontecer enquanto for menor ou igual ao meu número de páginas, que esta em $pagintion['pages'] que vem lá do meu select de "getProductsPage" em Category.php.
	for ($i=1; $i <= $pagination['page']; $i++) {
		// adicionando "array_pusch" mais um item dentro dentro do array, que vai ser o link quando meu usuário clicar nesta págian.
		// então incluimos as duas informações que trazemos do arquivo category.html "<li><a href="{$value.link}">{$value.page}</a></li>" link e page
		array_push($pages, [
			// abaixo criamos o caminho que vai levar quando o usuário clicar nesta página "/categories/". Selecionando o id da minha categoria "$category" que o usuário escolheu, consigo pegar ele de "$category->getidcategory" que esta carregado na minha URL. Dai concateno a categoria "$cagory->getidcategory" com a página "page" $i do meu incremento do for
			// antes da interrogação temos o caminho, a URL e depois da interrogação temos as variáveis.
 			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
 			// incluimos 'page' abaixo para mostrar visualmente o número da página para o usuário, na tela principal do site.
			'page'=>$i
		]);

	}

	$page = new Page();
// quando chamamos o template, nesta hora passamos as variáveis category, products e pages
	$page->setTpl("category", [
		'category'=>$category->getValues(),
		// "data" são os nossos produtos que estão no array retornada de getProductsPage na chave "data". No primeiro nome de chave do array
		// a variável 'products' vai alimentar o correspondete '{$product.desproduct}' no arquivo product-detail.html
		'products'=>$pagination["data"],
		'pages'=>$page
	]);

});

// criando a rota para ver detalhes do produto...
$app->get("/products/:desurl", function($desurl){

	$product = new Product();

// chamamos o método getFromURL passando a descrição da URL "$desurl"...
	$product->getFromURL($desurl);

	$page = new Page();

// chamando o nosso template, a página de detalhs do produto.
	$page->setTpl("product-detail", [
		'product'=>$product->getValues(),
		// vamos criar o método getCategories na classe Product(), pois estamos referenciando a váriavel $product que é instância da classe Product, para informar ao usuários em quais as categorias que este produto esta relacionado.
		// categories é uma variável que esta sendo referenciada lá no template product-detail.html.
		'categories'=>$product->getCategories()
	]);
});

$app->get("/cart", function(){

	// pegamos o carrinho que esta na sessão aberta e ativa...
	$cart = Cart::getFromSession();

	$page = new Page();

	// todas as variáveis abaixo estam sendo setadas na template cart.html...
	$page->setTpl("cart", [
		'cart'=>$cart->getValues(),
		// getProducts é o método que criamos em Cart.php que lista os produtos do carrinho para apresentar ao usuário.
		'products'=>$cart->getProducts(),
		'error'=>Cart::getMsgError()
	]);

});

// criando as rotas pra adicionar produtos no carrinho de compras
// "/cart/:idproduct/add" estou passando para a template o código do produto que vem da URL atraves deste parametro que esta dentro de (function($idproduct))
$app->get("/cart/:idproduct/add", function($idproduct){

	$product = new Product();

	$product->get((int)$idproduct);

	// verifica se tem sessão aberta para o usuário logado.
	// pegamos o carrinho que esta na sessão aberta e ativa...
	$cart = Cart::getFromSession();

	// abaixo vamos pegar a quantidade "qtd" de itens de um determinado produto selecionado pelo usuário, que foi informado no arquivo de html ==> product-detail.html na linha "<input type="number" size="4" class="input-text qty text" title="Qty" value="1" name="qtd" 
	// o isset faz com que eu seleciono a variável qtd da minha página, se qtd é maior que um ou seja se foi informado uma quantidade diferente de 1 (um) caso contrário, representado pela interrogação "?" então faço a variável qtd ugual a 1(um) mesmo, que é o padrão." 
	$qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;

	for ($i = 0; $i < $qtd; $i++) {

		// passamos finalmente o código do produto para o método que criamos de acionar produtos no carrinho "addProduct"
		$cart->addProduct($product);

	}

	// depois de adicionar o produto no carrinho, localizamos o usuário na página do carrinho pra ele ver como ficou sua compra no carrinho.
	header("Location: /cart");
	exit;
});

// criando as rotas pra remover apenas um produto por isso o termo "minus" produtos no carrinho de compras
// "/cart/:idproduct/minus" estou passando para a template o código do produto que vem da URL atraves deste parametro que esta dentro de (function($idproduct))
$app->get("/cart/:idproduct/minus", function($idproduct){

	$product = new Product();

	$product->get((int)$idproduct);

	// verifica se tem sessão aberta para o usuário logado.
	// pegamos o carrinho que esta na sessão aberta e ativa...
	$cart = Cart::getFromSession();

	// passamos finalmente o código do produto para o método que criamos de acionar produtos no carrinho "addProduct"
	$cart->removeProduct($product);

	// depois de adicionar o produto no carrinho, localizamos o usuário na página do carrinho pra ele ver como ficou sua compra no carrinho.
	header("Location: /cart");
	exit;
});


// criando as rotas pra remover todos os produtos de um tipo de produto do carrinho de compras
// "/cart/:idproduct/remove" estou passando para a template o código do produto que vem da URL atraves deste parametro que esta dentro de (function($idproduct))
// lá no arquivo cart.html na incluimos esta rota definida aqui em baixo: 
// "<a title="Remove this item" class="remove" href="/car/{$value.idproduct}/remove">×</a>"
$app->get("/cart/:idproduct/remove", function($idproduct){

	$product = new Product();

	$product->get((int)$idproduct);

	// pegamos o carrinho que esta na sessão aberta e ativa...
	$cart = Cart::getFromSession();

	// passamos finalmente o código do produto para o método que criamos de acionar produtos no carrinho "addProduct" e o segundo parametro true diz para o meu método remover todos os produtos do mesmo tipo de produto selecionado.
	$cart->removeProduct($product, true);

	// depois de adicionar o produto no carrinho, localizamos o usuário na página do carrinho pra ele ver como ficou sua compra no carrinho.
	header("Location: /cart");
	exit;
});

// ATENÇÃO: QUando a chamada é feito via POST não aceita escrever o nome da rota "/cart/freight"" na URL
$app->post("/cart/freight", function(){
	// pegamos o carrinho que esta na sessão aberta e ativa...
	$cart = Cart::getFromSession();

		// chamando o método para cálcular o frete...
	// o nome do campo do cep esta no arquivo cart.html na linha: "<input type="text" placeholder="00000-000" value="" id="cep" class="input-text" name="zipcode"
	// $nome = $_GET["zipcode"];
	
	// echo "Olá $nome legal!!!";
	$cart->setFreight($_POST['zipcode']);

	header("Location: /cart");
	exit;

});

// a rota abaixo faz o fechamento da compra, iniciando obrigatóriamente com o login do usuário e posteriormente com as informações dos dados pessoa e de pagamento.
// $app->get("/checkout", function(){

// 	// vefifica se o usuário esta logado... Passando false como parâmetro por que não quero que a sessão seja aberta para um administrador, mas sim para um usuário comum...
// 	User::verifyLogin(false);

// 	// buscando os dados do carrinho...
// 	$cart = Cart::getFromSession();

// 	// buscando os dados do endereço do usuário...
// 	$address = new Address();

// 	$page = new Page();

// 	// buscando o template de fechamento de compra do usuário... Veja que estamos trazendo para cá os dados do carrinho em 'cart' e os dados do endereço em 'address'
// 	$page->setTpl("checkout", [
// 		'cart'=>$cart->getValues(),
// 		'address'=>$address->getValues()
// 	]);
// });

// // a rota abaixo permite ao usuário comum fazer o login...
// $app->get("/login", function(){

// 	$page = new Page();

// 	// buscando o template de login do usuário comum...
// 	// passamos a mensagem de erro para o nosso template, para a página do site no arquivo login.html do usuário comum
//  	// 	{if='$error != '' "}
// 	//      <div class="alert alert-danger">
// 	//         {$error}
// 	//      </div>
// 	//  {/if}
// 	$page->setTpl("login", [
// 		'error'=>User::getError()
// 	]);
// });

$app->post("/login", function() {

	// abaixo fazemos um desvio da mensagem de erro para dentro da página do usuário e não um erro bruto que apareceria como estava antes, parecendo que o sistema deu um erro geral, descontrolado.
	// portanto colocamos esta mensagem de erro dentro do arquivo login.html...
	try {

		// note que o métodor login em "public static function login($login, $password)" no arquivo User.php é estático, por isso não é necessário fazer o new para ele aqui em baixo."
		// abaixo estamos passando o login e a senha para o usuário fazer login no arquivo login.html...
		User::login($_POST['login'], $_POST['password']);

	} catch(Exception $e) {

		// pra pegar o erro da Exception é com a função getMessage()
		User::setError($e->getMessage());
	}
	
	// depois do usuário fazer o login, mando ele para a tela do checkout onde finaliza a compra...
	header("Location: /checkout");
	exit;

});

$app->get("/checkout", function(){
        
    User::debuga("Passo 01 - verifica se consta Login conectado ===>");	
    //verifica se o usu�rio est� logado
    User::verifyLogin(false);
    
    $address = new Address();
			       
    // atualiza também o CEP do carrinho
    $cart = Cart::getFromSession();
    
    // aproveita o cep do carrinho para alimentar o array $_GET['zipcode']
    if(!isset($_GET['zipcode'])){
        
        $_GET['zipcode'] = $cart->getdeszipcode();
    
    }

    // verifica se o CEP foi enviado
    if(isset($_GET['zipcode'])){
        // forçar carregar já com os campos certo através do método loadFromCEP. Por que o padrão de nomes que vamos receber é diferente do padrão do nosso banco de dados.
        $address->loadFromCEP($_GET['zipcode']);
        // passa o novo CEP para o carrinho
        $cart->setdeszipcode($_GET['zipcode']);
        // salva os dados do carrinho
        $cart->save();
        // se mudou o CEP então mudou o frete, então tem que recalcular o valor total das compras considerando o novo CEP.
        $cart->getCalculateTotal();
    }
    
    // verifica se os campos estão vazios, caso esteja, limpa os campos, neste caso podemos manter o get como definido.
    if(!$address->getdesaddress()) $address->setdesaddress('');
    if(!$address->getdesnumber()) $address->setdesnumber('');
    if(!$address->getdescomplement()) $address->setdescomplement('');
    if(!$address->getdesdistrict()) $address->setdesdistrict('');
    if(!$address->getdescity()) $address->setdescity('');
    if(!$address->getdesstate()) $address->setdesstate('');
    if(!$address->getdescountry()) $address->setdescountry('');
    if(!$address->getdeszipcode()) $address->setdeszipcode('');

    $page = new Page();
    
    // atualiza os dados do carrinho na nossa página da web
    $page->setTpl("checkout", [
        'cart'=>$cart->getValues(),
        'address'=>$address->getValues(),
        'products'=>$cart->getProducts(),
        'error'=>Address::getMsgError()
    ]);
});

$app->post("/checkout", function(){
    
    User::verifyLogin(false);

    if(!isset($_POST['zipcode']) || $_POST['zipcode'] === ''){
        Address::setMsgError("Informe o CEP.");
        header("Location: /checkout");
        exit();
    }

    if(!isset($_POST['desaddress']) || $_POST['desaddress'] === ''){
        Address::setMsgError("Informe o endereço.");
        header("Location: /checkout");
        exit();
    }

    if(!isset($_POST['desdistrict']) || $_POST['desdistrict'] === ''){
        Address::setMsgError("Informe o bairro.");
        header("Location: /checkout");
        exit();
    }

    if(!isset($_POST['descity']) || $_POST['descity'] === ''){
        Address::setMsgError("Informe a cidade.");
        header("Location: /checkout");
        exit();
    }

    if(!isset($_POST['desstate']) || $_POST['desstate'] === ''){
        Address::setMsgError("Informe o estado.");
        header("Location: /checkout");
        exit();
    }

    if(!isset($_POST['descountry']) || $_POST['descountry'] === ''){
        Address::setMsgError("Informe o País.");
        header("Location: /checkout");
        exit();
    }

    $user = User::getFromSession();

    $address = new Address();

    $_POST['deszipcode'] = $_POST['zipcode']; //zipcode � o nome do campo na arquivo checkout.html
    $_POST['idperson'] = $user->getidperson();

    $address->setData($_POST);

    $address->save();

    $cart = Cart::getFromSession();

    $cart->getCalculateTotal(); // calcula o valor total do carrinho
    
    $order = new Order(); // criamos uma classe chamada Order

    $order->setData([
        'idcart'=>$cart->getidcart(),
        'idaddress'=>$address->getidaddress(),
        'iduser'=>$user->getiduser(),
        'idstatus'=>OrderStatus::EM_ABERTO,
        'vltotal'=>$cart->getvltotal()
    ]);

    $order->save();
    // neste momento pegamos o código do ID Order do pedido que esta na tela.
    header("Location: /order/".$order->getidorder());
    exit();

 //    switch ((int)$_POST['payment-method']) {

	// 	case 1:
	// 	header("Location: /order/".$order->getidorder()."/pagseguro");
	// 	break;

	// 	case 2:
	// 	header("Location: /order/".$order->getidorder()."/paypal");
	// 	break;

	// }

	// exit;
   
});

$app->get("/login", function(){
    
    $page = new Page();
    
    $page->setTpl("login", [
        // manda pra tela o erro de login quando o usuário informa os dados de login
        'error'=>User::getError(),
        // manda pra tela o erro de cadastro quando o usuário informa os dados pessoais, este erro foi passado para o aquivo login.html: 
        		// {if="$errorRegister != ''"}
	                // <div class="alert alert-danger">
	                //     {$errorRegister}
	                // </div>
                // {/if}
        'errorRegister'=>User::getErrorRegister(),
        // também passo meu array se ele existir se não passo um array com todos os nomes vazio. Estes campos serão recolocados no tamplate novamente, lá no login.html
        'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=>'', 'email'=>'', 'phone'=>'']
    ]);
});

$app->post("/login", function(){
    
    try{
    
        User::login($_POST['login'], $_POST['password']);
    
    } catch (Exception $e){
      
        User::setError(utf8_encode($e->getMessage()));
    }
    
    header("Location: /checkout");
    exit();
});

$app->get("/logout", function(){
    
    User::logout();
    
    header("Location: /login");
    exit();
});

// criando uma rota para edição dos dados do usuário. Esta na template login.html (<form id="register-form-wrap" action="/register" class="register" method="post"> )
$app->post("/register", function(){

	// para não perder os dados já digitados pelo usuário, quando formos válidar, temos que guardar os dados que já from digitados. Por issso vamos criar aqui uma sessão para fazer isso. Guardamos todos os dados num array.
	$_SESSION['registerValues'] = $_POST;
	// antes de gravar no BD, vamos fazer algumas críticas dos dados informados, apesar do front também ser responsável por fazer criticas dos dados no momento do cadastro. || (OU) && (E)
	if (!isset($_POST['name']) || $_POST['name'] == '') {

		User::setErrorRegister("Preencha o seu nome.");

		header("Location: /login");
		exit;
	}

	if (!isset($_POST['email']) || $_POST['email'] == '') {

		User::setErrorRegister("Preencha o seu e-mail.");

		header("Location: /login");
		exit;
	}

	if (!isset($_POST['password']) || $_POST['password'] == '') {

		User::setErrorRegister("Preencha a senha.");

		header("Location: /login");
		exit;
	}

	// temos que verificar também se o usuário que esta se cadastrando já não consta um nome igual na minha base dae dados.
	if (User::checkLoginExist($_POST['email']) === true) {

		User::setErrorRegister("Este endereço de e-mail já está sendo usado por outro usuário.");
		header("Location: /login");
		exit;	
	
	}

	$user = new User();

	// passando o método setData
	// setData() ==> Adiciona o objeto especificado ao DataObject usando o tipo de objeto como o formato de dados.
	// quando passamos outros dados como array para o setData é por que os dados são diferentes que os campos da query do meu select, por isso passamos os dados a seguir... Ou por exemplo quando queremos forçar que um campo receba um dado diferente para montar a query como no caso do 'inadmin'=>0 por que o usuário neste caso aqui é um usuário comum.
	// os campos a seguir serão gravados pela procedure no bd, fazendo as correspondências corretas dos respectivos campos a serem gravados na seguinte ordem: INSERT INTO tb_persons (desperson, desemail, nrphone) VALUES(pdesperson, pdesemail, pnrphone);
	$user->setData([
		'inadmin'=>0,
		'deslogin'=>$_POST['email'],
		'desperson'=>$_POST['name'],
		'desemail'=>$_POST['email'],
		'despassword'=>$_POST['password'],
		'nrphone'=>$_POST['phone']
	]);

	$user->save();

	// já que o usuário informou e gravamos os dados dele no banco de dados podemos validar o login dele já...
	User::login($_POST['email'], $_POST['password']);

	header('Location: /checkout');
	exit;
});

// como esta rota é para recuperação da senha do usuário comum e não do administrador. basta colocar /forgot
$app->get("/forgot", function() {

	$page = new Page();

	$page->setTpl("forgot");	

});

$app->post("/forgot", function(){

	$user = User::getForgot($_POST["email"], false);

	header("Location: /forgot/sent");
	exit;

});

$app->get("/forgot/sent", function(){

	$page = new Page();

	$page->setTpl("forgot-sent");	

});


$app->get("/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new Page();

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);	

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = User::getPasswordHash($_POST["password"]);

	$user->setPassword($password);

	$page = new Page();

	$page->setTpl("forgot-reset-success");

});

// rota para o minha area de perfil do usuário para jogar os dados do usuário na página do site
$app->get("/profile", function(){

	// false para informar que não é do administrativo
	User::debuga("Passo 00 - Antes de chamar verifyLogin().'<br>'");

	User::verifyLogin(false);

	// recuperando o usuário que esta dentro da sessão
	$user = User::getFromSession();

	$page = new Page();

	// passando dados para o template profile.htm.
	$page->setTpl("profile", [
		'user'=>$user->getValues(),
		// se foi identificado erro em "$app->post("/profile", function()" então passamos para o erro que esta na constanto 'profileMsg' para User::getSuceess()
		'profileMsg'=>User::getSuccess(),
		'profileError'=>User::getError()
	]);
	// User::debuga( "<br>"."Passo 07 - Passando ERROS para a template (html) para o usuário ver ").var_dump($page)."<br>";

});

// rota para pegar os dados do usuário na página do site.
$app->post("/profile", function(){

	// false porque o acesso não é administrativo
	User::verifyLogin(false);

	// verifica se os campos estão vazios
	if (!isset($_POST['desperson']) || $_POST['desperson'] === '') {
		User::setError("Preencha o seu nome.");
		header('Location: /profile');
		exit;
	}

	if (!isset($_POST['desemail']) || $_POST['desemail'] === '') {
		User::setError("Preencha o seu e-mail.");
		header('Location: /profile');
		exit;
	}

	// verifica se o usuário esta ativo
	$user = User::getFromSession();

	// verifica se o usuário trocou o email que estava antes. 
	if ($_POST['desemail'] !== $user->getdesemail()) {

		// Se ele mudou o email tenho que verificar se o novo email informado já não existe.
		if (User::checkLoginExists($_POST['desemail']) === true) {

			User::setError("Este endereço de e-mail já está cadastrado.");
			header('Location: /profile');
			exit;

		}

	}

	// coloco as variáveis do meu profile.html dentro do array $_POST e mais abaixo mando salvar em save()
	$_POST['inadmin'] = $user->getinadmin();
	$_POST['despassword'] = $user->getdespassword();
	$_POST['deslogin'] = $_POST['desemail'];

	$user->setData($_POST);
	
	$user->update();

	// se chegar até este ponto, então os dados foram gravados com sucesso
	User::setSuccess("Dados alterados com sucesso!");

	header('Location: /profile');
	exit;

});

$app->get("/order/:idorder", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	// var_dump($order->getValues());
	// exit;

	$page = new Page();

	$page->setTpl("payment", [
		'order'=>$order->getValues() // passando os dados que estão dentro de getValues para minha página da web
	]);

});

$app->get("/boleto/:idorder", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	// DADOS DO BOLETO PARA O SEU CLIENTE
	$dias_de_prazo_para_pagamento = 10;
	$taxa_boleto = 5.00;
	$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 

	$valor_cobrado = formatPrice($order->getvltotal()); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
	$valor_cobrado = str_replace(".", "", $valor_cobrado); //retira o ponto
	$valor_cobrado = str_replace(",", ".",$valor_cobrado); //retira a virgula
	$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

	$dadosboleto["nosso_numero"] = $order->getidorder();  // Nosso numero - REGRA: Máximo de 8 caracteres!
	$dadosboleto["numero_documento"] = $order->getidorder();	// Num do pedido ou nosso numero
	$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
	$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
	$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

	// DADOS DO SEU CLIENTE
	$dadosboleto["sacado"] = $order->getdesperson();
	$dadosboleto["endereco1"] = $order->getdesaddress() . " " . $order->getdesdistrict();
	$dadosboleto["endereco2"] = $order->getdescity() . " - " . $order->getdesstate() . " - " . $order->getdescountry() . " -  CEP: " . $order->getdeszipcode();

	// INFORMACOES PARA O CLIENTE
	$dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Hcode E-commerce";
	$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
	$dadosboleto["demonstrativo3"] = "";
	$dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
	$dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
	$dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@hcode.com.br";
	$dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto Loja Hcode E-commerce - www.hcode.com.br";

	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
	$dadosboleto["quantidade"] = "";
	$dadosboleto["valor_unitario"] = "";
	$dadosboleto["aceite"] = "";		
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";


	// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


	// DADOS DA SUA CONTA - ITAÚ
	$dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "48781";	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

	// DADOS PERSONALIZADOS - ITAÚ
	$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

	// SEUS DADOS
	$dadosboleto["identificacao"] = "Hcode Treinamentos";
	$dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
	$dadosboleto["endereco"] = "Rua Ademar Saraiva Leão, 234 - Alvarenga, 09853-120";
	$dadosboleto["cidade_uf"] = "São Bernardo do Campo - SP";
	$dadosboleto["cedente"] = "HCODE TREINAMENTOS LTDA - ME";

	// NÃO ALTERAR!
	$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "boletophp" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR;

	require_once($path . "funcoes_itau.php");
	require_once($path . "layout_itau.php");

});

$app->get("/profile/orders", function(){

	User::verifyLogin(false);

	$user = User::getFromSession();

	$page = new Page();

	$page->setTpl("profile-orders", [
		'orders'=>$user->getOrders()
	]);

});

$app->get("/profile/orders/:idorder", function($idorder){ //recebe a variável $idorder 

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	// var_dump($order->getValues());
	// exit;

	$cart = new Cart();

	$cart->get((int)$order->getidcart());

	$cart->getCalculateTotal();

	$page = new Page();

	User::setError("");

	$page->setTpl("profile-orders-detail", [
		'order'=>$order->getValues(),
		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts()
	]);	

});

$app->get("/profile/change-password", function(){ // na rota get apresentamos os campos para edição de dados do usuário.

	User::verifyLogin(false); // usuário tem que esta autenticado para acessar esta área

	$page = new Page();

	$page->setTpl("profile-change-password", [
		'changePassError'=>User::getError(),
		'changePassSuccess'=>User::getSuccess()
	]);

});

$app->post("/profile/change-password", function(){ // na rota post o usuário envia seus dados para serem alterados.

	User::verifyLogin(false);

	if (!isset($_POST['current_pass']) || $_POST['current_pass'] === '') // o primeiro teste verifica se o current_pass foi definido, ou seja, se é diferente de não definido e o segundo verifica se ele não esta vazio. Se qualquer uma dessas situações ocorrer, retorna ERRO.
	{

		User::setError("Digite a senha atual.");
		header("Location: /profile/change-password");
		exit;

	}

	if (!isset($_POST['new_pass']) || $_POST['new_pass'] === '') {

		User::setError("Digite a nova senha.");
		header("Location: /profile/change-password");
		exit;

	}

	if (!isset($_POST['new_pass_confirm']) || $_POST['new_pass_confirm'] === '') {

		User::setError("Confirme a nova senha.");
		header("Location: /profile/change-password");
		exit;

	}

	if ($_POST['current_pass'] === $_POST['new_pass']) {

		User::setError("A sua nova senha deve ser diferente da atual.");
		header("Location: /profile/change-password");
		exit;		

	}

	$user = User::getFromSession();

	if (!password_verify($_POST['current_pass'], $user->getdespassword())) {

		User::setError("A senha está inválida.");
		header("Location: /profile/change-password");
		exit;			

	}

	$user->setdespassword($_POST['new_pass']);

	$user->update();

	User::setSuccess("Senha alterada com sucesso.");

	header("Location: /profile/change-password");
	exit;


});

// git push origin master for 'https://git@github.com/joaosesconetto/ecommerce
?>