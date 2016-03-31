<?php

class CInspect {

    protected $db;

    function __construct($db) {
        $this->db = $db;
    }

    function selectContentWithID($id) {
      $c = null;
      $sql = 'SELECT * FROM VMovie WHERE id = ?';
      $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));

      if(isset($res[0])) {
        $c = $res[0];
      }
      else {
        die('Misslyckades: det finns inget innehåll med sådant id.');
      }

      return $c;
    }

    function printInspectInfo($res) {
      if(isset($res)) {
        $html = null;

          // Sanitize content before using it.
          $id = htmlentities($res->id, null, 'UTF-8');
          $title  = htmlentities($res->title, null, 'UTF-8');
          $director =  htmlentities($res->director, null, 'UTF-8');
          $length = htmlentities($res->length, null, 'UTF-8');
          $year = htmlentities($res->year, null, 'UTF-8');
          $plot = htmlentities($res->plot, null, 'UTF-8');
          $image = htmlentities($res->image, null, 'UTF-8');
          $subtext = htmlentities($res->subtext, null, 'UTF-8');
          $quality = htmlentities($res->quality, null, 'UTF-8');
          $speech = htmlentities($res->speech, null, 'UTF-8');
          $format = htmlentities($res->format, null, 'UTF-8');
          $genre = htmlentities($res->genre, null, 'UTF-8');
          $imdb = htmlentities($res->imdb, null, 'UTF-8');
          $youtube = htmlentities($res->youtube, null, 'UTF-8');

          $html .= "<a class='inspectImage' href='img.php?src={$image}'><img src='img.php?src={$image}&width=200&height=280' alt='{$title}'/></a>

          <table class='inspectTable'>
            <tr>
              <td><p>Title:</p> </td>
              <td><p>{$title}</p> </td>
            </tr>
            <tr>
              <td><p>Year:</p> </td>
              <td><p>{$year}</p> </td>
            </tr>
            <tr>
              <td><p>Genre:</p> </td>
              <td><p>{$genre}</p> </td>
            </tr>
            <tr>
              <td><p>Director:</p> </td>
              <td><p>{$director}</p> </td>
            </tr>
            <tr>
              <td><p>Length:</p> </td>
              <td><p>{$length}</p> </td>
            </tr>
            <tr>
              <td><p>Language:</p> </td>
              <td><p>{$speech}</p> </td>
            </tr>
            <tr>
              <td><p>Subtitle language:</p> </td>
              <td><p>{$subtext}</p> </td>
            </tr>
            <tr>
              <td><p>Quality:</p> </td>
              <td><p>{$quality}</p> </td>
            </tr>
            <tr>
              <td><p>Synopsis:</p> </td>
              <td><p>{$plot}</p> </td>
            </tr>
            <tr>
              <td><p>IMDB:</p> </td>
              <td><a href='{$imdb}'>Link to IMDB page</a> </td>
            </tr>
            <tr>
              <td><p>Price:</p> </td>
              <td><p>50 sek</p> </td>
            </tr>
          </table>

          <div class='youtubeContainer'>
          <iframe width='560' height='315'
          src='{$youtube}' frameborder='0' allowfullscreen>
          </iframe>
          </div>";
      }

      return $html;
    }

    function getTitle($id) {
      $c = null;
      $sql = 'SELECT Title FROM VMovie WHERE id = ?';
      $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));

      if(isset($res[0])) {
        $c = $res[0];
      }
      else {
        die('Misslyckades: det finns inget innehåll med sådant id.');
      }

      return $c;
    }
}
