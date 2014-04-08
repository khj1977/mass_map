<?php

require_once("framework/NullCache.php");
require_once("framework/Logger.php");
require_once("framework/Pdo.php");
// debug
// change conf with respect to stage.
require_once("framework/Conf.php");
// end of debug

class Env {

  protected $master;
  protected $slave;
  protected $memcache;
  protected $logger;
  protected $conf;
  
  protected $stage;

  static protected $instance = null;

  protected function __construct() {
    $this->conf = new Conf();
    // debug
    $this->conf->setStage("development");
    // end of debug

    /*
    $this->master = new My_Pdo(new PDO("mysql:dbname=" . Conf::MASTER_DB . ";host=" . Conf::MASTER_HOST, Conf::MASTER_USER, Conf::MASTER_PASS));
    $this->slave = new My_Pdo(new PDO("mysql:dbname=" . Conf::SLAVE_DB . ";host=" . Conf::SLAVE_HOST, Conf::SLAVE_USER, Conf::SLAVE_PASS));
    
    $this->memcache = new NullCache();
    */

    $this->logger = new Logger();
  }

  public function instance() {
    if (Env::$instance == null) {
      Env::$instance = new Env();    
    }

    return Env::$instance;
  }
  
  public function getConf() {
    return $this->conf;
  }
  
  public function setStage($stage) {
    $this->stage = $stage;
    // $this->conf->setStage($stage);
  }
  
  public function getMaster() {
    return $this->master;
  }
  
  public function getSlave() {
    return $this->slave;
  }
  
  public function getCache() {
    return $this->memcache;
  }
  
  public function getLogger() {
    return $this->logger;
  }
  
  public function handleError() {
    // Do nothing at this time.
  }
  
  public function logPath() {
    return $this->conf->log_path();
  }

  public function basePath() {
    return dirname(__FILE__) . "/../";
  }

}

?>