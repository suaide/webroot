
<?php

function makeplot($DADOS,$ROOTDIR,$TMPDIR, $ROOTSTYLE)
{
  $tmp=$TMPDIR."/".$DADOS["SESSION_ID"].".C";
  $base=$TMPDIR."/".$DADOS["SESSION_ID"];
  $arq=$TMPDIR."/".$DADOS["SESSION_ID"].".png";
  $val=$TMPDIR."/".$DADOS["SESSION_ID"].".php";
  
  $GRAPH = $DADOS["HOMEDIR"].'/'.$DADOS["multidir"].'/'.$DADOS["listapps"];
  if(!file_exists($GRAPH.'/id.php')) return;

  include($GRAPH.'/data.php');
  
  $FILE = $GRAPH.'/post.muroot';
  $var =  file_get_contents($FILE);
  $GRDADOS = unserialize(urldecode($var));
  if($GRDADOS["VERSION"]==2)
  {
    $FILE = $GRAPH."/values.dat";
    $var = file_get_contents($FILE);
    $DATA = unserialize(urldecode($var));
    $GRDADOS = array_merge($DADOS,$DATA);
  }
  
  $f = fopen($tmp,"w");
  
  $S = 1.5;
  
  $W = $DADOS["LARGURA"];
  $H = $DADOS["ALTURA"];
  $FUNDO = $DADOS["FUNDO"];
  $FRENTE = $DADOS["FRENTE"];
  $FONTE = $DADOS["FONTE"];
  
  $PALETTE = $DADOS["mapa_cores"]; 
  $NCONT = $DADOS["ncont"];    
   
  fwrite($f,
"void ".$DADOS["SESSION_ID"]."()
{    
  ".style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE,$PALETTE,$NCONT)."    
  double x1,x2,y1,y2;
  TGraphErrors *g = new TGraphErrors();
  g->SetTitle(\"".htmlspecialchars(utf8_decode($DADOS["titulo"]))."\"); 

  c = new TCanvas(\"c\",\"c\",".$W.",".$H."); 
");  

  $NL = $GRDADOS["NL"];
  $XLOW = 0;
  $XUP = 0;
  for($i = 0; $i<$NL; $i++)
  {    
    if(is_numeric($GRDADOS['R'.$i.'C0']) && is_numeric($GRDADOS['R'.$i.'C1']))
    {
      $X = floatval(trim($GRDADOS['R'.$i.'C0'])); 
      $Y = floatval(trim($GRDADOS['R'.$i.'C1'])); 
      $EY = floatval(trim($GRDADOS['R'.$i.'C2'])); 
      $EX = floatval(trim($GRDADOS['R'.$i.'C3']));
      if($X<$XLOW) $XLOW = $X;
      if($X>$XUP) $XUP = $X;
      fwrite($f,"  g->SetPoint(".$i.",".$X.",".$Y."); g->SetPointError(".$i.",".$EX.",".$EY."); \n");
    }    
  } 

  if($GRDADOS["funcao"]=="") return;
  $FUNC = $GRDADOS["funcao"];

  $MINFIT = $XLOW;
  $MAXFIT = $XUP;
  if(is_numeric($GRDADOS["limitemin"]) && is_numeric($GRDADOS["limitemax"]))
  {
    $MINFIT = floatval($GRDADOS["limitemin"]);
    $MAXFIT = floatval($GRDADOS["limitemax"]);
  }

    
  fwrite($f,"  
  TF1* f = new TF1(\"f\",\"".$FUNC."\",".$MINFIT.",".$MAXFIT.");
  ");

  if($GRDADOS["par"]!="")
  {
      fwrite($f,"  f->SetParameters(".htmlspecialchars($GRDADOS["par"]).",0);    \n");      
  }
  
  if(isset($DADOS["chi2_usa_ajuste"])) if($DADOS["chi2_usa_ajuste"]=="1")
  {
    for($i = 0; $i<$NPAR;$i++)
    {
      if(isset($PAR[$i])) fwrite($f,"  f->SetParameter(".$i.",".$PAR[$i].");   \n");
    }
  }

  $P1 = intval(trim($DADOS["chi2_px"]));
  $P2 = intval(trim($DADOS["chi2_py"]));
  $P1NB = intval(trim($DADOS["chi2_nx"]));
  $P2NB = intval(trim($DADOS["chi2_ny"]));
  
  if($P1NB<1) $P1NB=1;
  if($P2NB<1) $P2NB=1;
  if($P1NB>1000) $P1NB=1000;
  if($P2NB>1000) $P2NB=1000;
  
  $tickx = $DADOS["tickx"];
  $tickposx = $DADOS["tickposx"];
  $ticksizex =  $DADOS["ticksizex"];

  $ticky = $DADOS["ticky"];
  $tickposy = $DADOS["tickposy"];
  $ticksizey =  $DADOS["ticksizey"];

  $tickz = $DADOS["tickz"];
  $tickposz = $DADOS["tickposz"];
  $ticksizez =  $DADOS["ticksizez"];

  $fontex = $DADOS["fontetitx"];
  $fontey = $DADOS["fontetity"];
  $fontez = $DADOS["fontetitz"];
  $titlex = htmlspecialchars(utf8_decode($DADOS["eixox"]));
  $titley = htmlspecialchars(utf8_decode($DADOS["eixoy"]));
  $titlez = htmlspecialchars(utf8_decode($DADOS["eixoz"]));
  $offsetx = $DADOS["fontetitoffsetx"];
  $offsety = $DADOS["fontetitoffsety"];
  $offseta = $DADOS["fontetitoffsetz"];
  $sizetitx = $DADOS["fontetitsizex"];
  $sizetity = $DADOS["fontetitsizey"];
  $sizetitz = $DADOS["fontetitsizez"];
  
  $fontemarcx = $DADOS["fontemarcx"];
  $fontemarcy = $DADOS["fontemarcy"];
  $fontemarcz = $DADOS["fontemarcz"];
  $fontemarcoffsetx = $DADOS["fontemarcoffsetx"];
  $fontemarcoffsety = $DADOS["fontemarcoffsety"];
  $fontemarcoffseta = $DADOS["fontemarcoffsetz"];
  $fontemarcsizex = $DADOS["fontemarcsizex"];
  $fontemarcsizey = $DADOS["fontemarcsizey"];
  $fontemarcsizez = $DADOS["fontemarcsizez"];
    
  $morelogx = false;
  $morelogy = false;
  $morelogz = false;
  if(isset($DADOS["morelogx"])) if($DADOS["morelogx"]=="1") $morelogx = true;
  if(isset($DADOS["morelogy"])) if($DADOS["morelogy"]=="1") $morelogy = true;
  if(isset($DADOS["morelogz"])) if($DADOS["morelogz"]=="1") $morelogz = true;

  $centralizax = false;
  $centralizay = false;
  $centralizaz = false;
  if(isset($DADOS["centralizax"])) if($DADOS["centralizax"]=="1") $centralizax = true;
  if(isset($DADOS["centralizay"])) if($DADOS["centralizay"]=="1") $centralizay = true;
  if(isset($DADOS["centralizaz"])) if($DADOS["centralizaz"]=="1") $centralizaz = true;
  
  $logx = false;
  $logy = false;
  $logz = false;
  if(isset($DADOS["logx"])) if($DADOS["logx"]=="1") $logx = true;
  if(isset($DADOS["logy"])) if($DADOS["logy"]=="1") $logy = true;
  if(isset($DADOS["logz"])) if($DADOS["logz"]=="1") $logz = true;
  $gridx = false;
  $gridy = false;
  $gridz = false;
  if(isset($DADOS["gridx"])) if($DADOS["gridx"]=="1") $gridx = true;
  if(isset($DADOS["gridy"])) if($DADOS["gridy"]=="1") $gridy = true;
  if(isset($DADOS["gridz"])) if($DADOS["gridz"]=="1") $gridz = true;
  
  $forcerangex = false;
  $forcedivx   = false;
  $forcerangey = false;
  $forcedivy   = false;
  $forcerangez = false;
  $forcedivz   = false;
  
  if($_SESSION['escala']==0)
  {
    $forcerangex = true;
    $forcedivx   = true;    
    $forcerangey = true;
    $forcedivy   = true;    
    $forcerangez = true;
    $forcedivz   = true;    
  }
  
  $XMIN = 0;
  $XMAX = 1;
  $XDIV1 = 10;
  $XDIV2 = 1;
  
  $YMIN = 0;
  $YMAX = 1;
  $YDIV1 = 10;
  $YDIV2 = 1;
    
  $ZMIN = 0;
  $ZMAX = 1;
  $ZDIV1 = 10;
  $ZDIV2 = 1;
    
  if(is_numeric($DADOS["xmin"]) && is_numeric($DADOS["xmax"]))
  {
    $XMIN = floatval($DADOS["xmin"]);
    $XMAX = floatval($DADOS["xmax"]);
    $forcerangex = true;
  }
  if(is_numeric($DADOS["ymin"]) && is_numeric($DADOS["ymax"]))
  {
    $YMIN = floatval($DADOS["ymin"]);
    $YMAX = floatval($DADOS["ymax"]);
    $forcerangey = true;
  }
  if(is_numeric($DADOS["zmin"]) && is_numeric($DADOS["zmax"]))
  {
    $ZMIN = floatval($DADOS["zmin"]);
    $ZMAX = floatval($DADOS["zmax"]);
    $forcerangez = true;
  }
  
  if(is_numeric($DADOS["divxgr"]) || is_numeric($DADOS["divxpq"]))
  {
    $XDIV1 = floatval($DADOS["divxgr"]);
    $XDIV2 = floatval($DADOS["divxpq"]);  
    $forcedivx = true;
  }
  
  if(is_numeric($DADOS["divygr"]) || is_numeric($DADOS["divypq"]))
  {
    $YDIV1 = floatval($DADOS["divygr"]);
    $YDIV2 = floatval($DADOS["divypq"]);  
    $forcedivy = true;
  }
  
  if(is_numeric($DADOS["divzgr"]) || is_numeric($DADOS["divzpq"]))
  {
    $ZDIV1 = floatval($DADOS["divzgr"]);
    $ZDIV2 = floatval($DADOS["divzpq"]);  
    $forcedivz = true;
  }
  
  $SCALE = 1;
  
  if($DADOS["chi2_mode"]=="CHI2R") $SCALE = $NDF;
  if($SCALE<1) $SCALE=1;

  fwrite($f,'
  TH2F *h = new TH2F("h","'.htmlspecialchars(utf8_decode($DADOS["titulo"])).'",'.$P1NB.','.$XMIN.','.$XMAX.','.$P2NB.','.$YMIN.','.$YMAX.'); 

  bool init = true;
  float chimax = 0;
  int bxm=1, bym=1;
  float xxm = 0;
  float yym = 0;
  
  for(int i = 1; i<='.$P1NB.'; i++)
  {
    for(int j = 1; j<='.$P2NB.';j++)
    {
      float x = h->GetXaxis()->GetBinCenter(i);
      float y = h->GetYaxis()->GetBinCenter(j);
      f->SetParameter('.$P1.',x);
      f->SetParameter('.$P2.',y);
      float chi = g->Chisquare(f)/'.$SCALE.';
      if(init) 
      {
        chimax = chi;
        init = false;
      }
      if(chi<chimax)
      {
        chimax = chi;
        bxm = i;
        bym = j;
        xxm = x;
        yym = y;
      }
      h->SetBinContent(i,j,chi);
    }
  }
  
  ');


  $a = atributos_graficos("h","X",$titlex,$fontex,$offsetx,$sizetitx,$XDIV1,$XDIV2,$forcedivx,$XMIN,$XMAX,$forcerangex,$gridx,$logx,
                          $tickx,$tickposx,$ticksizex,$fontemarcx,$fontemarcsizex,$fontemarcoffsetx,$centralizax,$morelogx);
  fwrite($f,$a);
  
  $a = atributos_graficos("h","Y",$titley,$fontey,$offsety,$sizetity,$YDIV1,$YDIV2,$forcedivy,$YMIN,$YMAX,$forcerangey,$gridy,$logy,
                          $ticky,$tickposy,$ticksizey,$fontemarcy,$fontemarcsizey,$fontemarcoffsety,$centralizay,$morelogy);
  fwrite($f,$a); 

  $a = atributos_graficos("h","Z",$titlez,$fontez,$offsetz,$sizetitz,$ZDIV1,$ZDIV2,$forcedivz,$ZMIN,$ZMAX,$forcerangez,$gridz,$logz,
                          $tickz,$tickposz,$ticksizez,$fontemarcz,$fontemarcsizez,$fontemarcoffsetz,$centralizaz,$morelogz);
  fwrite($f,$a); 


  $OPTIONS = $DADOS["chi2_draw"];

  fwrite($f,"  

  TH1D *hpx = h->ProjectionX(\"px\",bym,bym);
  TH1D *hpy = h->ProjectionY(\"py\",bxm,bxm);

  gPad->SetTheta(".floatval($DADOS["chi2_theta"]).");
  gPad->SetPhi(".floatval($DADOS["chi2_phi"]).");
  ");

  $a = atributos_graficos("hpx","Y",$titlez,$fontez,$offsetz,$sizetitz,$ZDIV1,$ZDIV2,$forcedivz,$ZMIN,$ZMAX,$forcerangez,$gridz,$logz,
                          $tickz,$tickposz,$ticksizez,$fontemarcz,$fontemarcsizez,$fontemarcoffsetz,$centralizaz,$morelogz);
  fwrite($f,$a); 
  $a = atributos_graficos("hpy","Y",$titlez,$fontez,$offsetz,$sizetitz,$ZDIV1,$ZDIV2,$forcedivz,$ZMIN,$ZMAX,$forcerangez,$gridz,$logz,
                          $tickz,$tickposz,$ticksizez,$fontemarcz,$fontemarcsizez,$fontemarcoffsetz,$centralizaz,$morelogz);
  fwrite($f,$a); 

  
  $O = 0;
  
  if(file_exists($base.".png")) exec("/bin/rm ".$base.".png");
  if(file_exists($base."_1.png")) exec("/bin/rm ".$base."_1.png");
  if(file_exists($base."_2.png")) exec("/bin/rm ".$base."_2.png");
  
     
  if(isset($DADOS["chi2_2d"])) if($DADOS["chi2_2d"]=="1")
  {
    fwrite($f,"  h->Draw(\"".$OPTIONS."\");\n");
    fwrite($f,"  c->Print(\"".$base.".png\");\n");
    $O=1;
  }
  if(isset($DADOS["chi2_draw_px"])) if($DADOS["chi2_draw_px"]=="1")
  {
    fwrite($f,"  hpx->Draw();\n");
    if($O==1) { fwrite($f,"  c->Print(\"".$base."_1.png\");\n"); $O=2;}
    if($O==0) { fwrite($f,"  c->Print(\"".$base.".png\");\n");  $O=1;}    
  }
  if(isset($DADOS["chi2_draw_py"])) if($DADOS["chi2_draw_py"]=="1")
  {
    fwrite($f,"  hpy->Draw();\n");
    if($O==2) { fwrite($f,"  c->Print(\"".$base."_2.png\");\n"); $O=3;}
    if($O==1) { fwrite($f,"  c->Print(\"".$base."_1.png\");\n"); $O=2;}
    if($O==0) { fwrite($f,"  c->Print(\"".$base.".png\");\n");  $O=1;}    
  }
    
  //fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",$DADOS["ajuste"],"f"));  

  
  fwrite($f," return; } ");
  
  fclose($f);
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  if ($error!=0) return "ERROR";

  return $arq;
}

?>
