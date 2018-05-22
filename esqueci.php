<?php
function createRandomPassword() 
{
    $chars = "abcdefghijkmnopqrstuvwxyz023456789&%$#@*!";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= 7) 
    {
        $num = rand() % 40;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

  session_start();
    include("checa.php");

  include("conf.php");  
  if(checa_login())
  {
    header("Location: index.php"); 
    exit;
  }

  $db = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
  if(!$db) {header("Location: index.php?action=esqueci&err=db"); exit;}    
  
  $dbf = mysql_select_db($SQLDB, $db);
  if(!$dbf) {header("Location: index.php?action=esqueci&err=db"); exit;} 

  $email = mysql_real_escape_string($_POST["email"]);
  
  $q = "SELECT * FROM passwd WHERE email='$email';";
  $Q = mysql_query($q,$db);
  $n = mysql_num_rows($Q);
  
  if($n==0)
  {
    header("Location: index.php?action=esqueci&err=valida");
    exit;
  }
  
  $row = mysql_fetch_array($Q); 
  $pass = createRandomPassword();
  
  $q = "UPDATE passwd SET senha = '".md5($pass)."' WHERE email = '$email';";    
  mysql_query($q);
    
  $subject="[WebROOT] Esqueci minha senha";
  $to = $email;
  $headers=
'From: webroot@if.usp.br
Mime-Type: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-encoding: 8bit
';

  $body = 
'     Caro usuário

Acabamos de criar uma nova senha para você no WebROOT. O seu nome de usuário e senha são:

     usuário   : '.$row["login"].'
     senha     : '.$pass.'

Por favor, altere esta senha assim que possível.

      Atenciosamente,
      
            GRIPER
	    
OBS: Não responda este email. Ninguém irá recebê-lo.
';
  $success = mail($to, $subject, $body, $headers);
    
  header("Location: index.php?action=esqueci&err=Ok");
  exit;
      


?>
