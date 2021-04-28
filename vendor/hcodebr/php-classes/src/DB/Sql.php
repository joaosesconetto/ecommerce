<?php 

namespace Hcode\DB;

class Sql {

	// As constantes de conexão do BD abaixo foram utilizadas até a criação do BD no HostGator.
	// const HOSTNAME = "127.0.0.1";
	// const USERNAME = "root";
	// // No meu banco não coloquei senha
	// const PASSWORD = "";
	// const DBNAME = "db_ecommerce";

	const HOSTNAME = "108.167.132.228";
	const USERNAME = "clas4319_user";
	// No meu banco não coloquei senha
	const PASSWORD = "WVB9K&mJ]%Du";
	const DBNAME = "clas4319_db";
	
	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, 
			Sql::USERNAME,
			Sql::PASSWORD
		);

	}

	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

	}

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>