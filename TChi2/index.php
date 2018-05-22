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
  $app = 'TChi2';
  
  if(!isset($_POST["VERSION"])) 
  {
    $_POST["VERSION"]=1;
    $_POST["chi2_2d"]=1;
    $_POST["chi2_nx"]=10;
    $_POST["chi2_ny"]=10;
  }
  
  if(!isset($_POST["SESSION_ID"]))  $_POST["SESSION_ID"]='A'.time().rand();
  
  if(!isset($_POST["LARGURA"])) $_POST["LARGURA"] = 1050;
  if($_POST["LARGURA"]<100) $_POST["LARGURA"] = 100;
  if($_POST["LARGURA"]>1600) $_POST["LARGURA"] = 1600;
  
  if(!isset($_POST["ALTURA"])) $_POST["ALTURA"] = 720;
  if($_POST["ALTURA"]<100) $_POST["ALTURA"] = 100;
  if($_POST["ALTURA"]>1600) $_POST["ALTURA"] = 1600;

  if(!isset($_POST["FUNDO"])) $_POST["FUNDO"] = 0;

  if(!isset($_POST["chi2_theta"])) $_POST["chi2_theta"] = 30;
  if(!isset($_POST["chi2_phi"])) $_POST["chi2_phi"] = 50;  
  if($_POST["chi2_phi"]<0) $_POST["chi2_phi"] = 0;
  if($_POST["chi2_phi"]>360) $_POST["chi2_phi"] = 360;
  if($_POST["chi2_theta"]<0) $_POST["chi2_theta"] = 0;
  if($_POST["chi2_theta"]>360) $_POST["chi2_theta"] = 360;
  
  if($_POST["chi2_nx"]<1) $_POST["chi2_nx"]=1;
  if($_POST["chi2_ny"]<1) $_POST["chi2_ny"]=1;
  if($_POST["chi2_nx"]>1000) $_POST["chi2_nx"]=1000;
  if($_POST["chi2_ny"]>1000) $_POST["chi2_ny"]=1000;

   
  
  if(isset($_POST["filesave"])) titulo('(TChi2) '.htmlspecialchars($_POST["filesave"]));
  else titulo('(TChi2)');
  
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
        $FIG = makeplot($_POST,$ROOTDIR,$TMPDIR, $ROOTSTYLE);
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
        <h1> <a href="../">webROOT</a> (TChi2) - An&aacute;lise de Chi-quadrado </h1>
        <hr>
        </div>
        
          
        <div id="commands"> 
        
        <form method="post" action="index.php" onkeypress="return disableEnter(event)" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="600000" />
        <input type="hidden" name="FIG" value="'.$FIG.'">
        <input type="hidden" name="HOMEDIR" value="'.$HOMEDIR.'/'.$_SESSION["home"].'">
        <input type="hidden" name="VERSION" value="'.$_POST["VERSION"].'">
        <input type="hidden" name="SESSION_ID" value="'.$_POST["SESSION_ID"].'">
        
	    <center>'
	    .aplicacao($_POST).'<br>'
	    .select_app($HOMEDIR,$_POST,"TGraph").'<br>'
	    .chi2_settings($_POST).'<br>'
	    .chi2_draw($_POST).'<br>'
        .eixos($_POST,true)
        .'</center> <br><br><br></form></div>';
  
  echo '<div id="output">';
  mostra_figura($FIG,$TMPURL,$TMPDIR,$_POST["SESSION_ID"],false,false);
  mostra_figura($FIG,$TMPURL,$TMPDIR,$_POST["SESSION_ID"],false,false,"_1");
  mostra_figura($FIG,$TMPURL,$TMPDIR,$_POST["SESSION_ID"],false,false,"_2");
  echo '</center></div>';
  include("../footer.php");
  
        
?>
