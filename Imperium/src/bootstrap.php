<?php

function dump($array) {
  echo "<pre>" . htmlentities(print_r($array, 1)) . "</pre>";
}

function myExceptionHandler($exception) {
    echo "Imperium: Uncaught exception: <p>" . $exception->getMessage() . "</p><pre>" . $exception->getTraceAsString(), "</pre>";
}
set_exception_handler('myExceptionHandler');

//autoloader

function myAutoloader($class) {
  $path = IMPERIUM_INSTALL_PATH . "/src/{$class}/{$class}.php";
  if(is_file($path)) {
    include($path);
  }
  else {
    throw new Exception("Classfile '{$class}' does not exists.");
  }
}
spl_autoload_register('myAutoloader');
