

<?php

include("eval.php");    

function executa($comando,$id,$tmp, $dump=false)
{  
  // agora em todo o cluster
  //$a = 'ssh -i /sampa/home/webapp/id_rsa -x webapp@sampagw.if.usp.br "hostname; '.$comando.'"'; 
  $a = $comando;

  exec($a,$out,$err);
    
  exec("chown apache:apache ".$tmp."/".$id.".*");
  return $err;
}

function mostra_figura($FIG,$TMPURL,$TMPDIR,$ID,$AVG=false,$CLICK=true,$POSTFIX="")
{
    if($FIG=="ERROR")
    {
      echo '<h3> Houve um erro na gera&ccedil;&atilde;o da figura </h3>
      <center><br>
      Os erros mais comuns s&atilde;o em f&oacute;rmulas mal escritas, o uso de v&iacute;rgulas como separador decimal de n&uacute;meros (utilize o ponto) e espa&ccedil;os indevidos. Por favor cheque e tente novamente.
      </center>
      ';
      return;
    }
    if(isset($FIG)) 
    {
      $F = $TMPDIR.'/'.$ID.'.php';
      $IMGFILE = $TMPDIR.'/'.$ID.$POSTFIX.'.png';
      echo "\n";
    
      $SIZE = "";
      if($_SESSION["size"]==1) $SIZE = 'width="98%"';
    
      if(file_exists($IMGFILE)) echo '<center><img id="point_img" src="'.$TMPURL.'/'.$ID.$POSTFIX.'.png?'.time().'" '.$SIZE.' >';
      if(file_exists($F)) 
      {
        point($F);
        include($F);
        
        if($CLICK)
        {
        echo '<h3>Clique no gr&aacute;fico para obter coordenadas</h3>
        <form name="coordenadas" method="post">
        x = <input type="text" name="form_x" size="8" readonly="readonly"> &nbsp;&nbsp;&nbsp;&nbsp;
        y = <input type="text" name="form_y" size="8" readonly="readonly">
        </form>
        ';
        }
        
        if($AVG) echo '<br><br>
          <table border="0" width="350px">
          <tr><td></td><td></td><td colspan = 2><h5>Global</h5></td><td></td><td colspan = 2><h5>No intervalo</h5></td></tr>
          <tr><td><h5>Entradas</h5></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><h5>M&eacute;dia</h5></td><td><h5>Sigma</h5></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><h5>M&eacute;dia</h5></td><td><h5>Sigma</h5></td></tr>
          <tr><td><center>'.$ENTRIES.'</center></td><td></td><td><center>'.$AVG.'</center></td><td><center>'.$SIG.'</center></td><td></td><td><center>'.$AVGL.'</center></td><td><center>'.$SIGL.'</center></td></tr>
          </table>';
      

        if($AJUSTE==1)
        {
          echo '<br><h3>Resultados do ajuste</h3>';
	      echo '
	      <table border = "0" width = "35%">
	      <tr><td>N&uacute;mero de par&acirc;metros</td><td>'.$NPAR.'</td></tr>
	      <tr><td>Chi<sup>2</sup></td><td>'.$CHI2.'</td></tr>
	      <tr><td>N&uacute;mero de graus de liberdade</td><td>'.$NDF.'</td></tr>
	      </table>
	      <table border="0" width="35%">
	      <tr><td><h3>par&acirc;metro</h3></td><td><h3>Valor</h3></td><td><h3>Incerteza</h3></td></tr>';
	      for($i = 0;$i<$NPAR;$i++)
	      {
	       echo '<tr><td><center>'.$i.'</center></td><td><center>'.$PAR[$i].'</center></td><td><center>'.$ERR[$i].'</center><td><tr>';
	      }
	      echo '</table> ';
	      
	      echo '<h3>Matriz de covari&acirc;ncia</h3>';
	      echo '<table  border=0 cellpadding=0 cellspacing=0px style="border-left:1px solid #000; border-right:1px solid #000; color:#000" >
	      <tr><td style ="border-top:1px solid #000; border-bottom:1px solid #000;">&nbsp</td><td>
	      <table border=0 cellpadding=3 cellspacing=3 style="color:#000;">';
	      	      
	      for($i = 0;$i<$NPAR;$i++) 
	      { 
	        echo '<tr>';
	        for($j = 0;$j<$NPAR;$j++)
	        {
	         echo '<td><center>'.$COV[$i*$NPAR+$j].'</center></td>';
	        }
	        echo '</tr>';
	      }
	      echo '</table></td><td style ="border-top:1px solid #000; border-bottom:1px solid #000;">&nbsp</td></tr></table></td></tr></table>';
          echo '<br><center>OBS: Se os pontos n&atilde;o possuirem incertezas os erros nos par&acirc;metros s&atilde;o calculados
              ap&oacute;s multiplicar a matriz de covari&acirc;ncia por chi<sup>2</sup>/(NDF-1). </center>';
              
	      echo '<h3>Matriz de correla&ccedil;&atilde;o</h3>';
	      echo '<table  border=0 cellpadding=0 cellspacing=0px style="border-left:1px solid #000; border-right:1px solid #000; color:#000" >
	      <tr><td style ="border-top:1px solid #000; border-bottom:1px solid #000;">&nbsp</td><td>
	      <table border=0 cellpadding=3 cellspacing=3 style="color:#000;">';
	      	      
	      for($i = 0;$i<$NPAR;$i++) 
	      { 
	        echo '<tr>';
	        for($j = 0;$j<$NPAR;$j++)
	        {
	         echo '<td><center>'.number_format($COV[$i*$NPAR+$j]/sqrt($COV[$i*$NPAR+$i]*$COV[$j*$NPAR+$j]),2).'</center></td>';
	        }
	        echo '</tr>';
	      }
	      echo '</table></td><td style ="border-top:1px solid #000; border-bottom:1px solid #000;">&nbsp</td></tr></table></td></tr></table>';
        
        }
      
      } 
    }

}

function style($STYLE, $FUNDO, $FRENTE, $FONTE, $PALETTE = "1", $NCONT = "200")
{
  $a = '  
  int BB = '.$FUNDO.';
  int FF = '.$FRENTE.';
  int FO = '.$FONTE.';
  gROOT->SetStyle("'.$STYLE.'");
  gStyle->SetStripDecimals(kFALSE);
  gStyle->SetOptStat(0);
  //gStyle->SetPalette('.$PALETTE.',0); // 51 a 60
  gStyle->SetNumberContours('.$NCONT.');
  
  if('.$PALETTE.'==1) gStyle->SetPalette(1,0);  // arco iris
  if('.$PALETTE.'==2) gStyle->SetPalette(55,0); // deep rainbow
  if('.$PALETTE.'==3) gStyle->SetPalette(51,0); // azuis
  if('.$PALETTE.'==4) gStyle->SetPalette(52,0); // tons de cinza
  if('.$PALETTE.'==5) gStyle->SetPalette(53,0); // corpo negro
  if('.$PALETTE.'==6) gStyle->SetPalette(56,0); // corpo negro invertido
  if('.$PALETTE.'==7) gStyle->SetPalette(54,0); // gradiente 2 cores
  if('.$PALETTE.'==8) gStyle->SetPalette(57,0); // psicodelico
  
  if('.$PALETTE.'==9) //psicodelico 2
  {
     const Int_t Number = 3;
     Double_t Red[Number]    = { 1.00, 0.00, 0.00};
     Double_t Green[Number]  = { 0.00, 1.00, 0.00};
     Double_t Blue[Number]   = { 1.00, 0.00, 1.00};
     Double_t Length[Number] = { 0.00, 0.50, 1.00};
     Int_t nb=255;
     TColor::CreateGradientColorTable(Number,Length,Red,Green,Blue,nb);
  }
  
  //fonte
  gStyle->SetLabelFont(FO,"xyz");
  gStyle->SetLegendFont(FO);
  gStyle->SetStatFont(FO);
  gStyle->SetTitleFont(FO,"xyz");
  gStyle->SetTitleFont(FO,"h");
   
  // background color
  gStyle->SetCanvasColor(BB);
  gStyle->SetTitleFillColor(BB);
  gStyle->SetStatColor(BB);
  
  // foreground color
  gStyle->SetFrameLineColor(FF);
  gStyle->SetGridColor(FF);
  gStyle->SetStatTextColor(FF);
  gStyle->SetTitleTextColor(FF);
  gStyle->SetLabelColor(FF,"xyz");
  gStyle->SetTitleColor(FF,"xyz");
  gStyle->SetAxisColor(FF,"xyz");
  
  ';
  
  return $a;
}


