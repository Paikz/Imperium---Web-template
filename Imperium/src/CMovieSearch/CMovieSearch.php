<?php

class CMovieSearch {

  private $db;
  private $title;
  private $hits;
  private $page;
  private $order;
  private $orderby;
  private $genre;



  function __construct($db, $title, $hits, $page, $genre, $orderby, $order)
  {
    $this->db = $db;
    $this->title = $title;
    $this->hits = $hits;
    $this->page = $page;
    $this->orderby = $orderby;
    $this->order = $order;
    $this->genre = $genre;
  }

  function prepareSQL()
  {
    //$sqlOrig = 'SELECT * FROM Movie';
    $sqlOrig = '
    SELECT
      M.*,
      GROUP_CONCAT(G.name) AS genre
    FROM Movie AS M
      LEFT OUTER JOIN Movie2Genre AS M2G
        ON M.id = M2G.idMovie
      LEFT OUTER JOIN Genre AS G
        ON M2G.idGenre = G.id
        ';
    $params = [];
    $where    = null;
    $limit    = null;
    $sort     = " ORDER BY $this->orderby $this->order";
    $groupby  = ' GROUP BY id';

    if($this->title) {
      $where .= ' AND title LIKE ?';
      $params[] = $this->title.'%';
    }

    if($this->genre) {
      $where .= ' AND G.name = ?';
      $params[] = $this->genre;
    }

    if($this->hits && $this->page) {
      $limit = " LIMIT $this->hits OFFSET " . (($this->page - 1) * $this->hits);
    }

    $where = $where ? " WHERE 1 {$where}" : null;
    $sql = $sqlOrig . $where . $groupby . $sort . $limit;
    $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);
    return $res;
  }

  function getForm()
  {
    $html = <<<EOD
        <form action="movie_view.php">
            <input type=hidden name=hits value='{$this->hits}'/>
            <input type=hidden name=page value='1'/>
            <p><label><input class='searchBar' type='search' name='title' value='{$this->title}' Placeholder='Search...'/></label></p>
        </form>
EOD;

  return $html;
  }

}
