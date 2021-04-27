<?php

// abaixo definimos onde a classe esta, ou seja, o namespace dela... Então esta indicado aqui que esta nossa classe esta dentro a partir do nosso código User.php "Hcode" e dentro da pasta Model
namespace Hcode\Model;

// o contra barra inicial diz que é para iniciar da raiz.
use \Hcode\DB\Sql;
use Hcode\Mailer;
// Abaixo estou dizendo que a minha classe Model esta na pasta principal, ou seja, onde esta a pasta Model mesmo.
use \Hcode\Model;
	
// Esta classe User é um Model, e todo o Model vai ter get's e set's
class User extends Model 
{

	// Esta constante foi criada para guardar o nome da nossa sessão aberta no site. SESSION con o nome da nossa sessão "User", do objeto "User"
	const SESSION = "User";

	// esta é uma constante para redefinir a senha do usuário. Esta constante é uma chave que será passada na função base64_encode que tem a função de criar um código legivel, para ser enviado para o usuário como link para recuperar a sua senha.
	const SECRET = "HcodePhp7_Secret";
	
	// const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";
	// const ERROR = "UserError";
	// const ERROR_REGISTER = "UserErrorRegister";
	// const SUCCESS = "UserSucesss";	
// vefifica se o usuário esta ativo
	public static function getFromSession()
	{

		$user = new User();

		If (isset($_SESSION[User::SESSION]) && (INT)$_SESSION[User::SESSION]['iduser'] > 0) {

			$user->setData($_SESSION[User::SESSION]);

		}

		return $user;
	}

	// verifica se o usuário esta logado.
	public static function checkLogin($inadmin = true)
	{
		if (
			// Se aconter uma das três condições abaixo o usuário não esta logado.
			// abaixo verificamos se a sessão foi definida com a constante SESSION. No caso abaixo saimos pelo false, se a constante ou qualquer um dos outros testes retornarem falso, faz o que esta entre chave seguinte, ou seja, entra na tela de login
			// Se existe ou esta definita, ou seja, ainda ninguem esta logado no meu sistema
			!isset($_SESSION[User::SESSION])
			||
			// Se a minha SESSION não existir ou for falsa ou estiver vazia, perdeu o valor por exemplo
			!$_SESSION[User::SESSION]
			// Se existe ou esta definita
			||
			// Se o Id do usuário não existir. Caso exista iduser > 0 então tem algum usuário logado
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			
			echo "passei aqui no login em User.php";
			return false;

		} else 	{
				echo "passei aqui no login em User.php";
				// se for um administrador retorna true
				if($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {

					return true;
					
				// se não se não for administrador, mas for um usuário logado, retorna true
				} else if ($inadmin === false)	{

					return true;

				// caso não atenda nenhuma das anteriores, retorna false
				} else {

					return false;
				}

			}
			
	}

	// fazemos abaixo o metódo login que espera dois valores como parametros
	public static function login($login, $password)
	{
		echo "passei aqui no login em User.php";

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login

		));
		// var_dump($results);
		if (count($results) === 0){

			// como não temos uma Exception dentro do nosso namespace MODEL, chamamos a Exception principal, por isso colocamos o contra barra no inicio da exceção \Exception...
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}

		$data = $results[0];

		// Á variável $data atribuimos o primeiro resultado que a função sql pegou, que na verdade é somente um resultado mesmo que estamos esperando.
				
		// ******   CRIPTOGRAFA - INICIO COM CARACTERES MALUCOS   *****
			// $data2 = [
			// 	"nome"=>"Hcode"
			// ];
			// define('SECRETO', pack('a16', 'senha'));

			// $mcrypt = mcrypt_encrypt(
			// 	MCRYPT_RIJNDAEL_128,
			// 	SECRETO,
			// 	json_encode($data2),
			// 	MCRYPT_MODE_ECB
			// );
			// echo "Criptografei o a senha Hcode com caracteres MALUCOS  ======> " . $mcrypt ."<br>"; 

