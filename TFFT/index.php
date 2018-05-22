<?php
  session_start();
  include("../checa.php");

  include("../conf.php");    
  if(!checa_login())
  {
    header('Location: ../index.php'); 
    exit;
  }
  
  if(isset($_GET["action"]))
  {
    switch($_GET["action"])
    {
      case "abrir":
        include("../open.php");
	    break;
    }
  }
  include("../header.php");
  $app = 'TFFT';
  
  if(!isset($_POST["VERSION"])) 
  {
    $_POST["VERSION"]=1;
  }
  
  if(!isset($_POST["SESSION_ID"]))  $_POST["SESSION_ID"]='A'.time().rand();
  
  $_POST["LARGURA"] = 1050;  
  $_POST["ALTURA"] = 400;   
  
  if(isset($_POST["filesave"])) titulo('(TFFT) '.htmlspecialchars($_POST["filesave"]));
  else titulo('(TFFT)');
  
  include("makeplot.php");
  include("../javascripts.php");
  include("../comum.php");

  
  if(isset($_POST["FIG"])) $FIG = $_POST["FIG"]; else $FIG="";
    
  $FLAG = "";
  $PLOTA_FIG = true;
  $HIST_NAME = "";
    
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
        $_POST["dados_entrada"] = ' - ';
        $_POST["range_freq"] = ' - ';
        $_POST["max_freq"] = ' - ';
        $file = $_FILES["fileimp"]["tmp_name"];
        if($file!="")
        {
           $out  = $TMPDIR.'/'.$_POST["SESSION_ID"].'.tmp';
           $root = $TMPDIR.'/'.$_POST["SESSION_ID"].'.root';
           $tmp  = $TMPDIR.'/'.$_POST["SESSION_ID"].'.tmp.php';
           move_uploaded_file($_FILES["fileimp"]["tmp_name"],$out);
  
           $a = $ROOTDIR.'/bin/root -b -q '.$BASEDIR.'/TFFT/fft.C\(\"'.$out.'\",\"'.$root.'\",\"'.$tmp.'\"\)';
      
           executa($a,$DADOS["SESSION_ID"],$TMPDIR);

           $_POST["dados_entrada"] = $_FILES["fileimp"]["name"];
           if(file_exists($tmp)) include($tmp);
        }
        else echo "<script> alert('Selecione o arquivo de dados para importar');</script>";      
        break;	
      
     case "Tabela sinal":
       $PLOTA_FIG = false;
       $HIST_NAME = "sinal";
       break;
         
     case "Tabela INV":
       $PLOTA_FIG = false;
       $HIST_NAME = "sinal_inv";
       break;
         
     case "Tabela MAG":
       $HIST_NAME = "MAG_axis";
       $PLOTA_FIG = false;
       break;
         
     case "Tabela MAG FIL":
       $PLOTA_FIG = false;
       $HIST_NAME = "MAG_axis_filt";
       break;
         
     case "Tabela FASE":
       $PLOTA_FIG = false;
       $HIST_NAME = "FASE_axis";
       break;
         
     case "Tabela FASE FIL":
       $PLOTA_FIG = false;
       $HIST_NAME = "FASE_axis_filt";
       break;       
      	             
    }
    if(!$PLOTA_FIG)
    {
      $root = $TMPDIR.'/'.$_POST["SESSION_ID"].'_1.root';
      $data_file  = $TMPDIR.'/'.$_POST["SESSION_ID"].'.tmp.php';
  
      $a = $ROOTDIR.'/bin/root -b -q '.$BASEDIR.'/TFFT/extract.C\(\"'.$root.'\",\"'.$data_file.'\",\"'.$HIST_NAME.'\"\)';
      
      executa($a,$DADOS["SESSION_ID"],$TMPDIR);    
    }
  }
    
    
  $a = "filesave"; if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "titulo";   if(!isset($_POST[$a])) $_POST[$a]="";
  
  
  echo '<div id="topo">
        <h1> <a href="../">webROOT</a> (TFFT) - Transformada r&aacute;pida de Fourier </h1>
        <hr>
        </div>
        
          
        <div id="commands"> 
        
        <form method="post" action="index.php" onkeypress="return disableEnter(event)" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="600000" />
        <input type="hidden" name="FIG" value="'.$FIG.'">
        <input type="hidden" name="HOMEDIR" value="'.$HOMEDIR.'/'.$_SESSION["home"].'">
        <input type="hidden" name="VERSION" value="'.$_POST["VERSION"].'">
        <input type="hidden" name="LARGURA" value="'.$_POST["LARGURA"].'">
        <input type="hidden" name="ALTURA" value="'.$_POST["ALTURA"].'">
        <input type="hidden" name="SESSION_ID" value="'.$_POST["SESSION_ID"].'">
        <input type="hidden" name="dados_entrada" value="'.$_POST["dados_entrada"].'">
        <input type="hidden" name="range_freq" value="'.$_POST["range_freq"].'">
        <input type="hidden" name="max_freq" value="'.$_POST["max_freq"].'">
        
	    <center>'
	    .aplicacao($_POST,false).'<br>'
	    .fft($_POST).'<br>'
        .'</center> <br><br><br></form></div>';
  
  echo '<div id="output">';
  if($PLOTA_FIG) mostra_figura($FIG,$TMPURL,$TMPDIR,$_POST["SESSION_ID"]);
  else
  {
    if(file_exists($data_file)) include($data_file);
    if($tabela_ok)
    {
      $NC = 2;
      $NL = 0;
      imprime_tabela($data,$NOME,$NC,$NL,$TITLE);
    }
    else echo '<h2>N&atilde;o encontrei o gr&aacute;fico. Verifique se ele est&aacute; marcado e j&aacute; foi desenhado.</h2>';
  }
  echo '</center></div>';
  include("../footer.php");
  
        
?>
