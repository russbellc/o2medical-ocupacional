<?php

//require 'loader.php';
// if ($_FILES['photo-path']["error"] > 0) {
//     sleep(1);
//     echo '{success:true, file:' . json_encode($_FILES['photo-path']['name']) . '}';
// } else {
$name = $_POST['add_file'];
$extension = end(explode('.', $_FILES['photo-path']['name']));
$nombre_nuevo = $name . "." . $extension;
$ruta = "escaner/" . $nombre_nuevo;
$permitidos = array("application/pdf");
$limite_kb = 50000;
if (in_array($_FILES['photo-path']['type'], $permitidos) && $_FILES['photo-path']['size'] <= $limite_kb * 1024) {
    $resultado = move_uploaded_file($_FILES['photo-path']['tmp_name'], $ruta);
    sleep(1);
    echo '{success:true, file:"archivo subido"}';
} else {
    sleep(1);
    echo '{success:true, file:"archivo no permitido $extension, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes"}';
}
// }