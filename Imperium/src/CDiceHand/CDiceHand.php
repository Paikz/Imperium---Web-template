<?php

class CDiceHand {

  private $dices;
  private $numDices;
  private $saved;
  private $sumRound;
  private $lastRoll;

  public function __construct($numDices = 5) {
    for($i=0; $i < $numDices; $i++) {
      $this->dices[] = new CDiceImage();
    }
    $this->numDices = $numDices;
    $this->saved = 0;
    $this->sumRound = 0;
    $this->lastRoll = 0;
  }

  public function roll() {
    $this->sum = 0;
    for($i=0; $i < $this->numDices; $i++) {
      $this->lastRoll = $this->dices[$i]->rollDice(1);
      $this->sumRound += $this->lastRoll;
    }
  }

  public function savePoints() {
    $this->saved += $this->sumRound;
    $this->sumRound = 0;
  }

  public function getSavedPoints() {
    return $this->saved;
  }

  public function initRound() {
    $this->sumRound = 0;
    $this->saved = 0;
  }

  public function getRoundTotal() {
    return $this->sumRound;
  }

  public function setRoundTotal($number) {
    $this->sumRound = $number;
  }

  public function getLastRoll() {
    return $this->lastRoll;
  }

  public function GetRollsAsImageList() {
  $html = "<ul class='dice'>";
  foreach($this->dices as $dice) {
    $val = $dice->GetLast();
    $html .= "<li class='dice-{$val}'></li>";
  }
  $html .= "</ul>";
  return $html;
  }

}
