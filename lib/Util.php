<?php

class Util {

  static public function handleZero($val) {
    if (strlen($val) == 1) {
      $val = "0" . $val;
    }

    return $val;
  }

}

?>