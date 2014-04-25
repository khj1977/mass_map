<?php

$dir = dirname(__FILE__);
set_include_path($dir . "/../" . ":"  . "/usr/share/pear/:" . get_include_path());

require_once("framework/Env.php");
require_once("framework/Dispatcher.php");

abstract class BaseApplication {

  public function __construct() {
  }
  
  // result of merged $_GET and $_POST or $argv
  public function run($args) {
    Env::instance()->setStage(getenv($this->getAppName()));
      
    // $args = $this->getArgs();
    // execute action on controller
    $dispatcher = new Dispatcher();
    

    $this->preRun();
    $viewVals = $dispatcher->dispatch($args);
    $this->postRun();
    
    $viewPath = $dispatcher->getViewPath();
    require_once($viewPath);
  }
  
  protected function preRun($args) {  
    Env::instance()->getLogger()->log("info", sprintf("pre-run¥t%s¥t%s", $this->getAppName(), date("Y:m:d-H:i:s")));
    $this->xpreRun($args);
  }
  
  protected function postRun($args) {
    Env::instance()->getLogger()->log("info", sprintf("post-run¥t%s¥t%s", $this->getAppName(), date("Y:m:d-H:i:s")));
    
    $this->xpostRun($args);
  }
  
  protected function getAppName() {
    return get_class($this);
  }
  
  abstract protected function xpreRun($args);
  
  abstract protected function xpostRun($args);

}

?>