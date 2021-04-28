<?php

// abaixo definimos onde a classe esta, ou seja, o namespace dela... Então esta indicado aqui que esta nossa classe esta dentro a partir do nosso código User.php "Hcode" e dentro da pasta Model
namespace Hcode\Model;

// o contra barra inicial diz que é para iniciar da raiz.
use \Hcode\DB\Sql;
use Hcode\Mailer;
// Abaixo estou dizendo que a minha classe Model esta na pasta principal, ou seja, onde esta a pasta Model mesmo.
use \Hcode\Model;
			  
// Esta classe User é um Model, e todo o Model vai ter get's e set's
// Nunca esquecer que o nome do arquivo php tem que ser igual ao nome da classe Product = Product.php
class Product extends Model {	

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
	}

	public static function checkList($list)
	{
		// varrendo a minha lista. Cada item da lista vou chamar de $row. & comercial manipula a mesma variável.
		// var_dump($list);
		foreach ($list as &$row) {

			$p = new Product();
			// var_dump($row);
			$p->setData($row);
			// abaixo inclue no array o endereço da foto que não consta no listAll()
			// var_dump($p);
			$row = $p->getValues();
			// var_dump($row);
		}
		// var_dump($list);
		// retorna a lista de produtos já formatada com a foto inclusive.
		return $list;
	}

	public function save()
	{
		$sql = new Sql();
		
		 $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice,  :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
		 		":idproduct"=>$this->getidproduct(),
		 		":desproduct"=>$this->getdesproduct(),
		 		":vlprice"=>$this->getvlprice(),
		 		":vlwidth"=>$this->getvlwidth(),
		 		":vlheight"=>$this->getvlheight(),
		 		":vllength"=>$this->getvllength(),
		 		":vlweight"=>$this->getvlweight(),
		 		":desurl"=>$this->getdesurl()
		 	));
			
		 	$this->setData($results[0]);
		 	
	}
// Método get criado para verificar se o registro existe no banco de dados. Foi criado inicialmente para verificar a exclusão de um registro de categoria
	public function get($idproduct)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct",[
			':idproduct'=>$idproduct
		]);

		$this->setData($results[0]);

	}
// delete não espera parametro nenhum, por que já espera-se que o objeto já esteja carregado...
	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct",[
			':idproduct'=>$this->getidproduct()
		]);

	}
	
	public function checkPhoto()
	{

		if (file_exists(
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR .
			"img" . DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR .
			$this->getidproduct() . ".jpg"
		)) {

			$url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";
		
		} else {

			$url = "/res/site/img/product.jpg";

		}

		return $this->setdesphoto($url);
	}

	public function getValues()
	{
		// criando um método para checar se o produto tem ou não foto. Desta forma apenas fazemos o getValues para incluir a foto somente nesta classe de produtos que necessita de apresentar a foto. Esta regra pode ser utilizada para qualquer outro caso semelhante. Chamamos a isto de herança. Herança que o checkPhoto herda de getValues.
		$this->checkPhoto();

		// abaixo estamos estanciando a classe getValues "pai" para que além das funções do pai, sejam executadas mais estas abaixo que é em relação a imagem do produto.
		$values = parent::getValues();

		return $values;
	}

	// $file é o arquivo enviado pelo usuário.
	// Recordemos que na aula que falou de imagens, nós utilizamos a função "move_uploaded_file(filename, destination)" para fazer upload de imagens.
	// Lembramos que esta função gera um arquivo temporário no servidor que não faz referencia a nenhum tipo de extensão, mas o conteúdo do arquivo já esta nesta area temporária. Dai podemos mover este arquivo para a pasta que queremos realmente que ele fique. 
	// No nosso caso deste projeto, todos os arquivos elegemos serem do tipo .jpg. Então qualquer o usuário apesar de fazer upload de qualquer tipo de arquivo, neste momento,  utilizaremos a função "image" do php para converter todos os arquivos de qualquer tipo para .jpg.

	public function setPhoto($file)
	{

		// verificando qual o tipo de extensão do arquivo que esta sendo feito upload
		// 'name' é o nome do campo que esta dentro do nosso formulário html
		// explode procura pelo ponto '.'
		// Neste momento foi pego o arquivo onde tem ponto '.' e fez um array dele.
		$extension = explode('.',$file['name']);
		// end siginifica que a extensão e seja a última pocisão que ele achou neste array. Dai vai pegar somente as três ou quatro letras que é a extensão do nome do  arquivo.
		$extension = end($extension);

		switch ($extension) {

			case "jpg":
			case "jpeg":
				// imagecreatefromjpeg pertence a biblioteca do php.
				// "tmp_name" é o nome do temporário que consta no servidor
				$image = imagecreatefromjpeg($file["tmp_name"]);
			break;

			case "gif":
				$image = imagecreatefromgif($file["tmp_name"]);
			break;

			case "png":
				$image = imagecreatefrompng($file["tmp_name"]);
			break;

		}

		// $dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
		$dist = "c:\\ecommerce" . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR .
			"img" . DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR .
			$this->getidproduct() . ".jpg";
		//aqui...
		var_dump($dist);

		// transforma qualquer tipo de imagem testada acima para jpg ou jpeg como queira
		// $imagem é a imagem que peguei do temporário sem ponto e $dist é o destino onde eu quero jogar esta imagem.

		imagejpeg($image, $dist);

		// destroi a imagem na area do temporário
		imagedestroy($image);

		$this->checkPhoto();

	}

	public function getFromURL($desurl)
	{

		$sql = new Sql();

		// LIMIT 1 é para garantir que volte apenas uma linha. 
		$rows =  $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", [
			':desurl'=>$desurl
		]);

		// valor colocar os campos retornados no sql acima dentro do próprio objeto com o setData...
		$this->setData($rows[0]);
	}

	public function getCategories()
	{

		$sql = new Sql();

		return $sql->select("
			SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory WHERE b.idproduct = :idproduct
		", [

			':idproduct'=>$this->getidproduct()
		]);

	}


	// paginação. Recebe o número da página atual. Se não receber por padrão será o número 1, e quantos ítens por pagina, igual a 10
    public static function getPage($page = 1, $itemsPerPage = 5){
        $start = ($page - 1) * $itemsPerPage; //primeira pagina come�a no zero

        $sql = new Sql();

        // SQL_CALC_FOUND_ROWS, calcula a quantidade de itens a serem retornados definido em itemsPerPage
        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_products
            ORDER BY desproduct
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

	public static function getPageSearch($search, $page = 1, $itemsPerPage = 10){
        
        $start = ($page - 1) * $itemsPerPage; //primeira pagina come�a no zero

        $sql = new Sql();

        // SQL_CALC_FOUND_ROWS, calcula a quantidade de itens a serem retornados definido em itemsPerPage
        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_products 
            WHERE desproduct LIKE :search
            ORDER BY desproduct
            LIMIT $start, $itemsPerPage;
        ", [
        	':search'=>'%'.$search.'%'
        ]);

       // coloca na variável nrtotal a quantidade de itens retornado no sql acima
       $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");
       // var_dump([
       //      'data'=>$results,
       //      'total'=>(int)$resultTotal[0]["nrtotal"],
       //      'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage )
       //  ]);

        return [
            'data'=>$results,
            'total'=>(int)$resultTotal[0]["nrtotal"],
            'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage) //conta quantas paginas tem resultados
        ];
    }

}


?>