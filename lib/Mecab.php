<?php
dl('mecab.so');

// test
class PC_Mecab {
    protected $tagger;

    public function __construct() {
      $this->tagger = new MeCab_Tagger();
    }

    public function parse($str) {
    $parsed = $this->tagger->parse($str);
    $lines = explode("\n", $parsed);

    $words = array();
    foreach($lines as $line) {
      $splitted1 = explode("\t", $line);
      if (count($splitted1) < 2) {
        continue;
      }

      $word = $splitted1[0];
      $splitted2 = explode(",", $splitted1[1]);
      if (count($splitted2) < 7) {
        continue;
      }

      $hinshi = $splitted2[0];
      $genkei = $splitted2[6];

      if (preg_match("/^[あ-ん]*$/", $genkei) != 0) {
        continue;
      }

      if (mb_strlen($genkei) == 1) {
        continue;
      }

      $words[] = array("word" => $word,"genkei" => $genkei, "hinshi" => $hinshi);
  }


      return $words;
    }
    

  }

?>