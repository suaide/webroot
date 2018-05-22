<?php
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}
function valida($email)
{
// First, we check that there's one @ symbol, 
  // and that the lengths are right.
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) 
  {
    // Email invalid because wrong number of characters 
    // in one section or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) 
  {
    if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",$local_array[$i])) 
    {
      return false;
    }
  }
  // Check if domain is IP. If not, 
  // it should be valid domain name
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) 
  {
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) 
    {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])| ([A-Za-z0-9]+))$",$domain_array[$i])) 
      {
        return false;
      }
    }
  }
  return true;
}
function checa_email($email)
{
  if($email == "") return false;
  if(!valida($email)) return false;
  return true;
  if(endsWith($email,"usp.br")) return true;
  if(endsWith($email,"unicamp.br")) return true;
  if(endsWith($email,"unesp.br")) return true;
  if(endsWith($email,".edu")) return true;
  if(endsWith($email,"cern.ch")) return true;
  
  return false;
}

  session_start();
  include("checa.php");

  include("conf.php");  
  if(checa_login())
  {
    header("Location: index.php"); 
    exit;
  }

  $login = strtolower($_POST["login"]);
  $nome = $_POST["nome"];
  $email = $_POST["email"];
  $senha1= $_POST["senha1"];
  $senha2= $_POST["senha2"];
  
  if(preg_match('/[^a-z0-9\-\_\.]+/i',$login) || strlen($login)<3) { header("Location: index.php?action=cadastro&err=baduser"); exit; }
    
  if(!checa_email($email))  {header("Location: index.php?action=cadastro&err=email"); exit;}
  if(strlen($senha1)<8)     {header("Location: index.php?action=cadastro&err=senha"); exit;}
  if($senha1!=$senha2)      {header("Location: index.php?action=cadastro&err=senha"); exit;}
    
  $db = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
  //if(!$db) {header("Location: index.php?action=cadastro&err=db"); exit;}  
  
  $dbf = mysql_select_db($SQLDB, $db);
  //if(!$dbf) {header("Location: index.php?action=cadastro&err=db"); exit;} 
  
  $login = mysql_real_escape_string($login);
  $email = mysql_real_escape_string($email);
  $nome  = mysql_real_escape_string($nome);
  
  // checa se já existe
  $q = "SELECT login FROM passwd WHERE login = '$login' OR email='$email';";
  $n =  mysql_num_rows(mysql_query($q,$db));
  
  if($n>0) {header("Location: index.php?action=cadastro&err=user"); exit;}
    
  $home = substr($login,0,2).'/'.$login;
  
  $usp = 0;
  
  $q = "INSERT INTO passwd(tipo,login,nome,usp,email,created,lastlogin,home,senha) VALUES (
          'L','".$login."','".$nome."','".$usp."','".$email."',
          '".time()."','".time()."','".$home."','".md5($senha1)."');";
          
  
  mysql_query($q,$db);
    
  $subject="[WebROOT] Confirmação de cadastro";
  $to = $email;
  $headers=
'From: webroot@if.usp.br
Mime-Type: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-encoding: 8bit
';
  $body = 
'     Caro usuário

Acabamos de cadastrar você como usuário do WebROOT. Mas antes de usar é preciso confirmar o seu email.

Os dados cadastrados foram:

     usuário   : '.$login.'
     nome      : '.$nome.'
     e-mail    : '.$email.'
     
Para confirmar o cadastro, clique no link abaixo:

'.$BASEURL.'/confirma.php?conf=0&login='.$login.'&id='.$home.'

Para recusar e apagar esta solicitação, clique em:

'.$BASEURL.'/confirma.php?conf=1&login='.$login.'&id='.$home.'

      Atenciosamente,
      
            GRIPER
	    
OBS: Não responda este email. Ninguém irá recebê-lo.
';

    $success = mail($to, $subject, $body, $headers);
    header("Location: index.php?action=cadastro&err=ok");
   
  
?>
