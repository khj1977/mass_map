<?php

require_once("framework/BaseModel.php");
require_once("lib/Mecab.php");
require_once("lib/GeoMapper.php");
require_once("lib/GeoStationMapper.php");

class ProtoModel extends BaseModel {

  public function __construct() {
  }

  // nhk
  // format func: 形態素解析前の行をsplitしてデータや日付に分割して
  // ハッシュで返す
  public function media($args, $mapper, $formatFunc, $path, $limit = null) {
    $mecab = new PC_Mecab();

    $targetFile = Env::instance()->getConf()->base_path() . "/data/" . $path;
    $stream = fopen($targetFile, "r");
    if ($stream == false) {
      throw new Exception("ProtoModel::tv(): opening file has been failed: " . $targetFile);
    }

    $result = array();
    // for prototyping purpose, limit upper bound of data to the following value.
    $i = 0;
    while($line = fgets($stream)) {
      if ($i >= $limit) {
        // break;
      }

      $line = chop($line);

      // debug
      // map data with actual data of news or tv using explode or something like that.

      $formatted = $formatFunc($line);
      if ($formatted === false) {
        continue;
      }
      
      $data = $formatted["data"];
      $title = $formatted["title"];
      $mediaName = $formatted["media_name"];
      $writtenDate = $formatted["written_date"];

      $startYear = $formatted["start_year"];
      $startMonth = $formatted["start_month"];
      $startDay = $formatted["start_day"];
      $startHour = $formatted["start_hour"];
      $startMin = $formatted["start_min"];
      $styleMedia = $formatted["style_media"];

      if (preg_match("/[nN][hH][kK]/", $mediaName)) {
        $typeMedia = "#nhk";
      }
      else if (preg_match("/テレビ朝日/", $mediaName)) {
        $typeMedia = "#tvasahi";
      }
      else if (preg_match("/日本テレビ/", $mediaName)) {
        $typeMedia = "#nihontv";
      }
      else if (preg_match("/フジテレビ/", $mediaName)) {
        $typeMedia = "#fujitv";
      }
      else if (preg_match("/TBS/", $mediaName)) {
        $typeMedia = "#tbs";
      }
      else if (preg_match("/テレビ東京/", $mediaName)) {
        $typeMedia = "#tvtokyo";
      }
      else {
        $typeMedia = "not_defined";
      }
      
      

      // end of debug
      $kkeds = $mecab->parse($data);

      $targetWords = array("駅" => true, "電車" => true, "在来線" => true, "新幹線" => true, "地下鉄" => true);
      $hasTargetWord = false;

      $hasGeoDatas = false;
      $geoDatas = array();
      // 下記の処理は共通。あとで駅名の処理を無名関数で入れる。
      foreach($kkeds as $kked) {
        // handle only noun
        if ($kked["hinshi"] != "名詞") {
          continue;
        }

        if (array_key_exists($kked["word"], $targetWords)) {
          $hasTargetWord = true;
        }

        $geos = $mapper->map($kked["word"]);
        if ($geos == false) {
          continue;
        }
        else {
          $hasGeoDatas = true;
          $geoDatas[] = $geos;
        }
        

      }
      if ($hasTargetWord === false) {
        // continue;
      }

      if ($hasGeoDatas === true) {
        // 複数地点は$geoDatasが配列なのでそれで処理。
        $result[] = array("data" => $data, "geos" => $geoDatas, "date" => $writtenDate, "media_name" => $mediaName, "written_date" => $writtenDate, "title" => $title, "start_year" => $startYear, "start_month" => $startMonth, "start_day" => $startDay, "start_hour" => $startHour, "start_min" => $startMin, "type_media" => $typeMedia, "style_media" => $styleMedia);
      }
      

      ++$i;
    }

    fclose($stream);

    // return array of data for KML.
    return $result;
  }

  public function nhkStation($args) {
    $mecab = new PC_Mecab();
    $geoMapper = new GeoStationMapper(Env::instance()->getConf()->base_path() . "/data/stationlist.csv");

    // for prototyping, use fixed path
    // $targetFile = Env::instance()->getConf()->base_path() . "/data/311jcc_tv_news-utf8.txt";
    $targetFile = Env::instance()->getConf()->base_path() . "/data/nhk.csv";
    $stream = fopen($targetFile, "r");
    if ($stream == false) {
      throw new Exception("ProtoModel::tv(): opening file has been failed: " . $targetFile);
    }

    $result = array();
    // for prototyping purpose, limit upper bound of data to the following value.
    $limit = 3000;
    $i = 0;
    while($line = fgets($stream)) {
      if ($i >= $limit) {
        break;
      }

      $line = chop($line);

      // debug
      // map data with actual data of news or tv using explode or something like that.

      // 年月日
      // 記事の見出し
      // メディア名(固定)
      // 記事の書かれた日時 (年月日と同一)
      // protoでは複数の位置情報を取り扱わない
      $splitted = explode(",", $line);
      $hasTargetWord = false;
      $targetWords = array("駅" => true, "電車" => true, "在来線" => true, "新幹線" => true, "地下鉄" => true);

      $hasGeo = false;

      if (count($splitted) < 7) {
        continue;
      }
      $data = $splitted[5];
      $title = $splitted[3];
      $date = $splitted[0];
      $mediaName = "NHK";
      $writtenDate = $date;

      // format date
      /*
      $year = substr($date, 0, 4);
      $month = substr($date, 4, 2);
      $day = substr($date, 6, 2);
      */
      $splitted2 = explode(" ", $date);
      $splitted3 = explode("/", $splitted2[0]);
      $year = $splitted3[0];
      $month = $this->handleZero($splitted3[1]);
      $day = $this->handleZero($splitted3[2]);

      $splitted4 = explode(":", $splitted2[1]);
      $hour = $this->handleZero($splitted4[0]);
      $min = $this->handleZero($splitted4[1]);

      // end of debug
      $kkeds = $mecab->parse($data);

      foreach($kkeds as $kked) {
        // handle only noun
        if ($kked["hinshi"] != "名詞") {
          continue;
        }

        if (array_key_exists($kked["word"], $targetWords)) {
          $hasTargetWord = true;
        }

        $geos = $geoMapper->map($kked["word"]);
        if ($geos != false) {
          $hasGeo = true;
          $realGeos = $geos;
        }
      }

      if ($hasTargetWord === false) {
        continue;
      }
      
      if ($hasGeo !== true) {
        continue;
      }

      $result[] = array("data" => $data, "geos" => $realGeos, "date" => $date, "media_name" => $mediaName, "written_date" => $writtenDate, "title" => $title, "year" => $year, "month" => $month, "day" => $day, "hour" => $hour, "min" => $min);
      

      ++$i;
    }

    fclose($stream);

    // return array of data for KML.
    return $result;
  }


