
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
  $offsetz = $DADOS["fontetitoffsetz"];
  
  $sizetitx = $DADOS["fontetitsizex"];
  $sizetity = $DADOS["fontetitsizey"];
  $sizetitz = $DADOS["fontetitsizez"];
  
  $fontemarcx = $DADOS["fontemarcx"];
  $fontemarcy = $DADOS["fontemarcy"];
  $fontemarcz = $DADOS["fontemarcz"];
  
  $fontemarcoffsetx = $DADOS["fontemarcoffsetx"];
  $fontemarcoffsety = $DADOS["fontemarcoffsety"];
  $fontemarcoffsetz = $DADOS["fontemarcoffsetz"];
  
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
  
  
  
  $f = fopen($tmp,"w");
  fwrite($f,'
            void '.$DADOS["SESSION_ID"].'()
            {
              '.style($ROOTSTYLE,$FUNDO,$FRENTE,$FONTE).'    
              c = new TCanvas("c","c",'.$W.','.$H.');
              double x1,x2,y1,y2;');  

  if($DADOS["funcao"]!="")
  {
    $FUNC = $DADOS["funcao"];    
    fwrite($f,' TF2 *f = new TF2("f","'.$FUNC.'",'.$XMIN.','.$XMAX.','.$YMIN.','.$YMAX.');        
                f->SetNpx(1000); ');
                
    if($DADOS["par"]!="")
    {
      fwrite($f,"f->SetParameters(".htmlspecialchars($DADOS["par"]).",0);    \n");
    }
    
    fwrite($f,'f->Draw();    
               f->SetTitle("'.htmlspecialchars(utf8_decode($DADOS["titulo"])).'");');

    $a = atributos_graficos("f","X",$titlex,$fontex,$offsetx,$sizetitx,$XDIV1,$XDIV2,$forcedivx,$XMIN,$XMAX,$forcerangex,$gridx,$logx,
                          $tickx,$tickposx,$ticksizex,$fontemarcx,$fontemarcsizex,$fontemarcoffsetx,$centralizax,$morelogx);
    fwrite($f,$a);
  
    $a = atributos_graficos("f","Y",$titley,$fontey,$offsety,$sizetity,$YDIV1,$YDIV2,$forcedivy,$YMIN,$YMAX,$forcerangey,$gridy,$logy,
                          $ticky,$tickposy,$ticksizey,$fontemarcy,$fontemarcsizey,$fontemarcoffsety,$centralizay,$morelogy);
    fwrite($f,$a); 
    
    //$a = atributos_graficos("f","Z",$titlez,$fontez,$offsetz,$sizetitz,$ZDIV1,$ZDIV2,$forcedivz,$ZMIN,$ZMAX,$forcerangez,$gridz,$logz,
    //                      $tickz,$tickposz,$ticksizez,$fontemarcz,$fontemarcsizez,$fontemarcoffsetz,$centralizaz,$morelogz);
    //fwrite($f,$a); 

    fwrite($f,estatisticas_grafico($arq,$val,$W,$H,"c",0,"f"));   
  
  }

  fwrite($f,'return; }');  

  fclose($f);
  
  $error = executa($ROOTDIR."/bin/root -b -q ".$tmp,$DADOS["SESSION_ID"],$TMPDIR);
  if ($error!=0) return "ERROR";
  return $arq;
}

?>
