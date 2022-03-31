<?php

$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

$pieza1 = 'M5.8146 7.6075l-3.8362 -3.3024c-0.7795,2.4 -1.6335,11.4201 -1.4457,13.337l3.9626 -3.1311 1.3193 -6.9035z';
$pieza2 = 'M17.2594 14.9126l3.5008 2.4661c1.2969,-2.8004 0.3827,-10.3488 -1.7835,-12.4618l-2.2158 3.0791 0.4984 6.9165 0.0001 0.0001z';
$pieza3 = 'M16.761 7.9961l2.2158 -3.0791c-4.3524,-5.6776 -14.5205,-5.9622 -16.9984,-0.6118l3.7925 3.1795 10.9902 0.5114 -0.0001 0z';
$pieza4 = 'M4.4953 14.511l-3.8811 3.0673c2.9327,1.9051 8.4744,3.3 10.6783,2.9988 3.0862,0.0591 6.424,-1.826 8.9799,-2.335l0.476 -0.9496 -3.489 -2.3799 -12.7642 -0.4016 0.0001 0z';
$pieza5 = 'M17.2594 14.9126c-5.1106,1.2083 -7.5756,0.6827 -12.7642,-0.4016l1.0972 -7.1161c4.3724,-0.6035 6.8327,-0.3272 11.1685,0.6012 0.1665,2.3055 0.3319,4.611 0.4984,6.9165l0.0001 0z';
header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>
 <g id='Capa_x0020_1_0'>
  <metadata id='CorelCorpID_0Corel-Layer'/>
  <path stroke='black' fill='white' d='M10.5188 50.3425c-3.3626,-10.2588 -6.7253,-20.5177 -10.0878,-30.7765l0 -2.2722c1.3219,0.4522 2.2891,1.2537 3.6082,1.7048 2.7589,0.9437 6.6255,1.899 9.552,1.2581l7.2215 -2.3433 -10.2939 32.4289 0 0.0002z'/>
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