function estatisticas_grafico($arq,$val,$W,$H,$C,$AJUSTE,$f,$STATS=0,$hist="",$AVG="",$SIG="",$AVGL="",$SIGL="")
{
  $a = $C.'->Modified();
  '.$C.'->Update();
  '.$C.'->Print("'.$arq.'.eps");   
  ofstream file("'.$val.'");

  file << "<?php"<<endl;
  file << "   $XSIZE     = '.$W.';"<<endl;
  file << "   $YSIZE     = '.$H.';"<<endl;
  file << "  "<<endl;
  file << "  "<<endl;

  

  TList* list = '.$C.'->GetListOfPrimitives();
  int n = list->GetEntries();
  int NPAD = 0;
  for(int i=0;i<n;i++)
  {
    TObject *o = list->At(i);
    if(!strcmp(o->ClassName(),"TPad"))
    {
      TPad* p = (TPad*)o;
      file << "   $XLOWNDC["<<NPAD<<"]  = "<<p->GetXlowNDC()<<";"<<endl;
      file << "   $WNDC["<<NPAD<<"]  = "<<p->GetWNDC()<<";"<<endl;
      file << "   $XMIN["<<NPAD<<"]  = "<<p->AbsPixeltoX(p->UtoAbsPixel(0))<<";"<<endl;
      file << "   $XMAX["<<NPAD<<"]  = "<<p->AbsPixeltoX(p->UtoAbsPixel(1))<<";"<<endl;
      file << "   $XLOG["<<NPAD<<"]  = "<<p->GetLogx()<<";"<<endl;
      file << "  "<<endl;
      
      file << "   $YLOWNDC["<<NPAD<<"]  = "<<p->GetYlowNDC()<<";"<<endl;
      file << "   $HNDC["<<NPAD<<"]  = "<<p->GetHNDC()<<";"<<endl;
      file << "   $YMIN["<<NPAD<<"]  = "<<p->AbsPixeltoY(p->VtoAbsPixel(0))<<";"<<endl;
      file << "   $YMAX["<<NPAD<<"]  = "<<p->AbsPixeltoY(p->VtoAbsPixel(1))<<";"<<endl;
      file << "   $YLOG["<<NPAD<<"]  = "<<p->GetLogy()<<";"<<endl;
      file << "  "<<endl;
      file << "  "<<endl;
        
      NPAD++;
    }
  }
  
  if(NPAD==0)
  {
      TPad* p = gPad;
      file << "   $XLOWNDC[0]  = "<<p->GetXlowNDC()<<";"<<endl;
      file << "   $WNDC[0]  = "<<p->GetWNDC()<<";"<<endl;
      file << "   $XMIN[0]  = "<<p->AbsPixeltoX(p->UtoAbsPixel(0))<<";"<<endl;
      file << "   $XMAX[0]  = "<<p->AbsPixeltoX(p->UtoAbsPixel(1))<<";"<<endl;
      file << "   $XLOG[0]  = "<<p->GetLogx()<<";"<<endl;
      file << "  "<<endl;
      
      file << "   $YLOWNDC[0]  = "<<p->GetYlowNDC()<<";"<<endl;
      file << "   $HNDC[0]  = "<<p->GetHNDC()<<";"<<endl;
      file << "   $YMIN[0]  = "<<p->AbsPixeltoY(p->VtoAbsPixel(0))<<";"<<endl;
      file << "   $YMAX[0]  = "<<p->AbsPixeltoY(p->VtoAbsPixel(1))<<";"<<endl;
      file << "   $YLOG[0]  = "<<p->GetLogy()<<";"<<endl;
      file << "  "<<endl;
      file << "  "<<endl;
        
      NPAD++;    
  }
  
  file << "   $NPAD  = "<<NPAD<<";"<<endl;
  file << "  "<<endl;
    
  ';
  
  $a.='gSystem->Exec("convert -quality 100 -density 300 -resize '.$W.'x'.$H.' '.$arq.'.eps '.$arq.'");';
  $a.='gSystem->Exec("/bin/rm '.$arq.'.eps");';
  
  if($STATS==1)
  {
    $a.='
    file << "   \$MED       = "<<'.$hist.'->GetMean()<<";"<<endl;  
    file << "   \$ENTRIES   = "<<'.$hist.'->GetEntries()<<";"<<endl;  
    file << "   \$RMS       = "<<'.$hist.'->GetRMS()<<";"<<endl;  
    file << "   \$AVG       = "<<'.$AVG.'<<";"<<endl;  
    file << "   \$SIG       = "<<'.$SIG.'<<";"<<endl;  
    file << "   \$AVGL      = "<<'.$AVGL.'<<";"<<endl;  
    file << "   \$SIGL      = "<<'.$SIGL.'<<";"<<endl;  
    ';
  }
  
  if($AJUSTE==1)
  {
    $a.="
    int npar = ".$f."->GetNpar();
    file << \"   \$AJUSTE    = 1;\"<<endl;
    file << \"   \$CHI2      = \"<<".$f."->GetChisquare()<<\";\"<<endl;
    file << \"   \$NDF       = \"<<".$f."->GetNDF()<<\";\"<<endl;
    file << \"   \$NPAR      = \"<<".$f."->GetNpar()<<\";\"<<endl;
     
    bool fixed[100];
    int  id[100];
    int index = 0;
    for(int i = 0; i<npar; i++)
    {
      fixed[i] =false;
      file << \"   \$PAR[\"<<i<<\"]    = \"<<".$f."->GetParameter(i)<<\";\"<<endl; 
      file << \"   \$ERR[\"<<i<<\"]    = \"<<".$f."->GetParError(i)<<\";\"<<endl; 
      if(".$f."->GetParError(i)==0) fixed[i] = true;
      else id[i] = index++;
    }
     
    double *fCov = new double[npar*npar];
    double *fCov2 = new double[npar*npar];
    gMinuit->mnemat(fCov, npar);
    
    for(int i = 0; i<npar; i++) for(int j = 0; j<npar; j++) fCov2[i*npar+j] = 0;
    
    for(int i = 0; i<npar; i++) 
    for(int j = 0; j<npar; j++) 
    {
      if(!fixed[i] && !fixed[j])
      {
        fCov2[i*npar+j] = fCov[id[i]*npar+id[j]];
        //fCov2[i*npar+j] = fCov[i*npar+j];
      }
    }
    
    
    
    for(int i = 0; i<npar; i++)
    for(int j = 0; j<npar; j++)
    {
      file << \"   \$COV[\"<<i*npar+j<<\"]    = \"<<fCov2[i*npar+j]<<\";\"<<endl; 
    }
    
    
    ";
  }
  
  $a.="
  file << \"?>\"<<endl;
  file.close();
  ";
  
  return $a;
}

function atributos_graficos($P,$X,$title,$fonte, $offset, $sizetit, $ndiv1, $ndiv2, $forcediv, $min, $max, $forcerange, $grid, $log,
                            $tick="0", $tickpos="", $ticksize="0.02", $fontemarc="132", $sizemarc="0.035",$offsetmarc="0.01",
                            $centraliza=false, $morelog=false)
{
  $E["X"]="x";
  $E["Y"]="y";
  $E["Z"]="z";
  
  $a = '';
  
  $a .= '{
    TAxis *A = '.$P.'->Get'.$X.'axis();
    if(A)
    {
    A->SetTitle("'.$title.'");
    A->SetTitleFont('.$fonte.');
    A->SetTitleSize('.$sizetit.');
    A->SetTitleOffset('.$offset.');
    A->SetLabelFont('.$fontemarc.');
    A->SetLabelSize('.$sizemarc.');
    A->SetLabelOffset('.$offsetmarc.');
  ';
  
  if($centraliza)
  {
    $a.='A->CenterTitle(true);';
  }
  
  if($log)  
  {
    $a.='gPad->SetLog'.$E[$X].'();  ';
    if($morelog) $a.='A->SetMoreLogLabels();';
  }
  if($grid) $a.='gPad->SetGrid'.$E[$X].'(); ';
  
  if($forcerange) // forca escala
  {
    $a.= '
    A->SetLimits('.$min.','.$max.');
    A->SetRangeUser('.$min.','.$max.');
    ';
  }
  if($forcediv) // forca divisoes
  {
    $a.= '
    A->SetNdivisions('.$ndiv1.','.$ndiv2.',0,kFALSE);
    ';
  }
  
  $a.= '
  '.$E[$X].'1 = A->GetXmin();
  '.$E[$X].'2 = A->GetXmax();  
  
  A->SetTicks("'.$tickpos.'");
  A->SetTickLength('.$ticksize.');
  ';
  
  if($X!="Z") $a.='gPad->SetTick'.$E[$X].'('.$tick.');';
  
  $a.='
  gPad->Update();
  }
  }
  ';
  
  return $a;
}



function c_fieldset($id,$content, $legend = "Click to expand/collapse", $boolStartClosed = false)
{
  // This function will create a collapsible fieldset, similar to those
  // used in Drupal.  It will lack the snazziness, because we will not
  // be using jQuery, so that you can use this function as easily as
  // possible, without extra libraries having to be included.
  //
  // This function just returns the HTML and javascript all in a string.
  // To use:  $x = c_fieldSet("content here", "title of fieldset", true);
  //          print $x;

  $start_js_val = 1;
  $fsstate = "open";
  $content_style = "";
  
  $expire = time() + 3600;
    
  if(!isset($_COOKIE[$id]))
  {
    if ($boolStartClosed) 
    {
      $start_js_val = 0;
      $fsstate = "closed";
      $content_style = "display: none;";
      setcookie($id, 0, $expire);
    }
    else setcookie($id, 1, $expire);
  }
  else
  {
    if($_COOKIE[$id] == 0)
    {
      $start_js_val = 0;
      $fsstate = "closed";
      $content_style = "display: none;";
    }
  }
  
  $js = "<script type='text/javascript'> 
  var fieldset_state_$id = $start_js_val;  
  function toggle_fieldset_$id() 
  {    
    var content = document.getElementById('content_$id');
    var fs = document.getElementById('fs_$id');
    var today = new Date(); 
    var expiry = new Date(today.getTime() + 1 * 86400 * 1000); // plus 1 days    
    if (fieldset_state_$id == 1) 
    {
      fieldset_state_$id = 0; content.style.display = 'none'; fs.className = 'c-fieldset-closed'; document.cookie = '$id = 0';
    }
    else 
    {
      fieldset_state_$id = 1; content.style.display = ''; fs.className = 'c-fieldset-open';  document.cookie = '$id = 1';
    } 
  }  
  </script>
  <noscript><b>This page contains collapsible fieldsets which require Javascript to function properly.</b></noscript>";
  
  $rtn = "<fieldset class='c-fieldset-$fsstate' id='fs_$id'>
      <legend><a href='javascript: toggle_fieldset_$id();'>$legend</a></legend>
      <div id='content_$id' style='$content_style'> $content </div> </fieldset>
    $js    
  ";
    
  return $rtn;
}

function legenda($data)
{
  $id = $data["SESSION_ID"]."legenda";
  
  $flagdesenha="";
  $flagfundo="";
  $flaglinha="";
  
  if(isset($data["legendadesenha"])) if($data["legendadesenha"]="1") $flagdesenha="checked";
  if(isset($data["legendafundo"])) if($data["legendafundo"]="1") $flagfundo="checked";
  if(isset($data["legendalinha"])) if($data["legendalinha"]="1") $flaglinha="checked";
  
  if((!isset($data["legendacoluna"])) || (!is_numeric($data["legendacoluna"]))) $data["legendacoluna"]=1;
  if((!isset($data["legendax1"])) || (!is_numeric($data["legendax1"]))) $data["legendax1"]=0.5;
  if((!isset($data["legendax2"])) || (!is_numeric($data["legendax2"]))) $data["legendax2"]=0.9;
  if((!isset($data["legenday1"])) || (!is_numeric($data["legenday1"]))) $data["legenday1"]=0.65;
  if((!isset($data["legenday2"])) || (!is_numeric($data["legenday2"]))) $data["legenday2"]=0.9;
  
  $b = '<table border = "0" width ="100%">
        <tr>
          <td width = "22%"> Desenha</td><td width="8%"> <input type = "checkbox" name = "legendadesenha" value ="1" '.$flagdesenha.' title="Desenha a legenda no gr&aacute;fico"></td>
	      <td width = "17%"> Colunas</td><td><input type = "text" name = "legendacoluna" size ="4" value = "'.htmlspecialchars($data["legendacoluna"]).'" title="n&uacute;mero de colunas na legenda"></td>
        </tr>
        <tr>
          <td>Desenha borda </td><td><input type = "checkbox" name = "legendalinha" value ="1" '.$flaglinha.' title="Desenha a borda na legenda"></td>
	     <td>Cor</td><td>'.cor("legendalinhacor",$data["legendalinhacor"]).'</td>
        </tr>    
        <tr>
          <td>Pinta o fundo </td><td><input type = "checkbox" name = "legendafundo" value ="1" '.$flagfundo.' title="Pinta o fundo da legenda"></td>
	      <td>Cor </td><td>'.cor("legendafundocor",$data["legendafundocor"]).'</td>
        </tr>    
	<tr>
	  <td>Posi&ccedil;&atilde;o no gr&aacute;fico</td><td></td>
	  <td colspan = 2> 
	    x<sub>inf</sub> <input type = "text" name = "legendax1" size ="3" value = "'.htmlspecialchars($data["legendax1"]).'" title="Posi&ccedil&atilde;o X inferior entre 0 e 1"> &nbsp; &nbsp;&nbsp; &nbsp;
	    x<sub>sup</sub> <input type = "text" name = "legendax2" size ="3" value = "'.htmlspecialchars($data["legendax2"]).'" title="Posi&ccedil&atilde;o X superior entre 0 e 1"> <br>
	    y<sub>inf</sub> <input type = "text" name = "legenday1" size ="3" value = "'.htmlspecialchars($data["legenday1"]).'" title="Posi&ccedil&atilde;o Y inferior entre 0 e 1"> &nbsp; &nbsp;&nbsp; &nbsp;
	    y<sub>sup</sub> <input type = "text" name = "legenday2" size ="3" value = "'.htmlspecialchars($data["legenday2"]).'" title="Posi&ccedil&atilde;o Y superior entre 0 e 1"> 
	  </td>
	</tr>
        </table>
  ';
  
  $a = c_fieldset($id,$b,"<h3>Op&ccedil;&otilde;es de legenda</h3>",false);
  return $a;
  
}

function cria_lista($HOMEDIR, $dir="/", $tipo="TGraph")
{
  exec('ls -a "'.$HOMEDIR.'/'.$dir.'"',$files);
  $count = count($files);
  
  $lista = array();

  for ($i = 0; $i<$count; $i++)
  {
    $file = $files[$i];
    
    if ($file != "." && $file != ".." && file_exists($HOMEDIR.'/'.$dir.'/'.$file.'/id.php'))
    {         
      //echo $file.'<br>';
      include($HOMEDIR.'/'.$dir.'/'.$file.'/id.php');
      //echo $APP.' g ';
      if($APP==$tipo)
      {
        $name = md5($dir."/".$file);
        $lista[$name] = $file;
      }
    }    
  } 
  
  return $lista;

}

