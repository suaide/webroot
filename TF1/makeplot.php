
<?php

function makeplot($DADOS,$ROOTDIR,$TMPDIR, $ROOTSTYLE)
{
  $tmp=$TMPDIR."/".$DADOS["SESSION_ID"].".C";
  $arq=$TMPDIR."/".$DADOS["SESSION_ID"].".png";
  $val=$TMPDIR."/".$DADOS["SESSION_ID"].".php";
  $W = $DADOS["LARGURA"];
  $H = $DADOS["ALTURA"];
  $FUNDO = $DADOS["FUNDO"];
  $FRENTE = $DADOS["FRENTE"];
  $FONTE = $DADOS["FONTE"];
  
  $tickx = $DADOS["tickx"];
  $tickposx = $DADOS["tickposx"];
  $ticksizex =  $DADOS["ticksizex"];

  $ticky = $DADOS["ticky"];
  $tickposy = $DADOS["tickposy"];
  $ticksizey =  $DADOS["ticksizey"];

  $fontex = $DADOS["fontetitx"];
  $fontey = $DADOS["fontetity"];
  $titlex = htmlspecialchars(utf8_decode($DADOS["eixox"]));
  $titley = htmlspecialchars(utf8_decode($DADOS["eixoy"]));
  $offsetx = $DADOS["fontetitoffsetx"];
  $offsety = $DADOS["fontetitoffsety"];
  $sizetitx = $DADOS["fontetitsizex"];
  $sizetity = $DADOS["fontetitsizey"];
  
  $fontemarcx = $DADOS["fontemarcx"];
  $fontemarcy = $DADOS["fontemarcy"];
  $fontemarcoffsetx = $DADOS["fontemarcoffsetx"];
  $fontemarcoffsety = $DADOS["fontemarcoffsety"];
  $fontemarcsizex = $DADOS["fontemarcsizex"];
  $fontemarcsizey = $DADOS["fontemarcsizey"];

  $morelogx = false;
  $morelogy = false;
  if(isset($DADOS["morelogx"])) if($DADOS["morelogx"]=="1") $morelogx = true;
  if(isset($DADOS["morelogy"])) if($DADOS["morelogy"]=="1") $morelogy = true;

  $centralizax = false;
  $centralizay = false;
  if(isset($DADOS["centralizax"])) if($DADOS["centralizax"]=="1") $centralizax = true;
  if(isset($DADOS["centralizay"])) if($DADOS["centralizay"]=="1") $centralizay = true;

  
  $logx = false;
  $logy = false;
  if(isset($DADOS["logx"])) if($DADOS["logx"]=="1") $logx = true;
  if(isset($DADOS["logy"])) if($DADOS["logy"]=="1") $logy = true;
  $gridx = false;
  $gridy = false;
  if(isset($DADOS["gridx"])) if($DADOS["gridx"]=="1") $gridx = true;
  if(isset($DADOS["gridy"])) if($DADOS["gridy"]=="1") $gridy = true;
  
  $forcerangex = false;
  $forcedivx   = false;
  $forcerangey = false;
  $forcedivy   = false;
  
  if($_SESSION['escala']==0)
  {
    $forcerangex = true;
    $forcedivx   = true;    
    $forcerangey = true;
    $forcedivy   = true;    
  }
  
  $XMIN = 0;
  $XMAX = 1;
  $XDIV1 = 10;
  $XDIV2 = 1;
  
  $YMIN = 0;
  $YMAX = 1;
  $YDIV1 = 10;
  $YDIV2 = 1;
    
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
  
  
  
  $f = fopen($tmp,"w");
  fwrite($f,'TF1 *T = 0;
            double F1(double *x, double*par)  { return T(x[0]); }
            double F2(double *x, double *par) { return T->Integral('.$XMIN.',x[0]); }
            double F3(double *x, double *par) { return T->Derivative(x[0]); }
            double F4(double *x, double *par) { return T->Derivative2(x[0]); }
            void '.$DADOS["SESSION_ID"].'()
            {
              '.style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE).'    
              c = new TCanvas("c","c",'.$W.','.$H.');
              double x1,x2,y1,y2;');  

  if($DADOS["funcao"]!="")
  {
    $FUNC = $DADOS["funcao"];    
    fwrite($f,' T = new TF1("T","'.$FUNC.'",'.$XMIN.','.$XMAX.');    
                TF1 *f = 0;                
                f = new TF1("f",F'.$DADOS["tipofunc"].','.$XMIN.','.$XMAX.',1);;    
                f->SetLineWidth('.$DADOS["tamanholinha"].');
                f->SetLineStyle('.$DADOS["linha"].');
                f->SetLineColor('.$DADOS["corlinha"].');
                f->SetNpx(1000); ');
                
    if($DADOS["par"]!="")
    {
      fwrite($f,"T->SetParameters(".htmlspecialchars($DADOS["par"]).",0);    \n");
    }
    
    fwrite($f,'f->Draw();    
               f->SetTitle("'.htmlspecialchars(utf8_decode($DADOS["titulo"])).'");');

    $a = atributos_graficos("f","X",$titlex,$fontex,$offsetx,$sizetitx,$XDIV1,$XDIV2,$forcedivx,$XMIN,$XMAX,$forcerangex,$gridx,$logx,
                          $tickx,$tickposx,$ticksizex,$fontemarcx,$fontemarcsizex,$fontemarcoffsetx,$centralizax,$morelogx);
    fwrite($f,$a);
  
    $a = atributos_graficos("f","Y",$titley,$fontey,$offsety,$sizetity,$YDIV1,$YDIV2,$forcedivy,$YMIN,$YMAX,$forcerangey,$gridy,$logy,
                          $ticky,$tickposy,$ticksizey,$fontemarcy,$fontemarcsizey,$fontemarcoffsety,$centralizay,$morelogy);
    fwrite($f,$a); 

    fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",0,"f"));   
  
  }

  fwrite($f,'return; }');  

  fclose($f);
  
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  if ($error!=0) return "ERROR";

  return $arq;
}

?>
