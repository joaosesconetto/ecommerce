<?php
namespace Hcode;
// <!-- Esta é a nossa classe pager para gerenciar nossas telas, nosso html -->

// acima  temos que informar onde esta nossa classe esta, qual o namespace, que é o Hcode. É lá no Hcode que nossa classe está. Lembra do comando que colocamos lá no composer.json. Descrevo este comando logo aqui abaixo:
// 	"autoload": {
//        "psr-4": {
//            "Hcode\\": "vendor\\hcodebr\\php-classes\\src"
// 


// abaixo vamos utilizar outro namespace que é o Rain\Tpl
// Então quando chamarmos um new Tpl o sistema saberá que é do Rain\Tpl
use Rain\Tpl;

class page {
// declaramos a variável private mesmo pra outras classes não terem acesso.Aqui nos declaramos a variável e lá em baixo nos criamos o nosso vinculo tpl fazendo dele parte da nossa classe quando utilizamos o $this->tpl
	private $tpl;
	private $options = [];
	private $defaults = [
// por padrão estamos dizendo que o "header" é true.
		"header"=>true,
		"footer"=>true,
		"data"=>[]
	];
// Em primeiro lugar vamos criar os metódos mágicos construct e destruct

// As variáveis vão vir de acordo com a rota. Então dependendo da variável que estivermos chamando lá no slim e que vamos passar os dados aqui para esta classe page. Então para isso, temos que receber algumas opções da classe.

// O primeiro parametro, se não passar nada ela já é um array "$opts = array".
// Também podemos ter algumas opções de variáveis padrão como definido acima "private $default = [].
// O segundo parametro foi criado para receber mais uma classe que é a nossa nova classe PageAdmin, pois esta classe herda tudo desta classe Page. O segundo parametro $tpl_dir = "/views/" quer dizer que, se não for passado nenhum valor para este segundo parametro a variável $tpl_dir fica fazendo o valor ==> "/views/" mesmo, definido originalmente.
	public function __construct($opts = array(), $tpl_dir = "/views/"){

		$this->defaults["data"]["session"] = $_SESSION;
		// Abaixo vamos mesclar os dois array's: o array defaults declarado acima e o array opts passado para a funciotn __construct acimA. Devemos observar que um sobrescreve o outro. O último sempre vai sobrescrever os anteriores. Então queremos que sempre que for informado dados no array opts deve este opts sobrescrever o defaults. Os nossos dados vão estar na chave data deste options.
		$this->options = array_merge($this->defaults, $opts);
		// config
		// Nosso template precisa de uma pasta de html e uma pasta de cache, aos quais estão definidas a seguir...
		$config = array(
			// abaixo declaramos a variável de ambiente "DOCUMENT ROOT" para o sistema trazer o diretorio a pasta ROOT do meu servidor. Já que encontramos a respectiva pasta, onde está o seu template, está em views.
			// A linha abaixo era antes de criarmos a nossa classe PageAdmin
			// "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/", 
			// A linha abaixo foi atualizada para receber a nossa classe PageAdmin
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir, 
			// abaixo declaramos onde está a nossa pasta cache
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			// abaixo colocamos debug => false  porque não vamos precisar dele
			"debug"         => false // set to false to improve the speed
		);

		Tpl::configure( $config );

		// o $this->tpl torna o tpl um atributo da nossa classe. Isso é importante pra termos acesso aos outros metódos
		$this->tpl = new Tpl;

		// foreach ($this->$options["data"] as $key => $value) {
		// 	$this->tpl->assign($key, $valor);
		// }

		$this->setData($this->options["data"]);

		// A primeira tag que devemos desenhar o template na tela a o header com o draw. O draw espera o nome do arquivo que queremos desenhar. Vamos criar o arquivo header na pasta views
		// A partir da inclusão da classe PageAdmin que trata o nosso login, a construção do header deverá ser testado, pois na tela de login não tem header e nem footer.
		// $this->tpl->draw("header");

		if ($this->options["header"] === true) $this->tpl->draw("header");
			

	}

	private function setData($data = array())
	{
		foreach($data as $key => $value) {
			$this->tpl->assign($key, $value);
		}	
	}
	// abaixo vamos fazer um metódo somente para o conteúdo da nossa página
	// parametro 1: nome do template
	// parametro 2: quais são as variáveis que queremos passar, que por padrão é um array vazio.
	// parametro 3: se queremos que retorne o HTML ou se queremos que ele joga na tela que por paddrão é false
	public function setTpl($name, $data = array(), $returnHTML = false)
	{

		// observe que logo acima estamos fazendo este mesmo procedimento identico ao debaixo aqui, o foreach, portanto quando começamos a repetir funções, podemos criar um metódo para escreve-lo somente uma vez. Então vamos inibir o trecho abaixo e chamar o metódo setData acima, sempre que precisarmos de executar esta função.
		// foreach($data as $key => $value) {
		// 	$this-tpl->assign($key, $value);
		// }		

		$this->setData($data);

		// o draw constroi minha tela de HTML
		return $this->tpl->draw($name, $returnHTML);

		// depois deste ponto, vomos criar as pastas viewes e views-cache

	}

	public function __destruct(){

		// A partir da inclusão da classe PageAdimin que trata o nosso login, a construção do header deverá ser testado, pois na tela de login não tem header e nem footer.
		// $this->tpl->draw("footer");

		if ($this->options["footer"] === true) $this->tpl->draw("footer");
	}
}

?>