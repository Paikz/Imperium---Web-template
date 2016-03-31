<?php

class CHistogram{

    private $histogram = [];

    public function getHistogram($rolls){

        foreach ($rolls as $value) {
            if (!isset($this->histogram[$value])) {
                $this->histogram[$value] = "*";
            }else {
                $this->histogram[$value] .= "*";
            }
        }

        asort($this->histogram);

        $html = "<ul>";
        foreach($this->histogram as $value) {
            $html .= "<li>{$value} (" . strlen($value) . ")";
        }
        $html .= "</ul>";

        return $html;

    }

    public function getHistogramIncludeEmpty($rolls, $max) {

        foreach ($rolls as $key => $value) {
            if (!isset($this->histogram[$value])) {
                $this->histogram[$value] = "*";
            }else {
                $this->histogram[$value] .= "*";
            }
        }

        ksort($this->histogram);

        $html = "<ol>";

        for ($i=1; $i <= $max ; $i++) {
            $value = isset($this->histogram[$i]) ? $this->histogram[$i] : null;
            $html .= "<li>{$value}</li>";
        }
        $html .= "</ol>";

        return $html;

    }
}
