<?php
  session_start();
  include("checa.php");
  include("conf.php");  

  if(!checa_login())
  {
    header("Location: index.php");
    exit;
  }
  
  $senha1 = md5($_POST["senha1"]);
  $senha2 = md5($_POST["senha2"]);
  $senha3 = md5($_POST["senha3"]);
  
  if(strlen($_POST["senha2"])<8)
  {
    header("Location: index.php?action=senha&err=senha");
    exit;
  }
  
  if($senha2!=$senha3)
  {
    header("Location: index.php?action=senha&err=confirma");
    exit;
  }

  $db = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
  if(!$db) {header("Location: index.php?action=senha&err=db"); exit;}    
  
  $dbf = mysql_select_db($SQLDB, $db);
  if(!$dbf) {header("Location: index.php?action=senha&err=db"); exit;} 
  
  
  $q = "SELECT * FROM passwd WHERE login='".mysql_real_escape_string($_SESSION["login"])."' AND senha='$senha1';";
  $Q = mysql_query($q,$db);
  $n = mysql_num_rows($Q);      
  
  if($n==0)
  {
    header("Location: index.php?action=senha&err=atual");
    exit;
  }
  
  $q = "UPDATE passwd SET senha = '".$senha2."' WHERE login = '".mysql_real_escape_string($_SESSION["login"])."' AND senha = '$senha1';";
  mysql_query($q);
 
  header("Location: index.php?action=senha&err=ok");
  exit;
  
?>
