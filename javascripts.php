<?php
function savedialog($FILE)
{
    echo '
    <script language="javascript" type="text/javascript">
    
    function download()
    {
      tmp=window.open(\'download.php?F='.$FILE.'\',\'Download\');
    }
    </script>
    ';

}

function point($F)
{
  include($F);
  echo '
  
  <script language="javascript" type="text/javascript">
  
  var myImg = document.getElementById("point_img");
  myImg.onmousedown = point_it;
  
  function FindPosition(oElement)
  {
    if(oElement.offsetParent)
    {
      var posX = 0, posY = 0;
      do
      {
        posX += oElement.offsetLeft;
	    posY += oElement.offsetTop;
      } while (oElement = oElement.offsetParent);
      
      return [posX, posY];
    }
    else
    {
      return [oElement.x, oElement.y];
    }
  }
  
  function PageScroll() 
  {
		var xScroll, yScroll;
		yScroll = document.getElementById("output").scrollTop;
		xScroll = document.getElementById("output").scrollLeft;
		return [xScroll,yScroll];
  }
  
  function point_it(e)
  {    
    var PosX = 0;
    var PosY = 0;
    var ImgPos;
    
    ImgPos = FindPosition(myImg);
    PagPos = PageScroll();
    
    ImgX = myImg.clientWidth;
    ImgY = myImg.clientHeight;
    if(!e) var e = window.event;
    if(e.pageX || e.pageY)
    {
      PosX = e.pageX;
      PosY = e.pageY;
    }
    else if(e.clientX || e.clientY)
    {
      PosX = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
      PosY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    
    var NPAD = '.$NPAD.';
    var XLOWNDC = new Array();
    var WNDC  =  new Array();
    var XMIN  =  new Array();
    var XMAX  =  new Array();
    var XLOG  =  new Array();
  
    var YLOWNDC  =  new Array();
    var HNDC  =  new Array();
    var YMIN  =  new Array();
    var YMAX  =  new Array();
    var YLOG  =  new Array();
    ';
    
    for($i=0;$i<$NPAD;$i++)
    {   
      echo
      '
        XLOWNDC['.$i.'] = '.$XLOWNDC[$i].';
        WNDC['.$i.'] = '.$WNDC[$i].';
        XMIN['.$i.'] = '.$XMIN[$i].';
        XMAX['.$i.'] = '.$XMAX[$i].';
        XLOG['.$i.'] = '.$XLOG[$i].';
        
        YLOWNDC['.$i.'] = '.$YLOWNDC[$i].';
        HNDC['.$i.'] = '.$HNDC[$i].';
        YMIN['.$i.'] = '.$YMIN[$i].';
        YMAX['.$i.'] = '.$YMAX[$i].';
        YLOG['.$i.'] = '.$YLOG[$i].';
        
      ';
    }
    
    echo '    
    
    PosX = (PosX - (ImgPos[0]-PagPos[0]))/ImgX;
    PosY = 1 - (PosY - (ImgPos[1]-PagPos[1]))/ImgY;
    
    var pad = 0;
    for (var i = 0; i<NPAD; i++)
    {
      if( (PosX>XLOWNDC[i] && PosX<=(XLOWNDC[i]+WNDC[i])) && (PosY>YLOWNDC[i] && PosY<=(YLOWNDC[i]+HNDC[i])))
      {
        pad = i;
      }
    }
    
    
    var X = 0;
    var Y = 0;
    var A = 0;

    A = (XMAX[pad]-XMIN[pad])/WNDC[pad];
    X = A*(PosX-XLOWNDC[pad])+XMIN[pad];
    if(XLOG[pad] == 1)
    {
      X = Math.pow(10,X);
    }
    
    A = (YMAX[pad]-YMIN[pad])/HNDC[pad];
    Y = A*(PosY-YLOWNDC[pad])+YMIN[pad];      
    if(YLOG[pad] == 1)
    {
      Y = Math.pow(10,Y);
    }
    
    document.coordenadas.form_x.value = X;
    document.coordenadas.form_y.value = Y;
  }
    
  </script>
  ';
}

function movecursor($NC, $NL)
{
  echo '<script language="javascript" type="text/javascript">            
    function moveDown(TB){if (TB.split("C")[0] < '.$NL.') document.getElementById(eval(TB.split("C")[0] + \'+1\') + \'C\' + TB.split("C")[1]).focus();}    
    function moveUp(TB){if(TB.split("C")[0] > 0) document.getElementById(eval(TB.split("C")[0] + \'-1\') + \'C\' + TB.split("C")[1]).focus();}    
    function moveLeft(TB){if(TB.split("C")[1] > 0) document.getElementById(TB.split("C")[0] + \'C\' + eval(TB.split("C")[1] + \'-1\')).focus();            }  
    function moveRight(TB){ if(TB.split("C")[1] < '.$NC.') document.getElementById(TB.split("C")[0] + \'C\' + eval(TB.split("C")[1] + \'+1\')).focus(); } 
    function KP(TB,e) 
    {
        if (e.keyCode == 40 || e.keyCode == 13) moveDown(TB,e);       
        if (e.keyCode == 38) moveUp(TB);
        if (e.keyCode == 37) moveLeft(TB);
        if (e.keyCode == 39) moveRight(TB);
        if (e.keyCode == 13) moveDown(TB,e);
    }   
    function disableEnter(e)
    {
       var key;     
       if(window.event) key = window.event.keyCode; //IE
       else key = e.which; //firefox     
       return (key != 13);
     }
    </script>
   ';
}

?>
