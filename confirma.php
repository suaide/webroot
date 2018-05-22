<?php
  session_start();
  include("checa.php");
  include("conf.php");  
  if(checa_login())
  {
    header("Location: index.php"); 
    exit;
  }

  $home  = $_GET["id"];
  $login = $_GET["login"];
  $conf  = $_GET["conf"];
  
  $db = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
  if(!$db) {header("Location: index.php?action=cadastro&err=db"); exit;}    
  
  $dbf = mysql_select_db($SQLDB, $db);
  if(!$dbf) {header("Location: index.php?action=cadastro&err=db"); exit;} 

  $login = mysql_real_escape_string($login);
  $home  = mysql_real_escape_string($home);
  
  if($conf==0)
  {
    $q = "SELECT * FROM passwd WHERE login = '$login' AND home = '$home' AND tipo='L';";
    $Q = mysql_query($q);
    $n =  mysql_num_rows($Q);
    
    if($n==0) {header("Location: index.php?action=cadastro&err=ne"); exit;}
    if($n>1)  {header("Location: index.php?action=cadastro&err=gt"); exit;}
    
    $q = "UPDATE passwd SET tipo='U' WHERE login = '$login' AND home = '$home';";    
    mysql_query($q);
    
    exec('mkdir -p '.$HOMEDIR.'/'.$home);
    exec('/bin/cp -R '.$HOMEDIR.'/template/* '.$HOMEDIR.'/'.$home.'/');
    
    header("Location: index.php?action=cadastro&err=conf");
    exit;
  }

  if($conf==1)
  {
    $q = "DELETE FROM passwd WHERE login = '$login' AND home = '$home';";
    mysql_query($q,$db);
    header("Location: index.php?action=cadastro&err=canc");
	exit;
  }
  
  header("Location: index.php");

?>
