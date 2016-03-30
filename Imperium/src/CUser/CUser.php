<?php

class CUser {

  private $acronym;
  private $db;

  function __construct($acronym, $db)
  {
    $this->acronym = $acronym;
    $this->db = $db;
  }

  function getLoginForm()
  {
    $output = $this->isAuthenticated();

    $html =  <<<EOD

    <form method=post>
          <fieldset>
            <legend>Login</legend>
             <p><em>Log in with admin:admin or doe:doe.</em></p>
             <p><label>Username:<br/><input type='text' name='acronym' value=''/></label></p>
             <p><label>Password:<br/><input type='password' name='password' value=''/></label></p>
             <p><input type='submit' name='login' value='Login'/></p>
             <output><b>{$output}</b></output>
          </fieldset>
    </form>
EOD;

    return $html;
  }

  function getLogoutForm()
  {
    $output = $this->isAuthenticated();

    $html =  <<<EOD

    <form method=post>
          <fieldset>
            <legend>Login</legend>
             <p><input type='submit' name='logout' value='logout'/></p>
             <output><b>{$output}</b></output>
          </fieldset>
    </form>
EOD;

    return $html;
  }

  function getStatusForm()
  {
    $html =  <<<EOD
    <h2>User logged in as: {$this->acronym} ({$this->getName()}) </h2>
    </br>
    <a class='simpleLinkStyle' href="movie_view_edit.php">Edit movie</a>
    <p> </p>
    <a class='simpleLinkStyle' href="movie_view_delete.php">Remove movie</a>
    <p> </p>
    <a class='simpleLinkStyle' href="movie_create.php">Create movie</a>
EOD;

    return $html;
  }

  function login($user, $password)
  {
    if(isset($_POST['login'])) {

      $sql = "SELECT acronym, name FROM USER WHERE acronym = ? AND password = md5(concat(?, salt));";
      $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, array($user, $password));

      if(isset($res[0])) {
        $_SESSION['user'] = $res[0];
      }
      header('Location: movie_status.php');
    }
  }

  function logout()
  {
    unset($_SESSION['user']);
    header('Location: movie_logout.php');
  }

  function isAuthenticated()
  {
    $output = null;
    if ($this->acronym) {
      $output = "User logged in as: {$this->acronym} ({$_SESSION['user']->name})";
    }
    else {
      $output = "You are NOT logged in.";
    }

    return $output;
  }

  function getAcronym()
  {
    return $this->acronym;
  }

  function getName()
  {
    if (isset($_SESSION['user'])) {
      return $_SESSION['user']->name;
    }
    else {
      return "No name assigned";
    }
  }

}
