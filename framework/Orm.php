<?php

class Orm {

  public function __construct() {
  }
  
  
  // dispatch missing method to virtual method for getter and setter
  public function __call($name, $arguments) {
  }

}

?>