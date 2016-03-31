<?php

class CDice {

    protected $rolls = array();
    private $faces;
    private $last;

    public function __construct($faces = 6) {
        $this->faces = $faces;
    }

    public function rollDice($times = 1) {

        $this->rolls = array();

        for ($i=0; $i < $times; $i++) {
            $this->last = rand(1,$this->faces);
            $this->rolls[] = $this->last;
        }

        return $this->last;

    }

    public function getTotal() {
        return array_sum($this->rolls);
    }

    public function getAverage() {
        return $this->getTotal() / count($this->rolls);
    }

    public function initRound() {
      $this->sumRound = 0;
    }

    public function getLast() {
      return $this->last;
    }

    public function getRolls() {
        return $this->rolls;
    }

    public function getFaces() {
        return $this->faces;
    }
}
