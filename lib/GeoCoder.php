<?php

require_once("framework/Env.php");

class GeoCoder {

    public function __construct() {
    }

    public function search($location) {
      $conf = Env::instance()->getConf();
      $base_url = "http://" . $conf->conf["gmaps_host"] . "/maps/geo?output=xml" . "&key=" . $conf->conf["gmaps_key"];

      // $id = $row["id"];
      $request_url = $base_url . "&q=" . urlencode($location);
      $xml = simplexml_load_file($request_url);
  
      $status = $xml->Response->Status->code;
      if (strcmp($status, "200") != 0) {
        throw new Exception("Requesting geo code has been failed: " . $location);
      }

      $geocode_pending = false;
      $coordinates = $xml->Response->Placemark->Point->coordinates;
      $coordinatesSplit = split(",", $coordinates);

      $geo1 = $coordinatesSplit[1];
      $geo2 = $coordinatesSplit[0];
  
      return array("geo1" => $geo1, "geo2" => $geo2);
    }

}

?>