function select_app($HOMEDIR,$data,$tipo)
{
  $aplicacoes = cria_lista($HOMEDIR.'/'.$_SESSION["home"], $data["multidir"], $tipo);

  $id = $data["SESSION_ID"]."listadearquivos";
  $files = array_keys($aplicacoes);
  $count = count($aplicacoes);
  $b = '<table border = "0" width ="100%">
        <tr>
           <td width="20%">Pasta</td><td>'.pastas($data,"multidir").'</td>
           <td><input type = "submit" name="atualizalista" value = "Atualiza lista" title="Atualiza a lista de gr&aacute;ficos"></td>
        </tr>
        <tr><td>Aplica&ccedil;&atilde;o</td><td>'.apps($data,"listapps",$data["multidir"],"TH1 TGraph").'</td></tr>';
  $b.='</table>';
  $a = c_fieldset($id,$b,"<h3>Escolha da aplica&ccedil;&atilde;o</h3>",false);
  return $a;

}


function lista_app($HOMEDIR,$data,$tipo)
{
  $aplicacoes = cria_lista($HOMEDIR.'/'.$_SESSION["home"], $data["multidir"], $tipo);

  $id = $data["SESSION_ID"]."listadearquivos";
  $files = array_keys($aplicacoes);
  $count = count($aplicacoes);
  $b = '<table border = "0" width ="100%">
        <tr>
           <td width="20%">Pasta</td><td>'.pastas($data,"multidir").'</td>
           <td><input type = "submit" name="atualizalista" value = "Atualiza lista" title="Atualiza a lista de gr&aacute;ficos"></td>
        </tr>';
  $b .= '<table border = "0" width ="100%"><tr><td width="50pt"><center> P &nbsp;&nbsp;&nbsp; F</center></td></tr>';
  if($count==0) $b.='<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
  for ($i = 0; $i<$count; $i++)
  {
    $file = $files[$i];
    $name = $aplicacoes[$file];
    $flag1 = "";
    if(isset($data[$file."-pt"])) if($data[$file."-pt"]==1) $flag1="checked";
    $flag2 = "";
    if(isset($data[$file."-fn"])) if($data[$file."-fn"]==1) $flag2="checked";
    $b .= '<tr>
              <td><center>
                <input type = "checkbox" name = "'.$file.'-pt" value ="1" '.$flag1.' title="Adiciona os pontos do gr&aacute;fico">
                <input type = "checkbox" name = "'.$file.'-fn" value ="1" '.$flag2.' title="Adiciona a fun&ccedil;&atilde;o, se houver"></center>
                <input type = "hidden"   name = "'.$file.'-path" value = "">
              </td>
              <td>'.$name.'</td>
          </tr>';
   
  } 
  $b.='</table>';
  $a = c_fieldset($id,$b,"<h3>Aplica&ccedil;&otilde;es a serem combinadas</h3>",false);
  return $a;

}

function dados_toobig($data,$NOME)
{
  $id = $data["SESSION_ID"]."dadostoobig";

  $b = '<table border = "0" width ="100%">  
        <tr>
          <td width = "20%">Importar</td>
          <td colspan="2"> <input type = "file" name="fileimp"> <input type = "submit" name="action" value = "OK"
          title="Envia o arquivo selecionado"></td>
        </tr> 
        <tr>
        <td colspan="3"><center>Volume de dados muito grande para editar</center></td>
        </tr>
        </table>';
        
  $a = c_fieldset($id,$b,"<h3>Dados</h3>",false);
  return $a;

}

function dados_offline($data,$NOME)
{
  $id = $data["SESSION_ID"]."dadosoffline";

  $b = '<table border = "0" width ="100%">  
        <tr>
          <td width = "20%">Importar</td>
          <td colspan="2"> <input type = "file" name="fileimp"> <input type = "submit" name="action" value = "OK"
          title="Envia o arquivo selecionado"></td>
        </tr> 
        <tr>
          <td colspan="3"><br><center>
          <input type = "submit" name="action" value = "Imprime" title="Formata a tabela para impress&atilde;o">&nbsp;
          <input type = "submit" name="action" value = "Editar planilha" title="Editar/modificar planilha de dados">&nbsp;
          </center></td></tr>
        </table>';
        
  $a = c_fieldset($id,$b,"<h3>Dados</h3>",false);
  return $a;

}

function dados_inline($data,$NOME,$NC,$NL)
{
  $id = $data["SESSION_ID"]."dadosinline";
  
  $PRINTTABELA =true;
  
  
  for($j = 0; $j<$NC; $j++)
  {
   
   if($data["opcao".$j]!="2") echo '<input type="hidden" name="formula'.$j.'" value = "'.$data["formula".$j].'" >';
   
   if($data["opcao".$j]=="2") // calcular
    {

      $b = '<input type="hidden" name="source" value = "'.$j.'">
      <table border="0" width ="100%"> 
      <tr>
        <td colspan = 3><h3>Calcular valores para colunas</h3></td>
      </tr>      
      <tr>
        <td> <b>coluna '.$j.' - '.$NOME[$j].'</b>  = </td>
        <td colspan = 2> <input type="text" size="50" name="formula'.$j.'" value = "'.$data["formula".$j].'" > </td>
      </tr>
      <tr>
        <td> <b>Linhas</b> </td>
        <td> inicial:  <input type="text" size="8" name="li" value = "'.$data["li"].'"> </td>
        <td> final:  <input type="text" size="8" name="lf" value = "'.$data["lf"].'"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>     
      <tr>
        <td colspan = 3><center><input type = "submit" name="calcula" value = "Calcula valores"> <input type = "submit" name="action" value = "Cancela"> </center></td>
      </tr>
      <tr>
      <td colspan =3><center>Para uma ajuda sobre como escrever as f&oacute;rmulas para calcular os elementos de tabela,
      clique, na p&aacute;gina principal do WebROOT, em <br><b>ajuda->Ajuda para calcular elementos de tabelas.</b></center></td>
      </tr>
      </table></center>';
      
      $PRINTTABELA = false;
      
      //$math = new EvalMath();
      //$b.='<center><table border="0" width ="100%"><tr><td>';
      //$b.=$math->help();
      //$b.='</td></tr></table></center>';


    }
    if($_POST["opcao".$j]=="3") // trocar
    {
      
      $b = '<input type="hidden" name="source" value = "'.$j.'">
      <table border="0" width ="100%"> <tr><td><h3>Trocar colunas de lugar</h3></td></tr>      
      <tr><td> <center> Trocar <b>coluna '.$j.' - '.$NOME[$j].'</b>  por       
      <select name = "destination" title="escolha a coluna para trocar">';
      for($i = 0; $i<$NC;$i++)
      {
        $b.='<option value = "'.$i.'"> coluna '.$i.' - '.$NOME[$i].'</option>';
      }
           
      $b.='</center></td></tr><tr><td>&nbsp;</td></tr>
      <tr><td><center><input type = "submit" name="troca" value = "Troca colunas"> <input type = "submit" name="action" value = "Cancela"> </center></td></tr></table><br>';
      
      $PRINTTABELA = false;
        
    }
  }  

  if(!$PRINTTABELA)
  {
    for($i = 0; $i<$NL; $i++)
    {
      for($j = 0; $j<$NC; $j++)
      {
        $NAME  = 'R'.$i.'C'.$j;
        $ID    = $i.'C'.$j;
        $VALUE = $data['R'.$i.'C'.$j];
        echo '<input type="hidden" name="'.$NAME.'" value = "'.$VALUE.'">'; 
      }
    }
  }

 if($PRINTTABELA)
 {

  $b= '<table border = "0" width ="100%">  
        <tr>
          <td width = "20%">Importar</td>
          <td colspan="2"> <input type = "file" name="fileimp"> <input type = "submit" name="action" value = "OK"
          title="Envia o arquivo selecionado"></td>
        </tr> 
        <tr>
          <td colspan="3"><br><center>
          <input type = "submit" name="action" value = "Imprime" title="Formata a tabela para impress&atilde;o">&nbsp;
          <input type = "submit" name="action" value = "+1 L" title="Adiciona uma linha">&nbsp;
          <input type = "submit" name="action" value = "-1 L" title="Remove uma linha">&nbsp;
          <input type = "submit" name="action" value = "+10 L" title="Adiciona 10 linhas">&nbsp;
          <input type = "submit" name="action" value = "-10 L" title="Remove 10 linhas">&nbsp;
          <input type = "submit" name="limpa"  value = "limpa" title="limpa toda a tabela">&nbsp;
          </center></td></tr>
        </table>        
        <br>       
        <table border="0" width ="100%">
        <tr><td></td>';
  
  
  for($j = 0; $j<$NC; $j++)
  {
    $b.= '<td><center>'.$NOME[$j].'<br>
    <select name="opcao'.$j.'" onchange="if(this.value != \'0\') this.form.submit()" title="Selecione uma a&ccedil;&atilde;o para esta coluna">
      <option selected value="0">Op&ccedil;&otilde;es</option>
      <option value="1">Limpar</option>
      <option value="2">Calcular</option>
      <option value="3">Trocar</option>
    </select>
    ';        
  }
  
  $math = new EvalMath();
  $math->suppress_errors=true;
  $li = 0;
  $lf = $NL;
  if(isset($data['source'])) $s = $data["source"];
  if(isset($data['li'])) if(is_numeric($data['li'])) if($data['li']>0 && $data['li']<=$NL) $li = $data['li'];
  if(isset($data['lf'])) if(is_numeric($data['lf'])) if($data['lf']>=$li && $data['lf']<=$NL) $lf = $data['lf'];  
  
  //for($j = 0; $j<$NC; $j++)
  //{
  //  //$b = $b.'<td><center>'.$NOME[$j].'<br><input type="submit" name="limpa'.$j.'" value="limpa"></center></td>';
  //  $b = $b.'<td><center><input type="submit" name="limpa'.$j.'" value="'.$NOME[$j].'" title="clique para limpar a coluna"></center></td>';
  //}
  
  $b = $b.'</tr><tr><td></td>';
  $b = $b.'</tr>';
  for($i = 0; $i<$NL; $i++)
  {
    if(isset($data["troca"]))
    {
      $s = $data["source"];
      $d = $data["destination"];
      $TMP = $data['R'.$i.'C'.$s];
      $data['R'.$i.'C'.$s] = $data['R'.$i.'C'.$d];
      $data['R'.$i.'C'.$d] = $TMP;
    }
    if(isset($data["calcula"]))
    {
      if($i>=$li && $i<=$lf)
      {
        $math->evaluate('i = '.$i);
        for($col = 0; $col<$NC; $col++) 
        {
          if(is_numeric($data['R'.$i.'C'.$col])) $math->evaluate('col'.$col.' = '.$data['R'.$i.'C'.$col]);
          else $math->evaluate('col'.$col.' = 0');
        }
        $data['R'.$i.'C'.$s] = $math->evaluate($data["formula".$s]);
      }
    }
    $b = $b.'<tr>';
    $b = $b.'<td> '.$i.'</td>';
    for($j = 0; $j<$NC; $j++)
    {
      $ID    = $i.'C'.$j;
      $NAME  = 'R'.$ID;
      $VALUE = "";
      if(isset($data[$NAME])) $VALUE=$data[$NAME];
      if(isset($data["opcao".$j])) if($data["opcao".$j]=="1") {$VALUE=""; unset($data[$NAME]);}

      $b = $b.'<td><center><input type="text"size="10"name="'.$NAME.'"id="'.$ID.'"value="'.$VALUE.'"onkeydown=\'KP(this.id,event)\'></center></td>';
    }
    $b = $b.'</tr>';
  }
  
  $b = $b.'</table>';
  
  }

  $a = c_fieldset($id,$b,"<h3>Dados</h3>",false);
  return $a;

}

