<?php

// abaixo definimos onde a classe esta, ou seja, o namespace dela... Então esta indicado aqui que esta nossa classe esta dentro a partir do nosso código User.php "Hcode" e dentro da pasta Model
namespace Hcode\Model;

// o contra barra inicial diz que é para iniciar da raiz.
use \Hcode\DB\Sql;
use Hcode\Mailer;
// Abaixo estou dizendo que a minha classe Model esta na pasta principal, ou seja, onde esta a pasta Model mesmo.
use \Hcode\Model;
			  
// Esta classe User é um Model, e todo o Model vai ter get's e set's
class Category extends Model {	

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
	}

	public function save()
	{
		$sql = new Sql();
		
		 $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
		 		":idcategory"=>$this->getidcategory(),
		 		":descategory"=>$this->getdescategory()		 		
		 	));
			
		 	$this->setData($results[0]);

		 	// Neste momento chamamos o método updateFile para atualizar o nosso menu de categorias
		 	Category::updateFile();
			
	}
	// Método get criado para verificar se o registro existe no banco de dados. Foi criado inicialmente para verificar a exclusão de um 	registro de categoria
	public function get($idcategory)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory",[
			':idcategory'=>$idcategory
		]);

		$this->setData($results[0]);

	}
	// delete não espera parametro nenhum, por que já espera-se que o objeto já esteja carregado...
	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory",[
			':idcategory'=>$this->getidcategory()
		]);

		// Neste momento chamamos o método updateFile para atualizar o nosso menu de categorias
		Category::updateFile();
	}

	public static function updateFile()
	{
		// abaixo trazemos do banco de dados todas as categorias "listaALL" 
		$categories = Category::listAll();

		$html = [];
		// abaixo fazemos um foreach para trazer do banco de dados todas as categorias e vamos conctenando nas linha de html do meu arquivo categories-menu.html
		// o array_push coloca a linha descrita lá no meu arquivo categories-menu.html.
		// category é o nome da rota.
		foreach ($categories as $row) {
			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}
		// abaixo vamos salvar esta nossa linha no arquivo categories-menu.html. Para isso preciso do caminho do arquivo físico. Para pegar o diretório onde esta rodando o site. utilizaremos a variável de ambiente $_SERVER[]
		// implode é utilizado para transformar nosso array em uma string, então fazemos o implote por nada '' da minha variável $html que é um array.
		// o explode ao contrário transforma string em array.
		// Meu site esta rodando na pasta: C:\ecommerce\views então só preciso informar que o arquivo de categories-menu.html esta nesta mesma pasta views
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));

	}

	// método para trazer todas as categorias...
	public function getProducts($related = true)
	{

		$sql = new Sql();

		if ($related === true) {

			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct IN(
					SELECT a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory
				);
			", [
				':idcategory'=>$this->getidcategory()
			]);
		} else {
			
			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct NOT IN(
					SELECT a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory
				);
				", [
					':idcategory'=>$this->getidcategory()
				]);
		}
	}
	
	// criando páginação para a nossa tela principal
	public function getProductsPage($page = 1, $itemsPerPage = 4)
	{
		// a regra de calculo abaixo é feita da seguinte forma:
		// $page = 1 então 1 - 1 = zero, então comece a paginação da página zero e me graga 3 registro "$itemsPerPage"
		// depois: $page = 2, dois menos 1 = 1; 1 vezes 3 é igual a 3, então comece paginação a página 1 e me traga 3 registros. Mas é possível passar uma escolha do usuário de quantos itens por página ele quer ver, dai teriamos que passar tambem via _GET como fizemos em número de páginas.
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON c.idcategory = b.idcategory
			WHERE c.idcategory = :idcategory
			LIMIT $start, $itemsPerPage;
		", [
			':idcategory'=>$this->getidcategory()
		]);

		// a variável FOUND_ROWS pesquisada acima guardou o número de registros total na condição informada naquele sql. 
		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");
			
		return [
			// retorno minha lista de produtos, apartir do método estático "Product" já incluido a foto com a verificação na classe "checkList"
			'data'=>Product::checkList($results),
			// retorno o número total de registros selecionados, a primeira linha do array $resultTotal[0] e a coluna deste array que é "nrtotal". (int) é só para garantir que será número mesmo.
			'total'=>(int)$resultTotal[0]["nrtotal"],
			// retono o número de páginas a serem apresentadas, ou seja, o número de páginas geradas. ceil() é uma função do php que retorna um inteiro na divisão, ou seja, caso de fração na divisão tipo 11/10 da 1,1 então ceil() arredonda para 2 páginas e não 1,1, arredonda para cima e não para o inteiro mais próximo que deveria ser 1.
			'page'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}


	// o método a seguir inclui produtos em determinada categoria. Product dentro do parenteses indica que é uma classe.
	public function addProduct(Product $product)
	{

		$sql = new Sql();

		$sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)",[
			':idcategory'=>$this->getidcategory(),
			':idproduct'=>$product->getidproduct()
			]);
	}

	// o método a seguir exclui produtos de determinada categoria
	public function removeProduct(Product $product)
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND  idproduct = :idproduct", [
			':idcategory'=>$this->getidcategory(),
			':idproduct'=>$product->getidproduct()
			]);
	}

	//paginação. Recebe o número da página atual. Se não receber por padrão será o número 1, e quantos ítens por pagina, igual a 10
    public static function getPage($page = 1, $itemsPerPage = 5){
        $start = ($page - 1) * $itemsPerPage; //primeira pagina come�a no zero

        $sql = new Sql();

        // SQL_CALC_FOUND_ROWS, calcula a quantidade de itens a serem retornados definido em itemsPerPage
        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_categories 
            ORDER BY descategory
            LIMIT $start, $itemsPerPage;
        ");

       // coloca na variável nrtotal a quantidade de itens retornado no sql acima
       $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data'=>$results,
            'total'=>(int)$resultTotal[0]["nrtotal"],
            'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage) //conta quantas paginas tem resultados
        ];
    }

public static function getPageSearch($search, $page = 1, $itemsPerPage = 1){
        $start = ($page - 1) * $itemsPerPage; //primeira pagina come�a no zero

        $sql = new Sql();

        // SQL_CALC_FOUND_ROWS, calcula a quantidade de itens a serem retornados definido em itemsPerPage
        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_categories 
            WHERE descategory LIKE :search
            ORDER BY descategory
            LIMIT $start, $itemsPerPage;
        ", [
        	':search'=>'%'.$search.'%'
        ]);

       // coloca na variável nrtotal a quantidade de itens retornado no sql acima
       $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data'=>$results,
            'total'=>(int)$resultTotal[0]["nrtotal"],
            'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage) //conta quantas paginas tem resultados
        ];
    }
}

?>