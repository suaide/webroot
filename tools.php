<?php
function welcome()
{
  $a = 
'<h2> Bem-vindo '.$_SESSION["nome"].'</h2>
<h4> &Uacute;tima conex&atilde;o em '.date("j/n/Y - G:i:s",$_SESSION["lastlogin"]).
'<br> Cadastrado desde '.date("j/n/Y - G:i:s",$_SESSION["created"]).'</h4><br>
';
  
  return $a;
}

function mailform()
{
  $a = 
'
  <form method="POST" action="contata.php">
  <h3>Campos Marcados com (*) são obrigat&oacute;rios.</h3>
        <table width = "450" border = "0" align = "center">
        <tr><td>Nome (*): </td><td><input  type="text" name="Name"  size = "50" /></td></tr>
        <tr><td>E-mail (*): </td><td><input  type="text" name="EmailFrom" size = "50" /></td></tr>
        <tr><td>Assunto (*): </td><td><input  type="text" name="Subject"  size = "50" /></td></tr>
        <tr><td>Texto:</td><td><textarea name="Message" cols = "44" rows = "10"></textarea></td></tr>
        <tr><td></td><td><center><br><input  type="submit" name="submit" value="Enviar" /></center></td></tr>
        </table>
        </form>
';
  return $a;
}

function calculadora()
{
  readfile('calc.html');
}
function confianca()
{
  include("conf.php");
  if(!isset($_POST["conf_fig"])) $_POST["conf_fig"] = "confianca.".time().rand();
  
  
  if(!isset($_POST["conf_interv"])) $_POST["conf_interv"] = 0.95;
  if(!isset($_POST["conf_ndf"])) $_POST["conf_ndf"] = 1;
  if(!isset($_POST["conf_avg"])) $_POST["conf_avg"] = 0;
  if(!isset($_POST["conf_sig"])) $_POST["conf_sig"] = 1;

  $V1["0"]="";  
  $V1["1"]="";  
  $V1["2"]="";    
  $V1["3"]="";    
  if(!isset($_POST["conf_FDP"])) $_POST["conf_FDP"] = 0;
  $V1[$_POST["conf_FDP"]]=" selected ";
  
  $V2["0"]="";  
  $V2["1"]="";  
  $V2["2"]="";    
  if(!isset($_POST["conf_teste"])) $_POST["conf_teste"] = 0;
  $V2[$_POST["conf_teste"]]=" selected ";
  
  echo '
  <form method="POST" action="index.php?action=intervalos">
  <input type="hidden" name="conf_fig" value="'.$_POST["conf_fig"].'">
  <center>
  <table border = 0 width = 50%>
  <tr>
    <td width=52%> Fun&ccedil;&atilde;o densidade de probabilidade</td>
    <td> 
     <select name="conf_FDP" title="Escolha a Fun&ccedil;&atilde;o densidade de probabilidade">
      <option value="0"'.$V1["0"].' > Chi<sup>2</sup> </option>
      <option value="1"'.$V1["1"].' > Chi<sup>2</sup><sub>red</sub></option>
      <option value="2"'.$V1["2"].' > Gaussiana</option>
      <option value="3"'.$V1["3"].' > Student</option>
     </select>
    </td>
  </tr>
  <tr>
    <td width=30%> N&iacute;vel de confian&ccedil;a</td>
    <td> <input type = "text" name = "conf_interv" size ="6" value = "'.htmlspecialchars($_POST["conf_interv"]).'" title="Probabilidade entre 0 e 1"> </td>
  </tr>
  <tr>
    <td width=30%> Tipo de c&aacute;lculo</td>
    <td> 
     <select name="conf_teste" title="Escolha os limites do teste">
      <option value="0"'.$V2["0"].' > Sim&eacute;trico de dois lados </option>
      <option value="1"'.$V2["1"].' > Apenas limite inferior</option>
      <option value="2"'.$V2["2"].' > Apenas limite superior</option>
     </select>
    </td>
  </tr>
  <tr>
    <td width=30%> N&uacute;mero de graus de liberdade</td>
    <td> <input type = "text" name = "conf_ndf" size ="6" value = "'.htmlspecialchars($_POST["conf_ndf"]).'" title="N&uacute;mero de graus de liberdade. V&aacute;lido apenas para testes de chi-quadrado e Student"> </td>
  </tr>
  <tr>
    <td width=30%> M&eacute;dia</td>
    <td> <input type = "text" name = "conf_avg" size ="6" value = "'.htmlspecialchars($_POST["conf_avg"]).'" title="M&eacute;dia da distribui&ccedil;&atilde;o. V&aacute;lido apenas para testes de gaussiana ou Student"> </td>
  </tr>
  <tr>
    <td width=30%> Desvio padr&atilde;o</td>
    <td> <input type = "text" name = "conf_sig" size ="6" value = "'.htmlspecialchars($_POST["conf_sig"]).'" title="Desvio padr&atilde;o da distribui&ccedil;&atilde;o. V&aacute;lido apenas para testes de gaussiana ou Student"> </td>
  </tr>

  </table>
  <br>
  <input type = "submit" name="conf_submit" value = " CALCULA " title="Calcula os intervalos e mostra gr&aacute;ficos.">
  
  </form>
  </center>
  ';
  
  if(isset($_POST["conf_submit"]))
  {
    
    $Pinf = (1-$_POST["conf_interv"])/2;
    $Psup = $Pinf + $_POST["conf_interv"];
    if($_POST["conf_teste"]==1)
    {
      $Pinf = 1 - $_POST["conf_interv"];
      $Psup = 1;
    }
    if($_POST["conf_teste"]==2)
    {
      $Pinf = 0;
      $Psup = $_POST["conf_interv"];
    }
    
    $ROOT = $ROOTDIR.'/bin/root -b -q ';
    $MACRO = 'root/cl.C\('.$_POST["conf_FDP"].','.$_POST["conf_ndf"].','.$_POST["conf_avg"].','.$_POST["conf_sig"].',0,'.$Pinf.','.$Psup.',\"'.$TMPDIR.'/'.$_POST["conf_fig"].'\"\)'; 
    
    $err = executa($ROOT.$MACRO, $_POST["conf_fig"], $TMPDIR, false);
    if($err!=0)
    {
      echo '<h3> Houve um erro na gera&ccedil;&atilde;o da figura </h3>
      <center><br>
      Os erros mais comuns s&atilde;o em f&oacute;rmulas mal escritas, o uso de v&iacute;rgulas como separador decimal de n&uacute;meros (utilize o ponto) e espa&ccedil;os indevidos. Por favor cheque e tente novamente.
      </center>
      ';
      return;
    }
    
    include($TMPDIR.'/'.$_POST["conf_fig"].".php");
    
    $MIN = $CONF_MIN;
    $MAX = $CONF_MAX;
    
    if($Pinf == 0) $MIN = "- &infin;";
    if($Psup == 1) $MAX = "+ &infin;";
    
    echo '
    <center><br><table width = 200>
    <tr>
      <td colspan = 2><h3>Intervalos de confian&ccedil;a</h2></td>
    <tr>
    <tr>
      <td> Limite inferior </td>
      <td> '.$MIN.'</td>
    </tr>
    <tr>
      <td> Limite superior </td>
      <td> '.$MAX.'</td>
    </tr>
    </table></center>
    ';
    
    exec("convert -quality 100 -density 300 -resize 1200x800 ".$TMPDIR.'/'.$_POST["conf_fig"].".eps ".$TMPDIR.'/'.$_POST["conf_fig"].".png");
    exec("/bin/rm ".$TMPDIR.'/'.$_POST["conf_fig"].".eps");
    
    echo '<center><img src="'.$TMPURL.'/'.$_POST["conf_fig"].'.png?'.time().'" width=65% >';
    
    //,$fig,$TMPDIR);
  }
}

