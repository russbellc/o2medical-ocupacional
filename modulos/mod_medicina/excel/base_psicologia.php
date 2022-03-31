<?php

//$conexion = new mysqli('localhost', 'root', 'teraware', 'demo', 3306);
//if (mysqli_connect_errno()) {
//    printf("La conexión con el servidor de base de datos falló: %s\n", mysqli_connect_error());
//    exit();
//}
//$consulta = "SELECT concat(paterno,' ', materno, ' ' , nombre) AS alumno, fechanac, sexo, carrera FROM alumno INNER JOIN carrera ON alumno.idcarrera = carrera.idcarrera ORDER BY carrera, nombre";
//$resultado = $conexion->query($consulta);

$model = new model();

$base_psico = $model->base_psico($_REQUEST['hruta']);


//$resultado->num_rows
if ($base_psico->total > 0) {

//    print_r('No hay resultados para mostrar hola '.$diario->total);

    date_default_timezone_set('America/Mexico_City');

    if (PHP_SAPI == 'cli')
        die('Este archivo solo se puede ver desde un navegador web');

    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("Code Live") //Autor
            ->setLastModifiedBy("Code Live") //Ultimo usuario que lo modificó
            ->setTitle("Reporte")
            ->setSubject("Reporte")
            ->setDescription("Reporte")
            ->setKeywords("reporte")
            ->setCategory("Reporte excel");

    $tituloReporte = "Reporte: Pacientes   ";
    $titulosColumnas = array('N°', 'FECHA DE EXAMEN INICIAL', 'APELLIDOS Y NOMBRES', 'NACIONALIDAD', 'DNI/PASAPORTE', 'SEXO', 'FECHA DE NACIMIENTO', 'LUGAR DE NACIMIENTO', 'PROCEDENCIA ACTUAL (DEPARTAMENTO)', 'TELEFONO', 'EMPRESA', 'PUESTO LABORAL', 'AREA', 'TIPO DE EMO', 'CLINICA EVALUADORA', 'CONDICION DIAGNOSTICA', 'SE APLICÓ REEVALUACION', 'RESULTADOS DE REEVALUACION', 'MANEJO', 'ALTURA', 'CONFINADOS', 'BRIGADISTA', 'RESULTADOS CAPACIDAD INTELECTUAL', 'RESULTADOS PERSONALIDAD PAUL GRIEGER', 'RESULTADOS PERSONALIDAD HTP', 'RESULTADOS PSICOSENSOMETRICO', 'RESULTADOS ATENCION-CONCENTRACION', 'RESULTADOS BC-4', 'RESULTADOS ORIENTACION ESPACIAL', 'DESCARTE DE ACROFOBIA', 'DESCARTE CLAUSTROFOBIA', 'RESULTADOS TEST BARON SOLO BRIGADISTA ', 'RESULTADOS SALAMANCA SOLO BRIGADISTA', 'INTERCONSULTA', 'COMENTARIO/OBSERVACION');

    $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('A1:H1');

    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $tituloReporte)
            ->setCellValue('A3', $titulosColumnas[0])
            ->setCellValue('B3', $titulosColumnas[1])
            ->setCellValue('C3', $titulosColumnas[2])
            ->setCellValue('D3', $titulosColumnas[3])
            ->setCellValue('E3', $titulosColumnas[4])
            ->setCellValue('F3', $titulosColumnas[5])
            ->setCellValue('G3', $titulosColumnas[6])
            ->setCellValue('H3', $titulosColumnas[7]);

    //Se agregan los datos de los alumnos
//    $n = 4 + $i;
    foreach ($base_psico->data as $i => $row) {
        $n = 4 + $i;
        $nro = $i + 1;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $n, $nro)
                ->setCellValue('B' . $n, $row->adm)
                ->setCellValue('C' . $n, $row->fech_reg)
                ->setCellValue('D' . $n, $row->nom_ap)
                ->setCellValue('E' . $n, $row->documento)
                ->setCellValue('F' . $n, $row->pac_ndoc)
                ->setCellValue('G' . $n, $row->sexo)
                ->setCellValue('H' . $n, $row->fech_naci);
        $i++;
    }

//    $i = 4;
//    while ($row = $diario->fetch_array()) {
//        $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A' . $i, $row['NRO'])
//                ->setCellValue('B' . $i, $row['NRO'])
//                ->setCellValue('C' . $i, $row['EMPRESA'])
//                ->setCellValue('D' . $i, $row['FECHA'])
//                ->setCellValue('E' . $i, $row['NOMBRES'])
//                ->setCellValue('F' . $i, $row['SEXO'])
//                ->setCellValue('G' . $i, $row['FICHA'])
//                ->setCellValue('H' . $i,  utf8_encode($row['RUTA']));
//        $i++;
//    }A

    $estiloTituloReporte = array(
        'font' => array(
            'name' => 'Verdana',
            'bold' => true,
            'italic' => false,
            'strike' => false,
            'size' => 16,
            'color' => array(
                'rgb' => 'FFFFFF'
            )
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('argb' => 'FF000000')
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            )
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        )
    );

    $estiloTituloColumnas = array(
        'font' => array(
            'name' => 'Arial',
            'bold' => true,
            'color' => array(
                'rgb' => 'FFFFFF'
            )
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
            'rotation' => 90,
            'startcolor' => array(
                'rgb' => '608dff'
            ),
            'endcolor' => array(
                'argb' => 'FF2d54ae'
            )
        ),
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            )
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'wrap' => TRUE
    ));

    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
            array(
                'font' => array(
                    'name' => 'Arial',
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFFFFF')
                ),
                'borders' => array(
//                    'left' => array(
//                        'style' => PHPExcel_Style_Border::BORDER_THIN,
//                        'color' => array(
//                            'rgb' => '2e4b9e'
//                        )
//                    )
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => '000000'
                        )
                    )
                )
    ));

    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:H" . ($i - 1));

    for ($i = 'A'; $i <= 'H'; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)
                ->getColumnDimension($i)->setAutoSize(TRUE);
    }

    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Reporte');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    $objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //$inicio . ' AL ' . $final
    header('Content-Disposition: attachment;filename="Reporte ' . $inicio . '_al_' . $final . '.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
} else {
    print_r('No hay resultados para mostrar');
}
?>