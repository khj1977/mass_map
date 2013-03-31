<?php

require_once("../framework/BaseApplication.php");

class ProtoKmlApp extends BaseApplication {

  protected function xpreRun($args) {
  }

  protected function xpostRun($args) {
  }

}

if ($argc < 5) {
  printf("usage; php ProtoKmlApp.php module controller action kind\nSuch as php ProtoKmlApp.php media proto media nhk\n");
  exit;
}

$module = $argv[1];
$controller = $argv[2];
$action = $argv[3];

$app = new ProtoKmlApp();
$app->run(array("m" => "media", "c" => "proto", "a" => "media", "misc" => array($argv[4])
            ));
// $app->run(array("m" => "media", "c" => "proto", "a" => "nhkStation"));
// $app->run(array("m" => $module, "c" => $controller, "a" => $action));

?>