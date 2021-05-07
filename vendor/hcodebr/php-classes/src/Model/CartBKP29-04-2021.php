<?php

// abaixo definimos onde a classe esta, ou seja, o namespace dela... Então esta indicado aqui que esta nossa classe esta dentro a partir do nosso código User.php "Hcode" e dentro da pasta Model
namespace Hcode\Model;

// o contra barra inicial diz que é para iniciar da raiz.
use \Hcode\DB\Sql;
// Abaixo estou dizendo que a minha classe Model esta na pasta principal, ou seja, onde esta a pasta Model mesmo.
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\User;

			  

class Cart extends Model {	

	// esta constante vai guardar a sessão aberta no momento que a compra estiver sendo executada. Isto é necessário para controlar-mos de quem são os dados colocados no carrinho, ou a serem retirados, para quando ocorrer estas alterações, podermos alterar o banco de dados.
	const SESSION = "Cart";
	const SESSION_ERROR = "CartError";

	// a função a seguir controle para saber se precisa inserir um carrinho novo, se já tem um carrinho ligado a sessão, se a sessão foi perdida, se acabou o tempo, mas mesmo se isso acontecer nós temos o id da sessão, etc.
	public static function getFromSession(){

		$cart = new Cart();
		// primeira verificação: Esta carrinho já esta na sessão...
		// verifica se a sessão já foi definida ==> (isset($_SESSION[cART::SESSION]) 
		// verifica se o ID do carrinho não esta vazia, ou seja se o idcart é maior que zero
		// se as duas condições abaixo foram verdadeiras, significar que o carrinho já foi inserido no banco de dados e significa que ele esta na SESSÃO.
		if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {

			// então podemos carregar o carrinho... Vamos criar este método abaixo.
			$cart->get((int)$_SESSION[Cart::SESSION]['idcart']);
			// var_dump($cart);

		} else {
			// chamando um método para pegar o carrinho que esta na sessão.
			$cart->getFromSessionID();

			if (!(int)$cart->getidcart() > 0) {

				$data = [
					'dessessionid'=>session_id()
				];

				if (User::checkLogin(false)) {

					// se existir um carrinho ativo como esta sendo verificado acima. Vamos ver quem é o usuário que esta logado.
					$user = User::getFromSession();

					$data['iduser'] = $user->getiduser();

				}
				
				$cart->setData($data);

				$cart->save();

				$cart->setToSession(); 

			}

		}

		return $cart;

	}

	// não é um método estático, por que estamos fazendo uso da variável $this...
	public function setToSession()
	{

		$_SESSION[Cart::SESSION] = $this->getValues();
	
	}

