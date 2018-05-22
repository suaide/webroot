<?php

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}
  exit;
  session_start();
    include("checa.php");

  include("conf.php");  
  include('db/MyTXT.php');
    
  $db1 = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
      
  mysql_select_db($SQLDB);
  
  //$q = "DROP TABLE passwd;";
  //mysql_query($q);
  
  $q = "create table passwd (ID int not null primary key auto_increment, 
        tipo varchar(1), login varchar(30), nome varchar(35), usp varchar(12), 
        email varchar(30), created varchar(13), lastlogin varchar(13), home varchar(35), senha varchar(45));";
        
  mysql_query($q);
  
  $db = new MyTXT($PASSWD);
  $i = 0;
  foreach($db->rows as $row)
  {
    $i++;
    $q = "INSERT INTO passwd VALUES (
          '".$i."',
          '".$row["tipo"]."',
          '".$row["login"]."',
          '".$row["nome"]."',
          '".$row["usp"]."',
          '".$row["email"]."',
          '".$row["created"]."',
          '".$row["lastlogin"]."',
          '".$row["home"]."',
          '".$row["senha"]."');";
    echo $q;
    echo "<br>";
    mysql_query($q);
  }
  
echo 'FIM';   
  
?>
