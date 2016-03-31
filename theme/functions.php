<?php

function get_title($title) {
  global $imperium;
  return $title . (isset($anax['title_append']) ? $anax['title_append'] : null);
}