function imprime_tabela($data,$NOME,$NC,$NL,$TITLE="Tabela de dados")
{
 
  $TABELA = $data;
  $NC = $TABELA["NC"];
  $NL = $TABELA["NL"];
  
  $W = 14*$NC;
  
  echo '<h2>'.$TITLE.'</h2><center>
        <table border="0" width ="'.$W.'%">
        ';

  if($data["VERSION"]==2)
  {
   include("conf.php");
   $FILE  = $TMPDIR.'/'.$data["SESSION_ID"].'.dat';
    if(file_exists($FILE))
    {
      $var = file_get_contents($FILE);
      $TABELA = unserialize(urldecode($var));
    }
    else return;
  } 
  
  
  echo '<tr>';
  $SIZE = 100/$NC;
  for($j = 0; $j<$NC; $j++)
  {
    echo '<td width="'.$SIZE.'%"><center>'.$NOME[$j].'</center></td>';
  }
  echo '</tr><tr><td colspan='.$NC.'><pre style="tab-size: 16;"><hr style="width:100%" ><br>';
  for($i = 0; $i<$NL; $i++)
  {
    //echo '<tr>';
    for($j = 0; $j<$NC; $j++)
    {
      $A = "\t";
      if($j+1==$NC)$A="";
      $NAME  = 'R'.$i.'C'.$j;
      $VALUE = "";
      if(isset($TABELA[$NAME])) $VALUE=htmlspecialchars($TABELA[$NAME]);
      echo $VALUE.$A;
      //echo '<td><center>'.$VALUE.'</center></td>';
    }
    echo '<br>';
    //echo '</tr>';
  }
  
  echo '<hr style="width:100%" ></pre></td></tr></table>';

}

function fft($data)
{
  $id = $data["SESSION_ID"]."fft";

  if(isset($data["fft_signal"]))          $V["0"] = ' checked '; else $V["0"] = '';
  if(isset($data["fft_mag"]))             $V["1"] = ' checked '; else $V["1"] = '';
  if(isset($data["fft_fase"]))            $V["2"] = ' checked '; else $V["2"] = '';
  if(isset($data["fft_mag_filt"]))        $V["3"] = ' checked '; else $V["3"] = '';
  if(isset($data["fft_fase_filt"]))       $V["4"] = ' checked '; else $V["4"] = '';
  if(isset($data["fft_inversa"]))         $V["5"] = ' checked '; else $V["5"] = '';
  if(isset($data["desenha_filtro_MAG"]))  $V["6"] = ' checked '; else $V["6"] = '';
  if(isset($data["desenha_filtro_FASE"])) $V["7"] = ' checked '; else $V["7"] = '';
  
  $file = $data["dados_entrada"];
  
  $b = '<table border = "0" width ="100%">
        <tr>
          <td width = "10%">Sinal</td>
          <td colspan="2"> <input type = "file" name="fileimp"> <input type = "submit" name="action" value = "OK"
          title="Envia o arquivo selecionado e prepara FFTs"></td>
        </tr> 
        
        <tr>
          <td colspan="3">Usando dados de: <b>'.$file.'</b></td>
        </tr>
        <tr>
          <td colspan="3">Frequ&ecirc;ncia m&aacute;xima da FFT: <b>'.$data["range_freq"].' [1/u]</b></td>
        </tr>
        <tr>
          <td colspan="3">Frequ&ecirc;ncia principal da FFT: <b>'.$data["max_freq"].' [1/u]</b></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
          <td> <input type="checkbox" name="fft_signal" value="1" '.$V["0"].'></td>
          <td> Desenha sinal</td>
          <td> <input type = "submit" style="width:110px" name="action" value = "Tabela sinal" title="Exporta sinal como gr&aacute;fico"></td>
        </tr>
        <tr>
          <td> <input type="checkbox" name="fft_inversa" value="1" '.$V["5"].'></td>
          <td> Desenha inversa FFT</td>
          <td> <input type = "submit" style="width:110px"  name="action" value = "Tabela INV" title="Exporta inversa da FFT como gr&aacute;fico"></td>
        </tr>
        <tr>
          <td> <input type="checkbox" name="fft_mag" value="1" '.$V["1"].'></td>
          <td> Desenha magnitude da FFT</td>
          <td> <input type = "submit" style="width:110px"  name="action" value = "Tabela MAG" title="Exporta magnitude da FFT como gr&aacute;fico"></td>
        </tr>
        <tr>
          <td> <input type="checkbox" name="fft_mag_filt" value="1" '.$V["3"].'></td>
          <td> Desenha magnitude filtrada</td>
          <td> <input type = "submit" style="width:110px"  name="action" value = "Tabela MAG FIL" title="Exporta magnitude filtrada da FFT como gr&aacute;fico"></td>
        </tr>
        <tr>
          <td> <input type="checkbox" name="fft_fase" value="1" '.$V["2"].'></td>
          <td> Desenha fase da FFT</td>
          <td> <input type = "submit" style="width:110px"  name="action" value = "Tabela FASE" title="Exporta FASE da FFT como gr&aacute;fico"></td>
        </tr>
        <tr>
          <td> <input type="checkbox" name="fft_fase_filt" value="1" '.$V["4"].'></td>
          <td> Desenha fase filtrada</td>
          <td> <input type = "submit" style="width:110px"  name="action" value = "Tabela FASE FIL" title="Exporta fase filtrada da FFT como gr&aacute;fico"></td>
        </tr>
        </table>
        <br><i><b>OBS:</b> Para imprimir tabelas de gr&aacute;ficos &eacute; necess&aacute;rio que os respectivos gr&aacute;ficos estejam marcados e tenham sido desenhados.</i>
        <br><br>';
        
  $c = '<table border = "0" width ="100%">       
        
        <tr>
          <td> Freq. M&iacute;nima: <input type = "text" size="6" name = "freq_min_FFT" value = "'.htmlspecialchars($data["freq_min_FFT"]).'" title="Valor m&iacute;nimo de frequ&ecirc;ncia para desenhar FFT"></td>
          <td> Freq. M&aacute;xima: <input type = "text" size="6" name = "freq_max_FFT" value = "'.htmlspecialchars($data["freq_max_FFT"]).'" title="Valor m&aacute;ximo de frequ&ecirc;ncia para desenhar FFT"></td>
        </tr>   
        </table>
        <br><i><b>OBS:</b> Deixando os campos em branco desenha em todo o intervalo poss&iacute;vel.</i>
        ';
  $b.= c_fieldset($id."limites",$c,"<h3>Limites de frequ&ecirc;cia para desenhar FFT </h3>",false);  
  
  $b.='<br>';   
          
  $c = '<table border = "0" width ="100%">       
        <tr>
          <td></td><td colspan="2"> <i>MAG\' = G x MAG</i></td>
        </tr>
        <tr>
            <td width = "20%"> MAG  G = </td>
            <td colspan="2"> <input type = "text" size="40" name = "filtro_MAG" value = "'.htmlspecialchars($data["filtro_MAG"]).'" title="Filtro da magnitude da FFT"></td>
            
        </tr>
        <tr>
            <td> Par&acirc;metros </td><td colspan="2"> <input type = "text" size="40" name = "par_MAG" value = "'.htmlspecialchars($data["par_MAG"]).'" title="Valores NUM&Eacute;RICOS para os par&acirc;metros do filtro em magnitude, separados por v&iacute;rgulas. No caso de ajuste, estes valores ser&atilde;o utilizados como chutes iniciais."></td>
        </tr>
        <tr><td>&nbsp;</td></tr>   
        <tr>
          <td></td><td colspan="2"> <i>FASE\' = FASE + F para f<sub>x</sub> > 0</i><br><i>FASE\' = FASE - F para f<sub>x</sub> < 0</i></td>
        </tr>
        <tr>
            <td width = "20%"> FASE F = </td>
            <td colspan="2"> <input type = "text" size="40" name = "filtro_FASE" value = "'.htmlspecialchars($data["filtro_FASE"]).'" title="Filtro da fase da FFT"></td>
            
        </tr>
        <tr>
            <td> Par&acirc;metros </td><td colspan="2"> <input type = "text" size="40" name = "par_FASE" value = "'.htmlspecialchars($data["par_FASE"]).'" title="Valores NUM&Eacute;RICOS para os par&acirc;metros do filtro em fase, separados por v&iacute;rgulas. No caso de ajuste, estes valores ser&atilde;o utilizados como chutes iniciais."></td>
        </tr>
   
        </table>
        <br><i><b>OBS 1:</b> Os filtros s&atilde;o aplicados simetricamente. N&atilde;o é necessário descrever as fun&ccedil;&otilde;es para a parte negativa de frequ&ecirc;ncias.</i>
        <br><br><i><b>OBS 2:</b> Os filtros s&atilde;o aplicados em todas as frequ&ecirc;ncias, independende da faixa que est&aacute; sendo visulatizada nos gr&aacute;ficos.</i>
        <br><br><i><b>OBS 3:</b> Utiliza-se somente os valores de frequ&ecirc;ncia positivos para c&aacute;lculos de filtro.</i>
  ';
  
  $b.=c_fieldset($id."filtros",$c,"<h3>Filtros da FFT </h3>",false);
  
  $a = c_fieldset($id,$b,"<h3>Par&acirc;metros da FFT </h3>",false);
  return $a;
  
  
}


function histograma($data)
{
  $id = $data["SESSION_ID"]."hist";

  $V["1"] = "";
  $V["2"] = "";
  $V["3"] = "";
  if(!isset($data["tipohist"])) $data["tipohist"] = "1";
  $V[$data["tipohist"]]=" selected ";

  $VH["HIST"] = "";
  $VH["HIST E"] = "";
  $VH["E1"] = "";
  if(!isset($data["drawhist"])) $data["tipohist"] = "HIST";
  $VH[$data["drawhist"]]=" selected ";

  
  
  $b = '<table border = "0" width ="100%">
        <tr>
          <td width = "20%"> Canais</td>
          <td> <input type="text" size="4" name="nbins" value="'.htmlspecialchars($data["nbins"]).'" title="N&uacute;mero de canais no histograma"></td>
          <td>
          &nbsp; 
          &nbsp; 
          &nbsp; 
          <select name="tipohist" title="Tipo de histograma">
            <option value="1"'.$V["1"].' >Cont&aacute;gens </option>
            <option value="2"'.$V["2"].' >Probabilidade</option>
            <option value="3"'.$V["3"].' >Densidade prob.</option>
          </select>
          &nbsp; 
          &nbsp; 
          
          <select name="drawhist" title="Modo de desenho">
            <option value="HIST"'.$VH["HIST"].' >Histograma</option>
            <option value="HIST E"'.$VH["HIST E"].' >Hist + erro</option>
            <option value="E1"'.$VH["E1"].' >Erro</option>
          </select>
       	  </td>
        </tr>
	<tr>
            <td>Tipo de linha </td>
            <td colspan = 2>'.linha("marcador", $data["marcador"]).'&nbsp;&nbsp;&nbsp;'.espessura("tamanho",$data["tamanho"]).'&nbsp;&nbsp;&nbsp;'.cor("cormarcador", $data["cormarcador"]).'</td>
        </tr>
        </table>
  ';

  $a = c_fieldset($id,$b,"<h3>Par&acirc;metros do histograma ".$ind."</h3>",false);
  return $a;


}



