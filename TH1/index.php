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

  $app = 'TH1';
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
   
  if(isset($_POST["filesave"])) titulo('(TH1) '.htmlspecialchars($_POST["filesave"]));
  else titulo('(TH1)');

  include("makeplot.php");
  include("../javascripts.php");
  include("../comum.php");

  
  if(isset($_POST["NC"])) $NC = $_POST["NC"]; else $NC = 4;     
  if(isset($_POST["NL"])) $NL = $_POST["NL"]; else $NL = 10;
  if(isset($_POST["FIG"])) $FIG = $_POST["FIG"]; else $FIG="";
    
  $FLAG = "";
  $modo =1 ;
  
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
          include("../importadados.php");
        }
        else echo "<script> alert('Selecione o arquivo de dados para importar');</script>";
        break;
     
     case "Editar planilha":
      echo 
      '
      <script type="text/javascript">
        child1 = window.open( "../planilha/index.php?id='.$_POST["SESSION_ID"].'&modo='.$modo.'" )
        if (!child1) 
        {
          var y=document.createElement(\'alerta\');
          y.innerHTML = \'Para editar a planilha &eacute; necess&aacute;rio que voc&ecirc; permita popups de sampa.if.usp.br. Configure o seu browser adequadamente.\';
          alert(y.innerHTML);
        }
      </script> 
      ';     
       break;   
	
     case "+1 L":
       $NL = $NL+1;
       break;
       
     case "+10 L":
       $NL = $NL+10;
       if($NL>$_SESSION['maxv1']) 
       {
         include("../convertev2.php");
       }
       break;

     case "-1 L":
       $NL = $NL-1;
       break;
       
     case "-10 L":
       $NL = $NL-10;
       break;
             
    }
  }
    
  if($NC<2) $NC=2;
  if($NL<1) $NL=1;
  if($NC>4) $NC=4;
  
  
  movecursor($NC,$NL);
  
  $flagajuste="";
  $flagresiduos="";
  if(isset($_POST["ajuste"])) if($_POST["ajuste"]=="1") $flagajuste="checked";
  if(isset($_POST["residuos"])) if($_POST["residuos"]=="1") $flagresiduos="checked";

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
        <h1> <a href="../">webROOT</a> (TH1) - histograma de uma dimens&atilde;o em x </h1>
        <hr>
        </div>
        
        <form method="post" action="index.php" onkeypress="return disableEnter(event)" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
        <input type="hidden" name="NC" value="'.$NC.'">
        <input type="hidden" name="NL" value="'.$NL.'">
        <input type="hidden" name="FIG" value="'.$FIG.'">
        <input type="hidden" name="VERSION" value="'.$_POST["VERSION"].'">
        <input type="hidden" name="SESSION_ID" value="'.$_POST["SESSION_ID"].'">
          
        <div id="commands"> 
        
	    <center>'
	    .aplicacao($_POST).'<br>'
	    .histograma($_POST).'<br>'
        .eixos($_POST).'<br>'
        .funcao_teorica($_POST,false).'<br>';
        
  if($_POST["VERSION"]==1) echo dados_inline($_POST,$NOME,$NC,$NL);
  if($_POST["VERSION"]==2) echo dados_offline($_POST,$NOME);
  if($_POST["VERSION"]==3) echo dados_toobig($_POST,$NOME);
 
  echo '</center> <br><br><br></form></div>';
    
  
  echo '<div id="output">';
  if(isset($_POST["action"]) && $_POST["action"]=="Imprime")
  {
    for($i = 0; $i<4; $i++) $LABEL[$i] = $NOME[$i];
    
    if(isset($_POST["eixox"])) 
    {
      $LABEL[0] = htmlspecialchars($_POST["eixox"]);
      $LABEL[1] = $LABEL[0];
      $LABEL[2] = $LABEL[0];
      $LABEL[3] = $LABEL[0];
    }
    
    imprime_tabela($_POST,$LABEL,$NC,$NL);
  }
  else
  {
    mostra_figura($FIG,$TMPURL,$TMPDIR,$_POST["SESSION_ID"],true);
  }
  echo '</center></div>';
  
  include("../footer.php");
  
        
?>
