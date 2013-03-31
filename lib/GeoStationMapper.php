<?php

// GeoMapperと同じIFだが、基底クラスは特に作らない
// ただ、入れ替えて使う事を想定している。あとでリファクタリング
// Factoryクラスかなんかで動的にGeoMapperかGeoStationMapperか
// 動的に入れ替えたい。
class GeoStationMapper {

  protected $map;

  public function __construct($path) {
    $stream = fopen($path, "r");
    if ($stream == false) {
      throw new Exception("GeoStationMapper::__construct(): opening file has been failed.");
    }

    $this->map = array();
    while($line = chop(fgets($stream))) {
      $splitted = explode(",", $line);
      
      $key = $splitted[0];
      $this->map[$key] = array("geo2" => $splitted[2], "geo1" => $splitted[1]);
    }

    fclose($stream);
  }

  public function map($key) {
    if (!array_key_exists($key, $this->map)) {
      return false;
    }

    return $this->map[$key];
  }

}

?>