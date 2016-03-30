<?php

class CBlog extends CContent {

  private $genre;

  function __construct($database, $filter, $genre) {
    $this->db = $database;
    $this->filter = $filter;
    $this->genre = $genre;
  }

  function getBlogContent() {
    $qry = "SELECT * FROM Content WHERE TYPE = 'post' AND published <= NOW() ORDER BY published desc;";
    $res = $this->db->ExecuteSelectQueryAndFetchAll($qry);
    return $res;
  }

  function getSingleBlogContent($slug) {
    $qry = "SELECT * FROM Content WHERE TYPE = 'post' AND slug = '$slug' AND published <= NOW();";
    $res = $this->db->ExecuteSelectQueryAndFetchAll($qry);
    return $res;
  }

  function prepareBlogSQL()
  {
    $sqlOrig = "SELECT * FROM Content WHERE TYPE = 'post' AND published <= NOW()";
    $params = [];
    $where  = null;

    if($this->genre) {
      $where .= ' AND genre = ?';
      $params[] = $this->genre;
    }

    $sql = $sqlOrig . $where;
    $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);
    return $res;
  }

  function printBlogContent($res) {
    if(isset($res)) {
      $html = "<article>";
      foreach ($res as $c) {
        // Sanitize content before using it.
        $title  = htmlentities($c->title, null, 'UTF-8');
        $data   = $this->filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);

        if (str_word_count($data, 0) > 10 && !isset($_GET['slug'])) {
          $words = str_word_count($data, 2);
          $pos = array_keys($words);
          $data = substr($data, 0, $pos[10]) . "... <a href='blog.php?slug={$c->slug}'>Read more</a>";
          }

        $pubDate = htmlentities($c->published, null, 'UTF-8');
        $genre = htmlentities($c->genre, null, 'UTF-8');
        $html .= "<section><h2><a id='blogElements' href='blog.php?slug=" . $c->slug . "'>" . $title . "</a></h2><p>" . $data . "</p><footer class='blogfooter'><p style='display: inline;'>Tags: </p><a href=?genre={$genre}>" . $genre . "</a><br>" . $pubDate . "</footer></section><br>";
      }
      $html .= "</article>";
    }
    elseif ($slug) {
      $html = "Det fanns inte en sådan bloggpost.";
    }
    else {
      $html = "Det fanns inga bloggposter.";
    }
    return $html;
  }

  function printBlogTitles($res) {
    if(isset($res)) {
      $html = "<div class='sidebarBlog'>";
      foreach ($res as $c) {
        // Sanitize content before using it.
        $title  = htmlentities($c->title, null, 'UTF-8');
        $data   = $this->filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
        $pubDate = htmlentities($c->published, null, 'UTF-8');
        $html .= "<section><a id='blogElements' href='blog.php?slug=" . $c->slug . "'>" . $title . "</a></section><br>";
      }
      $html .= "</div>";
    }
    elseif ($slug) {
      $html = "Det fanns inte en sådan bloggpost.";
    }
    else {
      $html = "Det fanns inga bloggposter.";
    }
    return $html;
  }
}