function grafico($ind,$data,$embed_data)
{
  $id = $data["SESSION_ID"]."grafico".$ind;
  
  $id2 = $id.'extras';
  
  for($i = 1;$i<4; $i++) $V[$i]="";
  if(!isset($data["ligapontos"])) $data["ligapontos"]="1"; 
  $V[$data["ligapontos"]] = "selected";
  
  for($i = 1;$i<5; $i++) $VE[$i]="";
  if(!isset($data["tipoerro"])) $data["tipoerro"]="1"; 
  $VE[$data["tipoerro"]] = "selected";
  
  $b = '<table border = "0" width ="100%">
        <tr>
        <td> Liga pontos: </td>
        <td>
          <select name="ligapontos" title="Tipo de liga pontos">
                   <option value="1" '.$V["1"].'>N&atilde;o liga</option>
                   <option value="2" '.$V["2"].'>Linha simples</option>
                   <option value="3" '.$V["3"].'>Linha suave</option>
                   </select>        
        </td>
        <td>&nbsp;</td>
        <td> Tipo de erro: </td>
        <td>
          <select name="tipoerro" title="Tipo de erro">
                   <option value="1" '.$VE["1"].'>Barras comuns</option>
                   <option value="2" '.$VE["2"].'>Caixa</option>
                   <option value="3" '.$VE["3"].'>&Aacute;rea</option>
                   <option value="4" '.$VE["4"].'>N&atilde;o desenha</option>
                   </select>        
        </td>
        </tr> 
        </table>';
        
  $a = c_fieldset($id2,$b,"<h3>Op&ccedil;&otilde;es extras ".$ind."</h3>",true);
  
  $b = '<table border = "0" width ="100%">
        <tr>
            <td width = "20%">S&iacute;mbolo </td>
            <td colspan=2>'.marcador("marcador", $data["marcador".$ind]).'&nbsp;&nbsp;'.tamanho("tamanho",$data["tamanho".$ind]).'&nbsp;&nbsp;'.cor("cormarcador", $data["cormarcador".$ind]).'</td>
        </tr> ';
        
  if($embed_data)
  {
  }
  
  $b = $b.'</table> '.$a;
  //$b = $b.'</table> ';
  
  $a = c_fieldset($id,$b,"<h3>Par&acirc;metros do gr&aacute;fico ".$ind."</h3>",false);
  return $a;

}
function funcao_erro($data)
{
  $id = $data["SESSION_ID"]."funcao";
  
  $flagajuste="";
  $flagresiduos="";
  $cols = 2;
  $width = ' size = "52" ';
  
  $var = $data["par"];
  if(!isset($data["par"]))  $var = 1;
  if(!is_numeric($var)) $var = 1;
  if($var<1) $var = 1;
  if($var>15) $var = 15;

  $b = '<table border = "0" width ="100%">
        <tr>
            <td width = "30%"> F&oacute;rmula: y = </td><td colspan="'.$cols.'"> <input type = "text" '.$width.' name = "funcao" value = "'.htmlspecialchars($data["funcao"]).'" title="F&oacute;rmula matem&aacute;tica"></td>
        </tr>
        <tr>
            <td> No. de Par&acirc;metros </td>
            <td> <input type = "text"  size=5 name = "par" value = "'.htmlspecialchars($var).'" title="N&uacute;mero de par&acirc;metros na fun&ccedil;&atilde;o. Clique em ATUALIZAR"></td>
            <td> <input type = "submit" name="atualizar" value = "Atualizar"></td>
        </tr>    
        </table>';
  
   for($i = 1;$i<5; $i++) $V[$i]="";
   
   if(isset($data["distrx"])) $V[$data["distrx"]] = "selected";
   else $V["0"] = "selected";

    $c = '<table border = "0" width ="100%">
        <tr>
        <td width = "20%"> 
        vari&aacute;vel x = 
        </td>
        <td>
        <input type = "text" name = "valorx" size="6" value = "'.htmlspecialchars($data["valorx"]).'" title="Valor da vari&aacute;vel independente">    
         &nbsp;<u>+</u>&nbsp; <input type = "text" name = "errox" size="6" value = "'.htmlspecialchars($data["errox"]).'" title="Erro da vari&aacute;vel independente">
         &nbsp;&nbsp; Distribui&ccedil;&atilde;o:
         <select name="distrx" title="Distribui&ccedil;&atilde;o">
                   <option value="0" '.$V["0"].'>Gaussiana</option>
                   <option value="1" '.$V["1"].'>Uniforme</option>
         </select> 
         </td>
         </tr>
         </table>
         ' ;      

    $b .= c_fieldset($id.'independente',$c,"<h3>Vari&aacute;vel independente (x)</h3>",true);  
    
    for($i = 0;$i<$var;$i++)
    {
      for($j = 1;$j<5; $j++) $V[$j]="";
   
      if(isset($data["distr".$i])) $V[$data["distr".$i]] = "selected";
      else $V["0"] = "selected";
      $c = '
      <table border = "0" width ="100%">
        <tr>
        <td width = "20%"> 
        par&acirc;metro '.$i.' = 
        </td>
        <td>
        <input type = "text" name = "valor'.$i.'" size="6" value = "'.htmlspecialchars($data["valor".$i]).'" title="Valor do par&acirc;metro '.$i.'">    
         &nbsp;<u>+</u>&nbsp; <input type = "text" name = "erro'.$i.'" size="6" value = "'.htmlspecialchars($data["erro".$i]).'" title="Erro do par&acirc;metro '.$i.'">
         &nbsp;&nbsp; Distribui&ccedil;&atilde;o:
         <select name="distr'.$i.'" title="Distribui&ccedil;&atilde;o">
                   <option value="0" '.$V["0"].'>Gaussiana</option>
                   <option value="1" '.$V["1"].'>Uniforme</option>
         </select> 
         </td>
         </tr>
         </table>
         ' ;      

      $b .= c_fieldset($id.'parametro'.$i,$c,"<h3>Par&acirc;metro ".$i."</h3>",true);      
    }
    
    $size = 400/$var - $var;
    $c='<center><table border = "0" width ="400">
    <tr><td></td>';
    for($i = 0;$i<$var;$i++) $c.='<td><b><center>'.$i.'</center></b></td>';
    $c.='</tr>';
    for($i = 0;$i<$var;$i++)
    {
      $c.='<tr><td><b><center>'.$i.'</center></b></td>';
      for($j = 0;$j<=$i;$j++)
      {
        $n = "corrC".$i."L".$j;
        $da = $data[$n];
        if(!isset($data[$n])) if($i==$j) $da = 1; else $da = 0;
        if(!is_numeric($da)) if($i==$j) $da = 1; else $da = 0;
        
        $flag = "";
        if($i==$j) $flag=' readonly="readonly" ';
        
        $c.='<td><input type = "text" name = "'.$n.'" style="width:'.$size.'px;" value = "'.htmlspecialchars($da).'"'.$flag.'></td>';
      }
      $c.='</tr>';
    }
    $c.='</table></center>';
    $b .= c_fieldset($id.'correlacao',$c,"<h3>Matriz de Correla&ccedil;&atilde;o entre os par&acirc;metros</h3>",true);  
    
    
  $a = c_fieldset($id,$b,"<h3>Fun&ccedil;&atilde;o para propagar erro y = f(x)</h3>",false);
  return $a;

}