		// ******   CRIPTOGRAFA - FIM COM CARACTERES MALUCOS   *****
			
		// ******   CRIPTOGRAFA - INICIO COM CARACTERES LEGIVÉIS   *****
		
			// $final = base64_encode($mcrypt);
			// // abaixo novamente vemos a nossa variável criptografada como variável string agora.
			// // var_dump($final);

			// echo "Criptografei o a senha Hcode com caracteres LEGÍVEIS  ======> " . $final ."<br>"; 

		// ******   CRIPTOGRAFA - FIM COM CARACTERES LEGIVÉIS   *****

		// ******   DECRIPTOGRAFA - INICIO RETORNANDO A SENHA ORIGINAL   *****
			// $string = mcrypt_decrypt(
			// 	MCRYPT_RIJNDAEL_128,
			// 	SECRETO,
			// 	base64_decode($final), 
			// 	MCRYPT_MODE_ECB
			// );
			// echo "Decriptografei o a senha ao nome ORIGINAL ======> " . $string ."<br>"; 
		// ******   DECRIPTOGRAFA - FIM RETORNANDO A SENHA ORIGINAL   *****

		// ******   RECUPERANDO A SENHA ORIGINAL DO MEU BANCO DE DADOS   *****
			// $data2 = [
			// 	"nome2"=>$password
			// ];
			// $data2 = $results[0];
			
			// var_dump($data2);
			// var_dump($results[0]);
			// passo 1
				// $mcrypt = mcrypt_encrypt(
				// MCRYPT_RIJNDAEL_128,
				// User::SECRET,
				// json_encode($password),
				// MCRYPT_MODE_ECB
				// );
				// echo "Password do usuário hash ===> " . $data["despassword"]. "<br>";
				// echo "Password do usuário digitada ===> " . $password . "<br>";
				// echo "<br>". "Criptrografei a senha do meu usuário ===> " . $mcrypt ."<br>"; 
				// passo 2
				// $final1 = base64_encode($mcrypt);
				// echo "<br>". "Decriptografei o a senha ao nome ORIGINAL do meu BD ======> " . $final1 ."<br>"; 
				// passo 3
			// 	$string = mcrypt_decrypt(
			// 	MCRYPT_RIJNDAEL_128,
			// 	User::SECRET,
			// 	base64_decode($final1), 
			// 	MCRYPT_MODE_ECB
			// );
			// echo "Decriptografei o a senha ao nome ORIGINAL do meu BD ======> " . $string ."<br>"; 

		// senha = admin2 ==> $2y$12$obxBlfugwskpToXxalWfxuhZS6CxQ1B9i3BnZ715M9WIzRYKiyCqO
		// uDL2DeT6PwT7AuPFOOl6xQ==
					
		// outros testes de criptografar e decryptografar...
		
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

		echo "passei aqui no login em User.php";

