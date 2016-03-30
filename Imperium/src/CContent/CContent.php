<?php

class CContent {

    protected $db;
    protected $filter;

    function __construct($db, $filter) {
        $this->db = $db;
        $this->filter = $filter;
    }

    function createTableDb() {
    $qry = "DROP TABLE IF EXISTS Content;
            CREATE TABLE Content (
              id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
              slug CHAR(80) UNIQUE,
              url CHAR(80) UNIQUE,

              TYPE CHAR(80),
              title VARCHAR(80),
              DATA TEXT,
              FILTER CHAR(80),

              published DATETIME,
              created DATETIME,
              updated DATETIME,
              deleted DATETIME
            )
            ENGINE INNODB CHARACTER SET utf8;";
      $this->db->executeQuery($qry);
  }

    function getUrlToContent($content) {
      switch($content->TYPE) {
        case 'page': return "page.php?url={$content->url}"; break;
        case 'post': return "blog.php?slug={$content->slug}"; break;
        default: return null; break;
      }
    }

    function getContent() {
    $qry = "SELECT *, (published <= NOW()) AS available FROM Content;";
    $res = $this->db->ExecuteSelectQueryAndFetchAll($qry);
    return $res;
  }

  function selectContentWithID($id) {
    $c = null;
    $sql = 'SELECT * FROM Content WHERE id = ?';
    $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($id));

    if(isset($res[0])) {
      $c = $res[0];
    }
    else {
      die('Misslyckades: det finns inget innehåll med sådant id.');
    }

    return $c;
  }

  function getNextBlogIndex() {
      $qry = "SELECT * FROM Content WHERE type='post';";
      $res = $this->db->executeQuery($qry);
      return ($this->db->RowCount() + 1);
  }

  function submitForm($save, $params) {
    $output = null;
    if($save) {
      $sql = '
        UPDATE Content SET
          title = ?,
          slug = ?,
          url = ?,
          data = ?,
          type = ?,
          filter = ?,
          published = ?,
          updated = NOW()
        WHERE
          id = ?
      ';
      $params[2] = empty($params[2]) ? null : $params[2]; //To access the url variable we send in "2" because that is the place we put the url in params from "edit.php"
      $res = $this->db->ExecuteQuery($sql, $params);
      if($res) {
        $output = 'Informationen sparades.';

      }
      else {
        $output = 'Informationen sparades EJ.<br><pre>' . print_r($this->db->ErrorInfo(), 1) . '</pre>';
      }
    }
    return $output;
  }

  function createContent($params) {
    $qry = "INSERT INTO Content (title, TYPE, published, created)
    VALUES (?, ?,NOW(), NOW());";
    $res = $this->db->ExecuteQuery($qry, $params);
    if($res) {
      $output = 'Informationen sparades.';
    }
    else {
      $output = 'Informationen sparades EJ.<br><pre>' . print_r($this->db->ErrorInfo(), 1) . '</pre>';
    }
    return $output;
  }

  function deleteContent($params) {
    $qry = "DELETE FROM Content WHERE id = ?;";
    $res = $this->db->ExecuteQuery($qry, $params);
    if(isset($res[0])) {
      $output = 'Informationen raderades.';
    }
    else {
      $output = 'Informationen raderades EJ.<br><pre>' . print_r($this->db->ErrorInfo(), 1) . '</pre>';
    }
    return $output;
  }

  function resetContent() {
    $qry = "DELETE FROM Content;
    INSERT INTO Content (slug, url, TYPE,genre, title, DATA, FILTER, published, created) VALUES
  ('blogpost-1', NULL, 'post', 'announcement', 'Website update: New features!', 'New features on the website going live! Check out our brand new user system, it takes less than a minute to signup!', 'clickable,nl2br', NOW(), NOW()),
  ('blogpost-2', NULL, 'post', 'news', 'New movies in stock!', 'Brand new movies in stock!', 'clickable,nl2br', NOW(), NOW()),
  ('blogpost-3', NULL, 'post', 'announcement', 'Give away! Win a gift card worth 200 kr!', 'Share our facebook page to win a gift card!', 'clickable,nl2br', NOW(), NOW()),
  ('blogpost-4', NULL, 'post', 'deals', 'Christmas deal: 50% off all movies!', 'We all know how cosy it is to chill in front of a movie and a bag of chips. 50% off on all our movies!'', 'clickable,nl2br', NOW(), NOW()),
  ('blogpost-5', NULL, 'post', 'deals', 'Limited offer - rent 2 movies for the price of 1!', 'Head on over to the movies-section to choose from our broad selection!\nhttp://www.student.bth.se/~phes15/dbwebb-kurser/oophp/me/kmom10/movie_view.php', 'clickable,nl2br', NOW(), NOW()),
  ('blogpost-6', NULL, 'post', 'announcement','Now hiring!', 'We are looking to hire qualified indviduals for our customer service department. Send in a personal letter and your CV if you believe you are right for the job.', 'clickable,nl2br', NOW(), NOW()),
  ('blogpost-7', NULL, 'post', 'announcement','Welcome to RM - Rental Movies!', 'We are pleased to announce that we have finally launched our new website for all your movie needs. Head on over to the Movies-section to rent movies!', 'clickable,nl2br', NOW(), NOW())
;";
      $this->db->ExecuteQuery($qry);
  }
}
