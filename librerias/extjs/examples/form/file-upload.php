<?php
    $name = $_POST['name'];
if ($_FILES['photo-path']["error"] > 0){
	sleep(1);
    echo '{success:true, file:'.json_encode($_FILES['photo-path']['name']).'}';
	//echo "Error: " . $_FILES['photo-path']['error'] . "<br>";
  }else{
		$extension = end(explode('.', $_FILES['photo-path']['name']));
		$nombre_nuevo = $name.".".$extension;
		$ruta = "subidas/" . $nombre_nuevo;
		
		$permitidos = array("application/pdf");
		$limite_kb = 10000;
		if (in_array($_FILES['photo-path']['type'], $permitidos) && $_FILES['photo-path']['size'] <= $limite_kb * 1024){
			if (!file_exists($ruta)){
				echo "Nombre: " . $_FILES['photo-path']['name'] . "<br>";
				echo "Nuevo Nombre: " . $nombre_nuevo . "<br>";
				echo "Tipo: " . $_FILES['photo-path']['type'] . "<br>";
				echo "Tamano: " . ($_FILES["archivo"]["size"] / 1024) . " kB<br>";
				echo "Carpeta temporal: " . ($_FILES['photo-path']['tmp_name']) . " .<br>";
				$resultado = move_uploaded_file($_FILES['photo-path']['tmp_name'],$ruta);
				if($resultado){
					sleep(1);
					echo '{success:true, file:"este archivo existe.........."}';
				}else{
					sleep(1);
					echo '{success:true, file:"ocurrio un error al mover el archivo."}';
				}
			}else{
				sleep(1);
				echo '{success:true, file:"este archivo existe"}';
			}
		}else{
			sleep(1);
			echo '{success:true, file:"archivo no permitido $extension, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes"}';
			//echo "archivo no permitido $extension, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes";
		}
  }