  public function ntv($args) {
    $mecab = new PC_Mecab();
    $geoMapper = new GeoMapper(Env::instance()->getConf()->base_path() . "/data/chimei-geo.txt");

    // for prototyping, use fixed path
    $targetFile = Env::instance()->getConf()->base_path() . "/data/311jcc_tv_news-utf8.txt";
    $stream = fopen($targetFile, "r");
    if ($stream == false) {
      throw new Exception("ProtoModel::tv(): opening file has been failed: " . $targetFile);
    }

    $result = array();
    // for prototyping purpose, limit upper bound of data to the following value.
    $limit = 3000;
    $i = 0;
    while($line = fgets($stream)) {
      if ($i >= $limit) {
        break;
      }

      $line = chop($line);

      // debug
      // map data with actual data of news or tv using explode or something like that.

      // 年月日
      // 記事の見出し
      // メディア名(固定)
      // 記事の書かれた日時 (年月日と同一)
      // protoでは複数の位置情報を取り扱わない
      $splitted = explode("\t", $line);
      if (count($splitted) < 7) {
        continue;
      }
      $data = $splitted[7];
      $title = $splitted[3];
      $date = $splitted[1];
      $mediaName = $splitted[0];
      $writtenDate = $date;

      // format date
      /*
      $year = substr($date, 0, 4);
      $month = substr($date, 4, 2);
      $day = substr($date, 6, 2);
      */
      $splitted2 = explode(" ", $date);
      $splitted3 = explode("-", $splitted2[0]);
      $year = $splitted3[0];
      $month = $this->handleZero($splitted3[1]);
      $day = $this->handleZero($splitted3[2]);

      $splitted4 = explode(":", $splitted2[1]);
      $hour = $this->handleZero($splitted4[0]);
      $min = $this->handleZero($splitted4[1]);

      // end of debug
      $kkeds = $mecab->parse($data);

      foreach($kkeds as $kked) {
        // handle only noun
        if ($kked["hinshi"] != "名詞") {
          continue;
        }

        $geos = $geoMapper->map($kked["word"]);
        if ($geos == false) {
          continue;
        }

        $result[] = array("data" => $data, "geos" => $geos, "date" => $date, "media_name" => $mediaName, "written_date" => $writtenDate, "title" => $title, "year" => $year, "month" => $month, "day" => $day, "hour" => $hour, "min" => $min);
      }

      ++$i;
    }

    fclose($stream);
    
    // return array of data for KML.
    return $result;
  }


  public function test($args) {
    $mecab = new PC_Mecab();
    $geoMapper = new GeoMapper(Env::instance()->getConf()->base_path() . "/data/chimei-geo.txt");

    // for prototyping, use fixed path
    $targetFile = Env::instance()->getConf()->base_path() . "/data/asahi-utf8.txt";
    // $targetFile = Env::instance()->getConf()->base_path() . "/data/asahi-utf8.txt";
    $stream = fopen($targetFile, "r");
    if ($stream == false) {
      throw new Exception("ProtoModel::test(): opening file has been failed: " . $targetFile);
    }

    $result = array();
    // for prototyping purpose, limit upper bound of data to the following value.
    $limit = 50;
    $i = 0;
    while($line = fgets($stream)) {
      if ($i >= $limit) {
        break;
      }

      $line = chop($line);

      // debug
      // map data with actual data of news or tv using explode or something like that.

      // 年月日
      // 記事の見出し
      // メディア名(固定)
      // 記事の書かれた日時 (年月日と同一)
      // protoでは複数の位置情報を取り扱わない
      $splitted = explode("\t", $line);
      if (count($splitted) < 7) {
        continue;
      }
      $data = $splitted[6];
      $title = $splitted[5];
      $date = $splitted[0];
      $mediaName = $splitted[1];
      $writtenDate = $date;

      // format date
      $year = substr($date, 0, 4);
      $month = substr($date, 4, 2);
      $day = substr($date, 6, 2);

      // end of debug
      $kkeds = $mecab->parse($data);

      foreach($kkeds as $kked) {
        // handle only noun
        if ($kked["hinshi"] != "名詞") {
          continue;
        }

        $geos = $geoMapper->map($kked["word"]);
        if ($geos == false) {
          continue;
        }

        $result[] = array("data" => $data, "geos" => $geos, "date" => $date, "media_name" => $mediaName, "written_date" => $writtenDate, "title" => $title, "year" => $year, "month" => $month, "day" => $day);
      }

      ++$i;
    }

    fclose($stream);

    // return array of data for KML.
    return $result;
  }
  
  protected function handleZero($target) {
    if (strlen($target) == 1) {
      return "0" . $target;
    }

    return $target;
  }

}

?>