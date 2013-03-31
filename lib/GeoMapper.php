<?php

class GeoMapper {

  protected $map;

  // file format is chimei\tgeo1^tgeo2
  public function __construct($path) {
    $stream = fopen($path, "r");
    if ($stream == false) {
      throw new Exception("GeoMapper::__construct(): opening file has been failed.");
    }

    $this->map = array();
    while($line = chop(fgets($stream))) {
      $splitted = explode("\t", $line);
      
      $key = $splitted[3];
      $this->map[$key] = array("geo2" => $splitted[10], "geo1" => $splitted[11]);
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