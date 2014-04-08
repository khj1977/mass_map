<?php

class Conf {

  protected $internalConf;

  public function __construct() {
  }
  
  public function setStage($stage) {
    if ($stage == "production") {
      require_once("conf/production/Conf.php");
    }
    else {
      require_once("conf/development/Conf.php");
    }
    
    $this->internalConf = new InternalConf();
  }

  public function __call($methodName, $arguments) {
    return $this->internalConf->conf[$methodName];
  }
}

?>