<?php

if ($_FILES['photo-path']["error"] > 0) {
    sleep(1);
    echo '{success:true, file:' . json_encode($_FILES['photo-path']['name']) . '}';
} else {
    $name = $_POST['name'];
    $extension = end(explode('.', $_FILES['photo-path']['name']));
    $nombre_nuevo = $name . "." . $extension;
    $ruta = "espirometria/" . $nombre_nuevo;
    $permitidos = array("application/pdf");
    $limite_kb = 50000;
    if (in_array($_FILES['photo-path']['type'], $permitidos) && $_FILES['photo-path']['size'] <= $limite_kb * 1024) {
        if (!file_exists($ruta)) {
            $resultado = move_uploaded_file($_FILES['photo-path']['tmp_name'], $ruta);
            if ($resultado) {
                sleep(1);
                echo '{success:true, file:"El Archivo fue Enviado Correctamente"}';
            } else {
                sleep(1);
                echo '{success:false, file:"ocurrio un error al mover el archivo."}';
            }
        } else {
            sleep(1);
            echo '{success:false, file:"este archivo existe"}';
        }
    } else {
        sleep(1);
        echo '{success:false, file:"archivo no permitido $extension, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes"}';
    }
}