<?php

  echo '<form action="index.php" method="post" target="_blank"><center>
        Nome: <input type="text" name="nome" value="teste"><br><br>
        ID: <input type="text" name="ID" value="A1111111"><br><br>
        NC: <input type="text" name="NC" value="4"><br><br>
        NL: <input type="text" name="NL" value="10"><br><br>';
	
  for($i = 0; $i<10; $i++)
  {
    echo '<input type="hidden" name="COL'.$i.'" value = "COL'.$i.'">';
  }
  	
  echo '<input type = "submit" value="ok"></center></form>';

?>
