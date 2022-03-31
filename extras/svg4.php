<?php

$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

$pieza1 = 'M7.4276 42.6917l-6.6945 -6.4772c-0.4394,5.9929 0.3118,10.4563 2.6386,12.887l4.0559 -6.4098z';
$pieza2 = 'M23.4185 36.1071l-7.0984 6.5847 4.6252 6.4925c1.5354,-1.9406 2.3221,-4.3347 2.2051,-7.2756 0.0886,-1.9347 0.7571,-3.7524 0.2681,-5.8016z';
$pieza3 = 'M0.7331 36.2146c1.5756,-2.3669 9.052,-4.1941 11.9008,-3.861 6.5445,0.1394 10.2638,1.3441 10.7846,3.7535l-7.0984 6.5847 -8.8925 0 -6.6945 -6.4772z';
$pieza4 = 'M20.9453 49.1842l-4.6252 -6.4925 -8.8925 0 -4.0559 6.4098c2.8689,3.3874 6.202,4.05 9.874,2.7201 2.752,-0.228 5.7496,0.0154 7.6996,-2.6374z';
$pieza5 = 'M7.4276 41.0453c-1.0358,1.1055 -0.5858,2.8276 0.0744,3.3154l8.8181 0.039c0.9969,-0.8598 1.1079,-2.7354 0,-3.4323l-8.8925 0.078 0 -0.0001z';
header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>
 <g id='Capa_x0020_1_0'>
  <metadata id='CorelCorpID_0Corel-Layer'/>
  <path stroke='black' fill='white' d='M12.2143 0.6211c-3.1155,8.8045 -12.1672,26.2454 -11.6481,35.1246l5.195 -1.5147c4.4019,-1.9165 8.9685,-2.1785 15.2639,-0.2919l2.3934 1.472c-2.9945,-12.3256 -6.4508,-23.0333 -11.2044,-34.7902l0.0002 0.0002z'/>
 </g>
 <g id='Capa_x0020_1'>
  <metadata id='CorelCorpID_0Corel-Layer'/>                                                 
	<path fill='$color1' stroke='black' id='medio'  style='height: 20px; width: 21px;' d='$pieza1'/>
	<path fill='$color2' stroke='black' id='medio'  style='height: 20px; width: 21px;' d='$pieza2'/>
	<path fill='$color3' stroke='black' id='medio'  style='height: 20px; width: 21px;' d='$pieza3'/>
	<path fill='$color4' stroke='black' id='medio'  style='height: 20px; width: 21px;' d='$pieza4'/>
	<path fill='$color5' stroke='black' id='medio'  style='height: 20px; width: 21px;' d='$pieza5'/>
 </g>
</svg>";

echo $svg;
// It's that easy!