function menu()
{
  $a = 
'
<hr><h3>Selecione uma op&ccedil;&atilde;o abaixo</h3>
<ul>
<li> Mostra a pasta
<ul>
<li><a href="index.php?action=opendir&dir=."><b>Minhas aplica&ccedil;&otilde;es</b></a>
<li><a href="index.php"><b>Pasta atual</b></a><br><br>
</ul>
<li>Criar um(a) novo(a) ... <br><br>
<ul>
<li> <a href="TGraph/" target="_blank"><b>Gr&aacute;fico</b></a> - Gr&aacute;fico simples (um conjunto de dados) com ajuste de fun&ccedil;&atilde;o
<li> <a href="TMultiGraph/" target="_blank"><b>Combinado de gr&aacute;ficos</b></a> - Combinar gr&aacute;ficos simples em uma &uacute;nica figura
<li> <a href="TH1/" target="_blank"><b>Histograma 1D</b></a> - Histograma em uma dimens&atilde;o (x) com ajuste de fun&ccedil;&atilde;o
<li> <a href="TF1/" target="_blank"><b>Fun&ccedil;&atilde;o f(x)</b></a> - Fun&ccedil;&atilde;o de uma vari&aacute;vel f(x) com com op&ccedil;&atilde;o de integral e derivada
<li> <a href="TChi2/" target="_blank"><b>Mapa de Chi<sup>2</sup></b></a> - Analisa mapa de Chi<sup>2</sup> para dois par&acirc;metros de um ajuste de gr&aacute;fico
<li> <a href="TErrorProp/" target="_blank"><b>Propaga&ccedil;&atilde;o de erros</b></a> - Calcula densidades de probabilidades usando m&eacute;todo de Monte Carlo com uma vari&aacute;vel independente e par&acirc;metros correlacionados entre s&iacute;
<li> <a href="TFFT/" target="_blank"><b>FFT</b></a> - Tranformada r&aacute;pida de Fourier em 1D com possibilidade de filtragem de sinal
</ul><br>
<li> Calculadoras <br><br>
<ul>
<li> <a href="index.php?action=intervalos"><b>C&aacute;lculo de intervalos de confi&acirc;n&ccedil;a </b></a> (Chi<sup>2</sup>, gaussina, Student, etc.)
<li> <a href="index.php?action=calculadora"><b>Calculadora cient&iacute;fica simples</b></a>
</ul><br>
<li><a href="index.php?action=senha">Alterar senha<br><br>
<li><a href="index.php?action=preferencias">Minhas prefer&ecirc;ncias<br><br>
<li><a href="http://sampa.if.usp.br/wiki/index.php/Webroot" target="_blank">Documentação do WebROOT<br><br>
<li><a href="logout.php">Sair</a>
</ul><hr>
';

// <li> <a href="TF2/" target="_blank"><b>Fun&ccedil;&atilde;o f(x,y)</b></a> - Fun&ccedil;&atilde;o de duas vari&aacute;veis f(x,y) 

  return $a;
}

