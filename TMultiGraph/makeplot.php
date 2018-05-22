
<?php

function makeplot($DADOS, $APLIC, $ROOTDIR, $TMPDIR, $HOMEDIR, $ROOTSTYLE, $SESSION)
{  
  $home = $HOMEDIR."/".$SESSION["home"];
  $tmp  = $TMPDIR."/".$DADOS["SESSION_ID"].".C";
  $arq  = $TMPDIR."/".$DADOS["SESSION_ID"].".png";
  $val  = $TMPDIR."/".$DADOS["SESSION_ID"].".php";
  
  $f = fopen($tmp,"w");
  
  $S = 1.5;
  
  $W = $DADOS["LARGURA"];
  $H = $DADOS["ALTURA"];
  $FUNDO = $DADOS["FUNDO"];
  $FRENTE = $DADOS["FRENTE"];
  $FONTE = $DADOS["FONTE"];

  
  fwrite($f,
"void ".$DADOS["SESSION_ID"]."()
{    
  ".style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE)."    
  double x1,x2,y1,y2;
  TMultiGraph *g = new TMultiGraph();
  g->SetTitle(\"".htmlspecialchars(utf8_decode($DADOS["titulo"]))."\"); 
  c = new TCanvas(\"c\",\"c\",".$W.",".$H."); 
  TGraphErrors *G[1000];
  TF1 *F[1000];
  double X1[10000],X2[1000];
  int indexG = 0;
  int indexF = 0;
");  

  $c = count($APLIC);
  $md5 = array_keys($APLIC);
  
  for($i = 0; $i<$c; $i++)
  {
    $m = $md5[$i];
    $ok = false;
    if(isset($DADOS[$m."-pt"])) if($DADOS[$m."-pt"]==1) $ok = true;
    if(isset($DADOS[$m."-fn"])) if($DADOS[$m."-fn"]==1) $ok = true;
    if($ok)
    {
      $file = $home.'/'.$DADOS["multidir"].'/'.$APLIC[$m];
      
      $postfile = $file.'/post.muroot';       
      $var = file_get_contents($postfile);
      $GRAPH = unserialize(urldecode($var));
      
      if($DADOS[$m."-pt"]==1) // grafico
      {
        fwrite($f,"G[indexG] = new TGraphErrors();");
        if($GRAPH["VERSION"]==2)
        {
          $FILE =  $file.'/values.dat';;
          $var = file_get_contents($FILE);
          $DATA = unserialize(urldecode($var));
          $GRAPH = array_merge($GRAPH,$DATA);      
        }
        for($j = 0; $j<$GRAPH["NL"]; $j++)
        {
          $X = htmlspecialchars(trim($GRAPH['R'.$j.'C0'])); 
          $Y = htmlspecialchars(trim($GRAPH['R'.$j.'C1'])); 
          $EY = htmlspecialchars(trim($GRAPH['R'.$j.'C2'])); 
          $EX = htmlspecialchars(trim($GRAPH['R'.$j.'C3']));
    
          if(is_numeric($X) && is_numeric($Y))
          {
            if(!is_numeric($EX)) $EX = 0;
            if(!is_numeric($EY)) $EY = 0;
            fwrite($f,"G[indexG]->SetPoint(".$j.",".$X.",".$Y.");    G[indexG]->SetPointError(".$j.",".$EX.",".$EY."); \n");
          }   
        }
         $OPTIONS = "P";
          if($GRAPH["ligapontos"]=="2") $OPTIONS.="L"; 
          if($GRAPH["ligapontos"]=="3") $OPTIONS.="C"; 
          if($GRAPH["tipoerro"]=="2") $OPTIONS.="2"; 
          if($GRAPH["tipoerro"]=="3") $OPTIONS.="3"; 

        
        fwrite($f,"G[indexG]->SetTitle(\"".htmlspecialchars(utf8_decode($GRAPH["titulo"]))."\");
                   G[indexG]->SetMarkerStyle(".$GRAPH["marcador"].");  
                   G[indexG]->SetMarkerColor(".$GRAPH["cormarcador"].");
                   G[indexG]->SetMarkerSize(".$GRAPH["tamanho"]."/2.0);
                   G[indexG]->SetLineColor(".$GRAPH["cormarcador"].");
                   G[indexG]->SetFillColor(".$GRAPH["cormarcador"].");
                   G[indexG]->SetFillStyle(3003);

                   g->Add(G[indexG],\"".$OPTIONS."\");
                   indexG++;
               ");       
      }
      
      if($DADOS[$m."-fn"]==1 && $GRAPH["funcao"]!="") // funcao
      {
        $FUNC = $GRAPH["funcao"]; 
        fwrite($f,"F[indexF] = new TF1(Form(\"foo%d\",indexF),\"".$FUNC."\");
                   F[indexF]->SetTitle(\"".htmlspecialchars(utf8_decode($GRAPH["titulo"]))."\");
                   F[indexF]->SetLineWidth(".$GRAPH["tamanholinha"].");
                   F[indexF]->SetLineStyle(".$GRAPH["linha"].");
                   F[indexF]->SetLineColor(".$GRAPH["corlinha"].");
                   F[indexF]->SetNpx(1000);
                   X1[indexF] = -99999;
                   X2[indexF] = -99999;
               ");
        if($GRAPH["par"]!="")
        {
          fwrite($f,"F[indexF]->SetParameters(".htmlspecialchars($GRAPH["par"]).",0);    \n");
        }
        if($GRAPH["ajuste"]==1)
        {
          $status = $file.'/data.php';
          if(file_exists($status))
          {
            include($status);
            if($AJUSTE==1)
            {
              for($j=0;$j<$NPAR;$j++) fwrite($f,"F[indexF]->SetParameter(".$j.",".$PAR[$j].");\n");
            }
          }
          if(is_numeric($GRAPH["limitemin"]) && is_numeric($GRAPH["limitemax"]) && 
             (isset($GRAPH["flaglimite"]) || $GRAPH["tipolimite"]!="1"))
          {
            $MINFIT = floatval($GRAPH["limitemin"]);
            $MAXFIT = floatval($GRAPH["limitemax"]);
            fwrite($f,'X1[indexF] = '.$MINFIT.'; X2[indexF] = '.$MAXFIT.'; ');
          }

        }
        fwrite($f,"indexF++;\n");
      }
    }
  }
   
  fwrite($f," if(indexG==0)
  {
    TGraph* tmp = new TGraph();
    g->Add(tmp);
  }
  
  g->Draw(\"A\");        ");

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
  
  fwrite($f,"for(int i=0;i<indexF;i++) 
  {
    if(X1[i] == -99999 && X2[i] == -99999) F[i]->SetRange(x1,x2);
    else F[i]->SetRange(X1[i],X2[i]); 
    F[i]->Draw(\"sameL\");
  }");
  
  if(isset($DADOS["legendadesenha"])) if($DADOS["legendadesenha"]=="1")
  {
    $X1 = floatval($DADOS["legendax1"]);
    $X2 = floatval($DADOS["legendax2"]);
    $Y1 = floatval($DADOS["legenday1"]);
    $Y2 = floatval($DADOS["legenday2"]);
    $NC = floatval($DADOS["legendacoluna"]);
    $CL = $DADOS["legendalinhacor"];
    $CF = $DADOS["legendafundocor"];
    $LINHA = 0;
    $FUNDO = 0;
    if(isset($DADOS["legendalinha"])) if($DADOS["legendalinha"]=="1") $LINHA = 1; 
    if(isset($DADOS["legendafundo"])) if($DADOS["legendafundo"]=="1") $FUNDO = 1001; 
    
    fwrite($f,"TLegend *L = new TLegend($X1,$Y1,$X2,$Y2); 
               L->SetNColumns($NC); 
               L->SetBorderSize($LINHA);
               L->SetLineColor($CL);
               L->SetFillStyle($FUNDO);
               L->SetFillColor($CF);
               L->SetTextFont($FONTE);
               for(int i=0; i<indexG; i++) 
               {
                 L->AddEntry(G[i],G[i]->GetTitle(),\"p\");
               }
               for(int i=0; i<indexF; i++) 
               {
                 L->AddEntry(F[i],F[i]->GetTitle(),\"l\");
               }
               L->Draw();               
            ");    
  }
  

    
  fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",0,"f",0,"h"));  
  
  fwrite($f," return; } ");
  
  fclose($f);
  
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  if ($error!=0) return "ERROR";
  return $arq;
}

?>
