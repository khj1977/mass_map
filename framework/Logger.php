<?php

require_once("framework/Env.php");

class Logger {

  public function __construct() {
  }
  
  public function log($level, $message) {
    $path = Env::instance()->logPath() . date("Ymd") . ".log";
    $stream = fopen($path, "a");
    if ($stream === false) {
      Env::instance()->handleError();
    }
    
    $realMessage = date("Ymd-G:i:s") . "¥t". $message;
    
    fwrite($stream, $realMessage);
    
    fclose($stream);
  }

}

?>