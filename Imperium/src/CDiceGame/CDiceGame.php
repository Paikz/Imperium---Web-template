<?php

class CDiceGame {

  private $hand;
  private $gameOver;
  private $lastRoll;

  public function __construct() {
    $this->hand = $this->checkSession();
    $this->gameOver = false;
    $this->lastRoll = 0;
  }

  public function play() {

    $roll = isset($_GET['roll']) ? true : false;
    $init = isset($_GET['init']) ? true : false;
    $saved = isset($_GET['saved']) ? true : false;

    $this->executeChoice($roll, $init, $saved);

    $html = "<div class='diceGame'>";
    $html .= "<h1>Dice Game 100</h1>";
    $html .= "<div class='diceChoice'>";
    $html .= "<a class='choice-1' href='?init'>Restart round</a>";

    if ($this->hand->getSavedPoints() >= 100 || $this->hand->getSavedPoints() + $this->hand->getRoundTotal() >=100) {

        $this->gameOver = true;
    }

    if ($this->gameOver == false) {
      $html .= "<a class='choice-2' href='?roll'>Roll dice</a>";
      $html .= "<a class='choice-3' href='?saved'>Save unsafe points</a>";
    }

    $html .= "</div>";

    $html .= "<div class='dicePoints'>";
    $html .= '<p class="unsafePoints">Unsafe points: ' . $this->hand->getRoundTotal() . "</p>";
    $html .= '<p class="savedPoints">Saved points: ' . $this->hand->getSavedPoints() . "</p>";
    $html .= $this->hand->GetRollsAsImageList();
    $html .= "</div>";

    if ($this->gameOver == true) {
      $html .= "<br>" . '<h1>Game over - You won!</h1>';
    }

    $html .= "<div class='textField'>";
    $html .= "<p>Rules : Dice Game 100 is a simple but fun game of dice. The goal is to reach 100 points by rolling a dice. Watch out though: rolling a #1 results in a loss of all your unsafe points. Make sure to save your unsafe points when you're not feeling lucky. </p>";
    $html .= "</div>";

    $html .= "</div>";

    return $html;

  }

  public function checkSession() {
    if(isset($_SESSION['dicehand'])) {
      $this->hand = $_SESSION['dicehand'];
    }
    else {
      $this->hand = new CDiceHand(1);
      $_SESSION['dicehand'] = $this->hand;
    }
    return $this->hand;
  }

  public function executeChoice($roll, $init, $saved) {
    if ($roll == true) {
      $this->hand->roll();
      $this->lastRoll = $this->hand->getLastRoll();
      if ($this->lastRoll == 1) {
        $this->hand->setRoundTotal(0);
      }
    } else if ($init == true) {
      $this->hand->initRound();
    } else if ($saved == true) {
      $this->hand->savePoints();
    }
  }

}
