<?php
  session_start();
  include("checa.php");
  include("conf.php");
  include("tools.php");
  //include("db/MyTXT.php");
  
  if(checa_login() && isset($_POST["cmd"]))
  {
    if(isset($_POST["Abrir_x"]))
    {
      echo 
      '
      <script type="text/javascript">
        child1 = window.open( "'.$_POST["app"].'/index.php?action=abrir&file='.$_POST["file"].'&dir='.$_POST["dir"].'" )
        if (!child1) 
        {
          var y=document.createElement(\'alerta\');
          y.innerHTML = \'Para abrir uma aplica&ccedil;&atilde;o &eacute; necess&aacute;rio que voc&ecirc; permita popups de sampa.if.usp.br. Configure o seu browser adequadamente.\';
          alert(y.innerHTML);
        }

	    location.replace("index.php");
      </script> 
      ';
      exit;
    }
  }
  
  echo '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	<head>
	    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />	    
	    <title>webROOT by GRIPER</title>
	    <meta http-equiv="Content-Language" content="pt-br" />
	     
	    <meta http-equiv="imagetoolbar" content="no" />
	    <meta name="MSSmartTagsPreventParsing" content="true" />
	     
	    <meta name="description" content="webROOT" />
	    <meta name="keywords" content="webROOT" />
	     
	    <meta name="author" content="HEPIC IFUSP" />
	     
	    <style type="text/css" media="all">@import "style.css";</style>  
            <style type="text/css" media="all">@import "style.css";</style>
	</head>
  <body>
  ';
  
  
  echo '<div id="topo">
        <h1> webROOT - <a href="http://hepic.if.usp.br">HEPIC</a> (High Energy Physics and Instrumentation Center) - IFUSP - USP </h1>
        <hr>
        </div> ';
	
  if(!checa_login())
  {
  echo '

    <div id="commands">
    
    <h2> Bem-vindo</h2>
    
    <h3>Bem-vindo ao aplicativo WebROOT, desenvolvido pelo HEPIC (High Energy Physics and Instrumentation Center) na USP.</h3><br>
    
    <center> O WebROOT consiste de uma s&eacute;rie de aplicativos web para fazer an&aacute;lise de dados com o <a href="root.cern.ch">ROOT</a>,
    desenvolvido no CERN para experimentos em f&iacute;sica de altas energias. Estes aplicativos web facilitam o uso do ROOT para diversas
    an&aacute;lises de dados. Aproveitem!<br><br>
    
    <table width="300" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr>
    <form name="form1" method="post" action="login.php">
    <td>
    <table width="100%" border="0" cellpadding="3" cellspacing="1">
    <tr>
    <td colspan="2"><h2>Login no aplicativo</h2></td>
    </tr>
    <tr>
    <td width="88">Usu&aacute;rio</td>
    <td width="284"><input name="login" type="text" id="login"></td>
    </tr>
    <tr>
    <td>Senha</td>
    <td><input name="senha" type="password" id="senha"</td>
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit" value="Login"> <a href="index.php?action=esqueci">Esqueci minha senha</a></td>
    </tr>
    </table>
    </td>
    </form>
    </tr>
    </table>    
    <br><h3>Se voc&ecirc; ainda n&atilde;o &eacute; cadastrado para acessar o aplicativo, <a href="index.php?action=cadastro">clique aqui</a> e fa&ccedil;a o seu cadastro. Leva apenas alguns
    minutos.</h3>
    <h3>Se voc&ecirc; tem algum problema, ou sugest&atilde;o, <a href="index.php?action=contacta">clique aqui</a> para entrar em contato.</h3>
    </div>
    <div id="output">
    ';
    
    
    if(isset($_GET["action"]))
    {
      switch($_GET["action"])
      {
        case "contacta":      
          echo '<h2>Contactar administradores</h2>';
          if(isset($_GET["err"]))
          {
            if($_GET['err']=='vazio')   echo '<h2> Por favor, preencha os campos obrigatórios.</h2>';
            if($_GET['err']=='email')   echo '<h2> Não consegui enviar a mensagem. Tente novamente mais tarde.</h2>';
            if($_GET['err']=='ok')      echo '<h2> Mensagem enviada. Por favor, aguarde contato.</h2>';
          }
          
          echo mailform();
          break;
        
        case "login":
          echo ' <h2> Falha no login</h2> <br><center> ';	
	      if(isset($_GET["err"]))
	      {
	        if($_GET["err"]=="login") echo '<h2> O usu&aacute;rio n&atilde;o existe.</h2>'; 
            if($_GET["err"]=="senha") echo '<h2> A senha n&atilde;o confere.</h2>'; 
            if($_GET["err"]=="db")    echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>'; 
	      }
	      break;
	  
	    case "cadastro":
          echo '<h2> P&aacute;gina de registro de novo usu&aacute;rio</h2><br><center>';
	      if(isset($_GET["err"]))
	      {	  	
            if($_GET["err"]=="email") echo '<h2> O email deve ser v&aacute;lido para se cadastrar</h2>'; 
            if($_GET["err"]=="user")  echo '<h2> Este nome de usu&aacute;rio j&aacute; existe.</h2>'; 
            if($_GET["err"]=="baduser")  echo '<h2> Este nome de usu&aacute;rio cont&eacute;m caracteres inv&aacute;lidos eu &eacute; muito curto.</h2>'; 
            if($_GET["err"]=="usp")   echo '<h2> Este usu&aacute;rio j&aacute; possui cadastro no sistema.</h2>'; 
            if($_GET["err"]=="senha") echo '<h2> Problemas com a senha. Tente novamente.</h2>'; 
            if($_GET["err"]=="db") echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>'; 
            if($_GET["err"]=="ne") echo '<h2> N&atilde;o h&aacute; o que confirmar. Desculpe-me.</h2>'; 
            if($_GET["err"]=="gt") echo '<h2> Duplicidades no banco de dados. Contate um respons&aacute;vel.</h2>'; 
            if($_GET["err"]=="ok")    
	        {
	           echo '<h2> Cadastro efetuado.</h2>
	                 <br> <h1><center>Um e-mail com um link para confirma&ccedil;&atilde;o foi enviado para o e-mail cadastrado. 
	                 <br><br>&Eacute; necess&aacute;rio responder este e-mail para poder utilizar o webRoot. 
	                 <br><br>Aguarde!<center></h1>';
	           exit; 
	        }
            if($_GET["err"]=="conf")
            {
	          echo '<h2> Cadastro validado.</h2>
	          <br> O seu e-mail for validado e seu registro est&aacute; habilitado e pronto para uso.';
	          exit; 
            }
            if($_GET["err"]=="canc")
            {
	          echo '<h2> Cadastro cancelado.</h2>
	          <br> O seu e-mail n&atilde;o foi validado e seu registro est&aacute; cancelado.';
	          exit; 
            }
	      }
          echo '	
	        <FORM ACTION="register.php" METHOD="post">
            <table width="70%" border="0" cellpadding="3" cellspacing="1">
            <tr>
            <td>Usu&aacute;rio </td><td><input name="login" type="text" size"20"></input></td>
            </tr>
            <tr>
            <td>Nome completo </td><td><input name="nome" type="text" size"20"></input></td>
            </tr>
            <tr>
            <td>e-mail</td><td><input name="email" type="text" size"20"></input></td>
            </tr>
            <tr>
            <td>Senha (m&iacute;nimo 8 caracteres):</td><td><input name="senha1" type="password" size"20"></input></td>
            </tr>
            <tr>
            <td>Confirme senha :</td><td><input name="senha2" type="password" size"20"></input></td>
            </tr>
            </table>
	        <br>
            <input type="submit" value="Cadastre-me"></input>
            </FORM>
	        </center><br><br>
           ';	  
	     break;
	  
	case "esqueci":
          echo ' <h2> Esqueceu a senha?</h2> ';     
	  if(isset($_GET["err"]))
	  {
            if($_GET["err"]=="Ok")      { echo '<h2> Um e-mail foi enviado com a nova senha. Aguarde.</h2>'; break; } 
            if($_GET["err"]=="valida")  echo '<h2> N&atilde;o foi poss&iacute;vel encontrar usu&aacute;rio com estes dados.</h2>'; 
            if($_GET["err"]=="db") echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>'; 
	  }
          echo ' <br><center> Entre o seu e-mail no formul&aacute;rio abaixo que enviaremos uma nova senha.<br><br> ';     
          echo '
	    <FORM ACTION="esqueci.php" METHOD="post">
            <table width="70%" border="0" cellpadding="3" cellspacing="1">
            <tr>
            <td>e-mail (igual ao cadastrado):</td><td><input name="email" type="text" size"20"></input></td>
            </tr>
            </table>
	    <br>
            <input type="submit" value="Enviar nova senha"></input>
            </FORM>
	    </center>
            ';      
	  break;
     }
    }
    else  
    {
      echo '<center><br><br><br><img src="images/logo.png" width="350px"></center>';
      if(file_exists("news.php")) include("news.php");
    }
    echo '</div>';  
  }
  else
  {
    include("comum.php");
    echo '<div id="commands">'.welcome().menu();
    
    if(checa_admin())
    {
      echo menu_admin();
    }
    
    echo '</div>';
    
    echo '<div id="output">';
    
    if(isset($_GET["login"]))
    {
      echo '<br><br><br><br><h1> AVISO IMPORTANTE </h1>
      <br><br><br><br>
      <div style="margin-left:40px; margin-right:40px;">
      Agora o WebROOT possui a possibilidade de organizar as suas aplica&ccedil;&otilde;es em <b>pastas</b>. 
      Voc&ecirc; pode criar pastas e sub-pastas como bem quiser e mover aplica&ccedil;&otilde;es de uma pasta para
      outra. Na aplica&ccedil;&atilde;o <b>Combinado de gr&aacute;ficos</b> voc&ecirc; pode escolher a pasta na qual
      os gr&aacute;ficos ser&atilde;o listados para combinar.
      <br><br>
      Por conta disso, a apresenta&ccedil;&atilde;o das aplica&ccedil;&otilde;es na p&aacute;gina principal tamb&eacute;m
      mudou, ficando mais compacta e com um visual mais pr&oacute;ximo de um gerenciador de arquivos.
      <br><br>
      Qualquer problema ou bug encontrado, por favor nos avise. Na p&aacute;gina de login h&aacute; um link para um
      formul&aacute;rio de contato.
      <br><br>
      Aproveite!
      </div>
      </div>';
      exit;
    }

    if((isset($_GET["admin"]) || isset($_POST["admin"])) && checa_admin())
    {
            
      if($_GET["admin"] == "usuarios" || $_POST["admin"] == "usuarios")
      {
          $db = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
          if(!$db) echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>';  
          else
          {
            $dbf = mysql_select_db($SQLDB, $db);
            if(!$dbf) echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>';  
            //else lista_usuarios($db,$_GET["index"],$_GET["L"]);
            else 
            {
              phpMyEd();
            }
          }
	   }
        		  
	   if($_GET["admin"] == "cache")
	   {
	     exec('find '.$TMPDIR.'/ -mtime +2 -exec rm {} \;');
	     echo '<center><h1>Cache limpo</h1></center>';
	   }
    }    
    else if(isset($_GET["action"]))
    {
      switch($_GET["action"])
      {
        case "senha":
	    echo '<h2> Alterar a senha</h2>';
	    if(isset($_GET["err"]))
	    {
	      if($_GET["err"]=="confirma") echo '<h2> As novas senhas n&atilde;o s&atilde;o iguais.</h2>'; 
	      if($_GET["err"]=="atual") echo '<h2> A senha atual n&atilde;o confere.</h2>'; 
	      if($_GET["err"]=="senha") echo '<h2> A nova senha n&atilde;o segue as regras do site.</h2>'; 
	      if($_GET["err"]=="db") echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>'; 
	      if($_GET["err"]=="ok") echo '<h2> Senha alterada com sucesso.</h2>'; 
	    }
	    else
	    {
            echo '
            <center>	
	        <FORM ACTION="alterasenha.php" METHOD="post">
            <table width="70%" border="0" cellpadding="3" cellspacing="1">
	        <tr>
            <td>Senha atual:</td><td><input name="senha1" type="password" size"20"></input></td>
            </tr>
            <tr>
            <td>Nova Senha (m&iacute;nimo 8 caracteres):</td><td><input name="senha2" type="password" size"20"></input></td>
            </tr>
            <tr>
            <td>Confirme nova senha :</td><td><input name="senha3" type="password" size"20"></input></td>
            </tr>
            </table>
	        <br>
            <input type="submit" value="Altere"></input>
            </FORM>
	        </center>
            ';	  
	    }
	    break;
	
	case "intervalos":
	  echo '<h2> C&aacute;lculadora de intervalos de confi&acirc;n&ccedil;a</h2>';
	  confianca();
	  break;
	case "calculadora":
	  echo '<h2>Calculadora cient&iacute;fica</h2>';
	  calculadora();
	  break;
	
	case "preferencias":
      preferencias();	
	  break;  
    case "opendir":
     //echo urldecode($_GET["dir"]);
     $dir = urldecode($_GET["dir"]);
     $dir1 = realpath($HOMEDIR.'/'.$dir);
     $tmp = $HOMEDIR.'/'.$_SESSION["home"];
     $check = ($tmp === "" || strpos($dir1, $tmp) === 0);
     if(!$check) $dir=$_SESSION["home"];
     
     $_SESSION["current"]=$dir;
     
     echo browser2($HOMEDIR,$dir);
     break; 
	case "pasta":
	  if(isset($_GET["sub"]))
	    switch($_GET["sub"])
	    {
	      case "nova":
	        if(!isset($_GET["OK"]))
	        {
	          
	          $k = strlen($_SESSION["home"]);
              $sub = substr($_GET["value"],$k+1);
	          echo '
	          <h2>Criar nova pasta</h2>
	          <center>
              <FORM ACTION="index.php" METHOD="get">
              <input type="hidden" name = "action" value="pasta">
              <input type="hidden" name = "sub" value="'.$_GET["sub"].'">
              <input type="hidden" name = "value" value="'.$sub.'">
              <input type="hidden" name = "OK" value="OK">';
              
              $title = $sub;
              
              echo '
              <table width="70%" border="0" cellpadding="3" cellspacing="1">
	          <tr>
              <td>Pasta raiz:</td><td>'.$title.'</input></td>
              </tr>
              <tr>
              <td>Nome da pasta:</td><td><input name="nome" size="20"></input></td>
              </tr>
              </table>
	          <br>
              <input type="submit" value="Cria pasta"></input>
              </FORM>
              </center>
	        ';
	        }
	        else
	        { 
	          $dir =  $HOMEDIR.'/'.$_SESSION["home"].'/'.$_GET["value"].'/'.$_GET["nome"];
	          if(file_exists($dir))
	          {
	            echo "<script> alert('Ja existe parta/aplicacao com este nome.')</script>" ;
	            echo browser2($HOMEDIR,$_SESSION["current"]);
	          }
	          else
	          {
	            mkdir($dir);
	            echo "<script> alert('A pasta  foi criada.')</script>" ;
	            echo browser2($HOMEDIR,$_SESSION["current"]);
	          }
	        }
	        break;
	      case "apaga":
	      	if(!isset($_GET["OK"]))
	        {
	          $k = strlen($_SESSION["home"]);
              $sub = substr($_GET["value"],$k+1);
              $title = $sub;
              $above=realpath($HOMEDIR.'/'.$_GET["value"].'/../');
              $k = strlen($HOMEDIR.'/');
              $above = substr($above,$k);
	          echo '
	          <h2>Apagar a pasta</h2>
	          <center>
              <FORM ACTION="index.php" METHOD="get">
              <input type="hidden" name = "action" value="pasta">
              <input type="hidden" name = "sub" value="'.$_GET["sub"].'">
              <input type="hidden" name = "value" value="'.$sub.'">
              <input type="hidden" name = "above" value="'.$above.'">
              <input type="hidden" name = "OK" value="OK">
              
              Tem certeza que voc&ecirc; quer apagar a pasta abaixo? <br><br><h2>'.$title.'</h2></td>
             
              <br><b>OBS: Todo o conte&uacute;do desta pasta ser&aacute; apagado. Esta a&ccedil;&atilde;o n&atilde;o pode 
              ser desfeita</b><br>
	          <br>
              <input type="submit" value="Apaga a pasta"></input>
              </FORM>
              </center>
	        ';
	        }
	        else
	        { 
	          $dir =  $HOMEDIR.'/'.$_SESSION["home"].'/'.$_GET["value"];
	          if(file_exists($dir))
	          {
	            exec('/bin/rm -rf "'.$dir.'"');
	            $_SESSION["current"] = $_GET["above"];
	            echo "<script> alert(' pasta ".$_GET["value"]." foi apagada.')</script>" ;
	            echo browser2($HOMEDIR,$_SESSION["current"]);
	          }
	        }

	        break;
	      case "rename":
	        break;
	      case "copy":
	        break;

	    }
	  if(isset($_GET["err"]))
	  {
	    if($_GET["err"]=="apaga_ok") echo '<h2> A pasta e seu conte&uacute;do foram apagado.</h2>'; 
	    if($_GET["err"]=="nova_ok") echo '<h2> A pasta foi criada.</h2>'; 
	  }  
 
      }
    }
    else if(isset($_POST["cmd"]))
    {
      switch($_POST["cmd"])
      {
        case "ls":
          if(isset($_POST["Renomear_x"]))     echo lista($HOMEDIR,$_POST["dir"],$_POST["file"],"mv");
          else if(isset($_POST["Apagar_x"]))  echo lista($HOMEDIR,$_POST["dir"],$_POST["file"],"rm");
          else if(isset($_POST["Enviar_x"]))  echo lista($HOMEDIR,$_POST["dir"],$_POST["file"],"ln");
          else if(isset($_POST["Remover_x"])) echo lista($HOMEDIR,$_POST["dir"],$_POST["file"],"uln");
          else if(isset($_POST["Abrir_x"]))   echo browser2($HOMEDIR,$_SESSION["current"]);
	    break;
	    
	    case "ln":
	      $db = mysql_connect($SQLSERVER, $SQLUSER, $SQLPASS);
          if(!$db) echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>'; 
          else
          {
	        $dbf = mysql_select_db($SQLDB, $db);
            if(!$dbf) echo '<h2> Problemas com o banco de dados. Tente novamente mais tarde.</h2>'; 
            else
            {
	          atualiza_shares($db, $HOMEDIR,$_POST["dir"],$_POST["file"],$_POST["shares"]);
	          echo browser2($HOMEDIR,$_SESSION["current"]); 
	        }
	      }
	      break;
	    
	    case "uln":
          if($_POST["action"] =="Sim")
          {
            remove_my_share($HOMEDIR,$_POST["dir"],$_POST["file"]);
          }
	    
	      echo browser2($HOMEDIR,$_SESSION["current"]); 
	    break;
        
	    case "rm":
          if($_POST["action"] =="Sim")
          {
            $file = $HOMEDIR.'/'.$_POST["dir"].'/'.$_POST["file"];
            remove_all_shares($HOMEDIR,$_POST["dir"],$_POST["file"]); 
            exec('rm -rf "'.$file.'"');
          }
          echo browser2($HOMEDIR,$_SESSION["current"]);   
	    break;
	
	    case "mv":
          if($_POST["action"] =="Ok" && $_POST["novo"]!="")
          {
            //echo $_POST["dir"].'<br>'.$_SESSION["home"].'/'.$_POST["outdir"];
            rename_and_move_all_shares($HOMEDIR,$_POST["dir"],$_POST["file"],$_SESSION["home"].'/'.$_POST["outdir"],$_POST["novo"]);	        
          }
          echo browser2($HOMEDIR,$_SESSION["current"]);  
	      break;  	   	
      }
    }
    else echo browser2($HOMEDIR,$_SESSION["current"]);
    echo '</div>';
  }
  include("footer.php");  
?>