function funcao_teorica($data, $residuos, $ajuste = true, $extra = false)
{
  $id = $data["SESSION_ID"]."funcao";
  
  $flagajuste="";
  $flagresiduos="";
  $cols = 2;
  $width = ' size = "42" ';
  if($ajuste || $residuos) {$cols = 1; $width =  ' size = "26" ';} 
  if(isset($data["ajuste"]))   { if($data["ajuste"]=="1") $flagajuste="checked";}
  if(isset($data["residuos"])) { if($data["residuos"]=="1") $flagresiduos="checked";}

  $b = '<table border = "0" width ="100%">
        <tr>
            <td width = "20%"> F&oacute;rmula: y = </td><td colspan="'.$cols.'"> <input type = "text" '.$width.' name = "funcao" value = "'.htmlspecialchars($data["funcao"]).'" title="F&oacute;rmula matem&aacute;tica"></td>';
  
  if($ajuste) $b=$b.'<td><input type = "checkbox" name = "ajuste" value ="1" '.$flagajuste.' title="Realiza o ajuste da fun&ccedil;&atilde;o"> Ajuste   </td>';
  
  $b = $b.'</tr>
        <tr>
            <td> Par&acirc;metros </td><td colspan="'.$cols.'"> <input type = "text"  '.$width.' name = "par" value = "'.htmlspecialchars($data["par"]).'" title="Valores NUM&Eacute;RICOS para os par&acirc;metros da fun&ccedil;&atilde;o, separados por v&iacute;rgulas. No caso de ajuste, estes valores ser&atilde;o utilizados como chutes iniciais."></td>';
            
  if($residuos) $b = $b.'<td><input type = "checkbox" name = "residuos" value ="1" '.$flagresiduos.' title="Obt&eacute;m o gr&aacute;fico de res&iacute;duos">  Res&iacute;duos   </td>';
  
  $b = $b.'</tr>    
        <tr>
            <td> Tipo de linha</td>
            <td colspan=2> '.linha("linha", $data["linha"]).'&nbsp;&nbsp;'.espessura("tamanholinha",$data["tamanholinha"]).'&nbsp;&nbsp;'.cor("corlinha",  $data["corlinha"]).'</td>
        </tr>  
  ';
  
  if($extra)
  {
   for($i = 1;$i<5; $i++) $V[$i]="";
   
   if(isset($data["tipofunc"])) $V[$data["tipofunc"]] = "selected";
   else $V["1"] = "selected";

    $b = $b.'<tr>
              <td >Tipo de gr&aacute;fico</td>
              <td> <select name="tipofunc" title="Tipo de gr&aacute;fico a ser mostrado">
                   <option value="1" '.$V["1"].'>f(x)</option>
                   <option value="2" '.$V["2"].'>Integral de f(x)</option>
                   <option value="3" '.$V["3"].'>df(x)/dx</option>
                   <option value="4" '.$V["4"].'>d<sup>2</sup>f(x)/dx<sup>2</sup></option>
                   </select>
              <td></td>
            </tr>
    ';
  }
  
  $b = $b.'</table>';
  
  if($ajuste)
  {
    if(isset($data["flaglimite"])) if($data["flaglimite"]=="1") $flaglimite="checked";
    if(isset($data["flagw"])) if($data["flagw"]=="1") $flagw="checked";
    if(isset($data["flagex"])) if($data["flagex"]=="1") $flagex="checked";
    if(isset($data["flagint"])) if($data["flagint"]=="1") $flagint="checked";
   
    for($i=1;$i<5;$i++) $V[$i]="";
    if(isset($data["tipolimite"])) $V[$data["tipolimite"]] = " selected ";
    else $V["1"] = "selected";
    
    $d = '<select name = "tipolimite" title="Modo de desenho do ajuste">
          <option value="1" '.$V["1"].'> todo gr&aacute;fico</option>
          <option value="2" '.$V["2"].'> nos limites </option>
          <option value="3" '.$V["3"].'> pontilhado </option>
          ';

    for($i=1;$i<5;$i++) $V[$i]="";
    if(isset($data["tipoerros"])) $V[$data["tipoerros"]] = " selected ";
    else $V["1"] = "selected";
    
    $e = '<select name = "tipoerros" title="Representa&ccedil;&atilde;o das incertezas do ajuste">
          <option value="1" '.$V["1"].'> sem incertezas</option>
          <option value="2" '.$V["2"].'> incertezas sem covari&acirc;ncia</option>
          <option value="3" '.$V["3"].'> incertezas com covari&acirc;ncia</option>
          ';

    $c = '<table border = "0" width ="100%">
        <tr>
          <td width="20%"> Fixa par&acirc;metros:</td> 
          <td colspan=2>  <input type = "text" name = "parfix" size="32" value = "'.htmlspecialchars($data["parfix"]).'" title="Lista de par&acirc;metros que n&atilde;o ser&atilde;o ajustados">    
         </td>
        </tr>       

        <tr>
          <td> Limites de ajuste:</td> 
          <td colspan=2> Min: <input type = "text" name = "limitemin" size ="9" value = "'.htmlspecialchars($data[$l."limitemin"]).'" title="Valor m&iacute;nimo x para ajuste"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
               Max: <input type = "text" name = "limitemax" size ="9" value = "'.htmlspecialchars($data[$l."limitemax"]).'" title="Valor m&aacute;ximo x para ajuste"></td>
        </tr>       
        
        <tr>
          <td> Modos de ajuste: </td>
          <td colspan=2> 
             <input type = "checkbox" name = "flagw" value ="1" '.$flagw.' title="N&atilde;o considera incertezas nos pontos">  W = 1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             <input type = "checkbox" name = "flagex" value ="1" '.$flagex.' title="N&atilde;o considera incertezas em x">  EX = 0 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             <input type = "checkbox" name = "flagint" value ="1" '.$flagint.' title="Considera a integral no canal para o ajuste">  Integral
          </td>
        </tr>
        <tr>
          <td> Modos de desenho: </td>
           <td> '.$d.'</td>
           <td> '.$e.'</td>
        </tr>        
        </table>
        ';
        
    $d = c_fieldset($id.'ajuste',$c,"<h3>Op&ccedil;&otilde;es de ajuste</h3>",true);
    $b = $b.$d;
  
  }
  
  if($residuos)
  {
    for($i=1;$i<5;$i++) $V[$i]="";
    if(isset($data["tipo_residuos"])) $V[$data["tipo_residuos"]] = " selected ";
    else $V["1"] = " selected ";
    
    $e1 = '<select name = "tipo_residuos" title="Tipo dos res&iacute;duos">
          <option value="1" '.$V["1"].'> Res&iacute;duos reduzidos</option>
          <option value="2" '.$V["2"].'> Res&iacute;duos absolutos</option>
          ';
    
    
    for($i=1;$i<5;$i++) $V[$i]="";
    if(isset($data["posicao_residuos"])) $V[$data["posicao_residuos"]] = " selected ";
    else $V["1"] = " selected ";
    
    $e2 = '<select name = "posicao_residuos" title="Posi&ccedil;&atilde;o dos res&iacute;duos">
          <option value="1" '.$V["1"].'> Somente res&iacute;duos</option>
          <option value="2" '.$V["2"].'> Painel abaixo</option>
          <option value="3" '.$V["3"].'> Painel acima</option>
          ';

   if(isset($data["noErrRes"]))   if($data["noErrRes"]=="1") $flagNoErrRes="checked";
   
   for($i = 1;$i<4; $i++) $V[$i]="";
   if(!isset($data["ligapontosRes"])) $data["ligapontosRes"]="1"; 
   $V[$data["ligapontosRes"]] = "selected";
  
   for($i = 1;$i<5; $i++) $VE[$i]="";
   if(!isset($data["tipoerroRes"])) $data["tipoerroRes"]="1"; 
   $VE[$data["tipoerroRes"]] = "selected";

        

    $c = '<table border = "0" width ="100%">
        <tr>
          <td width="50%"> Posi&ccedil;&atilde;o: '.$e2.'</td> 
          <td > Tipo:  '.$e1.'</td>   
         </td>
        </tr>
        <tr>
        <td> Liga pontos: 
          <select name="ligapontosRes" title="Tipo de liga pontos">
                   <option value="1" '.$V["1"].'>N&atilde;o liga</option>
                   <option value="2" '.$V["2"].'>Linha simples</option>
                   <option value="3" '.$V["3"].'>Linha suave</option>
                   </select>        
        </td>
        <td> Erro: 
          <select name="tipoerroRes" title="Tipo de erro">
                   <option value="1" '.$VE["1"].'>Barras comuns</option>
                   <option value="2" '.$VE["2"].'>Caixa</option>
                   <option value="3" '.$VE["3"].'>&Aacute;rea</option>
                   <option value="4" '.$VE["4"].'>N&atilde;o desenha</option>
                   </select>        
        </td>
        </tr> 

        </table>';
    
    $c.= eixo('residuos', $data, true);
    
    $d = c_fieldset($id.'opresiduos',$c,"<h3>Op&ccedil;&otilde;es de res&iacute;duos</h3>",true);
    
    $b.=$d;
  }
    
  $a = c_fieldset($id,$b,"<h3>Fun&ccedil;&atilde;o te&oacute;rica y = f(x)</h3>",false);
  return $a;

}
function funcao_teorica_2($data, $residuos, $ajuste = true, $extra = false)
{
  $id = $data["SESSION_ID"]."funcao";
  
  $flagajuste="";
  $flagresiduos="";
  $cols = 2;
  $width = ' size = "42" ';
  if($ajuste || $residuos) {$cols = 1; $width =  ' size = "26" ';} 

  if(isset($data["ajuste"]))   if($data["ajuste"]=="1") $flagajuste="checked";
  if(isset($data["residuos"])) if($data["residuos"]=="1") $flagresiduos="checked";

  $b = '<table border = "0" width ="100%">
        <tr>
            <td width = "20%"> F&oacute;rmula: z = </td><td colspan="'.$cols.'"> <input type = "text"  '.$width.' name = "funcao" value = "'.htmlspecialchars($data["funcao"]).'" title="F&oacute;rmula matem&aacute;tica"></td>';
  
  if($ajuste) $b=$b.'<td><input type = "checkbox" name = "ajuste" value ="1" '.$flagajuste.' title="Realiza o ajuste da fun&ccedil;&atilde;o"> Ajuste   </td>';
  
  $b = $b.'</tr>
        <tr>
            <td> Par&acirc;metros </td><td colspan="'.$cols.'"> <input type = "text"  '.$width.' name = "par" value = "'.htmlspecialchars($data["par"]).'" title="Valores NUM&Eacute;RICOS para os par&acirc;metros da fun&ccedil;&atilde;o, separados por v&iacute;rgulas. No caso de ajuste, estes valores ser&atilde;o utilizados como chutes iniciais."></td>';
            
  if($residuos) $b = $b.'<td><input type = "checkbox" name = "residuos" value ="1" '.$flagresiduos.' title="Obt&eacute;m o gr&aacute;fico de res&iacute;duos">  Res&iacute;duos   </td>';
  
  
  if($extra)
  {
  }
  
  $b = $b.'</table>';
  
  if($ajuste)
  {
  }
    
  $a = c_fieldset($id,$b,"<h3>Fun&ccedil;&atilde;o te&oacute;rica z = f(x,y)</h3>",false);
  return $a;

}

function chi2_settings($data)
{
  $id = $data["SESSION_ID"]."chi2settings";

  if(isset($data["chi2_usa_ajuste"]))   if($data["chi2_usa_ajuste"]=="1") $flagajuste="checked";
  
  $b = '<table border = "0" width ="100%">
        <tr> 
          <td> Usa par&acirc;metros ajustados</td>
          <td> <input type = "checkbox" name = "chi2_usa_ajuste" value ="1" '.$flagajuste.' title="Se marcado utiliza os parâcirc;metros ajustados. Senão, utiliza valores de entrada."> </td>
          <td> Tipo de mapa</td>
          <td> '.Chi2Mode("chi2_mode",$data["chi2_mode"]).'</td>
        </tr>
        <tr>
          <td> Par&acirc;metro no eixo X </td>
          <td> <input type = "text"  size = "4" name = "chi2_px" value = "'.htmlspecialchars($data["chi2_px"]).'" title="N&uacute;mero do par&acirc;metro em X"></td>
          <td> No. de bins em X</td>
          <td> <input type = "text"  size = "4" name = "chi2_nx" value = "'.htmlspecialchars($data["chi2_nx"]).'" title="N&uacute;mero de bins em X"></td>
        </tr>
        <tr>
          <td> Par&acirc;metro no eixo Y </td>
          <td> <input type = "text"  size = "4" name = "chi2_py" value = "'.htmlspecialchars($data["chi2_py"]).'" title="N&uacute;mero do par&acirc;metro em Y"></td>
          <td> No. de bins em Y</td>
          <td> <input type = "text"  size = "4" name = "chi2_ny" value = "'.htmlspecialchars($data["chi2_ny"]).'" title="N&uacute;mero de bins em Y"></td>
        </tr>
        </table>
        ';
  $a = c_fieldset($id,$b,"<h3>Par&acirc;metros do mapa de Chi-2</h3>",false);
  return $a;
}

function chi2_draw($data)
{
  $id = $data["SESSION_ID"]."chi2draw";

  if(isset($data["chi2_2d"]))   if($data["chi2_2d"]=="1") $flag_2d="checked";
  if(isset($data["chi2_draw_px"]))   if($data["chi2_draw_px"]=="1") $flag_px="checked";
  if(isset($data["chi2_draw_py"]))   if($data["chi2_draw_py"]=="1") $flag_py="checked";
  
  if(!isset($data["ncont"])) $data["ncont"] = 20;
  if($data["ncont"]<2) $data["ncont"] = 2;
  if($data["ncont"]>250) $data["ncont"] = 250;
  
  $b = '<table border = "0" width ="100%">
        <tr>
          <td> Desenha mapa </td>
          <td> <input type = "checkbox" name = "chi2_2d" value ="1" '.$flag_2d.' title="Se marcado desenha mapa bidimensional."> </td>
          <td> Modo de desenho</td>
          <td> '.TH2DrawOption("chi2_draw",$data["chi2_draw"]).'</td>
        </tr>
        <tr>
          <td> Desenha P-X </td>
          <td> <input type = "checkbox" name = "chi2_draw_px" value ="1" '.$flag_px.' title="Se marcado desenha proje&ccedil;&atilde;o em X."> </td>
          <td> &Acirc;ngulo phi</td>
          <td> <input name="chi2_phi" title="Orienta&ccedil;&atilde;o em phi. Somente para mapas tridimensionais." type="range" min="0" max="360" value="'.$data["chi2_phi"].'"> </td>
        </tr>
        <tr>
          <td> Desenha P-Y </td>
          <td> <input type = "checkbox" name = "chi2_draw_py" value ="1" '.$flag_py.' title="Se marcado desenha proje&ccedil;&atilde;o em Y."> </td>
          <td> &Acirc;ngulo theta</td>
          <td> <input name="chi2_theta" title="Orienta&ccedil;&atilde;o em theta. Somente para mapas tridimensionais." type="range" min="0" max="360" value="'.$data["chi2_theta"].'"> </td>
       </tr>
       <tr>
         <td> Mapa de cores</td>
         <td colspan=2> '.mapa_de_cores("mapa_cores",$data["mapa_cores"]).'</td>
         <td> NCont: <input type = "text"  size = "4" name = "ncont" value = "'.htmlspecialchars($data["ncont"]).'" title="N&uacute;mero de contornos ou cores do mapa entre 2 e 250"></td>
        </tr>
        </table> 
        ';  
        
  $a = c_fieldset($id,$b,"<h3>Op&ccedil;&otilde;es de mapa de Chi-2</h3>",false);
  return $a;

}

function listadir($HOMEDIR, $dir, $data, $name = "dir")
{
  exec('ls -a "'.$HOMEDIR.'/'.$dir.'"',$files);
  $k = strlen($_SESSION["home"]);
  $count = count($files);
  $b = "";
  //echo $dir.'<br>';
  $tmp = substr($dir,$k+1);
  //echo $data["dir"].' ----  '.$tmp.'<br>';
  if ($data[$name]==$tmp) $V = ' selected ';
  else $V = "";
   
  $b .= '<option value="'.$tmp.'"'.$V.' >/'.$tmp.'</option>';

  for ($i = 0; $i<$count; $i++)
  {
    $file = $files[$i];
    if ($file != "." && $file != ".." && is_dir($HOMEDIR.'/'.$dir.'/'.$file) && $HOMEDIR.'/'.$dir.'/'.$file != $HOMEDIR.'/'.$_SESSION["home"].'/shared')
    {
        if(!file_exists($HOMEDIR.'/'.$dir.'/'.$file.'/id.php'))
        {   
          $b .= listadir($HOMEDIR,$dir.'/'.$file,$data,$name);   
        }

    }    
  }  
  return $b;
}

