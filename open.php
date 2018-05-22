<?php
        $MAXV1 = $_SESSION['maxv1'];
        $AVISOCONV = 0;
        
        if(substr($_GET["dir"],0,strlen($_SESSION["home"])) != $_SESSION["home"])
        {
          header('Location: ../index.php'); 
          exit;       
        }

        $dir = $HOMEDIR.'/'.$_GET["dir"];
        $file = $_GET["file"]; 
        
        include($dir.'/'.$file.'/id.php');
        //$_POST["SESSION_ID"]=$ID;
        
        
        if($VERSION>0)
        {   
          $postfile = $dir.'/'.$file.'/post.muroot';       
          $var = file_get_contents($postfile);
          $_POST = unserialize(urldecode($var));
          $NC = $_POST["NC"];
          $NL = $_POST["NL"];
          
          ///////////////////////////////////////////////////////////////////////////////
          // convertendo da versao 1 com dados inline para a versao 2 com dados separados
          if($NL>$MAXV1 && $VERSION==1)
          {
            $AVISOCONV = 1;
            $VERSION = 2;
            $F = $dir.'/'.$file.'/id.php';
            $f = fopen($F,"w");
            fwrite($f,"<?php\n");
            fwrite($f,"  \$ID       = '".$_POST["SESSION_ID"]."'; \n" );
            fwrite($f,"  \$TIME     = ".time()."; \n" );
            fwrite($f,"  \$APP      = '".$app."'; \n" );
            fwrite($f,"  \$VERSION  = ".$VERSION."; \n" );
            fwrite($f,"  \$NAME     = '".$NAME."'; \n" );
            fwrite($f,"?>\n");
            fclose($f);
            
            $DATAV2["NL"] = $NL;
            $DATAV2["NC"] = $NC;
            $DATAV2["ID"] = $ID;
            for($i = 0; $i<$NL; $i++) 
              for($j = 0; $j<$NC; $j++) 
              {
                $NAME   = 'R'.$i.'C'.$j;     
                if(isset($_POST[$NAME])) 
                {
                  $DATAV2[$NAME] = $_POST[$NAME];
                  unset($_POST[$NAME]);
                }
              }
            
            $F = $dir.'/'.$file.'/post.muroot';
            $str = serialize($_POST);
            $encode = urlencode($str);
            file_put_contents($F,$encode);
              
            $F = $dir.'/'.$file.'/values.dat';
            $str = serialize($DATAV2);
            $encode = urlencode($str);
            file_put_contents($F,$encode);
          }
          //
          ///////////////////////////////////////////////////////////////////////////////

          $_POST["action"]="";
          $_POST["SESSION_ID"]='A'.time().rand();
          $_POST["filesave"]=htmlspecialchars($file);
    
          $file1 = $dir.'/'.$file.'/id.php';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'.id.php');

          $file1 = $dir.'/'.$file.'/image.png';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'.png');
  
          $file1 = $dir.'/'.$file.'/image_1.png';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'_1.png');
  
          $file1 = $dir.'/'.$file.'/image_2.png';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'_2.png');
  
          $file1 = $dir.'/'.$file.'/image_3.png';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'_3.png');
  
          $file1 = $dir.'/'.$file.'/macro.C';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'.C');
        
	      $file1 = $dir.'/'.$file.'/data.php';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'.php');
          
	      $file1 = $dir.'/'.$file.'/objects.root';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'.root');
          
	      $file1 = $dir.'/'.$file.'/objects_1.root';
          if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'_1.root');
          
          if($VERSION==2)
          {
	        $file1 = $dir.'/'.$file.'/values.dat';
            if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'.dat');  
          }

          if($VERSION==3)
          {
	        $file1 = $dir.'/'.$file.'/values.txt';
            if(file_exists($file1)) copy($file1,$TMPDIR.'/'.$_POST["SESSION_ID"].'.txt');  
          }
        
          exec ("chmod a+rw ".$TMPDIR.'/'.$_POST["SESSION_ID"].".*");
        }
        
        $_POST["VERSION"]=$VERSION;
        
        $i = strlen($_SESSION["home"]);
        $_POST["dir"] = substr($_GET["dir"],$i+1);
        
        // esta checagem eh necessaria por conta de eu ter alterado o modo como mostramos ajustes nos limites
        if(isset($_POST["flaglimite"]))  $_POST["tipolimite"]="2";

        
        if($AVISOCONV==1)
        {
          echo "<script> alert('Há muitas linhas de dados no arquivo. Para tornar o uso mais ágil, separamos a edição da planilha de dados em uma outra janela.');</script>";
        }
        

?>