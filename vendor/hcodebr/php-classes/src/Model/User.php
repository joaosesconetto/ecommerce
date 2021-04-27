<?php

// abaixo definimos onde a classe esta, ou seja, o namespace dela... Então esta indicado aqui que esta nossa classe esta dentro a partir do nosso código User.php "Hcode" e dentro da pasta Model
namespace Hcode\Model;

// o contra barra inicial diz que é para iniciar da raiz.
use \Hcode\DB\Sql;
// Abaixo estou dizendo que a minha classe Model esta na pasta principal, ou seja, onde esta a pasta Model mesmo.
use \Hcode\Model;
use \Hcode\Mailer;

	
// Esta classe User é um Model, e todo o Model vai ter get's e set's
class User extends Model 
{

	// Esta constante foi criada para guardar o nome da nossa sessão aberta no site. SESSION con o nome da nossa sessão "User", do objeto "User"
	const SESSION = "User";

	// esta é uma constante para redefinir a senha do usuário. Esta constante é uma chave que será passada na função base64_encode que tem a função de criar um código legivel, para ser enviado para o usuário como link para recuperar a sua senha.
	const SECRET = "HcodePhp7_Secret";
	
	// const SECRET = "HcodePhp7_Secret";
	// const SECRET_IV = "HcodePhp7_Secret_IV";
	// constante de erro para erros de login do usuário comum
	const ERROR = "UserError";
	// constante do erro de entrada de dados do usuário quando informa os dados de cadastro no site
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS = "UserSucesss";	
// vefifica se o usuário esta ativo
	public static function getFromSession()
	{

		$user = new User();
		If (isset($_SESSION[User::SESSION]) && (INT)$_SESSION[User::SESSION]['iduser'] > 0) {
						
			$user->setData($_SESSION[User::SESSION]);
			// User::debuga( "<br>"."Passo 06 - Depois de chamar a setData apartir da getFromSession em User ").var_dump($user)."<br>";
		}

		return $user;
	}

	// verifica se o usuário esta logado.
	public static function checkLogin($inadmin = true)
	{
		
		if (
			// Se aconter uma das três condições abaixo o usuário não esta logado.
			// abaixo verificamos se a sessão foi definida com a constante SESSION. No caso abaixo saimos pelo false, se a constante ou qualquer um dos outros testes retornarem falso, faz o que esta entre chave seguinte, ou seja, entra na tela de login
			// Se existe ou esta definita, ou seja, ninguem ainda esta logado no sistema
			!isset($_SESSION[User::SESSION])
			||
			// Se a SESSION não existir ou for falsa ou estiver vazia, perdeu o valor por exemplo
			!$_SESSION[User::SESSION]
			// Se existe ou esta definita
			||
			// Se o Id do usuário não existir. Caso exista iduser > 0 então tem algum usuário logado
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			
				User::debuga("Passo 01 - checkLogin - Não esta definida a senha ou não existe a sessão ou Id usuário não existe ===>".(int)$inadmin."<br>");	


			return false;

		} else 	{
				// echo "passei aqui no login em User.php";
				// se for um administrador retorna true
				//  $inadmin === true checa que estou fazendo um login de um usuário da administração
				// (bool)$_SESSION[User::SESSION]['inadmin'] === true) checa se meu usuário e administrativo e minha rota é administrativa. Se ok nas duas condições meu usuário adm esta logado.
				if($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {
					User::debuga("Passo 01 - checkLogin existe e é administrador ===> ".(int)$inadmin."<br>");		
					return true;
					
				// se não se não for administrador, mas for um usuário logado, retorna true. Pode até ser um usuário que também é administrativo, mas se ele entrou com login de usuário e tiver logado retornará true.
				} else if ($inadmin === false)	{
					User::debuga("Passo 01 - checkLogin existe e é Usuário ===> ".(int)$inadmin."<br>");		
					
					return true;

				// caso não atenda nenhuma das anteriores, retorna false
				} else {
					User::debuga("Passo 01 - checkLogin não existe ===> ".(int)$inadmin."<br>");	
					return false;
				}

			}
			
	}

	// fazemos abaixo o metódo login que espera dois valores como parametros
	public static function login($login, $password)
	{
		// echo "passei aqui no login em User.php";

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE 	a.deslogin = :LOGIN", array(
			":LOGIN"=>$login ));
		
