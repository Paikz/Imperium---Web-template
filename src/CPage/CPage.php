<?php

class CPage extends CContent {

  function __construct($database) {
    $this->db = $database;
  }

  function getPageContent($url) {
    $qry = "SELECT * FROM Content WHERE TYPE = 'page' AND url = '$url' AND published <= NOW();";
    $res = $this->db->ExecuteSelectQueryAndFetchAll($qry);

    if(isset($res[0])) {
      $c = $res[0];
    }
    else {
      die('Misslyckades: det finns inget innehåll.');
    }

    return $c;
  }
}
