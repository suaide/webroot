<?php

  $out = $TMPDIR.'/'.$_POST["SESSION_ID"].'.tmp';
  move_uploaded_file($_FILES["fileimp"]["tmp_name"],$out);
      
  $file = fopen($out,"r");
  $i = 0;

  $NL = 10;
  
  $long = false;
  
  if($modo == 4) // para ler x, y, ey, ex
  {
    while(!feof($file))
    {
      $line = fgets($file);
      $line = str_replace(",",".",$line);
      $strToken=strtok($line," \t,"); 
      $j = 0;
      while($strToken!=null && $j<$NC)
      {
        $NAME  = 'R'.$i.'C'.$j;
        $DATAPOINTS[$NAME] = trim($strToken);
        $strToken=strtok(" \t,");
        $j = $j+1;
      } 
      $i=$i+1;
      if($i>=$_SESSION["max"]) break;
    }
    $NL = $i;
  }
  
  if($modo == 1) // para ler x, x, x, x
  {  
    $j = 0;
    $i = 0;
    while(!feof($file))
    {
      $line = fgets($file);
      $line = str_replace(",",".",$line);
      $NAME  = 'R'.$i.'C'.$j;
	  $DATAPOINTS[$NAME] = trim($line);
	  $j++;
	  if($j==4)
	  {
	      $j = 0;
	      $i++;
	  }	    
      if($i>=$_SESSION["max"]) {$long = true; break;}
    }
    $NL = $i+1;
  }
  
  fclose($file);
  
  $DATAPOINTS["NL"] = $NL;    
  $DATAPOINTS["NC"] = $NC;    
  $DATAPOINTS["ID"] = $_POST["SESSION_ID"];
  
  if($long)
  {
    $VERSION = 3;
    if(file_exists($out)) copy($out,$TMPDIR.'/'.$_POST["SESSION_ID"].'.txt');
    echo "<script> alert('Há muitas linhas de dados no arquivo, acima da capacidade de edição. Os dados serão utilizados em sua totalidade mas você não poderá editá-los no WebROOT.');</script>";
  }
  else if($NL>$_SESSION["maxv1"])
  {
    $VERSION = 2;
    $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.dat';
    $str = serialize($DATAPOINTS);
    $encode = urlencode($str);
    file_put_contents($F,$encode);
    echo "<script> alert('Há muitas linhas de dados no arquivo. Para tornar o uso mais ágil, separamos a edição da planilha de dados em uma outra janela.');</script>";
  }
  else
  {
    $VERSION = 1;
    $_POST = array_merge($_POST,$DATAPOINTS);
  }
  
  $_POST["VERSION"]=$VERSION;

?>
