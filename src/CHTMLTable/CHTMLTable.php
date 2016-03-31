<?php

class CHTMLTable {

  function printHTMLTable($result) {

    $html = "<table class='movieOrder'>
            <tr>
              <th>Order by title" . $this->orderby('title') . "</th>
              <th>or year" . $this->orderby('year') . "</th>
            </tr>
            </table>";

      $html .= "<div class='movieElements'>";

      foreach ($result as $key => $value) {
          $html .= "
                      <div class='movieBlock'>
                      <a href='inspect.php?id={$value->id}'><img src='img.php?src={$value->image}&width=200&height=280' alt='{$value->title}'/>
                      <p>{$value->title} <br> ({$value->year})</p></a>
                      </div>";

      }

      $html .= '</div>';

      return $html;
  }

  function orderby($column) {
    $nav  = "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'asc')) . "'>&darr;</a>";
    $nav .= "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'desc')) . "'>&uarr;</a>";
    return "<span class='orderby'>" . $nav . "</span>";
  }

  function getPageNavigation($hits, $page, $max, $min=1) {
    $nav  = "<a href='" . $this->getQueryString(array('page' => $min)) . "'>&lt;&lt;</a> ";
    $nav .= "<a href='" . $this->getQueryString(array('page' => ($page > $min ? $page - 1 : $min) )) . "'>&lt;</a> ";

    for($i=$min; $i<=$max; $i++) {
      $nav .= "<a href='" . $this->getQueryString(array('page' => $i)) . "'>$i</a> ";
    }

    $nav .= "<a href='" . $this->getQueryString(array('page' => ($page < $max ? $page + 1 : $max) )) . "'>&gt;</a> ";
    $nav .= "<a href='" . $this->getQueryString(array('page' => $max)) . "'>&gt;&gt;</a> ";
    return $nav;
  }

  function getHitsPerPage($hits) {
    $nav = "Hits per page: ";
    foreach($hits AS $val) {
      $nav .= "<a href='" . $this->getQueryString(array('hits' => $val)) . "'>$val</a> ";
    }
    return $nav;
  }

  function getMax($db, $hits)
  {
    // Get max pages from table, for navigation
    $sql = "SELECT COUNT(id) AS rows FROM VMovie";
    $res = $db->ExecuteSelectQueryAndFetchAll($sql);

    // Get maximal pages
    $max = ceil($res[0]->rows / $hits);

    return $max;
  }

  function getQueryString($options, $prepend='?') {
    // parse query string into array
    $query = array();
    parse_str($_SERVER['QUERY_STRING'], $query);
    // Modify the existing query string with new options
    $query = array_merge($query, $options);
    // Return the modified querystring
    return $prepend . http_build_query($query);
  }

}
