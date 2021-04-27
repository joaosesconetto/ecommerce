<?php

use \Hcode\Model\User;
use \Hcode\Model\Cart;

function formatPrice($vlprice)
{

	// o if abaixo faz $vlprice = 0 se vier valor nulo.
	if (!$vlprice > 0) $vlprice = 0;

	// funcão para formatar valores. 
	// 2 casas decimais
	// a divisão decimal será virgula 
	// a divisão de milhar será ponto
	return number_format($vlprice, 2, ",", ".");
}

function formatDate($date)
{

	return date('d/m/y', strtotime($date));
}

function checkLogin($inadmin = true)
{

	return User::checkLogin($inadmin);
}

function getUserName()
{

	$user = User::getFromSession();

	return $user->getdesperson();
}

function getCartNrQtd() 
{

	$cart = Cart::getFromSession();

		$totals = $cart->getProductsTotals();

		return $totals['nrqtd'];	

}

function getCartVlSubTotal() 
{

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return formatPrice($totals['vlprice']);	//retorna somente o valor total do itens, sem o valor do frete.

}
?>