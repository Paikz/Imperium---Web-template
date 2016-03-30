<?php

error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly

//define imperium paths
define('IMPERIUM_INSTALL_PATH', __DIR__ . '/..');
define('IMPERIUM_THEME_PATH', IMPERIUM_INSTALL_PATH . '/theme/render.php');

include(IMPERIUM_INSTALL_PATH . '/src/bootstrap.php');

//Start session
session_name(preg_replace('/[^a-z\d]/i', '', __DIR__));
session_start();

//Create imperium variable

$imperium = array();

//Dynamic navbar
$menu = array(
  'home'  => array('text'=>'Home',  'url'=>'?page=home'),
  'away'  => array('text'=>'Away',  'url'=>'?page=away'),
  'about' => array('text'=>'About', 'url'=>'?page=about'),
);

class CNavigation {
    public static function GenerateMenu($items, $class) {
      $html = "<nav class='$class'>\n";
      foreach($items as $key => $item) {
        $selected = (isset($_GET['page'])) && $_GET['page'] == $key ? 'selected' : null;
        $html .= "<a href='{$item['url']}' class='{$selected}'>{$item['text']}</a>\n";
      }
      $html .= "</nav>\n";
      return $html;
    }
}

//Global settings

$imperium['lang'] = 'sv';
$imperium['title_append'] = ' | Imperium - webbtemplate';
$imperium['stylesheets'] = array('css/style.css');
$imperium['favicon'] = 'img/favicon.ico';
$imperium['modernizr'] = 'js/modernizr.js';
$imperium['jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js';
//$anax['jquery'] = null; // To disable jQuery
$imperium['javascript_include'] = array();
//$anax['javascript_include'] = array('js/main.js'); // To add extra javascript files
$imperium['google_analytics'] = 'UA-22093351-1'; // Set to null to disable google analytics