		// função que verifica se a senha do usuário esta correta, através da verificação que a função fará quando gerar um rash de verificação.
		if (password_verify($password, $data["despassword"]) === true )
		{
			// echo "validei a senha";
			// vamos criar um novo objeto, vamos passar os dados desse novo usuário.
			$user = new User();
			
			// a chamada abaixo foi utilizada para testes do retorno do metódo Model
			// $user->setiduser($data["iduser"]);

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

	// Depois que passar ok por todas as verificações dentro da function verifyLogin, temos que ter certeza que a nossa sessão esta rodando no nosso servidor web, ou seja, se existir um section id, não precisamos iniciar a sessão.
	public static function verifyLogin($inadmin = true)
	{ 
		// os caracteres || siginifica OR
		// if (User::checkLogin($inadmin)) {
		if (
			
			// ANTES NOS FAZIAMOS TODAS ESTAS VERIFICAÇÕES AQUI EM BAIXO. AGORA ESTAMOS FAZENDO LÁ NA FUNÇÃO User::checkLogin($inadmin) passando o id do administrador.
			// abaixo verificamos se a sessão foi definida com a constante SESSION. No caso abaixo saimos pelo false, se a constante ou qualquer um dos outros testes retornarem falso, faz o que esta entre chave seguinte, ou seja, entra na tela de login
			// Se existe ou esta definita, ou seja, ainda ninguem esta logado no meu sistema
			!isset($_SESSION[User::SESSION])
			||
			// Se a minha SESSION não existir ou for falsa ou estiver vazia, perdeu o valor por exemplo
			!$_SESSION[User::SESSION]
			// Se existe ou esta definita
			||
			// Se o Id do usuário não existir. Caso exista iduser > 0 então tem algum usuário logado
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
			// Se o administrador não esta tentando entrar quando já esta logado na loja, dai não pode entrar como admin. O (bool) ou (int) ou ... é para fazer da nossa SESSION um tipo diferente.
			||
			(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
			
		) {
						
			header("Location: /admin/login");
			// exit sai do programa e não processa mais nada e manda para a area de login novamene.
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
		 		":desperson"=>$this->getdesperson(),
		 		":deslogin"=>$this->getdeslogin(),
		 		":despassword"=>$this->getdespassword(),
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

		$this->setData($results[0]);

	}

	public function update()
	{
		$sql = new Sql();
		
		 $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
		 		":iduser"=>$this->getiduser(),
		 		":desperson"=>$this->getdesperson(),
		 		":deslogin"=>$this->getdeslogin(),
		 		":despassword"=>$this->getdespassword(),
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
	// public static function getForgot($email)
	// {

	// 	// verificar primeiramente se este email esta cadastrado no nosso banco de dados:
	// 	$sql = new Sql();

	// 	$results = $sql->select("
	// 		SELECT * 
	// 		FROM tb_persons a 
	// 		INNER JOIN tb_users b USING(idperson) 
	// 		WHERE a.desemail = :email;
	// 		", array(
	// 			":email"=>$email
	// 		));

	// 	if (count($results) === 0) {

	// 		// é bom não informar que não encontramos o email do usuário, se não o robô por exemplo pode comear a tentar os email possíveis até encontrar um que exista.
	// 		throw new \Exception("Não foi possível recuperar a sua senha.");
	// 	}
	// 	else
	// 	{

	// 		// pegamos o iduser na primeira posição do array do $results
	// 		// desip é o número do ip do usuário que é pego com a função $SERVER["REMOTE_ADDR"]
	// 		$data = $results[0];
	// 		$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(":iduser"=>$data["iduser"],
	// 			":desip"=>$_SERVER["REMOTE_ADDR"]
	// 		));

	// 		if (count($results2) === 0)
	// 		{

	// 			throw new \Exception("Não foi possível recuperar a sua senha");

	// 		}
	// 		else
	// 		{

	// 			$dataRecovery = $results2[0];
	// 			// mcrypt_encrypt é uma função de criptográfia para o php. Ele recebe quatro parametros. 128bits ou 256bits, etc.
	// 			// primeiro parametro é o tipo ou modo de criptofrafia que é uma constante do php
	// 			// segundo parametro é a chave de criptografia definia por nós mesmos, ela tem que ter pelo menos 16 caracteres ou fix, 16, 24 ou 32 etc..., que foi até definica por nós como uma constante lá encima (const SESSION = "User";), no inicio desta classe.
	// 			// terceiro parametro é a nossa CHAVE de criptografia do nosso select que esta na posição zero, ou seja, é a chave primaria lá no do nosso arquivo "tb_userspasswordsrecoveries" Vamos então encripar esse número.
	// 			// quarto é o tipo de criptografia que vamos utilizar . No nosso caso aqui vamos utilizar o tipo de criptográfia ECB - Eletrônic code book. Este mode gera um dado randomico, para fazer a criptografia.
	// 			// $code na frente é o resultado do nosso código de referencia para encriptografar o link que vamos enviar para o usuário.
	// 			$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, 
	// 				$dataRecovery["idrecovery"], MCRYPT_MODE_ECB));
				

	// 			// abaixo criamos o link para passar para o usuário recuperar a senha. Criamos esta rota reset lá ... 
	// 			// a interrogação depois do reset, siginifica que vamos passar este código via "get" e code é igual a nossa variável $code.
	// 			$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

	// 			// agora vamos criar a classe "Mailer.php" responsável para enviar o nosso email na pasta: C:\ecommerce\vendor\hcodebr\php-classes\src

	// 			// voltamos da criação no nosso arquivo Mailer.php e continuamos a linhas a seguir...
	// 			// os dados deste array, são os dados que precisamos reinderizar dentro do html deste template e as variáveis correspondentes estão dentro do arquivo forgot.html, precedidos de '{$' e o nome da variável.
	// 			$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir Senha da Hcode Store", "forgot",
	// 				array(
	// 				"name"=>$data["desperson"],
	// 				"link"=>$link
	// 			));
	// 			// // Finalmente vamos enviar o email...
	// 			$mailer->send();

	// 			// feito isso podemos retornar os dados de $data, caso o metódo precise de alguma coisa como estamos recebendo lá na chamada do forgot.htm.
	// 			return $data;
	// 		}
	// 	}
	// }

// a classe getForgoto abaixo copiei do professor em: "https://github.com/hcodebr/ecommerce/blob/master/vendor/hcodebr/php-classes/src/Model/User.php"
public static function getForgot($email, $inadmin = true)
	{

	echo "passei aqui no getForgot ===> ";

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
		", array(
			":email"=>$email
		));

		if (count($results) === 0)
		{

			throw new \Exception("Não foi possível recuperar a senha.");

		}
		else
		{

			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data['iduser'],
				":desip"=>$_SERVER['REMOTE_ADDR']
			));

			if (count($results2) === 0)
			{

				throw new \Exception("Não foi possível recuperar a senha.");

			}
			else
			{

				$dataRecovery = $results2[0];

				$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);

				if ($inadmin === true) {

					$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

				} else {

					$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$code";
					
				}				

				$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
					"name"=>$data['desperson'],
					"link"=>$link
				));				

