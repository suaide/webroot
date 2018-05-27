<?php
  function checa_login()
  {
    if (!isset($_SESSION["webroot"])) return false;
    
    if (isset($_SESSION['LAST_ACTIVITY']))
    {
      if ((time() - $_SESSION['LAST_ACTIVITY'] > $_SESSION['MAXTIME'])) 
      {
        session_destroy();   // destroy session data in storage
        session_unset();     // unset $_SESSION variable for the runtime
        return false;
      }
    }
    $_SESSION['LAST_ACTIVITY'] = time();
    return true;
  }

  function checa_admin()
  {
    if (!checa_login()) return false;
    if ($_SESSION["tipo"]!="A") return false;
    
    return true;
  }


?>
