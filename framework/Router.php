<?php

require_once("framework/CliRouter.php");

class Router {

  protected $internalRouter;

  public function __construct() {
    // debug
    // change dynamically based on context
    $this->internalRouter = new CliRouter();
    // end of debug
  }
  
  public function getRoute($arg) {
    return $this->internalRouter->getRoute($arg);
  }

}

?>