function listaapps($HOMEDIR, $dir, $data, $name = "dir",$app="TGraph")
{
  exec('ls -a "'.$HOMEDIR.'/'.$dir.'"',$files);
  $k = strlen($_SESSION["home"]);
  $count = count($files);
  $b = "";

  for ($i = 0; $i<$count; $i++)
  {
    $file = $files[$i];
    if ($file != "." && $file != ".." && is_dir($HOMEDIR.'/'.$dir.'/'.$file) && $HOMEDIR.'/'.$dir.'/'.$file != $HOMEDIR.'/'.$_SESSION["home"].'/shared')
    {
        if(file_exists($HOMEDIR.'/'.$dir.'/'.$file.'/id.php'))
        {   
          if ($data[$name]==$file) $V = ' selected ';
          else $V = "";
          include($HOMEDIR.'/'.$dir.'/'.$file.'/id.php');
          if(strpos(' '.$app,$APP)) $b .=   '<option value="'.$file.'"'.$V.' >'.$file.'</option>';
        }

    }    
  }  
  return $b;
}
function apps($data, $name = "apps", $dir ="", $app = "TGraph")
{
  include("conf.php");
  $a  = '<select name="'.$name.'" title="Escolha a aplica&ccedil;&atilde;o"  style="width: 155px">';
  $a .= listaapps($HOMEDIR,$_SESSION["home"]."/".$dir,$data,$name,$app);
  $a .= '</select>';
  return $a;
}
function pastas($data, $name = "dir",$si = "155")
{
  include("conf.php");
  $a  = '<select name="'.$name.'" title="Escolha a pasta"  style="width: '.$si.'px">';
  $a .= listadir($HOMEDIR,$_SESSION["home"],$data,$name);
  $a .= '</select>';
  return $a;
}

function aplicacao($data,$janela=true)
{
  $id = $data["SESSION_ID"]."aplic_inline";
  
  $a ='';
  if($janela)
  {
    $b = '<table border = "0" width ="100%">
        <tr > 
          <td width="17%"> Tamanho </td>
          <td> X: <input type = "text" name = "LARGURA" size ="3" value = "'.htmlspecialchars($data["LARGURA"]).'" title="Largura da janela gráfica (min:100, max:1600)"> &nbsp;&nbsp;   
               Y: <input type = "text" name = "ALTURA"  size ="3" value = "'.htmlspecialchars($data["ALTURA"]) .'" title="Altura da janela gráfica (min:100: max: 1600)"></td>  
          <td> Cor de fundo: '.cor("FUNDO",$data["FUNDO"]).'</td>
          </tr>
          <tr>
          <td>Fonte:</td><td>'.fonte("FONTE",$data["FONTE"]).'</td>
          <td> Cor de frente: '.cor("FRENTE",$data["FRENTE"]).'</td>          
        </table>';
    $a = c_fieldset($id,$b,"<h3>Configura&ccedil;&atilde;o da janela gr&aacute;fica</h3>",true);
  }
  
  $id = $data["SESSION_ID"]."aplic";

  $b = '<table border = "0" width ="100%">
        <tr >
          <td width = "20%">Nome &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> 
          <td> <input type = "text" size = "35" name="filesave"  value = "'.htmlspecialchars($data["filesave"]).'" title="Nome da aplica&ccedil;&atilde;o a ser salva"></td>
          <td> <input type = "submit" name="action" value = "Salvar" title="Grava a aplica&ccedil;&atilde;o com este nome"></td>
        </tr>
        <tr>
          <td>Pasta</td>
          <td colspan="2"> '.pastas($data,"dir","252").'</td>
        </tr>
        <tr>
          <td> T&iacute;tulo </td><td> <input type = "text" name = "titulo" size = "35" value = "'.htmlspecialchars($data["titulo"]).'" title="T&iacute;tulo do gr&aacute;fico"></td>
          <td><input type = "submit" name="action" value = "PLOTA" title="Faz o gr&aacute;fico"></td>
        </tr>
        </table>
        ';
  $b.=$a;      
        
  $a = c_fieldset($id,$b,"<h3>Aplica&ccedil;&atilde;o</h3>",false);
  return $a;
}

function eixos($data,$eixoz=false)
{
  $id = $data["SESSION_ID"]."eixos";
  $b = eixo("x",$data).eixo("y",$data);
  if($eixoz) $b.=eixo("z",$data);
  $a = c_fieldset($id,$b,"<h3>Eixos</h3>",false);
   
  return $a;

}

function eixo($l, $data, $close=false)
{
  
  $id = $data["SESSION_ID"].$l;
  
  $flaglog="";
  if(isset($data["log".$l]))     if($data["log".$l]=="1") $flaglog="checked";
  
  $flaggrid="";
  if(isset($data["grid".$l]))     if($data["grid".$l]=="1") $flaggrid="checked";
  
  $a = "eixo".$l;    if(!isset($data[$a])) $data[$a]="";
  $a = $l."min";     if(!isset($data[$a])) $data[$a]="";
  $a = $l."max";     if(!isset($data[$a])) $data[$a]="";

  $b = '
  <table border = "0" width ="100%"><tr>
    <td width = "20%"> T&iacute;tulo </td><td> <input type = "text"  name = "eixo'.$l.'" size ="26" value = "'.htmlspecialchars($data["eixo".$l]).'" title="T&iacute;tulo do eixo"></td>
    <td> <input type = "checkbox" name = "log'.$l.'" value ="1" '.$flaglog.' title="Escala logar&iacute;tmica"> Log  </td>
  </tr>
  <tr>
    <td> Limites </td>
    <td>Min: <input type = "text" name = "'.$l.'min" size ="7" value = "'.htmlspecialchars($data[$l."min"]).'" title="Valor m&iacute;nimo para a escala do eixo"> &nbsp;&nbsp;&nbsp;    
        Max: <input type = "text" name = "'.$l.'max" size ="7" value = "'.htmlspecialchars($data[$l."max"]).'" title="Valor m&aacute;ximo para a escala do eixo"></td>
    <td> <input type = "checkbox" name = "grid'.$l.'" value ="1" '.$flaggrid.' title="Desenha grade neste eixo"> Grade  </td>
  </tr>  ';
  
  $b = $b.'
  <tr>
    <td>Divis&otilde;es</td>
  
    <td> Grd: <input type = "text" name = "div'.$l.'gr" size ="7" value = "'.htmlspecialchars($data["div".$l."gr"]).'" title="N&uacute;mero de divis&otilde;es grandes na escala"> &nbsp;&nbsp;&nbsp; 
     Peq: <input type = "text" name = "div'.$l.'pq" size ="7" value = "'.htmlspecialchars($data["div".$l."pq"]).'" title="N&uacute;mero de divis&otilde;es pequenas na escala"></td>
  </tr>
  ';
  
  $b = $b.'</table>';
  
  $V["0"]="";  
  $V["1"]="";  
  $V["2"]="";    
  if(!isset($data["tick".$l])) $data["tick".$l]="0";
  $V[$data["tick".$l]]=" selected ";
  $A =  
  '<select name="tick'.$l.'" title="Desenho das divis&otilde;es do eixo">
  <option value="0"'.$V["0"].' > Um lado </option>
  <option value="1"'.$V["1"].' > Dois lados</option>
  </select>
  '; 
   
  $V1["0"]="";  
  $V1["+"]="";  
  $V1["-"]="";  
  $V1["+-"]="";    
  if(!isset($data["tickpos".$l])) $data["tickpos".$l]="0";
  $V1[$data["tickpos".$l]]=" selected ";
  $B =  
  '<select name="tickpos'.$l.'" title="Posi&ccedil;&atilde;o das divis&otilde;es do eixo">
  <option value="0"'.$V1["0"].' > Padr&atilde;o </option>
  <option value="-"'.$V1["-"].' > - </option>
  <option value="+"'.$V1["+"].' > +</option>
  <option value="+-"'.$V1["+-"].' > +- </option>
  </select>
  ';  
  
  $offset["x"] = "1.0";
  $offset["y"] = "1.4";
  $offset["z"] = "1.4";
  if(!isset($data["ticksize".$l])) $data["ticksize".$l] = "0.02";
  if(!isset($data["fontetitoffset".$l]) || $data["fontetitoffset".$l]=="") $data["fontetitoffset".$l] = $offset[$l];
  if(!isset($data["fontetitsize".$l]) || $data["fontetitsize".$l]=="") $data["fontetitsize".$l] = "0.035";
  if(!isset($data["fontetit".$l])) $data["fontetit".$l] = "132";
  if(!isset($data["fontemarcoffset".$l]) || $data["fontemarcoffset".$l]=="") $data["fontemarcoffset".$l] = "0.01";
  if(!isset($data["fontemarcsize".$l]) || $data["fontemarcsize".$l]=="") $data["fontemarcsize".$l] = "0.035";
  if(!isset($data["fontemarc".$l])) $data["fontemarc".$l] = "132";
  
  $flagcentraliza="";
  if(isset($data["centraliza".$l]))     if($data["centraliza".$l]=="1") $flagcentraliza="checked";

  $flagmorelog="";
  if(isset($data["morelog".$l]))     if($data["morelog".$l]=="1") $flagmorelog="checked";

  
  $c = '
    <table border = "0" width ="100%">
    <tr>
      <td> T&iacute;tulo: </td>
      <td>'.fonte("fontetit".$l,$data["fontetit".$l]).'</td>
      <td> <input type = "text" name = "fontetitsize'.$l.'" size ="8" value = "'.htmlspecialchars($data["fontetitsize".$l]).'" title="Tamanho da fonte do t&iacute;tulo"></td>
      <td> <input type = "text" name = "fontetitoffset'.$l.'" size ="3" value = "'.htmlspecialchars($data["fontetitoffset".$l]).'" title="Deslocamento do t&iacute;tulo em rela&ccedil;&atilde;o ao eixo"></td>
    </tr>
    <tr>
      <td> Marca&ccedil;&otilde;es: </td>
      <td>'.fonte("fontemarc".$l,$data["fontemarc".$l]).'</td>
      <td> <input type = "text" name = "fontemarcsize'.$l.'" size ="8" value = "'.htmlspecialchars($data["fontemarcsize".$l]).'" title="Tamanho da fonte do marcador"></td>
      <td> <input type = "text" name = "fontemarcoffset'.$l.'" size ="3" value = "'.htmlspecialchars($data["fontemarcoffset".$l]).'" title="Deslocamento do marcador em rela&ccedil;&atilde;o ao eixo"></td>
    </tr>
    <tr>
      <td> Divis&ocirc;es: </td>
      <td>'.$A.'</td> <td>'.$B.'</td>
      <td> <input type = "text" name = "ticksize'.$l.'" size ="3" value = "'.htmlspecialchars($data["ticksize".$l]).'" title="Tamanho das divis&otilde;es do eixo"></td>
    </tr>

    <tr>
    <td colspan=2>
    <input type = "checkbox" name = "centraliza'.$l.'" value ="1" '.$flagcentraliza.' title="Centraliza o t&iacute;tulo do eixo"> Centraliza t&iacute;tulo &nbsp;&nbsp;&nbsp;
    </td>
    <td colspan=2>
    <input type = "checkbox" name = "morelog'.$l.'" value ="1" '.$flagmorelog.' title="Desenha mais marcadores caso a escala seja logar&iacute;tmica"> Mais log
    </td>
    </table>

  ';
  
  $d = c_fieldset($id."extra",$c,"<h3>Op&ccedil;&otilde;es extras</h3>",true);
  
  $b.=$d;
  
  $a = c_fieldset($id,$b,"<h3>Eixo ".$l."</h3>",$close);
   
  return $a;
  
}

