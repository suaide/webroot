
<?php

function makeplot($DADOS,$ROOTDIR,$TMPDIR, $ROOTSTYLE)
{
  include("../conf.php");

  $tmp=$TMPDIR."/".$DADOS["SESSION_ID"].".C";
  $arq=$TMPDIR."/".$DADOS["SESSION_ID"].".png";
  $val=$TMPDIR."/".$DADOS["SESSION_ID"].".php";

  $W = $DADOS["LARGURA"];
  $H = $DADOS["ALTURA"];
  $FUNDO = $DADOS["FUNDO"];
  $FRENTE = $DADOS["FRENTE"];
  $FONTE = $DADOS["FONTE"];
  
  $nbins = 10;
  if(isset($DADOS["nbins"]) && is_numeric(trim($DADOS["nbins"]))) $nbins =  htmlspecialchars(trim($DADOS["nbins"]));
    
  if(isset($DADOS["xmin"]) && is_numeric(trim($DADOS["xmin"]))) $xmin = trim($DADOS["xmin"]);
  
  if(isset($DADOS["xmax"]) && is_numeric(trim($DADOS["xmax"]))) $xmax = trim($DADOS["xmax"]);
      
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
    
  $npar = htmlspecialchars(utf8_decode($DADOS["par"]));
  
  $f = fopen($tmp,"w");
  fwrite($f,'
  
  #include "'.$BASEDIR.'/root/erro_TF1.C"

  void '.$DADOS["SESSION_ID"].'()
  {
    '.style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE).'    
    double x1,x2,y1,y2;
    double xmin  = '.$xmin.';
    double xmax  = '.$xmax.';
    int    nbins = '.$nbins.';
    int    npar  = '.$npar.';
    TH1D *h = new TH1D("h","'.htmlspecialchars(utf8_decode($DADOS["titulo"])).'",nbins,xmin,xmax);
    c = new TCanvas("c","c",'.$W.','.$H.');     
    h->SetLineStyle('.$DADOS["marcador"].');  
    h->SetLineColor('.$DADOS["cormarcador"].');
    h->SetLineWidth('.$DADOS["tamanho"].');
            
    double par[20], err[20], cor[20*20], cov[20*20], cho[20*20];
    int tipo[20];
    
    int count = 0;
    
    float xtemp[1000000];
    
    TF1 *d[5];   
    d[0] = new TF1("d1","gaus",-10,10);               d[0]->SetParameters(1,0,1);
    d[1] = new TF1("d2","1*(x<[1])*(x>[0])",-4,4);    d[1]->SetParameters(-sqrt(12)/2,sqrt(12)/2);
    
    TF1 *f = 0;
  ');  
  
   if($DADOS["funcao"]!="")
   {
     $FUNC = $DADOS["funcao"];    
     fwrite($f,' f = new TF1("f","'.$FUNC.'",xmin,xmax);');
   }
   else return "ERROR";
   
   for($i = 0; $i<$npar; $i++)
   {
     $P = $DADOS["valor".$i];
     $E = $DADOS["erro".$i];
     if(is_numeric($P)) fwrite($f,'  par['.$i.'] = '.$P.';'); else fwrite($f,'  par['.$i.'] = 0;');
     if(is_numeric($E)) fwrite($f,'  err['.$i.'] = fabs('.$E.');'); else fwrite($f,'  err['.$i.'] = 0;');
     fwrite($f,' tipo['.$i.'] = '.$DADOS['distr'.$i].';');
   }
   
   for($i = 0;$i<$npar;$i++)
   {
     for($j = 0;$j<=$i;$j++)
     {
        $n = "corrC".$i."L".$j;
        $COR = $DADOS[$n];
        if(!is_numeric($COR)) $COR = 0;
        fwrite($f,'  cor['.$i.'*'.$npar.'+'.$j.'] = '.$COR.';'); 
        fwrite($f,'  cor['.$j.'*'.$npar.'+'.$i.'] = '.$COR.';');
     }
   }
   $XM = $DADOS["valorx"]; if(!is_numeric($XM)) $XM = 0;
   $XE = $DADOS["errox"];  if(!is_numeric($XE)) $XE = 0;
   $XT = $DADOS["distrx"]; 
   fwrite($f,'
     double xmedio = '.$XM.';
     double xerr = '.$XE.';
     int tipox = '.$XT.';
     
     for(int i = 0; i<npar; i++) for(int j = 0; j<npar; j++) cov[i*npar+j] = cor[i*npar+j]*(err[i]*err[j]);
     
     //////////////////////////////
     //calcula matriz de cholewsky
     
     int NP = npar;
     double *C = cho;
     double *V = cov;
     // calculate sqrt(V) as lower diagonal matrix
     for( int i = 0; i < NP; ++i ) 
     {
       for( int j = 0; j < NP; ++j ) 
       {
         C[i*NP+j] = 0;
       }
     }

     for( int j = 0; j < NP; ++j ) 
     {
      // diagonal terms first
      double Ck = 0;
      for( int k = 0; k < j; ++k ) 
      {
        Ck += C[j*NP+k] * C[j*NP+k];
      } // k
      C[j*NP+j] = sqrt( fabs( V[j*NP+j] - Ck ) );

      // off-diagonal terms
      for( int i = j+1; i < NP; ++i ) 
      {
        Ck = 0;
        for( int k = 0; k < j; ++k ) 
        {
          Ck += C[i*NP+k] * C[j*NP+k];
        } //k
        if(C[j*NP+j]!=0 ) C[i*NP+j] = ( V[i*NP+j] - Ck ) / C[j*NP+j];
        else C[i*NP+j] = 0;
      }// i
     } // j 
     //
     //////////////////////////////

     double z[20], x[20];
     double XTEMP;
     
     for(int loop = 0; loop < 50000; loop++)
     {
       for( int i = 0; i < NP; ++i ) 
       {
         z[i] = d[tipo[i]]->GetRandom();
       }

       for( int i = 0; i < NP; ++i ) 
       {
         x[i] = 0;    
         for( int j = 0; j <= i; ++j ) 
         {
           x[i] += cho[i*NP+j] * z[j];
         } // j
       }
  
      for( int i = 0; i < NP; ++i ) 
      {
        f->SetParameter(i,x[i]+par[i]);
      }

      XTEMP = d[tipox]->GetRandom()*xerr+xmedio;
      
      xtemp[loop] = f->Eval(XTEMP);
      h->Fill(xtemp[loop]);
      count ++;
     }
     h->Sumw2(); 
   ');
    
    
  if($DADOS["tipohist"]=="2")
  {
    fwrite($f," h->Scale(1./h->GetEntries());");
  }  
  if($DADOS["tipohist"]=="3")
  {
    fwrite($f," h->Scale(1./h->GetEntries());");
    fwrite($f," h->Scale(1./h->GetBinWidth(1));");
  }  

  $a = atributos_graficos("h","X",$titlex,$fontex,$offsetx,$sizetitx,$XDIV1,$XDIV2,$forcedivx,$XMIN,$XMAX,$forcerangex,$gridx,$logx,
                          $tickx,$tickposx,$ticksizex,$fontemarcx,$fontemarcsizex,$fontemarcoffsetx,$centralizax,$morelogx);
  fwrite($f,$a);
  
  $a = atributos_graficos("h","Y",$titley,$fontey,$offsety,$sizetity,$YDIV1,$YDIV2,$forcedivy,$YMIN,$YMAX,$forcerangey,$gridy,$logy,
                          $ticky,$tickposy,$ticksizey,$fontemarcy,$fontemarcsizey,$fontemarcoffsety,$centralizay,$morelogy);
  fwrite($f,$a); 

  fwrite($f,"h->Draw(\"HIST\");
    double X1LIM = h->GetXaxis()->GetXmin();
    double X2LIM = h->GetXaxis()->GetXmax();
    int countl=0;
    
    double AVG  = 0;
    double AVGL = 0;
    double SIG  = 0;
    double SIGL = 0;
    for(int i = 0; i<count; i++)
    {
      AVG+=xtemp[i];
      if(xtemp[i]>=X1LIM && xtemp[i]<= X2LIM) {AVGL+=xtemp[i]; countl++;}
    }
    AVG/=(double)count;
    if(countl>0) AVGL/=(double)countl;
    for(int i = 0; i<count; i++)
    {      
      SIG  += (xtemp[i] - AVG)*(xtemp[i] - AVG);
      if(xtemp[i]>=X1LIM && xtemp[i]<= X2LIM) SIGL += (xtemp[i] - AVGL)*(xtemp[i] - AVGL);
    }
    if(count>1) { SIG  /= (double)(count-1); SIG  = sqrt(SIG); } else SIG = 0;
    if(countl>1) { SIGL /= (double)(countl-1); SIGL = sqrt(SIGL); } else SIGL = 0;
    
        
    ");

  fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",0,"f",1,"h","AVG","SIG","AVGL","SIGL"));  
  

  fwrite($f,'return; }');  

  fclose($f);
  
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  
  if ($error!=0) return "ERROR";
  return $arq;
}

?>
