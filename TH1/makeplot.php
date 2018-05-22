
<?php

function makeplot($DADOS,$ROOTDIR,$TMPDIR, $ROOTSTYLE)
{
  include("../conf.php");
  
  $tmp=$TMPDIR."/".$DADOS["SESSION_ID"].".C";
  $arq=$TMPDIR."/".$DADOS["SESSION_ID"].".png";
  $val=$TMPDIR."/".$DADOS["SESSION_ID"].".php";
  $txt=$TMPDIR."/".$DADOS["SESSION_ID"].".txt";
  if($DADOS["VERSION"]==2)
  {
    $FILE = $TMPDIR."/".$DADOS["SESSION_ID"].".dat";
    $var = file_get_contents($FILE);
    $DATA = unserialize(urldecode($var));
    $DADOS = array_merge($DADOS,$DATA);
  }

  
  $n = $DADOS["NL"];
  $m = $DADOS["NC"];
  $W = $DADOS["LARGURA"];
  $H = $DADOS["ALTURA"];
  $FUNDO = $DADOS["FUNDO"];
  $FRENTE = $DADOS["FRENTE"];
  $FONTE = $DADOS["FONTE"];
  
  $SCANMAX = 100;  

  $xmin = 9999999999999;
  $xmax = -9999999999999;
  
  $nbins = 10;
  if(isset($DADOS["nbins"]) && is_numeric(trim($DADOS["nbins"]))) $nbins =  htmlspecialchars(trim($DADOS["nbins"]));
  
  $OKXMIN = false;
  $OKXMAX = false;
  
  if(isset($DADOS["xmin"]) && is_numeric(trim($DADOS["xmin"]))) 
  {
    $xmin = trim($DADOS["xmin"]);
    $OKXMIN = true;
  }
  
  if(isset($DADOS["xmax"]) && is_numeric(trim($DADOS["xmax"]))) 
  {
    $xmax = trim($DADOS["xmax"]);
    $OKXMAX = true;
  }
  
  $nn = $n;
  if($nn>$SCANMAX) $nn = $SCANMAX;
  
  if(!$OKXMIN || !$OKXMAX)
  {
    if($DADOS["VERSION"]!=3)
    {
    	for($i = 0; $i<$nn; $i++)
      	  for($j = 0; $j<$m; $j++)
          {
            $L = 'R'.$i.'C'.$j;
            $tmpx = trim($DADOS[$L]);
            if(is_numeric($tmpx))
            {
	          if(!$OKXMIN) { if($xmin>$tmpx) $xmin = $tmpx;}
	          if(!$OKXMAX) { if($xmax<$tmpx) $xmax = $tmpx;} 
	        }
          }
    }  
    else
    {
      $n = 1;
      $m = 40000;
      
      $file = fopen($txt,"r");
      $scan = 0;
      while(!feof($file))
      {
         $line = fgets($file);
         $line = str_replace(",",".",$line);
	     $tmpx = trim($line);
         if(is_numeric($tmpx))
         {
	        if(!$OKXMIN) { if($xmin>$tmpx) $xmin = $tmpx;}
	        if(!$OKXMAX) { if($xmax<$tmpx) $xmax = $tmpx;} 
	     }
	     $scan++;
	     if($scan>$SCANMAX) break;
      }
      fclose($file);
    }
  }
    
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
  fwrite($f,'
  
  #include "'.$BASEDIR.'/root/erro_TF1.C"
  #include <vector>

  void '.$DADOS["SESSION_ID"].'()
  {
    '.style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE).'    
    double x1,x2,y1,y2;
    TH1D *h = new TH1D("h","'.htmlspecialchars(utf8_decode($DADOS["titulo"])).'",'.$nbins.','.$xmin.','.$xmax.');
    c = new TCanvas("c","c",'.$W.','.$H.');     
    h->SetLineStyle('.$DADOS["marcador"].');  
    h->SetLineColor('.$DADOS["cormarcador"].');
    h->SetLineWidth('.$DADOS["tamanho"].');
        
    vector<double> xtemp('.$n*$m.');
    long int actual_size = '.$n*$m.';
    int count = 0;
  ');  

  $a = atributos_graficos("h","X",$titlex,$fontex,$offsetx,$sizetitx,$XDIV1,$XDIV2,$forcedivx,$XMIN,$XMAX,$forcerangex,$gridx,$logx,
                          $tickx,$tickposx,$ticksizex,$fontemarcx,$fontemarcsizex,$fontemarcoffsetx,$centralizax,$morelogx);
  fwrite($f,$a);
  
  $a = atributos_graficos("h","Y",$titley,$fontey,$offsety,$sizetity,$YDIV1,$YDIV2,$forcedivy,$YMIN,$YMAX,$forcerangey,$gridy,$logy,
                          $ticky,$tickposy,$ticksizey,$fontemarcy,$fontemarcsizey,$fontemarcoffsety,$centralizay,$morelogy);
  fwrite($f,$a); 


    if($DADOS["VERSION"]!=3)
    {  
      for($i = 0; $i<$n; $i++)
      {
        for($j = 0; $j<$m; $j++)
        {
          $L = 'R'.$i.'C'.$j;
          if(is_numeric(trim($DADOS[$L]))) fwrite($f,"  xtemp[count++] = ".trim($DADOS[$L])."; ");
        }
      }
    }
    else
    {
      fwrite($f,
      '
      ifstream in("'.$txt.'");
      double tmpdata;
      while(!in.eof())
      {
        in >> tmpdata;
        xtemp[count++] = tmpdata;
        if(count>actual_size)
        {
          actual_size*=2;
          xtemp.resize(actual_size);
        }
        //count++;
        //xtemp.push_back(tmpdata);
      }
      in.close();
      '); 
      
    }
    fwrite($f,"
    if(count == 0) return;
    for(int i = 0; i<count; i++)
    {
      h->Fill(xtemp[i]);
    }   
    h->Sumw2();     
    ");
    
  if($DADOS["tipohist"]=="2")
  {
    fwrite($f," h->Scale(1./h->GetEntries());");
  }  
  if($DADOS["tipohist"]=="3")
  {
    fwrite($f," h->Scale(1./h->GetEntries());");
    fwrite($f," h->Scale(1./h->GetBinWidth(1));");
  }  


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
  else fwrite($f,'double x1fit = '.$XMIN.', x2fit = '.$XMAX.'; ');


  fwrite($f,"
  
  h->Draw(\"".$DADOS["drawhist"]."\");  
  TF1 *f = 0;
  ");
   
  if($DADOS["funcao"]!="")
  {
    $FUNC = $DADOS["funcao"];    
    fwrite($f,'  
    f = new TF1("f","'.$FUNC.'",x1fit,x2fit);
    f->SetLineWidth('.$DADOS["tamanholinha"].');
    f->SetLineStyle('.$DADOS["linha"].');
    f->SetLineColor('.$DADOS["corlinha"].');
    f->SetNpx(1000);
    ');
    
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
      //fwrite($f,'  h->Fit(f,"'.$OPTIONS.' W");');
      fwrite($f,'  h->Fit(f,"'.$OPTIONS.'");');
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

    
  }
    fwrite($f,"
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

  fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",$DADOS["ajuste"],"f",1,"h","AVG","SIG","AVGL","SIGL"));  
  

  fwrite($f,'return; }');  

  fclose($f);
  
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  
  if ($error!=0) return "ERROR";
  return $arq;
}

?>
