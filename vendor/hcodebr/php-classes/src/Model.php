<?php

// O namespace da nossa classe Model é o Hcode por que ela esta no nosso namespace principal, ou seja, na mesma pasta onde esta a pasta Model "C:\ecommerce\vendor\hcodebr\php-classes\src"
namespace Hcode;

use \Hcode\Model\User;

class Model {

// Dentro da variável $values vamos colocar todos os atributos do usuário, quando a tabela pesquisada for a tb_users claro.
	private $values = [];
	// Criamos baixo um função que chamamos de metódo mágico. 
	// Primeiro parametro é o nome do metódo (set ou get) que foi chamado.
	// Segundo parametro são os argumentos "where"
	public function __call($name, $args){
		// Esta substring recorda da posição zero até a posição 3, ou seja, trará a substring "set" ou "get"
		$method = substr($name, 0, 3);
		// abaixo pegamos o restante da string que recortamoa acima
		$fieldName = substr($name, 3, strlen($name));
		// echo "Passo 01 - Carregando os set's e/ou get's ===> " . "$method"."$fieldName"."<br>";
		switch($method)
		{

			// case "get":
			// 	return $this->values[$fieldName];
			// break;
			// Abaixo vamos fazer um iff ternário para resolver um problema que a procedure para inclusão de categorias não conseguiu resolver quando foi incluir uma nova catégoria.
			// Desta forma se for passado um campo com valor retorna ele mesmo como esta no primeiro parametro $this, caso o nome campo que não foi definido, retorna NULL ou 
			case "get":
				return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
			break;
			
			
			case "set":
				$this->values[$fieldName] = $args[0];
			break;

			
		}

	}	

	public function setData($data = array())
	{
		
		foreach ($data as $key => $value) {
			// aqui vemos o método "set" e o nome do campo que esta vindo na variável $key.
			// Só que para fazer esta concatenação em php tudo que for criado dinâmicamente tem que ser colocado entre chaves, como esta abaixo
			// "set" concatena o a chave .$key e na frente os valores.
			// Esta string "set" que esta sendo concatenada com o nome do campo "$key". Esta string completa concatenada depois vai ser executada como um metódo mesmo.
			$this->{"set".$key}($value);
			
			// $var = ('$this->set.').$key."(".$value.")";
			// User::debuga( "<br>"."Passo 05 - Carrega no setData os Sets  ").var_dump($var);
			
		}
		
	}

	public function getValues()
	{
		// porque não acessamos o atributo diretamente? por que ele é privado.
		// echo "01 - Passei aqui e retornei os valores da minha sessão... ==> "."<br>";
		// echo $this->values; 
		// retorna os valores da consulta no banco de dados para coloca-los dentro da nossa sessão SESSION.
		// var_dump($this->values)." ===> " . "<br>";
		return $this->values;
	}
}

?>