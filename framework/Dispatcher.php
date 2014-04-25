<?php

require_once("framework/Env.php");
require_once("framework/Router.php");

class Dispatcher {

  protected $module;
  protected $controller;
  protected $action;

  public function __construct() {
  }
  
  public function dispatch($arguments) {
    $router = new Router();
    // map URL to module, controller, and action.
    $route = $router->getRoute($arguments);
  
    $this->module = urldecode($route["m"]);
    $this->controller = urldecode($route["c"]);
    $this->action = urldecode($route["a"]);
    
    $basePath = Env::instance()->basePath();
    $controllerPath = $basePath . sprintf("/app/%s/controllers/%sController.php",
                                          $this->module,
                                          ucwords($this->controller));
  
    require_once($controllerPath);
    
    $controllerClassName = $this->controller . "Controller";
    $controller = new $controllerClassName();
    
    $actionName = $this->action;

    $controller->preAction();
    $actionResult =  $controller->$actionName($arguments);
    $controller->postAction();

    return $actionResult;
  }
  
  public function getViewPath() {
    $viewPath = Env::instance()->basePath() . sprintf("/app/%s/views/%s/%s.kml", 
      $this->module,
       $this->controller,
       $this->action);
       
    return $viewPath;
  }

}

?>