function menu_admin()
{
  if(!checa_admin()) return;
  
  $a =
'
<h3>Tarefas administrativas</h3>
<ul>
<li> <a href="index.php?admin=usuarios&index=0&L=20">Gerenciar usu&aacute;rios</a><br><br>
<li> <a href="index.php?admin=cache">Limpa cache tempor&aacute;rio</a>
</ul>
';
  return $a;
}

function preferencias()
{  
  $c[0] = "";
  $c[1] = "checked";
  echo '
<h2> Prefer&ecirc;ncias </h2>  
<br>
<center>
<form action="preferencias.php" method="post">
<table width="50%" border="0">
<tr><td width="15%"><input type="checkbox" name="simples" value="1" '.$c[$_SESSION["simples"]].'></td><td >Se marcado, mostra aplica&ccedil;&otilde;es em modo simples. Bom para quando h&aacute; muitas aplica&ccedil;&otilde;es salvas.</td></tr>
<tr><td width="15%"><input type="checkbox" name="escala"  value="1" '.$c[$_SESSION["escala"]].'></td><td >Se marcado, escolhe limites dos eixos autom&aacute;ticamente nos gr&aacute;ficos, caso estes limites não sejam explicitamente escolhidos.</td></tr>
<tr><td width="15%"><input type="checkbox" name="size"    value="1" '.$c[$_SESSION["size"]].'></td><td >Se marcado, escalona figura (gr&aacute;fico) para preencher toda a janela. Se desmarcado, mostra a figura no seu tamanho real.</td></tr>
</table><br><br>
<input type="submit" name="action" value="Salvar configura&ccedil;&otilde;es">
</form>
  ';
}

