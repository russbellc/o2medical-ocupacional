<?php

//require 'loader.php';
if ($_FILES['photo-path']["error"] > 0) {
    sleep(1);
    echo '{success:true, file:' . json_encode($_FILES['photo-path']['name']) . '}';
} else {
    $name = $_POST['name'];
    $extension = end(explode('.', $_FILES['photo-path']['name']));
    $nombre_nuevo = $name . "." . $extension;
    $ruta = "escaner/" . $nombre_nuevo;
    $permitidos = array("application/pdf");
    $limite_kb = 50000;
    if (in_array($_FILES['photo-path']['type'], $permitidos) && $_FILES['photo-path']['size'] <= $limite_kb * 1024) {
        if (!file_exists($ruta)) {
            $resultado = move_uploaded_file($_FILES['photo-path']['tmp_name'], $ruta);
            if ($resultado) {
                sleep(1);
                echo '{success:true, file:"El Archivo fue Enviado Correctamente"}';
                $server = "localhost";
                $username = "root";
                $password = "teraware";
                $db = 'db_mecsa';
                $con = mysql_connect($server, $username, $password)or die("no se ha podido establecer la conexion");
                mysql_select_db($db, $con)or die("la base de datos no existe");
                mysql_query("INSERT INTO escanea_pdf VALUES (null ,'$name', '1')", $con);
            } else {
                sleep(1);
                echo '{success:true, file:"ocurrio un error al mover el archivo."}';
            }
        } else {
            sleep(1);
            echo '{success:true, file:"este archivo existe"}';
        }
    } else {
        sleep(1);
        echo '{success:true, file:"archivo no permitido $extension, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes"}';
    }
}