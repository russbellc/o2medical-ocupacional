<?php

$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

$pieza1 = 'M6.2892 35.4491l-2.8205 -2.9173 -2.602 1.852c0.561,6.9992 1.8366,12.3661 4.5272,14.5051l1.9276 -4.4929 -1.0323 -8.9469z';
$pieza2 = 'M17.564 35.8082l4.8295 -3.6697c1.8012,2.6575 0.5598,12.4193 -4.1244,16.5661l-1.676 -4.7976 0.9709 -8.0988z';
$pieza3 = 'M3.4687 32.5318l2.8205 2.9173 11.2748 0.3591 4.8295 -3.6697c-0.287,-0.9555 -2.8701,-1.3217 -4.3335,-1.7847l-6.1004 -0.6756 -6.4311 1.3866 -2.0598 1.4669 0 0.0001z';
$pieza4 = 'M16.5931 43.907l1.676 4.7976c-4.3878,1.5248 -8.8429,1.4173 -12.8752,0.1843l1.9276 -4.4929 9.2717 -0.489 -0.0001 0z';
$pieza5 = 'M12.5656 44.6641c4.5402,-0.0685 5.8098,-1.2012 5.5276,-5.2193 0.3827,-4.2791 -0.2114,-4.8354 -5.4248,-4.6276 -6.7654,-0.4642 -6.8953,-0.2634 -6.789,4.728 0.0579,4.3642 0.4701,5.5713 6.6862,5.1189z';
header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>
 <g id='Capa_x0020_1_0'>
  <metadata id='CorelCorpID_0Corel-Layer'/>
  <polygon stroke='black' fill='white' points='1.1978,2.2477 0.9991,34.1949 5.5492,30.9563 11.8255,29.6025 '/>
  <path stroke='black' fill='white' d='M11.8255 0.5504c0,0 -4.3682,17.8091 -4.342,17.8764 0.0263,0.0673 4.1505,11.7072 4.342,11.1757l0.8932 -2.4775 3.4304 -9.515 -4.3236 -17.0596z'/>
  <polygon stroke='black' fill='white' points='22.155,0.9498 22.3535,32.6976 21.5437,31.5864 17.3931,30.083 11.8255,29.6025 '/>
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