function phpMyEd()
{

echo '
<style type="text/css">
	hr.pme-hr		     { border: 0px solid; padding: 0px; margin: 0px; border-top-width: 1px; height: 1px; }
	table.pme-main 	     { border: #004d9c 1px solid; border-collapse: collapse; border-spacing: 0px; width: 95%; }
	table.pme-navigation { border: #004d9c 0px solid; border-collapse: collapse; border-spacing: 0px; width: 95%; }
	td.pme-navigation-0, td.pme-navigation-1 { white-space: nowrap; }
	th.pme-header	     { border: #004d9c 1px solid; padding: 4px; background: #add8e6; }
	td.pme-key-0, td.pme-value-0, td.pme-help-0, td.pme-navigation-0, td.pme-cell-0,
	td.pme-key-1, td.pme-value-1, td.pme-help-0, td.pme-navigation-1, td.pme-cell-1,
	td.pme-sortinfo, td.pme-filter { border: #004d9c 1px solid; padding: 3px; }
	td.pme-buttons { text-align: left;   }
	td.pme-message { text-align: center; }
	td.pme-stats   { text-align: right;  }
</style>
';

/*
 * IMPORTANT NOTE: This generated file contains only a subset of huge amount
 * of options that can be used with phpMyEdit. To get information about all
 * features offered by phpMyEdit, check official documentation. It is available
 * online and also for download on phpMyEdit project management page:
 *
 * http://platon.sk/projects/main_page.php?project_id=5
 *
 * This file was generated by:
 *
 *                    phpMyEdit version: 5.7.1
 *       phpMyEdit.class.php core class: 1.204
 *            phpMyEditSetup.php script: 1.50
 *              generating setup script: 1.50
 */

// MySQL host name, user name, password, database, and table
$opts['page_name'] = 'index.php';
$opts['cgi']['persist']=array('admin'=>'usuarios');

$opts['hn'] = 'localhost';
$opts['un'] = 'webroot';
$opts['pw'] = 'webrootsenha';
$opts['db'] = 'webroot';
$opts['tb'] = 'passwd';

// Name of field which is the unique key
$opts['key'] = 'ID';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('ID');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 30;

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'ACPVDF';

// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

// Navigation style: B - buttons (default), T - text links, G - graphic links
// Buttons position: U - up, D - down (default)
$opts['navigation'] = 'DB';

// Display special page elements
$opts['display'] = array(
	'form'  => true,
	'query' => true,
	'sort'  => true,
	'time'  => true,
	'tabs'  => true
);

// Set default prefixes for variables
$opts['js']['prefix']               = 'PME_js_';
$opts['dhtml']['prefix']            = 'PME_dhtml_';
$opts['cgi']['prefix']['operation'] = 'PME_op_';
$opts['cgi']['prefix']['sys']       = 'PME_sys_';
$opts['cgi']['prefix']['data']      = 'PME_data_';

/* Get the user's default language and use it if possible or you can
   specify particular one you want to use. Refer to official documentation
   for list of available languages. */
$opts['language'] = 'PT-BR';

/* Table-level filter capability. If set, it is included in the WHERE clause
   of any generated SELECT statement in SQL query. This gives you ability to
   work only with subset of data from table.

$opts['filters'] = "column1 like '%11%' AND column2<17";
$opts['filters'] = "section_id = 9";
$opts['filters'] = "PMEtable0.sessions_count > 200";
*/

/* Field definitions
   
Fields will be displayed left to right on the screen in the order in which they
appear in generated list. Here are some most used field options documented.

['name'] is the title used for column headings, etc.;
['maxlen'] maximum length to display add/edit/search input boxes
['trimlen'] maximum length of string content to display in row listing
['width'] is an optional display width specification for the column
          e.g.  ['width'] = '100px';
['mask'] a string that is used by sprintf() to format field output
['sort'] true or false; means the users may sort the display on this column
['strip_tags'] true or false; whether to strip tags from content
['nowrap'] true or false; whether this field should get a NOWRAP
['select'] T - text, N - numeric, D - drop-down, M - multiple selection
['options'] optional parameter to control whether a field is displayed
  L - list, F - filter, A - add, C - change, P - copy, D - delete, V - view
            Another flags are:
            R - indicates that a field is read only
            W - indicates that a field is a password field
            H - indicates that a field is to be hidden and marked as hidden
['URL'] is used to make a field 'clickable' in the display
        e.g.: 'mailto:$value', 'http://$value' or '$page?stuff';
['URLtarget']  HTML target link specification (for example: _blank)
['textarea']['rows'] and/or ['textarea']['cols']
  specifies a textarea is to be used to give multi-line input
  e.g. ['textarea']['rows'] = 5; ['textarea']['cols'] = 10
['values'] restricts user input to the specified constants,
           e.g. ['values'] = array('A','B','C') or ['values'] = range(1,99)
['values']['table'] and ['values']['column'] restricts user input
  to the values found in the specified column of another table
['values']['description'] = 'desc_column'
  The optional ['values']['description'] field allows the value(s) displayed
  to the user to be different to those in the ['values']['column'] field.
  This is useful for giving more meaning to column values. Multiple
  descriptions fields are also possible. Check documentation for this.
*/

$opts['fdd']['ID'] = array(
  'name'     => 'ID',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['tipo'] = array(
  'name'     => 'Tipo',
  'select'   => 'T',
  'maxlen'   => 1,
  'options'  => 'LACPDV',
  'sort'     => true
);
$opts['fdd']['login'] = array(
  'name'     => 'Login',
  'select'   => 'T',
  'maxlen'   => 30,
  'options'  => 'LACPDV',
  'sort'     => true
);
$opts['fdd']['nome'] = array(
  'name'     => 'Nome',
  'select'   => 'T',
  'maxlen'   => 35,
  'options'  => 'LACPDV',
  'sort'     => true
);
$opts['fdd']['usp'] = array(
  'name'     => 'Usp',
  'select'   => 'T',
  'maxlen'   => 12,
  'options'  => 'ACPDV',
  'sort'     => true
);
$opts['fdd']['email'] = array(
  'name'     => 'Email',
  'select'   => 'T',
  'maxlen'   => 60,
  'options'  => 'LACPDV',
  'sort'     => true
);
$opts['fdd']['created'] = array(
  'name'     => 'Created',
  'select'   => 'T',
  'maxlen'   => 13,
  'options'  => 'ACPDV',
  'sort'     => true
);
$opts['fdd']['lastlogin'] = array(
  'name'     => 'Lastlogin',
  'select'   => 'T',
  'maxlen'   => 13,
  'options'  => 'LACPDV',
  'sort'     => true
);
$opts['fdd']['home'] = array(
  'name'     => 'Home',
  'select'   => 'T',
  'maxlen'   => 35,
  'options'  => 'ACPDV',
  'sort'     => true
);
$opts['fdd']['senha'] = array(
  'name'     => 'Senha',
  'select'   => 'T',
  'maxlen'   => 45,
  'options'  => 'ACPDV',
  'sort'     => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
new phpMyEdit($opts);
return;

}

function lista_outras()
{
  echo '<h1> Outras tarefas administrativas</h1>';
}

function EH_DIR($dir)
{
  if(file_exists($dir."/id.php")) return false;
    
  return true;
}


function browser2($HOMEDIR,$dir)
{
  //echo $dir."<br>";
  exec('ls -a "'.$HOMEDIR.'/'.$dir.'"',$files);
  $k = strlen($_SESSION["home"]);
  $sub = substr($dir,$k+1);
     
  $dir1 = realpath($HOMEDIR.'/'.$dir);
  $tmp = $HOMEDIR.'/'.$_SESSION["home"];
  $check = ($tmp === "" || strpos($dir1, $tmp) === 0);
  if(!$check) $dir=$_SESSION["home"];
  
  $b = "";
  $above=realpath($HOMEDIR.'/'.$dir.'/../');
  $k = strlen($HOMEDIR.'/');
  $above = substr($above,$k);
  
  if($dir==$_SESSION["home"])
  {
    if(in_array("shared",$files))
    {
       $index = array_search("shared",$files);       
       unset($files[$index]);
       array_push($files,"shared");
       $files=array_values($files);
       //Print_r($files);
    }
  }
  
  $count = count($files);
  $cdir   = 0;
  $cfiles = 0;
  
  for ($i = 0; $i<$count; $i++)
  {
    $file = $files[$i];
    //echo $file."    =  ";
    if ($file != "." && $file != ".." && is_dir($HOMEDIR.'/'.$dir.'/'.$file) )
    {
     //echo $HOMEDIR.'/'.$dir.'/'.$file."  =  ";
     if(EH_DIR($HOMEDIR.'/'.$dir.'/'.$file))
     {
       //echo "ok<br>";
       $LISTA_DIR[$cdir] =  $file;
       $cdir++;  
     }
     else
     {
       //echo "not ok  ";
       $LISTA_FILE[$cfiles] = $file;
       $cfiles++;
       //echo $cfiles."<br>";
     }
    } 
  }

  $size = 25;
  $margin = -36;
  if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) $margin = -50;
  if($_SESSION['simples'] == 1) 
  {
    $size = 15;
    $margin = -32;
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) $margin = -45;
  }
  //if(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) $margin = -45;
  //if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) $margin = -45;
  //if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) $margin = -45;
  //if(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) $margin = -45;
  //if(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini')) $margin = -45;
  
  $a .= '<div align="right" style="margin-top:'.$margin.'px;"><br>';
  
  if($dir!=$_SESSION["home"]) 
  {
    if($dir!=$_SESSION["home"].'/shared') $a.='<a href="index.php?action=pasta&sub=apaga&value='.$dir.'"><img src="images/delete.jpg" width="'.$size.'" title="apagar esta pasta"></a>&nbsp;&nbsp;';
    $a.='<a href="index.php?action=opendir&dir='.urlencode(".").'"><img src="images/home.png" width="'.$size.'" title="Ir para a pasta principal"></a>&nbsp;';    
    $a.='<a href="index.php?action=opendir&dir='.urlencode($above).'"><img src="images/up.png" width="'.$size.'" title="Ir uma pasta acima"></a>&nbsp;';
  
  }

  
  if($dir!=$_SESSION["home"].'/shared') $a.='<a href="index.php?action=pasta&sub=nova&value='.$dir.'"><img src="images/new_folder.png" width="'.$size.'" title="criar uma nova sub-pasta"></a>&nbsp;';  
  $a.='<a href="index.php?action=opendir&dir='.urlencode($dir).'"><img src="images/refresh.jpg" width="'.$size.'" title="atualizar conte&uacute;do desta pasta"></a>&nbsp;';  
  $a.='<br></div>';
  $tag = "";
  
  if($HOMEDIR.'/'.$dir== $HOMEDIR.'/'.$_SESSION["home"].'/shared')
  {
    $F = $HOMEDIR.'/'.$_SESSION["home"].'/shared/';
    exec('find -L '.$F.' -type l -delete');
    $tag="shared";
  }
  
  
  for($i = 0; $i<$cdir; $i++)
  {
        $file = $LISTA_DIR[$i];
        $title = $file;
        if($HOMEDIR.'/'.$dir.'/'.$file != $HOMEDIR.'/'.$_SESSION["home"].'/shared')
        {                  
        }
        else 
        {     
          $title = "Arquivos compartilhados comigo";  
        }        
        $a.=lista($HOMEDIR,$dir, $file, "ls", $tag, true);         
  }
  
  for($i = 0; $i<$cfiles; $i++)
  {

    $file = $LISTA_FILE[$i];
    $a .= lista($HOMEDIR,$dir, $file, "ls", $tag);
  }

  $id = md5($dir."browser");
  $title = $sub;
  
  if($dir==$_SESSION["home"])  $title="Minhas aplica&ccedil;&otilde;es";
  if($HOMEDIR.'/'.$dir== $HOMEDIR.'/'.$_SESSION["home"].'/shared') $title = "Arquivos compartilhados comigo";

  $b .= c_fieldset($id,$a,'<h2> '.$title.'</h2>',false);
  
  return $b;
}


function lista_dir($HOMEDIR, $target, $name="inputdir")
{
  include("conf.php");
  $a  = '<select name="'.$name.'" title="Escolha a pasta"  style="width: 155px">';
  $a .= cria_lista_dir($HOMEDIR,$_SESSION["home"],$target);
  $a .= '</select>';
  return $a;

}
function cria_lista_dir($HOMEDIR, $dir, $target="")
{
  exec('ls -a "'.$HOMEDIR.'/'.$dir.'"',$files);
  $k = strlen($_SESSION["home"]);
  $count = count($files);
  $b = "";
  //echo $dir.'<br>';
  $tmp = substr($dir,$k+1);
  //echo $data["dir"].' ----  '.$tmp.'<br>';
  if ($target==$tmp) $V = ' selected ';
  else $V = "";
  $b .= '<option value="'.$tmp.'"'.$V.' >/'.$tmp.'</option>';

  for ($i = 0; $i<$count; $i++)
  {
    $file = $files[$i];
    if ($file != "." && $file != ".." && is_dir($HOMEDIR.'/'.$dir.'/'.$file) && $HOMEDIR.'/'.$dir.'/'.$file != $HOMEDIR.'/'.$_SESSION["home"].'/shared')
    {
     if(!file_exists($HOMEDIR.'/'.$dir.'/'.$file.'/id.php'))
      {   
        $b .= cria_lista_dir($HOMEDIR,$dir.'/'.$file,$target);   
      }
    }    
  }  
  return $b;

}

function lista($HOMEDIR,$dir, $file, $action, $tag="", $isdir = false)
{
  $icon = true;
  $size = 15;
  $start = ' <div style="float:left; width:180px; height:125px; margin:1px 1px 1px 1px;"> <center>';
  $stop  = ' </center> </div> ';
  if($_SESSION['simples'] == 1) 
  {
        $icon = false;
        $size = 15;
        $start = ' <table border = 0 width = "90%" > <center>';
        $stop  = ' </center> </table> ';    
  }


    $a = "";
    $a.= '<form name="'.$ID.'" method = "post" action = "index.php">';       
    $a.= '<input type="hidden" name="file" value="'.$file.'">';
    $a.= '<input type="hidden" name="dir" value="'.$dir.'">';    

    if($isdir)
    {
      $title = $file;
      if($HOMEDIR.'/'.$dir.'/'.$file == $HOMEDIR.'/'.$_SESSION["home"].'/shared') $title = "Arquivos compartilhados comigo";
      $a.= $start;
      $a.= '<input type="hidden" name="cmd" value="opendir">';
      $a.= '<input type="hidden" name="app" value="'.$APP.'">';
      if($icon)
      {
        $a.= '<a href="index.php?action=opendir&dir='.urlencode($dir.'/'.$file).'"> <img src = "images/folder.jpg" height="64"> </a><br>';
        $a.= '<a href="index.php?action=opendir&dir='.urlencode($dir.'/'.$file).'"> <b>'.$title.'</b> </a>';
      }
      else
      {
         $a.= '<tr><td><img src = "images/folder.gif" height="15"> <a href=index.php?action=opendir&dir='.urlencode($dir.'/'.$file).'><b>'.$title.'</b></a> </td> ';
	     $a.= '</td><td style="vertical-align:middle" width="32%">';
      }
      
      $a.='</form>';
      $a.=$stop;
      return $a;
    }
        
    $F = $HOMEDIR.'/'.$dir.'/'.$file.'/id.php';
    if(file_exists($F)) include ($F);
        
    if($action=="rm")  $a.= "<h2> Apagar arquivo</h2>";
    if($action=="mv")  $a.= "<h2> Renomear ou mover arquivo</h2>";
    if($action=="ln")  $a.= "<h2> Compartilhar arquivo com colegas</h2>";
    if($action=="uln") $a.= "<h2> Remover compartilhamento</h2>";
    
    $NSHARE = 0;
    $F = $HOMEDIR.'/'.$dir.'/'.$file.'/share.php';
    if(file_exists($F)) include ($F);
            
    if($action=="ls")
    {              
      $a.= '<input type="hidden" name="cmd" value="ls">';
      $a.= '<input type="hidden" name="app" value="'.$APP.'">';
            
      $a.= $start;
      $a.= '<tr>';
      if($icon) 
      {
        $a.= '
                   <a href='.$APP.'/index.php?action=abrir&file='.urlencode($file).'&dir='.urlencode($dir).' target="_blank">
                   <img src = "link.php?file='.$HOMEDIR.'/'.$dir.'/'.$file.'/thumb.png" height="64"  >
                   </a> 
                   <br>
                   <a href='.$APP.'/index.php?action=abrir&file='.urlencode($file).'&dir='.urlencode($dir).' target="_blank"><b>'.$file.'</b></a><br>';
                   
        if($tag=="shared") $a.="Compartilhado de ".$OWNER."<br>";
                                              
      }
      else
      {
         $a.= '<td><img src = "images/graph.jpg" height="15"> <a href='.$APP.'/index.php?action=abrir&file='.urlencode($file).'&dir='.urlencode($dir).' target="_blank"><b>'.$file.'</b></a> </td> ';
	     $a.= '</td><td style="vertical-align:middle" width="32%">';
      }
	  
	  $b = ' ('.$NSHARE.') ';
	          
      if($icon)
      {
	  }
	  else
	  {
	      $a.= '</td><td style="vertical-align:middle" width="32%">';
	      $b = ' ('.$NSHARE.') ';
	  }
	  
      if($tag!="shared")
      {
            $a.= '<input type = "image" style="vertical-align:middle" src="images/share.png"  width="'.$size.'"  title="Compartilhar com colegas" name="Enviar" value = "Enviar">'.$b.' &nbsp;&nbsp;&nbsp;&nbsp;';
            $a.= '<input type = "image" style="vertical-align:middle" src="images/rename.png"  width="'.$size.'"  title="Renomear" name="Renomear" value = "Renomear"> &nbsp;&nbsp;&nbsp;&nbsp;';
            $a.= '<input type = "image" style="vertical-align:middle" src="images/delete.jpg"  width="'.$size.'"  title="Apagar" name="Apagar" value = "Apagar"> ';
      }
      else
      {
            $a.= '<input type = "image" style="vertical-align:middle" src="images/unshare.png"  width="'.$size.'" title="Remover compartilhamento" name="Remover" value = "Remover">';
      }
      if($icon)
      {
        $a.='<br><br></center></td></tr></table>';
      }
     
      $a.= $stop.'</form>';
    }
    else
    {
      $a.= '<center> <table border = 0 width = "90%">';
      $a.= '<tr>';
      $a.= '<td width = "250px"> <img src = "link.php?file='.$HOMEDIR.'/'.$dir.'/'.$file.'/thumb.png"> </td>';
      $a.= '<td>';
      $a.= 'Nome: <b>'.$file.'</b> &nbsp;&nbsp; ';
      $a.= '<br> Salvo em: '.date("d/m/Y - H:i:s",$TIME).'<br>';

      if($action=="rm")
      {
        $a.= 'Confirma a remo&ccedil;&atilde;o?<br><br>';
        $a.= 'N&uacute;mero de compartilhamentos: '.$NSHARE.'<br><br>';
	    $a.= '<input type="hidden" name="cmd" value="rm">';
        $a.= '<input type = "submit" name="action" value = "Sim"> &nbsp;&nbsp;&nbsp;&nbsp;';
        $a.= '<input type = "submit" name="action" value = "N&atilde;o"> ';
      }
      if($action=="uln")
      {
        $a.= 'Confirma a remo&ccedil;&atilde;o do compartilhamento?<br><br>';
	    $a.= '<input type="hidden" name="cmd" value="uln">';
        $a.= '<input type = "submit" name="action" value = "Sim"> &nbsp;&nbsp;&nbsp;&nbsp;';
        $a.= '<input type = "submit" name="action" value = "N&atilde;o"> ';
      }
      if($action=="mv")
      {
        $k = strlen($_SESSION["home"]);
        $tmp = substr($dir,$k+1);
        
        $a.= 'Digite o novo nome para esta aplica&ccedil;&atilde;o:<br><br>';
        $a.= 'N&uacute;mero de compartilhamentos: '.$NSHARE.'<br><br>';
	    $a.= '<input type="hidden" name="cmd" value="mv">';
	    $a.= 'Pasta: '.lista_dir($HOMEDIR,$tmp,"outdir");
        $a.= '<br>Nome: <input type = "text" name="novo" value="'.$file.'"> &nbsp;&nbsp;&nbsp;&nbsp;';
        $a.= '<input type = "submit" name="action" value = "Ok"> ';
      }
      if($action=="ln")
      {
        
        $a.= '<br>Edite a lista de compartilhamento, colocando ou retirando os e-mails dos seus colegas (<b>deve ser o e-mail cadastrado no WebROOT</b>), um e-mail em cada linha [ENTER]:<br><br>';
        $a.= 'N&uacute;mero de compartilhamentos: '.$NSHARE.'<br><br>';
        $a.= '<input type="hidden" name="cmd" value="ln">';
        $a.= '<textarea name="shares" cols="40" rows ="4">'; 
        for($i = 0; $i<$NSHARE; $i++) $a.=$S[$i]."\n";        
        $a.= '</textarea><br><br>';
        $a.= '<input type = "submit" name="action" value = "Atualizar compartilhamento"> ';
      }    
    }    
    $a.= '</td></tr>';
    $a.= '</table></form></center>';
    
    
    return $a;
}
function atualiza_shares($db,$HOMEDIR, $dir, $file, $data)
{
  // primeiro removes shares antigas, se existirem
  
  remove_all_shares($HOMEDIR, $dir, $file);

  // depois cria novos shares, se existirem
  $F = $HOMEDIR.'/'.$dir.'/'.$file.'/share.php';
  $A = $HOMEDIR.'/'.$dir.'/'.$file;
  $lines = explode("\r", $data);
  $c = count($lines);
  Print_r($lines);
  echo '<br>linhas = '.$c;
  
  $f = fopen($F,"w");
  fwrite($f,"<?php\n");
  $NSHARE = 0;

  for($i = 0; $i<$c; $i++)
  {
    
    $q = "SELECT * FROM passwd WHERE email='".trim($lines[$i])."';";
    $Q = mysql_query($q);
    $n = mysql_num_rows($Q);
    if($n>0 && trim($lines[$i])!=$_SESSION["email"])
    {
      fwrite($f,"  \$S[".$i."]       = '".trim($lines[$i])."'; \n" );
      
      $row = mysql_fetch_array($Q);
      $home = $row["home"];
      $sdir = $HOMEDIR.'/'.$home.'/shared';
      if(!is_dir($sdir)) mkdir($sdir,0777,true);
      $link0 = $HOMEDIR.'/'.$home.'/shared/'.$file;
      $link = $link0;
      $index=0;
      while(file_exists($link))
      {
        $link=$link0.'-'.$index;
	    $index++;
      }
      fwrite($f,"  \$LINK[".$i."]       = '".$link."'; \n" );
      
      //echo 'ln -s "'.$A.'" "'.$link.'"';
      exec('ln -s "'.$A.'" "'.$link.'"');
      
      //symlink($A,$link);
      
      //enviar email para informar o compartilhamento
      email_share($_SESSION["nome"],$_SESSION["email"], $row["nome"], $row["email"],$file);
      
      $NSHARE++;
    }
  }

  fwrite($f,"  \$NSHARE = ".$NSHARE."; \n" );
  fwrite($f,"  \$OWNER = '".$_SESSION["nome"]."'; \n" );
  fwrite($f,"  \$OWNER_EMAIL = '".$_SESSION["email"]."'; \n" );
  fwrite($f,"?>\n");
  fclose($f);

}
function remove_all_shares($HOMEDIR, $dir, $file)
{
  // primeiro removes shares antigas, se existirem
  $F = $HOMEDIR.'/'.$dir.'/'.$file.'/share.php';
  $A = $HOMEDIR.'/'.$dir.'/'.$file;
  
  $NSHARE = 0;
  if(file_exists($F)) 
  {
    include($F);
    for($i = 0; $i<$NSHARE; $i++)
    {    
        if(file_exists($LINK[$i])) if(is_link($LINK[$i])) exec('/bin/rm -f "'.$LINK[$i].'"');
    }
    exec('/bin/rm -f "'.$F.'"');  

  }

}
function remove_my_share($HOMEDIR, $dir, $file)
{
  // primeiro removes shares antigas, se existirem
  $F = $HOMEDIR.'/'.$dir.'/'.$file.'/share.php';
  $A = $HOMEDIR.'/'.$dir.'/'.$file;
  $NSHARE = 0;
  
  if(file_exists($F))
  {
    include($F);
    unlink($F);
    
    $f = fopen($F,"w");
    fwrite($f,"<?php\n");
    $shift = 0;
    for($i = 0; $i<$NSHARE; $i++)
    {
      if($S[$i]==$_SESSION["email"] && $LINK[$i]==$A)
      {
        $shift++;
      }
      else
      {
        fwrite($f,"  \$S[".($i-$shift)."]       = '".$S[$i]."'; \n" );
        fwrite($f,"  \$LINK[".($i-$shift)."]    = '".$LINK[$i]."'; \n" );
      }
    }  
    fwrite($f,"  \$NSHARE = ".($NSHARE-$shift)."; \n" );
    fwrite($f,"  \$OWNER = '".$_SESSION["nome"]."'; \n" );
    fwrite($f,"  \$OWNER_EMAIL = '".$_SESSION["email"]."'; \n" );
    fwrite($f,"?>\n");
    fclose($f);
    
  }
  if(file_exists($A)) if(is_link($A)) exec('/bin/rm -f "'.$A.'"');
}
function rename_and_move_all_shares($HOMEDIR, $dir, $file1, $outdir, $file2)
{
  $F = $HOMEDIR.'/'.$dir.'/'.$file1.'/share.php';
  $A = $HOMEDIR.'/'.$outdir.'/'.$file2;
  $N = $HOMEDIR.'/'.$outdir.'/'.$file2.'/share.php';
  
  $NSHARE = 0;
  if(file_exists($F)) include($F); 
  
  remove_all_shares($HOMEDIR,$dir,$file1);
  
  $f1 = $HOMEDIR.'/'.$dir.'/'.$file1;
  $f2 = $HOMEDIR.'/'.$outdir.'/'.$file2;
  
  rename($f1,$f2);
  
  $f = fopen($N,"w");
  fwrite($f,"<?php\n");
  for($i = 0; $i<$NSHARE; $i++)
  {
      fwrite($f,"  \$S[".$i."]       = '".$S[$i]."'; \n" );
      fwrite($f,"  \$LINK[".$i."]    = '".$LINK[$i]."'; \n" );

      exec('ln -s "'.$A.'" "'.$LINK[$i].'"');
      //symlink($A,$LINK[$i]);
  }  
  fwrite($f,"  \$NSHARE = ".$NSHARE."; \n" );
  fwrite($f,"  \$OWNER = '".$_SESSION["nome"]."'; \n" );
  fwrite($f,"  \$OWNER_EMAIL = '".$_SESSION["email"]."'; \n" );
  fwrite($f,"?>\n");
  fclose($f);
  
}

function email_share($from, $fromemail, $to, $toemail, $file)
{

  $subject="[WebROOT] compartilhamento de aplicação";

  $toe = $toemail;

  $headers=
'From: '.$fromemail.'
Mime-Type: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-encoding: 8bit
';

  $body=
'  Olá '.$to.'

Acabei de compartilhar com você a aplicação

'.$file.'

Para você ter acesso, basta se conectar ao WebROOT e clicar em Arquivos Compartilhados Comigo, no menu Minhas Aplicações.

Atenciosamente,

      '.$from.'
      
';

  $success = mail($toe, $subject, $body, $headers);

}
?>
