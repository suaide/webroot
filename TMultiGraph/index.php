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
  $app = 'TMultiGraph';
  if(!isset($_POST["VERSION"])) $_POST["VERSION"]=1;
  
  if(!isset($_POST["SESSION_ID"]))  $_POST["SESSION_ID"]='A'.time().rand();
  
  if(!isset($_POST["LARGURA"])) $_POST["LARGURA"] = 1050;
  if($_POST["LARGURA"]<100) $_POST["LARGURA"] = 100;
  if($_POST["LARGURA"]>1600) $_POST["LARGURA"] = 1600;
  
  if(!isset($_POST["ALTURA"])) $_POST["ALTURA"] = 720;
  if($_POST["ALTURA"]<100) $_POST["ALTURA"] = 100;
  if($_POST["ALTURA"]>1600) $_POST["ALTURA"] = 1600;

  if(!isset($_POST["FUNDO"])) $_POST["FUNDO"] = 0;
  
   
  
  if(isset($_POST["filesave"])) titulo('(TMultiGraph) '.htmlspecialchars($_POST["filesave"]));
  else titulo('(TMultiGraph)');
  
  include("makeplot.php");
  include("../javascripts.php");
  include("../comum.php");

  
  if(isset($_POST["FIG"])) $FIG = $_POST["FIG"]; else $FIG="";
    
  $FLAG = "";
  
  $aplicacoes = cria_lista($HOMEDIR.'/'.$_SESSION["home"], $_POST["multidir"], "TGraph");
  
  if(isset($_POST["action"]))
  {
    switch($_POST["action"])
    {
      case "PLOTA":
        $FIG = makeplot($_POST,$aplicacoes,$ROOTDIR,$TMPDIR, $HOMEDIR, $ROOTSTYLE,$_SESSION);
        $FLAG = '?'.time();
	    break;

      case "Salvar":
        include("../save.php");
    	break;
      	             
    }
  }
    
    
  $a = "filesave"; if(!isset($_POST[$a])) $_POST[$a]="";
  $a = "titulo";   if(!isset($_POST[$a])) $_POST[$a]="";
  
  
  echo '<div id="topo">
        <h1> <a href="../">webROOT</a> (TMultiGraph) - Combinar gr&aacute;ficos simples </h1>
        <hr>
        </div>
        
          
        <div id="commands"> 
        
        <form method="post" action="index.php" onkeypress="return disableEnter(event)" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="600000" />
        <input type="hidden" name="FIG" value="'.$FIG.'">
        <input type="hidden" name="VERSION" value="'.$_POST["VERSION"].'">
        <input type="hidden" name="SESSION_ID" value="'.$_POST["SESSION_ID"].'">
        
	    <center>'
	    .aplicacao($_POST).'<br>'
        .eixos($_POST).'<br>'
	    .legenda($_POST).'<br>'
        .lista_app($HOMEDIR,$_POST,"TGraph")
        .'</center> <br><br><br></form></div>';
  
  echo '<div id="output">';
  mostra_figura($FIG,$TMPURL,$TMPDIR,$_POST["SESSION_ID"],false);
  echo '</center></div>';
  include("../footer.php");
  
        
?>
