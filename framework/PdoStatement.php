<?php

require_once("framework/Env.php");

class My_PdoStatement {

	protected $statement;
	protected $sql;
	
	public function __construct($statment, $sql) {
		$this->statement = $statement;
		$this->sql = $sql;
	}
	
	public function execute($arguments) {
		Env::instance()->logger()->log("info", $sql);
		
		$this->statement->execute($arguments);
	}

}

?>