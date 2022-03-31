<?php

//$conexion = new mysqli('localhost', 'root', 'teraware', 'demo', 3306);
//if (mysqli_connect_errno()) {
//    printf("La conexión con el servidor de base de datos falló: %s\n", mysqli_connect_error());
//    exit();
//}
//$consulta = "SELECT concat(paterno,' ', materno, ' ' , nombre) AS alumno, fechanac, sexo, carrera FROM alumno INNER JOIN carrera ON alumno.idcarrera = carrera.idcarrera ORDER BY carrera, nombre";
//$resultado = $conexion->query($consulta);

$model = new model();
$inicio = $_REQUEST['fini'];
$final = $_REQUEST['ffinal'];
$empresas = $_REQUEST['empresa'];

$diario = $model->report_diario("$inicio", "$final", "$empresas");


//$resultado->num_rows
if ($diario->total > 0) {

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

    $tituloReporte = "Reporte: " . $diario->total . " Pacientes   Fecha: " . $inicio . " al " . $final;
    $titulosColumnas = array('N°', 'H.C', 'EMPRESA', 'FECHA', 'NOMBRES', 'SX', 'FICHA', 'RUTA');

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
    foreach ($diario->data as $i => $row) {
        $n = 4 + $i;
        $nro = $i + 1;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $n, $nro)
                ->setCellValue('B' . $n, $row->NRO)
                ->setCellValue('C' . $n, $row->EMPRESA)
                ->setCellValue('D' . $n, $row->FECHA)
                ->setCellValue('E' . $n, $row->NOMBRES)
                ->setCellValue('F' . $n, $row->SEXO)
                ->setCellValue('G' . $n, $row->FICHA)
                ->setCellValue('H' . $n, utf8_encode($row->RUTA));
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
//    }

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