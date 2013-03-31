<?php

require_once("framework/BaseController.php");
require_once("app/media/models/ProtoModel.php");
require_once("framework/Env.php");
require_once("lib/GeoMapper.php");
require_once("lib/GeoStationMapper.php");
require_once("lib/Util.php");

class ProtoController extends BaseController {

  protected $nhkFormatFunc;
  protected $asahiFormatFunc;
  protected $jccFormatFunc;

  public function __construct() {
    $this->asahiFormatFunc = function($line) {
      $splitted = explode("\t", $line);
      if (count($splitted) < 7) {
        return false;
      }
      $data = $splitted[6];
      $title = $splitted[5];
      $date = $splitted[0];
      $mediaName = $splitted[1];
      $writtenDate = $date;

      // format date
      $startYear = substr($date, 0, 4);
      $startMonth = substr($date, 4, 2);
      $startDay = substr($date, 6, 2);

      return array("data" => $data, "title" => $title, "media_name" => $mediaName, "written_date" => $writtenDate, "start_year" => $startYear, "start_month" => $startMonth, "start_day" => $startDay, "start_hour" => "00", "start_min" => "00", "written_date" => $date, "style_media" => "#asahi_shinbun");
    };

    $this->jccFormatFunc = function($line) {
      $splitted = explode("\t", $line);
      if (count($splitted) < 7) {
        return false;
      }
      $data = $splitted[7];
      $title = $splitted[3];
      $date = $splitted[1];
      $mediaName = $splitted[0];
      $writtenDate = $date;

      // format date
      $splitted2 = explode(" ", $date);
      $splitted3 = explode("-", $splitted2[0]);
      $startYear = $splitted3[0];
      $startMonth = Util::handleZero($splitted3[1]);
      $startDay = Util::handleZero($splitted3[2]);

      $splitted4 = explode(":", $splitted2[1]);
      $startHour = Util::handleZero($splitted4[0]);
      $startMin = Util::handleZero($splitted4[1]);

      if (preg_match("/NHK総合/", $mediaName)) {
        $styleMedia = "#nhk";
      }
      else if (preg_match("/テレビ朝日/", $mediaName)) {
        $styleMedia = "#tvasahi";
      }
      else if (preg_match("/日本テレビ/", $mediaName)) {
        $styleMedia = "#nihontv";
      }
      else if (preg_match("/フジテレビ/", $mediaName)) {
        $styleMedia = "#fujitv";
      }
      else if (preg_match("/TBS/", $mediaName)) {
        $styleMedia = "#tbs";
      }
      else if (preg_match("/テレビ東京/", $mediaName)) {
        $styleMedia = "#tvtokyo";
      }

      return array("data" => $data, "title" => $title, "media_name" => $mediaName, "written_date" => $writtenDate, "start_year" => $startYear, "start_month" => $startMonth, "start_day" => $startDay, "start_hour" => $startHour, "start_min" => $startMin, "written_date" => $date, "style_media" => $styleMedia);
    };

    $this->nhkFormatFunc = function($line) {
      $splitted = explode(",", $line);
      if (count($splitted) < 7) {
        return false;
      }
      $data = $splitted[5];
      $title = $splitted[3];
      $date = $splitted[0];
      $mediaName = "NHK";
      $writtenDate = $date;
      
      // format date
      $splitted2 = explode(" ", $date);
      $splitted3 = explode("/", $splitted2[0]);

      if (count($splitted3) < 3) {
        return false;
      }
      $startYear = $splitted3[0];
      $startMonth = Util::handleZero($splitted3[1]);
      $startDay = Util::handleZero($splitted3[2]);

      $splitted4 = explode(":", $splitted2[1]);
      $startHour = Util::handleZero($splitted4[0]);
      $startMin = Util::handleZero($splitted4[1]);

      return array("data" => $data, "title" => $title, "media_name" => $mediaName, "written_date" => $writtenDate, "start_year" => $startYear, "start_month" => $startMonth, "start_day" => $startDay, "start_hour" => $startHour, "start_min" => $startMin, "written_date" => $date, "style_media" => "#nhk");
    };
    
  }

  public function media($args) {

    $kindMedia = $args["misc"][0];
    // default format func is nhk.
    
    if ($kindMedia == "nhk") {
      $formatFunc = $this->nhkFormatFunc;
      $path = "nhk.csv";
    }
    else if ($kindMedia == "asahi") {
      $formatFunc = $this->asahiFormatFunc;
      $path = "asahi-utf8.txt";
    }
    else if ($kindMedia == "jcc") {
      $formatFunc = $this->jccFormatFunc;
      $path = "311jcc_tv_news-utf8.txt";
    }

    // geoMapper can be station palce mapper.
    // $geoMapper = new GeoMapper(Env::instance()->getConf()->base_path() . "/data/chimei-geo.txt");
    $geoMapper = new GeoStationMapper(Env::instance()->getConf()->base_path() . "/data/stationlist.csv");

    $model = new ProtoModel();

    return $model->media($args, $geoMapper, $formatFunc, $path);
  }

  public function nhk($args) {
    $model = new ProtoModel();

    return $model->nhk($args);
  }

  public function nhkStation($args) {
    $model = new ProtoModel();

    return $model->nhkStation($args);
  }


  public function ntv($args) {
    $model = new ProtoModel();

    return $model->ntv($args);
  }


  public function preAction($actionName, $arguments) {
    // Do nothing.
  }

  public function postAction($actionName, $arguments) {
    // Do nothing.
  }
  
}

?>