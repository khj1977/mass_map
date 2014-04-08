<?php

class InternalConf {

  public $conf;

  public function __construct() {
    $this->conf = array();
  
    $this->conf["base_path"] = dirname(__FILE__) . "/../../";
  
    $this->conf["master_host"] = "";
    $this->conf["master_port"] = "";
    $this->conf["master_user"] = "";
    $this->conf["master_pass"] = "";
    $this->conf["master_db"] = "";
  
    $this->conf["slave_hsot"] = "";
    $this->conf["slave_port"] = "";
    $this->conf["slave_user"] = "";
    $this->conf["slave_pass"] = "";
    $this->conf["slave_db"] = "";

    $this->conf["gmaps_key"] = "";
    $this->conf["gmaps_host"] = "maps.google.com";

    $this->conf["log_path"] = $this->conf["base_path"] . "/logs/";
  }
  
  

}

?>