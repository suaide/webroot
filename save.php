<?php 
        $nome = trim(htmlspecialchars($_POST["filesave"]));
        $nome = mb_ereg_replace("'", " (linha) ", $nome);
        $nome = trim($nome);
        $_POST["filesave"] = $nome;
        if($nome=="")
        {
          $nome = $_POST["SESSION_ID"];
          $_POST["filesave"] = $nome;
        }
        $DIR  = $HOMEDIR.'/'.$_SESSION["home"].'/'.$_POST["dir"].'/'.$nome;
        if(!is_dir($DIR)) mkdir($DIR,0777,true);
    
        if(!is_dir($DIR)) echo "<script> alert('Nao foi possivel salvar. Contate o administrador.');</script>";
        else
        {
          $F = $DIR.'/id.php';
          $f = fopen($F,"w");
          fwrite($f,"<?php\n");
          fwrite($f,"  \$ID       = '".$_POST["SESSION_ID"]."'; \n" );
          fwrite($f,"  \$TIME     = ".time()."; \n" );
          fwrite($f,"  \$APP      = '".$app."'; \n" );
          fwrite($f,"  \$VERSION  = ".$_POST["VERSION"]."; \n" );
          fwrite($f,"  \$NAME     = '".$nome."'; \n" );
          fwrite($f,"?>\n");
          fclose($f);
          
          $D = $TMPDIR.'/'.$_POST["SESSION_ID"].'.id.php';
          copy($F,$D);
      
          $F = $DIR.'/post.muroot';
          $str = serialize($_POST);
          $encode = urlencode($str);
          file_put_contents($F,$encode);
      
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.C';
          $D = $DIR.'/macro.C';
          if(file_exists($F)) copy($F,$D);
      
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.php';
          $D = $DIR.'/data.php';
          if(file_exists($F)) copy($F,$D);
          
          // tabela de dados para VERSAO 2 
          // permite editar os dados mas em planilha separada da aplicacao
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.dat';
          $D = $DIR.'/values.dat';
          if(file_exists($F)) copy($F,$D);

          // tabela de dados para VERSAO 3 
          // tabela de dados muito grande mantida em arquivo txt separado
          // nao permite edicao no webroot
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.txt';
          $D = $DIR.'/values.txt';
          if(file_exists($F)) copy($F,$D);
          
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.root';
          $D = $DIR.'/objects.root';
          if(file_exists($F)) copy($F,$D);
          
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'_1.root';
          $D = $DIR.'/objects_1.root';
          if(file_exists($F)) copy($F,$D);
      
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.png';
          $D = $DIR.'/image.png';
          $T = $DIR.'/thumb.png';
          if(file_exists($F))
          {
            copy($F,$D);
	        thumbnail($D,$T,250);
          }      
          
          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'_1.png';
          $D = $DIR.'/image_1.png';
          if(file_exists($F)) copy($F,$D);

          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'_2.png';
          $D = $DIR.'/image_2.png';
          if(file_exists($F)) copy($F,$D);

          $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'_3.png';
          $D = $DIR.'/image_3.png';
          if(file_exists($F)) copy($F,$D);
      
          echo "<script> alert('Aplicacao salva.');</script>";
         }
?>

