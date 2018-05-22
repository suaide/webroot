<?php
  $VERSION = 2;
             
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
                          
    $F = $TMPDIR.'/'.$_POST["SESSION_ID"].'.dat';
    $str = serialize($DATAV2);
    $encode = urlencode($str);
    file_put_contents($F,$encode);
    
    $_POST["VERSION"] = $VERSION;
    
    echo "<script> alert('Número elevado de linhas de dados. Para tornar o uso mais ágil, separamos a edição da planilha de dados em uma outra janela.');</script>";

?>