		if (count($results) === 0){

			// como não temos uma Exception dentro do nosso namespace MODEL, chamamos a Exception principal, por isso colocamos o contra barra no inicio da exceção \Exception...
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}

		$data = $results[0];

		// echo "Este é o hash do BD ===> ";
		// var_dump($data["despassword"]);
		// echo "<br>";
		// echo "Esta é a senha informada pelo usuário ===> " . $password . "<BR>";

		// $code2 = 'admin';
		// var_dump(User::SECRET);
		// $var = base64_decode($code2);
		// // echo "var ===> " . $var . "<br>";
		// $idretorno = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET,	base64_decode($code2), 
		// 		MCRYPT_MODE_ECB	);
		
		// $final1 = base64_encode($idretorno);
		// echo "<br>". "Decriptografei a string 'admin' para ficar legével ======> " . $final1 ."<br>"; 	
		// in5vXTqE6uk6FXGgDKvLjg==
		// in5vXTqE6uk6FXGgDKvLjg==
		// $dataretorno = password_get_info("in5vXTqE6uk6FXGgDKvLjg==");
		// var_dump($dataretorno["algo"]);
		// var_dump($dataretorno["algoName"]);
		// var_dump($dataretorno["options"]);

		// echo "passei aqui no login em User.php";

		// função que verifica se a senha do usuário esta correta, através da verificação que a função fará quando gerar um rash de verificação.
		if (password_verify($password, $data["despassword"]) === true )
		{
			
			$user = new User();
			
			$data['desperson'] = utf8_encode($data['desperson']);
			
			$user->setData($data);
			
			// Pra funcionar um login, precisamos criar uma sessão. O login tem que esta dentro de uma sessão...
			// SESSION é uma constante e foi declarada lá em cima. Ela guardará os dados da sessão aberta.
			// A sessão esta dendo declarada na classe User "User::SESSION". Claro que nesta classe foi declarado lá em cima nesta classe a constante SESSION.
			// getValues joga os dados consultados no banco de dados dentro da nossa sessão SESSION
			$_SESSION[User::SESSION] = $user->getValues();
			// retorna todos os dados consultados no banco de dados.
			return $user;

		} else {
				
			// como não temos uma Exception dentro do nosso MODEL, chamamos a Exception principal, por isso colocamos o contra barra no inicio da exceção \Exception...
			throw new \Exception("Usuário inexistente ou senha inválida.");

		}
		// echo "não validei a senha";
	}

	// Depois que passar ok por todas as verificações dentro da function verifyLogin, temos que ter certeza que a nossa sessão esta rodando no nosso servidor web, ou seja, se existir um section id, se não precisamos iniciar a sessão.
	public static function verifyLogin($inadmin = true)
	{ 
		if (User::checkLogin($inadmin)) {
			
			User::debuga("Passo 02 - VerifyLogin recebe ===> true, ou seja existe sessão aberta ativa.'<br>'");		

		} else			
		{
			User::debuga("Passo 02 - VerifyLogin recebe ===> false, ou seja NÃO existe sessão aberta ativa.'<br>'");			
		}

		// os caracteres || siginifica OR
		if (!User::checkLogin($inadmin)) {
			
			if ($inadmin) {
			
				User::debuga("Passo 3.1 - Despois de ver quem é o usuário ===> É O ADMINISTRADOR.'<br>'");
				header("Location: /admin/login");	
				
			} else {
				User::debuga("Passo 3.1 - Despois de ver quem é o usuário ===> É O USUÁRIO.'<br>'");
				header("Location: /login");

			}
			
			exit;

		}

	}

	// Criando um metódo estático para fazer logout. Para fazer o logout, tivemos que criar uma rota lá no arquivo index.php "$app->get('/admin/logout', function()"
	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;
	}

	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}

	public function save()
	{
		$sql = new Sql();
		
		 $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
		 		":desperson"=>utf8_decode($this->getdesperson()),
		 		":deslogin"=>$this->getdeslogin(),
		 		// antes de gravar no BD, criptografo a senha...
		 		":despassword"=>User::getPasswordHash($this->getdespassword()),
		 		":desemail"=>$this->getdesemail(),
		 		":nrphone"=>$this->getnrphone(),
		 		":inadmin"=>$this->getinadmin()
		 	));

		 	$this->setData($results[0]);
	}

	public function get($iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(":iduser"=>$iduser

	));

		$data = $results[0];

		// a função utf8_encode é para resolver a parte da acentuação...
		$data['desperson'] = utf8_encode($data['desperson']);

		$this->setData($data);

	}

	public function update()
	{
		$sql = new Sql();
		
		 $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
		 		":iduser"=>$this->getiduser(),
		 		":desperson"=>utf8_decode($this->getdesperson()),
		 		":deslogin"=>$this->getdeslogin(),
		 		":despassword"=>User::getPasswordHash($this->getdespassword()),
		 		":desemail"=>$this->getdesemail(),
		 		":nrphone"=>$this->getnrphone(),
		 		":inadmin"=>$this->getinadmin()
		 	));

		 	$this->setData($results[0]);

	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));
	}

	// Adicionamos abaixo o metódo estático getForgot.
	public static function getForgot($email, $inadmin = true)
	{

		// verificar primeiramente se este email esta cadastrado no nosso banco de dados:
		$sql = new Sql();

		$results = $sql->select("
			SELECT * 
			FROM tb_persons a 
			INNER JOIN tb_users b USING(idperson) 
			WHERE a.desemail = :email;
			", array(
				":email"=>$email
			));

		if (count($results) === 0) {

			// é bom não informar que não encontramos o email do usuário, se não o robô por exemplo pode comear a tentar os email possíveis até encontrar um que exista.
			throw new \Exception("Não foi possível recuperar a sua senha.");
		}
		else
		{

			// pegamos o iduser na primeira posição do array do $results
			// desip é o número do ip do usuário que é pego com a função $SERVER["REMOTE_ADDR"]
			$data = $results[0];
			
			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if (count($results2) === 0)
			{

				throw new \Exception("Não foi possível recuperar a sua senha");

			}
			else
			{

				$dataRecovery = $results2[0];
				// mcrypt_encrypt é uma função de criptográfia para o php. Ele recebe quatro parametros. 128bits ou 256bits, etc.
				// primeiro parametro é o tipo ou modo de criptofrafia que é uma constante do php
				// segundo parametro é a chave de criptografia definia por nós mesmos, ela tem que ter pelo menos 16 caracteres ou fix, 16, 24 ou 32 etc..., que foi até definica por nós como uma constante lá encima (const SESSION = "User";), no inicio desta classe.
				// terceiro parametro é a nossa CHAVE de criptografia do nosso select que esta na posição zero, ou seja, é a chave primaria lá no do nosso arquivo "tb_userspasswordsrecoveries" Vamos então encripar esse número.
				// quarto é o tipo de criptografia que vamos utilizar . No nosso caso aqui vamos utilizar o tipo de criptográfia ECB - Eletrônic code book. Este mode gera um dado randomico, para fazer a criptografia.
				// $code na frente é o resultado do nosso código de referencia para encriptografar o link que vamos enviar para o usuário.
				$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, 
					$dataRecovery["idrecovery"], MCRYPT_MODE_ECB));
				

				// abaixo criamos o link para passar para o usuário recuperar a senha. Criamos esta rota reset lá ... 
				// a interrogação depois do reset, siginifica que vamos passar este código via "get" e code é igual a nossa variável $code.

				if ($inadmin === true) {

					$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
				
				} else 	{

					$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$code";

				}

				// agora vamos criar a classe "Mailer.php" responsável para enviar o nosso email na pasta: C:\ecommerce\vendor\hcodebr\php-classes\src

				// voltamos da criação no nosso arquivo Mailer.php e continuamos a linhas a seguir...
				// os dados deste array, são os dados que precisamos reinderizar dentro do html deste template e as variáveis correspondentes estão dentro do arquivo forgot.html, precedidos de '{$' e o nome da variável.

				$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir Senha da Hcode Store", "forgot",
					array(
					"name"=>$data["desperson"],
					"link"=>$link
				));
				// // Finalmente vamos enviar o email...
				$mailer->send();

				// feito isso podemos retornar os dados de $data, caso o metódo precise de alguma coisa como estamos recebendo lá na chamada do forgot.htm.
				return $data;
			}
		}
	}

