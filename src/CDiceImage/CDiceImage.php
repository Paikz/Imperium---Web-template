<?php

class CDiceImage extends CDice {

  const FACES = 6;

  public function __construct() {
    parent::__construct(self::FACES);
  }

  public function printDice() {

      $html = "<ul class='dice'>";
      foreach($this->rolls as $value) {
          $html .= "<li class='dice-$value'></li>";
      }
      $html .= "</ul>";

      return $html;
  }

}
