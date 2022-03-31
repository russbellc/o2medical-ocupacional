<?php

$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

$pieza1 = 'M5.7921 36.9503l-4.5756 -3.6177c-0.4087,5.7945 0.1748,8.8098 1.3571,13.1976l3.5941 -3.0721 -0.3756 -6.5079 0 0.0001z';
$pieza2 = 'M17.7697 35.9829l3.4429 -2.426c0.9461,3.8646 0.8457,7.9051 -1.7362,12.365l-2.2028 -3.0614 0.4961 -6.8776z';
$pieza3 = 'M5.0776 36.3822l-3.861 -3.0496c4.8201,-2.6929 10.1669,-4.8827 19.5484,-0.6602l0.4476 0.8846 -3.4429 2.426 -12.6921 0.3992z';
$pieza4 = 'M17.2736 42.8605l2.2028 3.0614c-3.0567,4.1043 -12.5598,7.4374 -16.9028,0.6083l3.5941 -3.0721 11.1059 -0.5976z';
$pieza5 = 'M17.7697 35.9829c-5.0823,-1.2012 -7.5331,-0.6791 -12.6921,0.3992l1.0902 7.076c4.3476,0.6 6.7949,0.3248 11.1059,-0.5976 0.1654,-2.2925 0.3307,-4.585 0.4961,-6.8776l-0.0001 0z';
header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>
 <g id='Capa_x0020_1_0'>
   <metadata id='CorelCorpID_0Corel-Layer'/>
  <path stroke='black' fill='white' d='M11.0568 0.8191c-3.3328,10.1676 -6.6656,20.3353 -9.9982,30.5029l0 2.252c1.3102,-0.4482 2.2688,-1.2426 3.5761,-1.6897 2.7344,-0.9353 6.5667,-1.8821 9.4671,-1.2469l7.1573 2.3225 -10.2023 -32.1406 0 -0.0002z'/>
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