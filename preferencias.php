<?php
  session_start();
  include("checa.php");

  include("conf.php");  
  include('db/MyTXT.php');

  if(!checa_login())
  {
    header("Location: index.php");
    exit;
  }

  $escala = 0;
  $simples = 0;
  $size = 0;
  if(isset($_POST["escala"])) if($_POST["escala"]==1) $escala=1;
  if(isset($_POST["simples"])) if($_POST["simples"]==1) $simples=1;
  if(isset($_POST["size"])) if($_POST["size"]==1) $size=1;

  $F = $HOMEDIR.'/'.$_SESSION["home"].'/conf.php';
       
  $f = fopen($F,"w");
  fwrite($f,"<?php\n");
  fwrite($f,"  \$_SESSION['escala']        = ".$escala."; \n" );
  fwrite($f,"  \$_SESSION['simples']       = ".$simples."; \n" );
  fwrite($f,"  \$_SESSION['size']          = ".$size."; \n" );
  fwrite($f,"?>\n");
  fclose($f);
       
  include($F);

  header("Location: index.php");
  exit;


?>