<?php

$color1 = $_REQUEST['color1'];
$color2 = $_REQUEST['color2'];
$color3 = $_REQUEST['color3'];
$color4 = $_REQUEST['color4'];
$color5 = $_REQUEST['color5'];

header('Content-Type: text/html; charset=utf-8');
// Time to render some SVG:
$svg = "
<svg xmlns='http://www.w3.org/2000/svg' version='1.1' width='25px' height='55px'>

<path id='1' fill='$color1' stroke='#000000' stroke-width='0.8' d='M15.878,9.238c3.694,0,6.685,2.991,6.685,6.683c0,3.663-2.991,6.654-6.685,6.654c-3.662,0-6.653-2.993-6.653-6.654 C9.224,12.228,12.215,9.238,15.878,9.238z'/>
<path id='2' fill='$color2' stroke='#000000' stroke-width='0.8' stroke-linecap='round' stroke-linejoin='round' d='M25.279,6.52c-5.188-5.156-13.58-5.156-18.77,0l0,0l4.668,4.669l0,0c2.595-2.594,6.807-2.594,9.432,0L25.279,6.52z'/>
<path id='3' fill='$color3' stroke='#000000' stroke-width='0.8' stroke-linecap='round' stroke-linejoin='round' d='M6.509,25.292c-5.19-5.189-5.19-13.583,0-18.772l0,0l4.668,4.669l0,0c-2.623,2.625-2.623,6.836,0,9.431L6.509,25.292z'/>
<path id='4' fill='$color4' stroke='#000000' stroke-width='0.8' stroke-linecap='round' stroke-linejoin='round' d='M25.279,6.52c5.159,5.189,5.159,13.583,0,18.772l0,0l-4.668-4.67l0,0c2.594-2.595,2.594-6.806,0-9.431L25.279,6.52z'/>
<path id='5' fill='$color5' stroke='#000000' stroke-width='0.8' stroke-linecap='round' stroke-linejoin='round' d='M6.509,25.292c5.188,5.188,13.582,5.188,18.769,0l0,0l-4.669-4.67l0,0c-2.624,2.625-6.836,2.625-9.431,0L6.509,25.292z'/>

</svg>";

echo $svg;
// It's that easy!