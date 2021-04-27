<?php

namespace Hcode;	

// Agora vamos utilizar a classe PHPMailer. Ela não esta dentro de um VENDOR. Ela esta no escopo principal então não precisamos colocar o USE dela. Mas vamos utilizar a reinderização do template do html, o raintpl pra fazer esta reinderização.
use Rain\Tpl;

class Mailer
{

	const USERNAME = "jsesconetto@gmail.com";
	const PASSWORD = "JoaoSesco99$#@";
	// constante com o nome do remetente que é o nome da loja da Hcode: Hcode Store.
	const NAME_FROM = "Hcode Store";

	private $mail;
	// primeiro parametro que nossa classe receberá: Endereço que vamos encir
	// segundo nome do destinatário
	// terceiro o assunto
	// quarto nome do arquivo do template que vamos mandar lá pro tamplete
	// quinto são os dados que vamos passar no email enviado que é um array vazio, para caso não passemos nada neste array não vai dar problema nessa nossa funcion construtora "__construct"
	public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
	{
		// config
		// Nosso template precisa de uma pasta de html e uma pasta de cache, aos quais estão definidas a seguir...
		$config = array(
			// abaixo declaramos a variável de ambiente "DOCUMENT ROOT" para o sistema trazer o diretorio a pasta ROOT do meu servidor. Já que encontramos a respectiva pasta, onde está o seu template, está em views.
			// A linha abaixo era antes de criarmos a nossa classe PageAdmin
			// "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/", 
			// A linha abaixo foi atualizada para receber a nossa classe Mailer
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/email/", 
			// abaixo declaramos onde está a nossa pasta cache
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			// abaixo colocamos debug => false  porque não vamos precisar dele
			"debug"         => false // set to false to improve the speed
		);

		Tpl::configure( $config );

		// o $this->tpl torna o tpl um atributo da nossa classe. Isso é importante pra termos acesso aos outros metódos. Para o template de email, o professor disse que poderia ser uma variável normal sem o this.
		// $this->tpl = new Tpl;
		$tpl = new Tpl;

		// abaixo vamos passar os dados para o nosso template de email. O template tpl esta em page.php:
		foreach($data as $key => $value) {
			$tpl->assign($key, $value);

		}
		// true no segundo parametro é para o template ser jogado dentro da nossa variável $html e não para a tela.
		// Esta variável $html estamos passando para o phpmailer  lá embaixo na chamada: 
		// $this->mail->msgHTML($html);
		$html = $tpl->draw($tplName, true);
		//Create a new PHPMailer instance
		// o contra barra no inicio do metodo PHPMailer é para indicar que esta no escopo principal.
		// C:\xampp\htdocs\email\vendor\phpmailer\phpmailer\class.phpmailer.php
		// Já a variável do $mail, pediu para colocar o $this.
		$this->mail = new \PHPMailer;

		//Tell PHPMailer to use SMTP.
		
		$this->mail->isSMTP();

		//Enable SMTP debugging
		//SMTP::DEBUG_OFF = off (for production use)
		//SMTP::DEBUG_CLIENT = client messages
		//SMTP::DEBUG_SERVER = client and server messages
		// $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
		// 2 quando estiver em desenvolvimento; 1 quando estiver em teste e 0 quando estiver em produção
		$this->mail->SMTPDebug = 0;

		$this->mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$this->mail->Host = 'smtp.gmail.com';
		//Use `$mail->Host = gethostbyname('smtp.gmail.com');`
		//if your network does not support SMTP over IPv6,
		//though this may cause issues with TLS

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$this->mail->Port = 587;

		//Set the encryption mechanism to use - STARTTLS or SMTPS
		// $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$this->mail->SMTPSecure = 'tls';

		//Whether to use SMTP authentication
		$this->mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		// $this->mail->Username = 'username@gmail.com';
		// para este projeto ecommerce, vamos criar uma constante para o nome do nosso usuário:
		// $this->mail->Username = "jsesconetto@gmail.com";
		$this->mail->Username = Mailer::USERNAME;

		//Password to use for SMTP authentication
		// vamos fazer a mesma coisa com a senha
		// $this->mail->Password = 'JoaoSesco99$#@';
		$this->mail->Password = Mailer::PASSWORD;

		//Set who the message is to be sent from
		$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

		//Set an alternative reply-to address
		// $this->mail->addReplyTo('replyto@example.com', 'First Last');

		//Set who the message is to be sent to
		// Meu destinatário...
		$this->mail->addAddress($toAddress, $toName);

		//Set the subject line
		// Assunto do meu email
		$this->mail->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		// file_get_contents pega o conteudo de um arquivo da minha pasta local, no caso aqui 'contents.html'
		// $this->mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
		// No nosso projeto do ecommerce trocamos o comando acima também assim como todas as variáveis trocadas nas chamadas acima. No original tinha o nossa nosso metódo para chamada do sql trocamos por esta variável $html que vamos reinderizar com o RenTpl
		$this->mail->msgHTML($html);

		//Replace the plain text body with one created manually
		// Abaixo é um texto alternativo. Caso o meu destinatário não tenha um leitor de email que suporte html, ele vai aparecer apenas como texto.
		$this->mail->AltBody = 'Desculpe, mas o seu leitor de email não suporta o html...';
	}

	public function send()
	{ 
		return $this->mail->send();
	}

}

?>