<?php

abstract class BaseController {

  public function __construct() {
  }

  // actionnameAction is actual action.
  // actionnamePre is called before actual action called by dispatcher.
  // actionnamePost is called after actual action called by dispatcher.

  // $methodName is name of action.
  // $arguments is arguments as hash.
  // typically, this method is used to handle cache.
  abstract public function preAction($actionName, $arguments);
  
  abstract public function postAction($actionName, $arguments);

}

?>