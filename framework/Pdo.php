<?php

require_once("framework/Env.php");

// used as master or slave instance val of Env class.
// typically, initialized by instance of BaseApplication.
class My_Pdo {

  protected $pdo;

  public function __construct($pdo) {
    $this->pdo = $pdo;
  }
  
  // return My_PdoStatement object
  // take log of preapred sql
  public function prepare($sql) {
    Env::instance()->getLogger()->log("info", "prepare:¥t" . $sql);
  
    $rawStatement = $this->pdo->prepare($sql);
    $statement = new My_PdoStatement($rawStatement, $sql);
    
    return $statement;
  }
  
  public function query($sql) {
    Env::instance()->getLogger()->log("info", "query:¥t" . $sql);
    
    $this->pdo->query($sql);
  }

}

?>