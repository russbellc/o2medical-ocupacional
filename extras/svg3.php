<?php

$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

$pieza1 = 'M6.0921 34.2695l-4.5756 -3.6177c-0.4087,5.7945 0.1748,8.8098 1.3571,13.1976l3.5941 -3.0721 -0.3756 -6.5079 0 0.0001z';
$pieza2 = 'M18.0697 33.3021l3.4429 -2.426c0.9461,3.8646 0.8457,7.9051 -1.7362,12.365l-2.2028 -3.0614 0.4961 -6.8776z';
$pieza3 = 'M5.3776 33.7014l-3.861 -3.0496c4.8201,-2.6929 10.1669,-4.8827 19.5484,-0.6602l0.4476 0.8846 -3.4429 2.426 -12.6921 0.3992z';
$pieza4 = 'M17.5736 40.1797l2.2028 3.0614c-3.0567,4.1043 -12.5598,7.4374 -16.9028,0.6083l3.5941 -3.0721 11.1059 -0.5976z';
$pieza5 = 'M18.0697 33.3021c-5.0823,-1.2012 -7.5331,-0.6791 -12.6921,0.3992l1.0902 7.076c4.3476,0.6 6.7949,0.3248 11.1059,-0.5976 0.1654,-2.2925 0.3307,-4.585 0.4961,-6.8776l-0.0001 0z';
header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>
 <g id='Capa_x0020_1_0'>
  <metadata id='CorelCorpID_0Corel-Layer'/>
  <polygon stroke='black' fill='white' points='1.4329,31.0885 0.7422,0.5047 11.4141,27.7873 6.5663,28.3436 '/>
  <line stroke='black' fill='white' x1='20.5433' y1='1.2557' x2='19.817' y2= '3.1438' />
  <line stroke='black' fill='white' x1='19.6193' y1='3.6573' x2='19.0489' y2= '5.1049' />
  <line stroke='black' fill='white' x1='18.395' y1='6.8887' x2='17.7414' y2= '8.4848' />
  <line stroke='black' fill='white' x1='16.994' y1='10.3623' x2='16.493' y2= '11.7831' />
  <line stroke='black' fill='white' x1='15.704' y1='13.8575' x2='15.1435' y2= '15.2659' />
  <line stroke='black' fill='white' x1='14.659' y1='16.5586' x2='14.0052' y2= '18.2485' />
  <line stroke='black' fill='white' x1='13.6316' y1='19.9383' x2='12.8844' y2= '21.4407' />
  <line stroke='black' fill='white' x1='12.3239' y1='22.9426' x2='11.7636' y2= '24.5385' />
  <line stroke='black' fill='white' x1='21.0102' y1='3.0395' x2='21.0102' y2= '4.26' />
  <line stroke='black' fill='white' x1='20.9169' y1='6.701' x2='20.9169' y2= '8.2969' />
  <line stroke='black' fill='white' x1='21.1971' y1='10.3623' x2='21.1971' y2= '11.6768' />
  <line stroke='black' fill='white' x1='21.0102' y1='13.5545' x2='21.1038' y2= '15.4319' />
  <line stroke='black' fill='white' x1='20.9169' y1='17.5913' x2='21.0102' y2= '19.375' />
  <line stroke='black' fill='white' x1='21.1038' y1='21.1588' x2='21.1038' y2= '22.7547' />
  <line stroke='black' fill='white' x1='21.1971' y1='24.6325' x2='21.1971' y2= '25.9469' />
  <line stroke='black' fill='white' x1='20.9971' y1='27.1672' x2='20.9971' y2= '28.3878' />  
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