// a classe getForgoto abaixo copiei do professor em: "https://github.com/hcodebr/ecommerce/blob/master/vendor/hcodebr/php-classes/src/Model/User.php"
	// public static function getForgot($email, $inadmin = true)
	// {

	// // echo "passei aqui no getForgot ===> ";

	// 	$sql = new Sql();

	// 	$results = $sql->select("
	// 		SELECT *
	// 		FROM tb_persons a
	// 		INNER JOIN tb_users b USING(idperson)
	// 		WHERE a.desemail = :email;
	// 	", array(
	// 		":email"=>$email
	// 	));

	// 	if (count($results) === 0)
	// 	{

	// 		throw new \Exception("Não foi possível recuperar a senha.");

	// 	}
	// 	else
	// 	{

	// 		$data = $results[0];

	// 		$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
	// 			":iduser"=>$data['iduser'],
	// 			":desip"=>$_SERVER['REMOTE_ADDR']
	// 		));

	// 		if (count($results2) === 0)
	// 		{

	// 			throw new \Exception("Não foi possível recuperar a senha.");

	// 		}
	// 		else
	// 		{

	// 			$dataRecovery = $results2[0];

	// 			$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

	// 			$code = base64_encode($code);

	// 			if ($inadmin === true) {

	// 				$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

	// 			} else {

	// 				$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$code";
					
	// 			}				

	// 			$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
	// 				"name"=>$data['desperson'],
	// 				"link"=>$link
	// 			));				

	// 			$mailer->send();

	// 			return $link;

	// 		}

	// 	}

	// }


	public static function validForgotDecrypt($code)
	{
	
		$idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET,	base64_decode($code), 
				MCRYPT_MODE_ECB	);

		$sql = new Sql();

		$results = $sql->select("
			SELECT * FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE a.idrecovery = :idrecovery
			AND
			a.dtrecovery IS NULL
			AND
			DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
			));
		
		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{
			return $results[0];
		}

	}

