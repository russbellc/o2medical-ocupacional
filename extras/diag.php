<?php

//$color1 = $_REQUEST['color1'];
//$color2 = $_REQUEST['color2'];
//$color3 = $_REQUEST['color3'];
//$color4 = $_REQUEST['color4'];
//$color5 = $_REQUEST['color5'];
$diagnos = $_REQUEST['diagnos'];
$color1 = 'white';
$color2 = 'white';
$color3 = 'white';
$color4 = 'white';
$color5 = 'white';

$corona = '';
$placa = '';
$barra1 = 'red';
$barra11 = 0;
$barra2 = 'red';
$barra22 = 0;
$barra3 = 'red';
$barra33 = 0;
if ($diagnos == 9) {
    $corona = 'blue';
} else if ($diagnos == 10) {
    $corona = 'red';
} else if ($diagnos == 7) {
    $placa = 'red';
} else if ($diagnos == 3) {
    $barra1 = 'blue';
    $barra11 = 1;
    $barra2 = 'blue';
    $barra22 = 1;
} else if ($diagnos == 4) {
    $barra1 = 'red';
    $barra11 = 1;
    $barra2 = 'red';
    $barra22 = 1;
} else if ($diagnos == 5) {
    $barra1 = 'red';
    $barra11 = 1;
} else if ($diagnos == 8) {
    $barra3 = 'blue';
    $barra33 = 1;
}

header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' xml:space='preserve' style='shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd'
xmlns:xlink='http://www.w3.org/1999/xlink'>
<!-- 'O' corona -->
<path fill='none' stroke='$corona' stroke-width='2.5' d='M15.878,1c8.247,0,14.921,6.673,14.921,14.92 c0,8.217-6.675,14.921-14.921,14.921c-8.215,0-14.919-6.706-14.919-14.921C0.958,7.673,7.662,1,15.878,1z'/>
<!-- 'C' placa -->
<path fill='none' stroke='$placa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' d='M1.713,11.26 c1.953-5.947,7.556-10.26,14.165-10.26c6.611,0,12.215,4.313,14.166,10.26'/>
<!-- 'X' extraer, curacion y fractura-->
<line  fill='none' stroke='$barra1' stroke-opacity='$barra11' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' x1='1.655' y1='30.143' x2='30.46' y2='1.341'/>
<line  fill='none' stroke='$barra2' stroke-opacity='$barra22' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' x1='30.284' y1='30.305' x2='1.48' y2='1.5'/>
<!-- '-' PUENTE -->
<line  fill='none' stroke='$barra3' stroke-opacity='$barra33' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' x1='31.786' y1='15.708' x2='0' y2='15.707'/>
</svg>";

echo $svg;
// It's that easy!