function fonte($name, $VALUE)
{
  $V["132"]="";
  $V["12"]="";
  $V["22"]="";
  $V["32"]="";
  $V["42"]="";
  $V["52"]="";
  $V["62"]="";
  $V["72"]="";
  $V["82"]="";
  $V["92"]="";
  $V["102"]="";
  $V["112"]="";
  
  $V[$VALUE]=' selected ';
  
  $A =  
  '<select name="'.$name.'" title="Seleciona a fonte">
  <option value="132"'.$V["132"].' >Times </option>
  <option value="12"'.$V["12"].' >Times I</option>
  <option value="22"'.$V["22"].' >Times N</option>
  <option value="32"'.$V["32"].' >Times N + I</option>
  <option value="42"'.$V["42"].' >Arial</option>
  <option value="52"'.$V["52"].' >Arial I</option>
  <option value="62"'.$V["62"].' >Arial N</option>
  <option value="72"'.$V["72"].' >Arial N + I</option>
  <option value="82"'.$V["82"].' >Courier</option>
  <option value="92"'.$V["92"].' >Courier I</option>
  <option value="102"'.$V["102"].' >Courier N</option>
  <option value="112"'.$V["112"].' >Courier N + I</option>
  </select>
  ';
  return $A;
}
function TH2DrawOption($name, $VALUE)
{
  $V["ARR"]="";
  $V["COL"]="";
  $V["COLZ"]="";
  $V["CONT"]="";
  $V["CONT0"]="";
  $V["CONT1"]="";
  $V["CONT2"]="";
  $V["SURF"]="";
  $V["SURF1"]="";
  $V["SURF2"]="";
  $V["SURF3"]="";
  $V["LEGO"]="";
  $V["LEGO1"]="";
  $V["LEGO2"]="";
  
  $V[$VALUE]=' selected ';
  
  $A =  
  '<select name="'.$name.'" title="Seleciona a fonte">
  <option value="ARR"'.$V["ARR"].' >Gradiente </option>
  <option value="COL"'.$V["COL"].' >Cores</option>
  <option value="COLZ"'.$V["COLZ"].' >Cores-Z</option>
  <option value="CONT"'.$V["CONT"].' >Contorno</option>
  <option value="CONT0"'.$V["CONT0"].' >Contorno 0</option>
  <option value="CONT1"'.$V["CONT1"].' >Contorno 1</option>
  <option value="CONT2"'.$V["CONT2"].' >Contorno 2</option>
  <option value="SURF"'.$V["SURF"].' >Superf&iacute;cie</option>
  <option value="SURF1"'.$V["SURF1"].' >Superf&iacute;cie 1</option>
  <option value="SURF2"'.$V["SURF2"].' >Superf&iacute;cie 2</option>
  <option value="SURF3"'.$V["SURF3"].' >Superf&iacute;cie 3</option>
  <option value="LEGO"'.$V["LEGO"].' >Lego</option>
  <option value="LEGO1"'.$V["LEGO1"].' >Lego 1</option>
  <option value="LEGO2"'.$V["LEGO2"].' >Lego 2</option>
  </select>
  ';
  return $A;
}
function Chi2Mode($name, $VALUE)
{
  $V["CHI2"]="";
  $V["CHI2R"]="";
  
  $V[$VALUE]=' selected ';
  
  $A =  
  '<select name="'.$name.'" title="Seleciona o modo de Chi2">
  <option value="CHI2"'.$V["CHI2"].' >Chi2 </option>
  <option value="CHI2R"'.$V["CHI2R"].' >Chi2 reduzido</option>
  </select>
  ';
  return $A;
}

function cor($name, $VALUE)
{
  for($i = 0;$i<35; $i++) $V[$i]="";
  
  $V[$VALUE]=' selected ';
  
  $A =  
  '<select name="'.$name.'" title="Seleciona a cor">
  <option value="1"'.$V["1"].' style="color:#000000">preto</option>
  <option value="0"'.$V["0"].' style="color:#000000">branco</option>
  <option value="2"'.$V["2"].' style="color:#FF0000">vermelho</option>
  <option value="3"'.$V["3"].' style="color:#00FF00">verde</option>
  <option value="4"'.$V["4"].' style="color:#0000FF">azul</option>
  <option value="5"'.$V["5"].' style="color:#FFFF00">amarelo</option>  
  <option value="6"'.$V["6"].' style="color:#FF00FF">roxo</option>
  <option value="7"'.$V["7"].' style="color:#00FFFF">ciano</option>
  <option value="8"'.$V["8"].' style="color:#008000">oliva</option>
  <option value="9"'.$V["8"].' style="color:#5954d7">roxo</option>
  <option value="12"'.$V["12"].' style="color:#4c4c4c">cinza 1</option>
  <option value="13"'.$V["13"].' style="color:#666666">cinza 2</option>
  <option value="14"'.$V["14"].' style="color:#7f7f7f">cinza 3</option>
  <option value="15"'.$V["15"].' style="color:#999999">cinza 4</option>
  <option value="16"'.$V["16"].' style="color:#b2b2b2">cinza 5</option>
  <option value="17"'.$V["17"].' style="color:#cccccc">cinza 6</option>
  <option value="18"'.$V["18"].' style="color:#e5e5e5">cinza 7</option>
  <option value="11"'.$V["11"].' style="color:#c0b6ac">bege 1</option>
  <option value="21"'.$V["21"].' style="color:#ccc6aa">bege 2</option>
  <option value="23"'.$V["23"].' style="color:#bab5a3">bege 3</option>
  <option value="24"'.$V["24"].' style="color:#b2a597">bege 4</option>
  <option value="25"'.$V["25"].' style="color:#b7a39b">bege 5</option>
  <option value="26"'.$V["26"].' style="color:#ad998c">bege 6</option>
  <option value="27"'.$V["27"].' style="color:#9b8e81">bege 7</option>
  <option value="28"'.$V["28"].' style="color:#876656">bege 9</option>
  <option value="29"'.$V["29"].' style="color:#afcec6">verde 1</option>
  <option value="30"'.$V["30"].' style="color:#84c1a3">verde 2</option>
  <option value="31"'.$V["31"].' style="color:#77928b">verde 3</option>
  <option value="32"'.$V["32"].' style="color:#829e8c">verde 4</option>
  <option value="33"'.$V["33"].' style="color:#adbcc6">verde 5</option>
  <option value="34"'.$V["34"].' style="color:#7a8e99">verde 6</option>
  </select>
  ';
  return $A;
}
function marcador($name, $VALUE)
{
  for($i = 20;$i<35; $i++) $V[$i]="";
  
  $V[$VALUE]=' selected ';
  
  $A =  
  '<select name="'.$name.'" title="Tipo de marcador">
  <option value="20"'.$V["20"].' >&#9899; - c&iacute;rculo cheio</option>
  <option value="24"'.$V["24"].' >&#9898; - c&iacute;rculo aberto</option>
  <option value="21"'.$V["21"].' >&#9724; - quadrado cheio</option>
  <option value="25"'.$V["25"].' >&#9723; - quadrado aberto</option>
  <option value="22"'.$V["22"].' >&#9650; - triângulo cheio</option>
  <option value="26"'.$V["26"].' >&#9651; - triângulo aberto</option>
  <option value="23"'.$V["23"].' >&#9660; - triângulo cheio</option>
  <option value="32"'.$V["32"].' >&#9661; - triângulo aberto</option>
  <option value="33"'.$V["33"].' >&#9830; - diamante cheio </option>
  <option value="27"'.$V["27"].' >&#9826; - diamante aberto</option>
  <option value="29"'.$V["29"].' >&#9733; - estrela cheia </option>
  <option value="30"'.$V["30"].' >&#9734; - estrela aberta</option>
  <option value="34"'.$V["34"].' >&#9547; - cruz cheia</option>
  <option value="28"'.$V["28"].' >&#9580; - crua aberta </option>
  <option value="31"'.$V["31"].' >&#9728; - asterisco </option>
  </select>
  ';
  return $A;
}
function linha($name, $VALUE)
{
  for($i = 1;$i<20; $i++) $V[$i]="";
  
  $V[$VALUE]=' selected ';
  
  $A = 
  '<select name="'.$name.'" title="Tipo de linha">
  <option value="1"'.$V["1"].' >s&oacute;lida</option>
  <option value="3"'.$V["3"].' >pontilhada</option>
  <option value="2"'.$V["2"].' >tracejada</option>
  <option value="5"'.$V["5"].' >pontilhada/tracejada</option>
  </select>
  ';
  return $A;
}
function espessura($name, $VALUE)
{
  for($i = 1;$i<9; $i++) $V[$i]="";
  
  $V[$VALUE]=' selected ';
  
  $A = 
  '<select name="'.$name.'" title="Espessura da linha">
  <option value="1"'.$V["1"].' >x 1</option>
  <option value="2"'.$V["2"].' >x 2</option>
  <option value="3"'.$V["3"].' >x 3</option>
  <option value="4"'.$V["4"].' >x 4</option>
  <option value="5"'.$V["5"].' >x 5</option>
  <option value="6"'.$V["6"].' >x 6</option>
  <option value="7"'.$V["7"].' >x 7</option>
  <option value="8"'.$V["8"].' >x 8</option>
  </select>
  ';
  return $A;
}

function tamanho($name, $VALUE)
{
  for($i = 1;$i<9; $i++) $V[$i]="";
  
  $V[$VALUE]=' selected ';
  
  $A = 
  '<select name="'.$name.'" title="Tamanho do marcador">
  <option value="1"'.$V["1"].'>x 1</option>
  <option value="2"'.$V["2"].'>x 2</option>
  <option value="3"'.$V["3"].'>x 3</option>
  <option value="4"'.$V["4"].'>x 4</option>
  <option value="5"'.$V["5"].'>x 5</option>
  <option value="6"'.$V["6"].'>x 6</option>
  <option value="7"'.$V["7"].'>x 7</option>
  <option value="8"'.$V["8"].'>x 8</option>
  </select>
  ';
  return $A;
}

function mapa_de_cores($name, $VALUE)
{
  for($i = 1;$i<10; $i++) $V[$i]="";
  
  $V[$VALUE]=' selected ';
  
  $A = 
  '<select name="'.$name.'" title="Tamanho do marcador">
  <option value="1"'.$V["1"].'>Arco-&iacute;ris</option>
  <option value="2"'.$V["2"].'>Arco-&iacute;ris 2</option>
  <option value="3"'.$V["3"].'>Azul profundo</option>
  <option value="4"'.$V["4"].'>Tons de cinza</option>
  <option value="5"'.$V["5"].'>Corpo negro</option>
  <option value="6"'.$V["6"].'>Corpo negro invertido</option>
  <option value="7"'.$V["7"].'>Gradiente duas cores</option>
  <option value="8"'.$V["8"].'>Psicod&eacute;lico</option>
  <option value="9"'.$V["9"].'>Psicod&eacute;lico 2</option>
  </select>
  ';
  return $A;
}


function thumbnail($src,$dest,$desired_width)
{
  /* read the source image */
  $source_image = imagecreatefrompng($src);
  $width = imagesx($source_image);
  $height = imagesy($source_image);
  
  /* find the "desired height" of this thumbnail, relative to the desired width  */
  $desired_height = floor($height*($desired_width/$width));
  
  /* create a new, "virtual" image */
  $virtual_image = imagecreatetruecolor($desired_width,$desired_height);
  
  /* copy source image at a resized size */
  imagecopyresampled($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
  
  /* create the physical thumbnail image to its destination */
  imagepng($virtual_image,$dest);
}



?>