	// busco os dados do carrinho ativo, através da sessão aberta que consigo pegar através da função do php session_id()
	public function getFromSessionID()
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
			':dessessionid'=>session_id()
		]);

		if (count($results) > 0) {
		
			$this->setData($results[0]);
		
		}
	
	}
	
	public function get(int $idcart)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
			':idcart'=>$idcart
		]);

		if (count($results) > 0) {

			$this->setData($results[0]);

		}
		

	}

	// salvando os dados do carrinho...
	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", [
			':idcart'=>$this->getidcart(),
			':dessessionid'=>$this->getdessessionid(),
			':iduser'=>$this->getiduser(),
			':deszipcode'=>$this->getdeszipcode(),
			':vlfreight'=>$this->getvlfreight(),
			':nrdays'=>$this->getnrdays()
		]);

		$this->setData($results[0]);

	}

	// criando um método para adionar produtos ao carrinho de compras
	// nosso método recebe uma instância da class Product (Product $product) e uma variável $product que já é uma instância da class Product
	// Depois que fizemos estestes dois métodos abaixo, vamos fazer as rotas para afetar as templates de html.
	
	public function addProduct(Product $product)
	{

		// instanciamos a classe Sql para nos conectar ao banco de dados.
		$sql = new Sql();

		// fazendo o insert e o update na tabela tb_cartsproducts que contém os produtos incluidos ou excluidos do nosso carrinho. Lembrando que os produtos excluidos não serão excluidos fisicamente, apenas informada a data de exclusão, assim saberemos que este produto com data informada de exclusão não esta mais no carrinho.
		$sql->query("INSERT INTO tb_cartsproducts (idcart, idproduct) VALUES(:idcart, :idproduct)", [
			// idcart vem da propria classe
			':idcart'=>$this->getidcart(),
			// idproduct vem da variável passada para este método
			':idproduct'=>$product->getidproduct()
		]);

		// sempre que for acrescentado mais produtos o frete deve ser alterado tambem...
		$this->getCalculateTotal();

	}

	// agora sim, criamos o método para "excluir" o produto do carrinho, que será como expliquei acima.
	// nosso método recebe uma instância da class Product (Product $product) e uma variável $product que já é uma instância da class Product
	// o segundo parametro $all = false, siginifica que esperamos a informação de limpar somente um produto ou todos igual a este do carrinho. A principio $all será false que significa que esta sendo retirado um produto de cada vez e não todos de uma vez só. O usuário remove um produto da mesma marca e modelo por exemplo quando clica na lista de unidades que permite remover um de cada vez ou o usuário remove todos os produtos quando clicar no x (xis) que tem ao lado do produto.
	public function removeProduct(Product $product, $all = false)
	{

		$sql = new Sql();

		if ($all) {
			// dtremoved IS NULL é para remover somente aqueles que estão no banco de dados com dtremoved nula, ou seja, ainda não foram setados com data de exclusão. 
			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND
					dtremoved IS NULL", [
				// idcart vem da propria classe
				':idcart'=>$this->getidcart(),
				// idproduct vem da variável passada para este método
				':idproduct'=>$product->getidproduct()
			]);
		} else 	{	
			// dtremoved IS NULL é para remover somente aqueles que estão no banco de dados com dtremoved nula, ou seja, ainda não foram setados com data de exclusão. E LIMIT 1 é para excluir somente um produto do tipo, por que pode ter mais de um produto do mesmo no banco de dados.
			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND
					dtremoved IS NULL LIMIT 1", [
				':idcart'=>$this->getidcart(),
				':idproduct'=>$product->getidproduct()
			]);

		}

		// sempre que retirar produtos do carrinho o frete deve ser alterado também...
		$this->getCalculateTotal();
	}	
	
	// mostrando todos os produtos que já foram adicionados ao carrinho, que estão dentro do carrinho...
	public function getProducts()
	{

		$sql = new Sql();

		// COUNT(*) retorna a quantidade total para cada agrupamento de produtos e SUM, soma o valor total dos produtos agrupados 
		// o método informado abaixo "checkList" que é uma extensão da classe Product inclui no sql as imagens dos produtos do carrinho.
		// return Product::checkList($sql->select("
		// 	SELECT b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllenght, b.weight, COUNT(*) AS nrqtd, SUM(b.vlprice) AS vltotal 
		// 	FROM tb_cartsproducts a 
		// 	INNER JOIN tb_products b ON a.idproduct = b.idproduct 
		// 	WHERE a.idcart = :idcart AND a.dtremoved IS NULL 
		// 	GROUP BY b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllenght, b.weight
		// 	ORDER BY b.desproduct
		// ", [

		// 	':idcart'=>$this->getidcart()
		// ]));

		// O MESMO SELECT ACIMA FICA MAIS ORGANIZADO OU MELHOR DE SE VER COMO COLOCAMOS ABAIXO...

		// var_dump("SELECT b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl, COUNT(*) AS nrqtd, SUM(b.vlprice) AS vltotal 
		// 	FROM tb_cartsproducts a 
		// 	INNER JOIN tb_products b ON a.idproduct = b.idproduct 
		// 	WHERE a.idcart = :idcart AND a.dtremoved IS NULL 
		// 	GROUP BY b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl
		// 	ORDER BY b.desproduct");
		// exit;

		$rows = $sql->select("
			SELECT b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl, COUNT(*) AS nrqtd, SUM(b.vlprice) AS vltotal 
			FROM tb_cartsproducts a 
			INNER JOIN tb_products b ON a.idproduct = b.idproduct 
			WHERE a.idcart = :idcart AND a.dtremoved IS NULL 
			GROUP BY b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl
			ORDER BY b.desproduct
		", [

			':idcart'=>$this->getidcart()
		]);

		return Product::checkList($rows);
	}

	// esta função busca todos os itens colocados no carrinho e que não foram retirados do carrinho, ou seja, que esteja com a dtremoved em branco. Já faz a soma do total dos preços vlprice da largura(vlwidth da altura(vlheight) do comprimento(vllength) e peso(vlwight)
	public function getProductsTotals()
	{

		$sql = new Sql();
		// quando faço referencia a $ths->getidcart é por que estou enxergando o conteúdo da variável idcart, por que é como se eu estivesse dentro da função Sql(), por que já instânciei ela aqui nesta função quando fiz $sql = new Sql() acima.
		// o valor dos correios será calculado considerando estes valoes somados abaixo, exceto é claro o valor do produto.
		$results = $sql->select("
			SELECT SUM(vlprice) AS vlprice, SUM(vlwidth) AS vlwidth, SUM(vlheight) AS vlheight, SUM(vllength) AS vllength, SUM(vlweight) AS vlweight, COUNT(*) AS nrqtd
			FROM tb_products a
			INNER JOIN tb_cartsproducts b ON a.idproduct = b.idproduct
			WHERE b.idcart = :idcart AND dtremoved IS NULL;
			", [

				':idcart'=>$this->getidcart()
		]);

		if (count($results) > 0) {
			return $results[0];
		} else {
			return [];
		}

	}

	public function setFreight($nrzipcode)
	{

		$nrzipcode = str_replace("-", '', $nrzipcode);

		// echo "Número do CEP ===> " . $nrzipcode;
		// exit;

		$totals = $this->getProductsTotals();

		if ($totals['nrqtd'] > 0) {

			// os correios não permitem objetos com altura menor que 2 cm por issso a regra a seguir...
			if ($totals['vlheight'] < 2) $totals['vlheight'] = 2;

			// o mesmo é exigido para o comprimento, não pode ser menor que 16 cm...
			if ($totals['vllength'] < 16) $totals['vllength'] = 16;				

			// PARA PASSAR O TERCEIRO parametro para a API dos correios, vamos utilizar uma função do php chamada http_build_query. Ela espera um array. Não precisamos passar todos os valores como visto abaixo...
			$qs = http_build_query([
				'nCdEmpresa'=>'',
				'sDsSenha'=>'',
				'nCdServico'=>'40010',
				'sCepOrigem'=>'71936250',
				'sCepDestino'=>$nrzipcode,
				'nVlPeso'=>$totals['vlweight'],
				'nCdFormato'=>'1',
				'nVlComprimento'=>$totals['vllength'],
				'nVlAltura'=>$totals['vlheight'],
				'nVlLargura'=>$totals['vlwidth'],
				'nVlDiametro'=>'0',
				'sCdMaoPropria'=>'S',
				'nVlValorDeclarado'=>$totals['vlprice'],
				'sCdAvisoRecebimento'=>'S'
			]);

			
			// para passar estas informações para o webserver, vamos usar uma função que vai ler xml, por que o webserver vai retornr a informação do tipo xml.
			// O SEGUNDO PARAMETRO depois do endereço da URL, vamos passar o tipo de calculo que queremos fazer:
			// CalcPrecoPrazo: retorna o preço e o prazo de entrega de uma encomenda.
			// Calcpreco: retorna o preço de envio de uma encomenda.
			// Calcprazo: retorna o prazo de entrega de uma encomenda.

			// vamos pazer a primeira regra: CalcPrecoPrazo: retorna o preço e o prazo de entrega de uma encomenda.
						
			$xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);
			// var_dump($xml);
			// exit;      

			// as variáveis a seguir estão sendo extraidas do array do retorno de simplexml_load_file...
			$result = $xml->Servicos->cServico;

			// mostrando alguma mensagem de erro que retornou dos correios...
			if ($result->MsgErro != '') {

				Cart::setMsgError($result->MsgError);

			} else {

				Cart::clearMsgError();
			}

			$this->setnrdays($result->PrazoEntrega);
			$this->setvlfreight(Cart::formatValueToDecimal($result->Valor));
			$this->setdeszipcode($nrzipcode);  

			// o cep é mostrado lá no cart.html no campo value indicado a seguir...
			// <input type="text" placeholder="00000-000" value="{$cart.deszipcode}" id="cep" class="

			// o valor do frete e o número de dias para entraga e mostrado em cart.html na linha a seguir...
			// tem um if para mostrar o nº de dias apenas se nrdays for > 0;
			// <td>{$cart.vlfreight}{if="$cart.nrdays > 0"} <small>prazo de {$cart.nrdays} dia(s)</small>{/if}</td>
			$this->save();

			return $result;             


		} else {


		}
		
	}

	public static function formatValueToDecimal($value):float
	{

		// caso tenha algum ponto na string do VALOR informado é retirado pela função do php abaixo
		$value = str_replace(".", '', $value);

		// e abaixo, caso tenha alguma virgula  na string do VALOR informado é substituido por ponto		
		return str_replace(',', '.', $value);
	}

	// criando um método para retornar a mensgem de erro dos correios a principio...
	public static function setMsgError($msg)
	{

		$_SESSION[Cart::SESSION_ERROR] = $msg;


	}

	// este método apenas retorna a mensagem de erro localizada em setMsgError acima...
	public static function getMsgError()
	{

		// se existir uma mensagem de erro retorna a mensagem através da constante SESSION_ERROR, se não retorna branco.
		$msg = (isset($_SESSION[Cart::SESSION_ERROR])) ? $_SESSION[Cart::SESSION_ERROR] : "";

		// limpa a mensagem de erro...
		Cart::clearMsgError();

		return $msg;
	}

	// abaixo um método para limpar a mensagem de erro localizada na sessão SESSION_ERROR...
	public static function clearMsgError()
	{

		$_SESSION[Cart::SESSION_ERROR] = NULL;

	}

	// sempre que retirar produtos do carrinho o frete deve ser alterado também...
	public function updateFreight() 
	{

		// o frete só será calculado se já tiver sido informado...
		if ($this->getdeszipcode() != '' ) {

			// chama o método que calcula o frete
			$this->setFreight($this->getdeszipcode());

		}
	}
	
	// inclui no getValues os valores de subtotal e o total geral do carrinho de caompra...
	public function getValues()
	{

		$this->getCalculateTotal();

		return parent::getValues();

	}

	// calcula os valores de subtotal e o total geral com o valor do frete do carrinho de caompra...
	public function getCalculateTotal()
	{

		$this->updateFreight();

		// observe que quando fazemos um get, estamos pegando algum dado por isso o get vem precedido de parenteses
		// agora quando fazemos um set, estamos passando uma informação, então sempre existe valor dentro dos parenteses.
		$totals = $this->getProductsTotals();
		// veja que para pegar o valor total do select, temos que jogar o array em uma variável "$totals" e depois seta-la no array do metodo set com "$this->setvlsubtotal($totals['vlprice'])". Neste caso estamos criando mais uma variável dentro do array seteres chamada vlsubtotal...
		$this->setvlsubtotal($totals['vlprice']);
		$this->setvltotal(($totals['vlprice'] + $this->getvlfreight()));

	}
}

?>