// a classe abaixo foi copiado do professor em: https://github.com/hcodebr/ecommerce/blob/master/vendor/hcodebr/php-classes/src/Model/User.php
// public static function validForgotDecrypt($code)
// 	{
// 		echo "passei aqui no validForgotDecrypt ===> ";

// 		$code = base64_decode($code);

// 		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

// 		var_dump($idrecovery);

// 		$sql = new Sql();

// 		$results = $sql->select("
// 			SELECT *
// 			FROM tb_userspasswordsrecoveries a
// 			INNER JOIN tb_users b USING(iduser)
// 			INNER JOIN tb_persons c USING(idperson)
// 			WHERE
// 				a.idrecovery = :idrecovery
// 				AND
// 				a.dtrecovery IS NULL
// 				AND
// 				DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
// 		", array(
// 			":idrecovery"=>$idrecovery
// 		));

		

// 		if (count($results) === 0)
// 		{
			
// 			throw new \Exception("Não foi possível recuperar a senha.");
// 		}
// 		else
// 		{

// 			return $results[0];

// 		}

// 	}
	
	public static function setForgotUsed($idrecovery)
	{

		$sql = new Sql();
		// NOW() retorna a data atual;
		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(":idrecovery"=>$idrecovery));
	}

	public function setPassword($password)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));
	}

	// setError coloca a mensagem de erro na nossa constante de erro dentro da classe User
	public static function setError($msg)
	{

		$_SESSION[User::ERROR] = $msg;

	}

	// pega o erro da sessão.
	public static function getError()
	{

		// no if ternário verifica se o  erro esta definido "(isse($_SESSION[User:ERROR])"
		// se não é vazio "$_SESSION[User::ERROR])"
		// se tiver definido e não for vazio retorna a mensagem de erro "? $_SESSION[User::ERROR]" 
		$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';
		


		// assim que identificamos o erro limpamos de imediato a area de erro da página do site.
		// criamos o método para limpar o erro.
		User::clearError();

		return $msg;

	}

	public static function clearError()
	{
		// limpa a sessão
		$_SESSION[User::ERROR] = NULL;

	}

	// as mensagens abaixo não são mensagens de erro, mas informativas para o usuário
	public static function setSuccess($msg){
	    $_SESSION[User::SUCCESS] = $msg;
	}
	
	public static function getSuccess(){
	    $msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS] ? $_SESSION[User::SUCCESS] : '');
	    
	    User::clearError();
	    
	    return $msg;
	}
	
	public static function clearSuccess(){
	    $_SESSION[User::SUCCESS] = NULL;
	}
	
	public static function setErrorRegister($msg)
	{

		$_SESSION[User::ERROR_REGISTER] = $msg;

	}

	public static function getErrorRegister(){
	    // se existir o erro retorno ele se não retorno uma string vazia.
	    $msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER] ? $_SESSION[User::ERROR_REGISTER] : '');
	    // limpo o erro da minha sessão.
	    User::clearErrorRegister();
	    
	    return $msg;
	}

	public static function clearErrorRegister(){
	    $_SESSION[User::ERROR_REGISTER] = NULL;
	}

	public static function checkLoginExist($Login)
	{
		
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :deslogin", [
			':deslogin'=>$Login
		]);

		return (count($results) > 0);
	}

	
	public static function getPasswordHash($password){
	    return password_hash($password, PASSWORD_DEFAULT, [
	        'cost'=>12
	    ]);
	}

	public function getOrders(){
        $sql = new Sql();

        $results = $sql->select("
          SELECT * FROM tb_orders a 
          INNER JOIN tb_ordersstatus b USING(idstatus)
          INNER JOIN tb_carts c USING(idcart)
          INNER JOIN tb_users d ON d.iduser = a.iduser
          INNER JOIN tb_addresses e USING(idaddress)
          INNER JOIN tb_persons f ON f.idperson = d.idperson
          WHERE a.iduser = :iduser 
        ", [
            ':iduser'=>$this->getiduser()
        ]);

        return $results;
    }

    //paginação. Recebe o número da página atual. Se não receber por padrão será o número 1, e quantos ítens por pagina, igual a 10
    public static function getPage($page = 1, $itemsPerPage = 5){
        $start = ($page - 1) * $itemsPerPage; //primeira pagina come�a no zero

        $sql = new Sql();

        // SQL_CALC_FOUND_ROWS, calcula a quantidade de itens a serem retornados definido em itemsPerPage
        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_users a 
            INNER JOIN tb_persons b USING(idperson) 
            ORDER BY b.desperson 
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
            FROM tb_users a 
            INNER JOIN tb_persons b  USING(idperson)
            WHERE b.desperson LIKE :search OR b.desemail = :search OR a.deslogin LIKE :search
            ORDER BY b.desperson 
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
   
   public static function debuga($msg){

		$debuga = false;
		// Tem que desinibir os var_dump por que o echo não deixa de apresentar os var_dump
		// Por enquanto tem var dump em 
		// User = Passo 06;  
		// Model.php = Passo 05
		// site.php = Passo 07 
    	// $debuga = true;
    	
    	if ($debuga) {

    		echo $msg;
    	}

    }
}
	


?>