<?php
// namespace Hcode é de onde devemos buscar as denpendências das classes utilizadas nesta classe PageAdmin
namespace Hcode;
// A classe PageAdmin conta com praticamente tudo que temos na classe Page, por isso vamos vincular a classe Page a esta nossa classe PageAdmin, ou seja, extender esta nossa classe aqui à classe Page.
// Apenas lá na classe Page devemos alterar a rota para que a classe Page quando executada na chamada da classe PageAdmin enchegue esta nossa classe aqui. Para isso devemos alterar a linha ""tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/", lá da classe page PARA ==> 
// Primeiro a classe Page deve receber mais um parametro:
// Linha original da classe Page que vamos alterar: Public function __construct($opts = array()){ 
// Linha da  classe Page alterada para receber as instruções da nossa nova classe PageAdmin:
// public function __construct($opts = array(), $tpl_dir = "/views/"){
class PageAdmin extends page {
// Abaixo criaremos um outro caminho para recuperar os templates da parte administrativa deste projeto. Este outro caminho é o segundo paramentro $tpl_dir = "/views/admin/" 
	public function __construct($opts = array(), $tpl_dir = "/views/admin/"){
		// var_dump($opts);
		// echo "Passei na function __construct " ;
		// abaixo estamos criando um parentesco da classe Page para ser executado pela nossa classe PageAdmin, ou seja, vamos reaproveitar todo o codigo criado na classe construtor da classe Page. Passando o parametro $tpl_dir, que é o caminho onde a classe page vai ter que buscar os arquivos que a classe PageAdmin utilizará e que esta na pasta views/admin. Se nada fosse passado no segundo parametro $tpl_dir, por padrão como definimos lá na classe Page seria então views mesmo. O que definimos aqui chamamos de Herança.
		parent::__construct($opts, $tpl_dir);

	}
}

?>