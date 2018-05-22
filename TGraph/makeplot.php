
<?php

function makeplot($DADOS,$ROOTDIR,$TMPDIR, $ROOTSTYLE)
{
  include("../conf.php");
  
  $tmp=$TMPDIR."/".$DADOS["SESSION_ID"].".C";
  $arq=$TMPDIR."/".$DADOS["SESSION_ID"].".png";
  $val=$TMPDIR."/".$DADOS["SESSION_ID"].".php";
  if($DADOS["VERSION"]==2)
  {
    $FILE = $TMPDIR."/".$DADOS["SESSION_ID"].".dat";
    $var = file_get_contents($FILE);
    $DATA = unserialize(urldecode($var));
    $DADOS = array_merge($DADOS,$DATA);
  }
  
  $f = fopen($tmp,"w");
  
  $S = 1.5;
  
  $W = $DADOS["LARGURA"];
  $H = $DADOS["ALTURA"];
  $FUNDO = $DADOS["FUNDO"];
  $FRENTE = $DADOS["FRENTE"];
  $FONTE = $DADOS["FONTE"];
   
  fwrite($f,
"
#include \"".$BASEDIR."/root/erro_TF1.C\"
#include \"".$BASEDIR."/root/canvas.C\"

void ".$DADOS["SESSION_ID"]."()
{    
  ".style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE)."    
  double x1,x2,y1,y2;
  TGraphErrors *g = new TGraphErrors();
  g->SetTitle(\"".htmlspecialchars(utf8_decode($DADOS["titulo"]))."\"); 
  TCanvas *c = new TCanvas(\"c\",\"c\",".$W.",".$H."); 
  TPad *pad_graf = gPad;
  TPad *pad_res  = gPad;
  double MeanRes = 0;
  double RmsRes  = 0;
");  

  if($DADOS["residuos"]=="1")
  {
    if($DADOS["posicao_residuos"]=="2")
    {
      fwrite($f,"divide_canvas_vert(c,0.3); c->cd(1); pad_graf = gPad; c->cd(2); pad_res = gPad; \n");
      
    }
    if($DADOS["posicao_residuos"]=="3")
    {
      fwrite($f,"divide_canvas_vert(c,0.7); c->cd(2); pad_graf = gPad; c->cd(1); pad_res = gPad; \n");
    }
  }
  
  fwrite($f,"pad_graf->cd();\n");

  $NL = $DADOS["NL"];
  for($i = 0; $i<$NL; $i++)
  {    
    if(is_numeric($DADOS['R'.$i.'C0']) && is_numeric($DADOS['R'.$i.'C1']))
    {
      $X = floatval(trim($DADOS['R'.$i.'C0'])); 
      $Y = floatval(trim($DADOS['R'.$i.'C1'])); 
      $EY = floatval(trim($DADOS['R'.$i.'C2'])); 
      $EX = floatval(trim($DADOS['R'.$i.'C3']));
      fwrite($f,"  g->SetPoint(".$i.",".$X.",".$Y."); g->SetPointError(".$i.",".$EX.",".$EY."); \n");
    }    
  } 

  $OPTIONS = "AP";
  if($DADOS["ligapontos"]=="2") $OPTIONS.="L"; 
  if($DADOS["ligapontos"]=="3") $OPTIONS.="C"; 
  if($DADOS["tipoerro"]=="2") $OPTIONS.="2"; 
  if($DADOS["tipoerro"]=="3") $OPTIONS.="3"; 
  if($DADOS["tipoerro"]=="4") $OPTIONS.="X";
   
  fwrite($f,"  
  g->Draw(\"".$OPTIONS."\");
  g->SetMarkerStyle(".$DADOS["marcador"].");  
  g->SetMarkerColor(".$DADOS["cormarcador"].");
  g->SetMarkerSize(".$DADOS["tamanho"]."/2.0);
  g->SetLineColor(".$DADOS["cormarcador"].");
  g->SetFillColor(".$DADOS["cormarcador"].");
  g->SetFillStyle(3003);
");

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

  $a = atributos_graficos("g","X",$titlex,$fontex,$offsetx,$sizetitx,$XDIV1,$XDIV2,$forcedivx,$XMIN,$XMAX,$forcerangex,$gridx,$logx,
                          $tickx,$tickposx,$ticksizex,$fontemarcx,$fontemarcsizex,$fontemarcoffsetx,$centralizax,$morelogx);
  fwrite($f,$a);
  
  $a = atributos_graficos("g","Y",$titley,$fontey,$offsety,$sizetity,$YDIV1,$YDIV2,$forcedivy,$YMIN,$YMAX,$forcerangey,$gridy,$logy,
                          $ticky,$tickposy,$ticksizey,$fontemarcy,$fontemarcsizey,$fontemarcoffsety,$centralizay,$morelogy);
  fwrite($f,$a); 
  
  $OPTIONS = "N R F ";
  if(isset($DADOS["flagw"])) if($DADOS["flagw"]=="1") $OPTIONS.="W ";
  if(isset($DADOS["flagex"])) if($DADOS["flagex"]=="1") $OPTIONS.="EX0 ";
  if(isset($DADOS["flagint"])) if($DADOS["flagint"]=="1") $OPTIONS.="I ";
  
  if(is_numeric($DADOS["limitemin"]) && is_numeric($DADOS["limitemax"]))
  {
    $MINFIT = floatval($DADOS["limitemin"]);
    $MAXFIT = floatval($DADOS["limitemax"]);
    fwrite($f,'double x1fit = '.$MINFIT.', x2fit = '.$MAXFIT.'; ');
  }
  else fwrite($f,'double x1fit = x1, x2fit = x2; ');
 
  fwrite($f,
"  
  TF1 *f = 0;

");
    
  if($DADOS["funcao"]!="")
  {
    $FUNC = $DADOS["funcao"];
    
    fwrite($f,"  
    f = new TF1(\"f\",\"".$FUNC."\",x1fit,x2fit);
    f->SetNpx(1000);
    f->SetLineStyle(".$DADOS["linha"].");
    f->SetLineColor(".$DADOS["corlinha"].");
    f->SetLineWidth(".$DADOS["tamanholinha"].");
    ");
    
    if($DADOS["par"]!="")
    {
      fwrite($f,"  f->SetParameters(".htmlspecialchars($DADOS["par"]).",0);    \n");
      if($DADOS["parfix"]!="")
      {
        $lista = explode(',',htmlspecialchars($DADOS["parfix"]));
        foreach($lista as $par)
        {
          if(is_numeric($par))
          {
            fwrite($f,'  
            f->FixParameter('.$par.', f->GetParameter('.$par.')); ');
          }
        }
      }
      
    }
    if($DADOS["ajuste"]==1)
    {
      //fwrite($f,'g->Fit(f,"'.$OPTIONS.' W"); ');
      fwrite($f,'g->Fit(f,"'.$OPTIONS.'"); ');
      fwrite($f,'int np = f->GetNpar(); 
                 double *COV = new double[np*np]; 
                 gMinuit->mnemat(COV, np);
                 TF1 *ff = 0;');
    }
    
    if($DADOS["tipolimite"]=="1")
    {
      fwrite($f,'f->SetRange(x1,x2);');
      fwrite($f,"f->Draw(\"sameL\");    \n");
    }
    if($DADOS["tipolimite"]=="2")
    {
      fwrite($f,"f->Draw(\"sameL\");    \n");
    }
    if($DADOS["tipolimite"]=="3")
    {
      fwrite($f,"ff= new TF1(*f); ff->SetLineStyle(f->GetLineStyle()+1); ff->SetRange(x1,x2);");
      fwrite($f,"ff->Draw(\"sameL\");    \n");
      fwrite($f,"f->Draw(\"sameL\");    \n");
    }
    if($DADOS["tipoerros"]!="1" && $DADOS["ajuste"]==1)
    {
       if($DADOS["tipoerros"]=="2") $flag = "false";
       if($DADOS["tipoerros"]=="3") $flag = "true";
       fwrite($f,'double xemin, xemax;
                  f->GetRange(xemin,xemax);
                  if (ff) ff->GetRange(xemin,xemax);
                  TGraphErrors *ge = funcao_erro(f,COV,xemin,xemax,100,'.$flag.');
                  TGraph *gup = funcao_erro_superior(ge);
                  TGraph *gdown = funcao_erro_inferior(ge);
                  gup->Draw("L"); 
                  gdown->Draw("L");
                  gup->SetLineStyle(2);
                  gup->SetLineColor('.$DADOS["corlinha"].');
                  gup->SetLineWidth(1);
                  gdown->SetLineStyle(2);
                  gdown->SetLineColor('.$DADOS["corlinha"].');
                  gdown->SetLineWidth(1);
                  
                  ');   
       
    }
        
  if($DADOS["residuos"]=="1")
  {

  $ticky = $DADOS["tickresiduos"];
  $tickposy = $DADOS["tickposresiduos"];
  $ticksizey =  $DADOS["ticksizeresiduos"];

  $fontey = $DADOS["fontetitresiduos"];
  $titley = htmlspecialchars(utf8_decode($DADOS["eixoresiduos"]));
  $offsety = $DADOS["fontetitoffsetresiduos"];
  $sizetity = $DADOS["fontetitsizeresiduos"];
  
  $fontemarcy = $DADOS["fontemarcresiduos"];
  $fontemarcoffsety = $DADOS["fontemarcoffsetresiduos"];
  $fontemarcsizey = $DADOS["fontemarcsizeresiduos"];
   
  $morelogy = false;
  if(isset($DADOS["morelogresiduos"])) if($DADOS["morelogresiduos"]=="1") $morelogy = true;

  $centralizay = false;
  if(isset($DADOS["centralizaresiduos"])) if($DADOS["centralizaresiduos"]=="1") $centralizay = true;

  
  $logy = false;
  if(isset($DADOS["logresiduos"])) if($DADOS["logresiduos"]=="1") $logy = true;
  $gridy = false;
  if(isset($DADOS["gridresiduos"])) if($DADOS["gridresiduos"]=="1") $gridy = true;
  
  $forcerangey = false;
  $forcedivy   = false;
  
  if($_SESSION['escala']==0)
  {
    $forcerangey = true;
    $forcedivy   = true;    
  }
   
  $YMIN = 0;
  $YMAX = 1;
  $YDIV1 = 10;
  $YDIV2 = 1;
    
  if(is_numeric($DADOS["residuosmin"]) && is_numeric($DADOS["residuosmax"]))
  {
    $YMIN = floatval($DADOS["residuosmin"]);
    $YMAX = floatval($DADOS["residuosmax"]);
    $forcerangey = true;
  }
    
  if(is_numeric($DADOS["divresiduosgr"]) || is_numeric($DADOS["divresiduospq"]))
  {
    $YDIV1 = floatval($DADOS["divresiduosgr"]);
    $YDIV2 = floatval($DADOS["divresiduospq"]);  
    $forcedivy = true;
  }

      $OPTIONS = "AP";
      if($DADOS["ligapontosRes"]=="2") $OPTIONS.="L"; 
      if($DADOS["ligapontosRes"]=="3") $OPTIONS.="C"; 
      
      if($DADOS["tipoerroRes"]=="2") $OPTIONS.="2"; 
      if($DADOS["tipoerroRes"]=="3") $OPTIONS.="3"; 
      if($DADOS["tipoerroRes"]=="4") $OPTIONS.="X";

      if($DADOS["tipo_residuos"]=="1") fwrite($f," bool reduzidos = true;\n");
      else fwrite($f," bool reduzidos = false;\n");

      fwrite($f,"  
      pad_res->cd();
      TGraphErrors *r = new TGraphErrors(g->GetN());
      double *x = r->GetX();
      double *y = r->GetY();
      double *e = r->GetEY();
      double *X = g->GetX();
      double *Y = g->GetY();
      double *E = g->GetEY();
      double *S = g->GetEX();
      double RX2 = 0;
      double RX = 0;
      for(int i = 0; i<g->GetN(); i++)
      {
        double sigma = 1;
        if(E[i] != 0 || S[i]!=0) 
        {
          deriv = f->Derivative(X[i]);
          sigma = sqrt(E[i]*E[i] + deriv*deriv*S[i]*S[i]);
        }
        x[i] = X[i];
        if(reduzidos)
        {
          y[i] = (Y[i] - f(x[i])) / sigma;
          e[i] = 1;
        }
        else
        {
          y[i] = (Y[i] - f(x[i])) ;
          e[i] = sigma;
        }
        RX+=y[i];
        RX2+=y[i]*y[i];
      }
      MeanRes = RX/g->GetN();
      RmsRes  = sqrt(RX2/g->GetN() - MeanRes*MeanRes);
      r->SetTitle(\"".htmlspecialchars($DADOS["titulo"])."\");
      r->SetMarkerStyle(".$DADOS["marcador"].");  
      r->SetMarkerColor(".$DADOS["cormarcador"].");
      r->SetLineColor(".$DADOS["cormarcador"].");
      r->SetMarkerSize(".$DADOS["tamanho"]."/2.0);
      r->SetFillColor(".$DADOS["cormarcador"].");
      r->SetFillStyle(3003);
      r->Draw(\"".$OPTIONS."\");  
      
      float xresmin = g->GetXaxis()->GetXmin();
      float xresmax = g->GetXaxis()->GetXmax();
      ");
  
      $scale = 1;
      if($DADOS["posicao_residuos"]!="1") 
      {
        $scale = 0.3/0.7;
      
        fwrite($f,"pad_res->Modified(); pad_res->Update();
                 TPaveText* titulo = (TPaveText*)(pad_res->FindObject(\"title\"));
                 if(titulo) titulo->SetTextSize(0.104); 
                 pad_res->Modified(); pad_res->Update();\n");
        if($DADOS["posicao_residuos"]=="2") fwrite($f,"r->SetTitle(\"\");\n");
        if($DADOS["posicao_residuos"]=="3") fwrite($f,"g->SetTitle(\"\");\n");

      }
      
      $a = atributos_graficos("r","X",$titlex,$fontex,$offsetx,$sizetitx/$scale,$XDIV1,$XDIV2,$forcedivx,$XMIN,$XMAX,$forcerangex,$gridx,$logx,
                              $tickx,$tickposx,$ticksizex/$scale,$fontemarcx,$fontemarcsizex/$scale,$fontemarcoffsetx,$centralizax,$morelogx);
      fwrite($f,$a);
      
      fwrite($f,"r->GetXaxis()->SetLimits(xresmin,xresmax);");
  
      $a = atributos_graficos("r","Y",$titley,$fontey,$offsety*$scale,$sizetity/$scale,$YDIV1,$YDIV2,$forcedivy,$YMIN,$YMAX,$forcerangey,$gridy,$logy,
                              $ticky,$tickposy,$ticksizey,$fontemarcy,$fontemarcsizey/$scale,$fontemarcoffsety,$centralizay,$morelogy);
      fwrite($f,$a);   
    }
  }
  
  //fwrite($f,"pad_graf->cd();");
  
  fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",$DADOS["ajuste"],"f"));  
  
  fwrite($f," return; } ");
  
  fclose($f);
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  if ($error!=0) return "ERROR";
  
  return $arq;
}

?>
