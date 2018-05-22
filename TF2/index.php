<?php
  session_start();
  include("../checa.php");
  include("../conf.php");    
  if(!checa_login())
  {
    header('Location: ../index.php'); 
    exit;
  }
  include("../header.php");
  $MAX = 400;
  $app = 'TF2';
  if(!isset($_POST["VERSION"])) $_POST["VERSION"]=1;
  
  if(isset($_GET["action"]))
  {
    switch($_GET["action"])
    {
      case "abrir":
        include("../open.php");
	    break;
    }
  }
  
  if(!isset($_POST["SESSION_ID"]))  $_POST["SESSION_ID"]='A'.time().rand();
  if(!isset($_POST["LARGURA"])) $_POST["LARGURA"] = 1050;
  if($_POST["LARGURA"]<100) $_POST["LARGURA"] = 100;
  if($_POST["LARGURA"]>1600) $_POST["LARGURA"] = 1600;
  
  if(!isset($_POST["ALTURA"])) $_POST["ALTURA"] = 720;
  if($_POST["ALTURA"]<100) $_POST["ALTURA"] = 100;
  if($_POST["ALTURA"]>1600) $_POST["ALTURA"] = 1600;

  if(!isset($_POST["FUNDO"])) $_POST["FUNDO"] = 0;
   
  if(isset($_POST["filesave"])) titulo('(TF2) '.htmlspecialchars($_POST["filesave"]));
  else titulo('(TF2)');

  include("makeplot.php");
  include("../javascripts.php");
  include("../comum.php");
  
  if(isset($_POST["FIG"])) $FIG = $_POST["FIG"]; else $FIG="";
    
  $FLAG = "";
  
  if(isset($_POST["action"]))
  {
    switch($_POST["action"])
    {
      case "PLOTA":
        $FIG = makeplot($_POST,$ROOTDIR,$TMPDIR, $ROOTSTYLE);
        $FLAG = '?'.time();
	    break;

      case "Salvar":
        include("../save.php");
    	break;
      
      case "OK":
        $file = $_FILES["fileimp"]["tmp_name"];
        if($file!="")
        {
          $out = $TMPDIR.'/'.$_POST["SESSION_ID"].'.dat';
          move_uploaded_file($_FILES["fileimp"]["tmp_name"],$out);
      
          $lines = file($out);
          $i = 0;
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
            if($i>=$MAX) break;
          }
          $NL = $i;
          $_POST["NL"] = $NL;    
        }
        else echo "<script> alert('Selecione o arquivo de dados para importar');</script>";
        break;
	             
    }
  }
      

  $a = "filesave"; if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "titulo";   if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "nbins";    if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "marcador"; if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "cormarcador"; if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "tamanho";  if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "funcao";   if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "par";      if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "linha";    if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "corlinha"; if(!isset($_POST[$a])) $_POST[$a]="";
  
  
  $NOME[0]="x";
  $NOME[1]="x";
  $NOME[2]="x";
  $NOME[3]="x";
  
  
  echo '<div id="topo">
        <h1> <a href="../">webROOT</a> (TF2) - fun&ccedil;&atilde;o de duas vari&aacute;vel em x e y</h1>
        <hr>
        </div>
        
        <form method="post" action="index.php" onkeypress="return disableEnter(event)" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="600000" />
        <input type="hidden" name="FIG" value="'.$FIG.'">
        <input type="hidden" name="VERSION" value="'.$_POST["VERSION"].'">
        <input type="hidden" name="SESSION_ID" value="'.$_POST["SESSION_ID"].'">
          
        <div id="commands"> 
        
	     <center>'
	    .aplicacao($_POST).'<br>'
	    .funcao_teorica_2($_POST,false,false,true).'<br>'
        .eixos($_POST,true)
        .'</center> <br><br><br></form></div>';
        
  echo '<div id="output">';  
  mostra_figura($FIG,$TMPURL,$TMPDIR,$_POST["SESSION_ID"]);
  echo '</center></div>';
  include("../footer.php");
  
        
?>