				$mailer->send();

				return $link;

			}

		}

	}


	// public static function validForgotDecrypt($code)
	// {
	
	// 	$idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET,	base64_decode($code), 
	// 			MCRYPT_MODE_ECB	);

	// 	$sql = new Sql();

	// 	$results = $sql->select("
	// 		SELECT * FROM tb_userspasswordsrecoveries a
	// 		INNER JOIN tb_users b USING(iduser)
	// 		INNER JOIN tb_persons c USING(idperson)
	// 		WHERE a.idrecovery = :idrecovery
	// 		AND
	// 		a.dtrecovery IS NULL
	// 		AND
	// 		DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
	// 	", array(
	// 		":idrecovery"=>$idrecovery
	// 		));
	// 	// echo " passei aqui na linha 324 ===> ". var_dump($results);
	// 	var_dump($results);
	// 	echo "idrecovey ===> " . $idrecovery;

	// 	if (count($results) === 0)
	// 	{
	// 		throw new \Exception("Não foi possível recuperar a senha.");
	// 	}
	// 	else
	// 	{
	// 		return $results[0];
	// 	}
	// }

// a classe abaixo foi copiado do professor em: https://github.com/hcodebr/ecommerce/blob/master/vendor/hcodebr/php-classes/src/Model/User.php
public static function validForgotDecrypt($code)
	{
		echo "passei aqui no validForgotDecrypt ===> ";

		$code = base64_decode($code);

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

		var_dump($idrecovery);

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
				a.idrecovery = :idrecovery
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
}

?>