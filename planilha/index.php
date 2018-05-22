<?php
  session_start();
  include("../checa.php");
  include("../conf.php");  
  if(!checa_login())
  {
    header('Location: ../index.php'); 
    exit;
  }
  
  if(isset($_GET["modo"])) $_POST["modo"] = $_GET["modo"];
  if(isset($_GET["id"])) 
  {
    $_POST["SESSION_ID"] = $_GET["id"];
    $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.dat';
    $var = file_get_contents($F);
    $DATA = unserialize(urldecode($var));
    $_POST = array_merge($_POST,$DATA);
  }
   
  if(isset($_POST["NC"]))  $NC  = $_POST["NC"];  else $NC  = 4;     
  if(isset($_POST["NL"]))  $NL  = $_POST["NL"];  else $NL  = 10;

  include("../header.php");
  titulo('(DADOS) '.htmlspecialchars($_POST["nome"]));
  include("../javascripts.php");
  include("../comum.php"); 
  
  $D = $TMPDIR.'/'.$_POST["SESSION_ID"].'.id.php';
  include($D);
   
  movecursor($NC,$NL);

  if(isset($_POST["action"]))
  {
    switch($_POST["action"])
    {
      	
     case "+1 L":
       $NL = $NL+1;
       break;
       
     case "+10 L":
       $NL = $NL+10;
       break;

     case "-1 L":
       $NL = $NL-1;
       break;
       
     case "-10 L":
       $NL = $NL-10;
       break;
       
     case "OK":
        $file = $_FILES["fileimp"]["tmp_name"];
        if($file!="")
        {
           $out = $TMPDIR.'/'.$_POST["SESSION_ID"].'.tmp';
  		   move_uploaded_file($_FILES["fileimp"]["tmp_name"],$out);      
  		   $lines = file($out);
  		   $i = 0;  
  		   $NL = 10;  
           $modo = $_POST["modo"];
           if($modo == 4) // para ler x, y, ey, ex
           {
             foreach($lines as $line)
             {
               $strToken=strtok($line," \t,"); 
               $j = 0;
               while($strToken && $j<$NC)
               {
                 $NAME  = 'R'.$i.'C'.$j;
                 $_POST[$NAME] = $strToken;
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
             foreach($lines as $line)
             {
               $NAME  = 'R'.$i.'C'.$j;
	           $_POST[$NAME] = htmlspecialchars($line);
	           $j++;
	           if($j==4)
	           {
	               $j = 0;
	               $i++;
	           }	    
               if($i>=$_SESSION["max"]) break;
             }
             $NL = $i;
           }
  
           $_POST["NL"] = $NL;    
           $_POST["NC"] = $NC;            
        }
       else echo "<script> alert('Selecione o arquivo de dados para importar');</script>";     
       break;
       
     case "ATUALIZAR dados":
       $TMP  = $_POST["SESSION_ID"];
       $TMP2 = $_POST["action"];
       unset($_POST["SESSION_ID"]);
       unset($_POST["action"]);
       $F = $TMPDIR.'/'.$TMP.'.dat';
       $str = serialize($_POST);
       $encode = urlencode($str);
       file_put_contents($F,$encode);
       $_POST["SESSION_ID"]=$TMP;
       $_POST["action"] = $TMP2;
       echo "<script> alert('Dados foram salvos e podem ser utilizados'); </script>";       
       break;
     
     case "Reverter dados":
       for($i = 0; $i<$NL; $i++)
       {
         for($j = 0; $j<$NC; $j++) unset($_POST['R'.$i.'C'.$j]);
       }
       $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.dat';
       $var = file_get_contents($F);
       $DATA = unserialize(urldecode($var));
       $_POST = array_merge($_POST,$DATA);
       echo "<script> alert('Dados foram revertidos'); </script>";       

       break;
                  
    }
  }
  
  if($NL<1) $NL=1;
  if($NL>$_SESSION["max"]) $NL=$_SESSION["max"]; 
  
  movecursor($NC,$NL);
  
  $width = ($NC+1)*80;
  if($width<400) $width=400;
  
  if(!isset($_POST["COL0"])) 
  {
    if($_POST["modo"]==1) $_POST["COL0"] = "x";
    if($_POST["modo"]==4) $_POST["COL0"] = "x";
  }
  if(!isset($_POST["COL1"])) 
  {
    if($_POST["modo"]==1) $_POST["COL1"] = "x";
    if($_POST["modo"]==4) $_POST["COL1"] = "y";
  }
  if(!isset($_POST["COL2"])) 
  {
    if($_POST["modo"]==1) $_POST["COL2"] = "x";
    if($_POST["modo"]==4) $_POST["COL2"] = "Erro y";
  }
  if(!isset($_POST["COL3"])) 
  {
    if($_POST["modo"]==1) $_POST["COL3"] = "x";
    if($_POST["modo"]==4) $_POST["COL3"] = "Erro x";
  }

  echo '<center><form name="'.$ID.'" method="post" action="index.php" onkeypress="return disableEnter(event)" enctype="multipart/form-data">
        <input type="hidden" name="NC" value="'.$NC.'">
        <input type="hidden" name="NL" value="'.$NL.'">
        <input type="hidden" name="modo" value="'.$_POST["modo"].'">
        <input type="hidden" name="SESSION_ID" value="'.$_POST["SESSION_ID"].'">';
        
   echo'<table border="0" width ="'.$width.'"> <tr><td><h2>Edi&ccedil;&atilde;o de dados</h2><h3>'.$NAME.'</h3><hr></td></tr></table>';

  $PRINTTABELA =true;
  
  for($j = 0; $j<$NC; $j++)
  {
  
    echo '<input type="hidden" name="COL'.$j.'" value="'.$_POST['COL'.$j].'">'; 
    
    if($_POST["opcao".$j]!="2") echo '<input type="hidden" name="formula'.$j.'" value = "'.$_POST["formula".$j].'" >';
    
    if($_POST["opcao".$j]=="2") // calcular
    {

      echo '<input type="hidden" name="source" value = "'.$j.'">'; 
      echo'<table border="0" width ="'.$width.'"> 
      <tr>
        <td colspan = 3><h3>Calcular valores para colunas</h3></td>
      </tr>      
      <tr>
        <td> <b>coluna '.$j.' - '.$_POST['COL'.$j].'</b>  = </td>
        <td colspan = 2> <input type="text" size="50" name="formula'.$j.'" value = "'.$_POST["formula".$j].'" > </td>
      </tr>
      <tr>
        <td> <b>Linhas</b> </td>
        <td> inicial:  <input type="text" size="8" name="li" value = "'.$_POST["li"].'"> </td>
        <td> final:  <input type="text" size="8" name="lf" value = "'.$_POST["lf"].'"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>     
      <tr>
        <td colspan = 3><center><input type = "submit" name="calcula" value = "Calcula valores"> <input type = "submit" name="action" value = "Cancela"> </center></td>
      </tr>
      <tr>
        <td colspan =3><hr></td>
      </tr>
      </table></center>';
      
      $PRINTTABELA = false;

    }
    if($_POST["opcao".$j]=="3") // trocar
    {
      
      echo '<input type="hidden" name="source" value = "'.$j.'">'; 
      echo'<table border="0" width ="'.$width.'"> <tr><td><h3>Trocar colunas de lugar</h3></td></tr>      
      <tr><td> <center> Trocar <b>coluna '.$j.' - '.$_POST['COL'.$j].'</b>  por       
      <select name = "destination" title="escolha a coluna para trocar">';
      for($i = 0; $i<$NC;$i++)
      {
        echo '<option value = "'.$i.'"> coluna '.$i.' - '.$_POST['COL'.$i].'</option>';
      }
           
      echo '</center></td></tr><tr><td>&nbsp;</td></tr>';     
      echo '<tr><td><center><input type = "submit" name="troca" value = "Troca colunas"> <input type = "submit" name="action" value = "Cancela"> </center><hr></td></tr></table><br>';
      
      $PRINTTABELA = false;
        
    }
  }  
  
  if(!$PRINTTABELA)
  {
    echo '\n';
    for($i = 0; $i<$NL; $i++)
    {
      for($j = 0; $j<$NC; $j++)
      {
        $NAME  = 'R'.$i.'C'.$j;
        $ID    = $i.'C'.$j;
        $VALUE = $_POST['R'.$i.'C'.$j];
        echo '<input type="hidden" name="'.$NAME.'" value = "'.$VALUE.'">\n'; 
      }
    }
  }
  
        
  if($PRINTTABELA)
  {

   echo'<table border="0" width ="'.$width.'">

        <tr><td colspan='.($NC+1).'><center>Importar: <input type = "file" name="fileimp"> <input type = "submit" name="action" value = "OK"></center><br></td></tr>
        
	    <tr><td colspan='.($NC+1).'><center><input type="submit" name="action" value="ATUALIZAR dados"> &nbsp; &nbsp; 
	    <input type="submit" name="action" value="Reverter dados"> 
	    <br><br></center></td></tr> 
	    <tr><td colspan='.($NC+1).'><center>N&atilde;o esque&ccedil;a de ATUALIZAR os dados para eles serem utilizados na sua aplica&ccedil;&atilde;o.
	    <br><br></center></td></tr> 

        <tr><td colspan='.($NC+1).'><center><input type = "submit" name="action" value = "+1 L">&nbsp;
        <input type = "submit" name="action" value = "-1 L">&nbsp;
        <input type = "submit" name="action" value = "+10 L">&nbsp;
        <input type = "submit" name="action" value = "-10 L">&nbsp;
        <input type = "submit" name="limpa" value = "limpa">&nbsp;
        </center><br><hr></td></tr>
		
	<tr><td></td>';
    
  
  for($j = 0; $j<$NC; $j++)
  {
    echo '<td><center>'.$_POST['COL'.$j].'<br>
    <select name="opcao'.$j.'" onchange="if(this.value != \'0\') this.form.submit()" title="Selecione uma a&ccedil;&atilde;o para esta coluna">
      <option selected value="0">Op&ccedil;&otilde;es</option>
      <option value="1">Limpar</option>
      <option value="2">Calcular</option>
      <option value="3">Trocar</option>
    </select>
    ';        
  }

  $math = new EvalMath();
  $li = 0;
  $lf = $NL;
  if(isset($_POST['source'])) $s = $_POST["source"];
  if(isset($_POST['li'])) if(is_numeric($_POST['li'])) if($_POST['li']>0 && $_POST['li']<=$NL) $li = $_POST['li'];
  if(isset($_POST['lf'])) if(is_numeric($_POST['lf'])) if($_POST['lf']>=$li && $_POST['lf']<=$NL) $lf = $_POST['lf'];
  
  echo '</tr><tr><td></td>';
  echo '</tr>';
  for($i = 0; $i<$NL; $i++)
  {
    if(isset($_POST["troca"]))
    {
      $s = $_POST["source"];
      $d = $_POST["destination"];
      $TMP = $_POST['R'.$i.'C'.$s];
      $_POST['R'.$i.'C'.$s] = $_POST['R'.$i.'C'.$d];
      $_POST['R'.$i.'C'.$d] = $TMP;
    }
    if(isset($_POST["calcula"]))
    {
      if($i>=$li && $i<=$lf)
      {
        $math->evaluate('i = '.$i);
        for($col = 0; $col<$NC; $col++) 
        {
          if(is_numeric($_POST['R'.$i.'C'.$col]))  $math->evaluate('col'.$col.' = '.$_POST['R'.$i.'C'.$col]);
          else $math->evaluate('col'.$col.' = 0');
        }
        $_POST['R'.$i.'C'.$s] = $math->evaluate($_POST["formula".$s]);
      }
    }

    echo '<tr>';
    echo '<td> '.$i.'</td>';
    for($j = 0; $j<$NC; $j++)
    {

      if(isset($_POST["opcao".$j])) if($_POST["opcao".$j]=="1") unset($_POST['R'.$i.'C'.$j]);

      $NAME  = 'R'.$i.'C'.$j;
      $ID    = $i.'C'.$j;
      $VALUE = $_POST['R'.$i.'C'.$j];
      echo '<td><input type="text" size="'.($width/$NC/8).'" name="'.$NAME.'" id="'.$ID.'" value = "'.$VALUE.'" onkeydown=\'KP(this.id,event)\'></td>';
    }
    echo '</tr>';
  }
  
  }
    
  echo '</table> </center>';
  
?>
