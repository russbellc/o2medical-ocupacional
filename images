<?php

$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

$pieza1 = 'M5.2409 1.5713l2.0835 4.1941c-3.1122,1.0299 -2.5925,9.287 -0.8917,9.4677l-2.5772 3.0236 -3.1772 -2.5937c0.4051,-7.1705 1.8555,-12.0224 4.5626,-14.0917z';
$pieza2 = 'M17.1323 14.3957l4.7032 3.9035c2.5063,-4.2756 0.3012,-12.0626 -3.8138,-16.5366l-1.5697 4.6146c1.5886,0.5043 1.4067,7.8543 0.6803,8.0185z';
$pieza3 = 'M18.0217 1.7626l-1.5697 4.6146c-1.4752,-1.0772 -8.3835,-0.9484 -9.1276,-0.6118l-2.0835 -4.1941 3.4276 -0.9567 5.3575 -0.1701 3.9957 1.3181z';
$pieza4 = 'M6.4327 15.2331l-2.5772 3.0236 1.5449 1.0193 6.0827 1.3181 5.8571 -0.5102 4.4953 -1.7847 -4.7032 -3.9035c0.522,1.3417 -8.0126,1.5602 -10.6996,0.8374z';
$pieza5 = 'M11.0283 5.6744c-3.7784,0.1004 -5.4532,-0.9969 -5.7661,4.7823 -0.0012,5.3185 -0.1276,4.9677 5.7213,5.1012 7.3169,-0.3909 6.2504,0.0154 6.2646,-5.0161 0.1335,-4.5035 0.5102,-4.5095 -6.2197,-4.8673l-0.0001 -0.0001z';
header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>
 <g id='Capa_x0020_1_0'>
  <metadata id='CorelCorpID_0Corel-Layer'/>
  <polygon stroke='black' fill='white' points='0.8245,48.2135 0.6206,15.3994 5.2941,18.7259 11.7406,20.1164 '/>
  <polygon stroke='black' fill='white' points='22.3504,49.5466 22.5545,16.9373 21.7227,18.0787 17.4594,19.6229 11.7406,20.1164 '/>
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