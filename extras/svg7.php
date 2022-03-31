<?php
$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

$pieza1 = 'M7.4268 10.2094l-4.0784 -6.4453c-3.0567,3.6224 -3.0496,9.8433 -2.6539,12.9602l6.7323 -6.515 0 0.0001z';
$pieza2 = 'M21.0213 3.6803l-4.6512 6.5291 7.1386 6.6224c0.1311,-1.9358 0.3579,-9.5823 -2.4874,-13.1516l0 0.0001z';
$pieza3 = 'M3.3484 3.7642l4.1835 6.4453 8.8382 0 4.6512 -6.5291c-2.1661,-2.9563 -5.1472,-2.4378 -8.1248,-2.7024 -2.6008,-0.5835 -5.0894,-1.4575 -9.548,2.7862l-0.0001 0z';
$pieza4 = 'M16.3701 10.2094l7.1386 6.6224c-0.6248,2.6185 -4.3335,3.8409 -10.8874,3.776 -1.2035,0.2374 -10.8213,-1.2898 -11.9268,-3.8835l6.7323 -6.515 8.9433 0 0 0.0001z';
$pieza5 = 'M7.6512 8.2642l8.4957 0c0.5291,0 0.9626,0.4323 0.9626,0.9626l0 1.9665c0,0.5303 -0.4335,0.9626 -0.9626,0.9626l-8.4957 0c-0.5303,0 -0.9626,-0.4323 -0.9626,-0.9626l0 -1.9665c0,-0.5303 0.4323,-0.9626 0.9626,0';
header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>
 <g id='Capa_x0020_1_0'>
  <metadata id='CorelCorpID_0Corel-Layer'/>
  <path stroke='black' fill='white' d='M12.3049 52.4433c-3.133,-8.8538 -12.2355,-26.3927 -11.7135,-35.3216l5.2244 1.5231c4.4266,1.9273 9.0186,2.1907 15.3496,0.2936l2.4068 -1.4803c-3.0113,12.3948 -6.487,23.1626 -11.2673,34.9855l0 -0.0003z'/>
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