<?php
  //include("checa.php");
  // diretorios que precisam ser acessados do web server
  $BASEURL    = 'http://webroot.if.usp.br';
  $BASEDIR    = '/sampa/admin/www/html/webroot';
  $TMPDIR     = '/sampa/admin/www/html/webroot/tmp';
  $TMPURL     = $BASEURL.'/tmp';

  // diretorios sem acesso pelo web server

  $ROOTDIR    = '/sampa/admin/webapp/root';
  $HOMEDIR    = '/sampa/admin/webapp/webroot/home';
  $PASSWD     = '/sampa/admin/webapp/webroot/passwd.txt';
  $ID         = '/sampa/admin/webapp/id_rsa';

  $ADMIN      = 'sampa-admin@dfn.if.usp.br';

  // outras configuracoes
  $MAXTIME    = 18000;
  $ROOTSTYLE  = 'Modern';

  $SQLSERVER  = "localhost";
  $SQLDB      = "webroot";
  $SQLUSER    = "webroot";
  // senha removida por questoes de seguranca
  $SQLPASS    = "*****";

?>
