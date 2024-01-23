<?php

class MYPDF extends TCPDF {

    public $user;

    public function Header() {
        
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, $this->user->sed_desc, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Pagina - ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(
        PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$model = new model();
$pdf->user = $model->user;

// set document information
// Información referente al PDF
$pdf->SetCreator("");
$pdf->SetAuthor('');
$pdf->SetTitle('');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// Contenido de la cabecera
// Fuente de la cabecera y el pie de página
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Márgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer la fuente
$pdf->SetFont('helvetica', 'B', 16);

// Añadir página
$pdf->AddPage();
$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');

$datos_report = $model->datos_report($_REQUEST['adm']);

$pdf->SetFont('helvetica', 'BU', 15);
$pdf->Cell(0, 0, 'INFORME ODONTOLOGICO', 0, 1, 'C');
$pdf->Ln(5);
$f = 0;
$h = 5;
$w = 40;
$w2 = 50;
$w3 = 8;
$texh = 8;
$texh2 = 7;
$ali = 'L';


////$pdf->Cell($w3,$h, "DATOS PERSONALES",1,,'C',1);
$pdf->SetFont('helvetica', 'B', $texh);
//
$pdf->SetFillColor(194, 217, 241);
$pdf->Cell($w2, $h, 'DATOS GENERALES', 0, 1, 'L', 1);
$pdf->Cell(0, 3 * $h, '', 1);
$pdf->ln(0);


$pdf->Cell($w - 25, $h, 'NONBRES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(120, $h, ': ' . $datos_report->data[0]->NOMBRES, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(10, $h, 'EDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(15, $h, ': ' . $datos_report->data[0]->edad . ' Años', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(5, $h, 'DNI ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(20, $h, ': ' . $datos_report->data[0]->pac_ndoc, $f, 1);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w - 25, $h, 'EMPRESA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(115, $h, ': ' . $datos_report->data [0]->EMPRESA, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(22, $h, 'TIPO DE FICHA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $datos_report->data[0]->FICHA, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w - 24, $h, 'ACTIVIDAD', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 35, $h, ': ' . $datos_report->data[0]->adm_act, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(10, $h, 'SEXO ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(21, $h, ': ' . $datos_report->data [0]->SEXO, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(31, $h, 'FECHA DE REGISTRO ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $datos_report->data [0]->FECHA, $f, 1);


$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(180, $h, 'ODONTOGRAMA', 0, 1, 'C', 1);
$pdf->Ln(6);







$diente_1 = $model->diente_1();
$diente_2 = $model->diente_2();
$diente_3 = $model->diente_3();
$diente_4 = $model->diente_4();
$diente_pieza_desc1 = $model->diente_pieza_desc1($_REQUEST['adm']);
$diente_pieza_desc2 = $model->diente_pieza_desc2($_REQUEST['adm']);
$diente_pieza_desc3 = $model->diente_pieza_desc3($_REQUEST['adm']);
$diente_pieza_desc4 = $model->diente_pieza_desc4($_REQUEST['adm']);
$diente_txt = $model->diente_txt($_REQUEST['adm']);

$pdf->ImageSVG("images/div1.svg", 103.7, 15.5, 8, 128);
$pdf->ImageSVG("images/div2.svg", 21, 105, 170, 0);

$p = 8;
foreach ($diente_1->data as $i => $row) {
    $fondo1 = 'white';
    $fondo2 = 'white';
    $fondo3 = 'white';
    $fondo4 = 'white';
    $fondo5 = 'white';
    $diagnos = '';
    foreach ($diente_pieza_desc1->data as $i => $val) {
        if ($row->dient_nro == $val->gramap_diente) {
            if ($val->gramap_pieza == 1) {
                $fondo1 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 2) {
                $fondo2 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 3) {
                $fondo3 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 4) {
                $fondo4 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 5) {
                $fondo5 = $val->gramap_fondo;
            }
        }
    }
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $diagnos = $r->gramad_diag_raiz;
        }
    }
    $p = $p + 10.8;
    
    
    //$link="http://app.cm-optima.com/extras";
    
    $pdf->ImageSVG("http://app.cm-optima.com/extras/svg.php?color1=$fondo1&color2=$fondo2&color3=$fondo3&color4=$fondo4&color5=$fondo5", $p, 78, 30, 30);
    $pdf->ImageSVG("http://app.cm-optima.com/extras/diag.php?diagnos=$diagnos", $p, 78, 30, 30);
}


$p = 40.4;
foreach ($diente_2->data as $i => $row) {
    $fondo1 = 'white';
    $fondo2 = 'white';
    $fondo3 = 'white';
    $fondo4 = 'white';
    $fondo5 = 'white';
    $diagnos = '';
    foreach ($diente_pieza_desc2->data as $i => $val) {
        if ($row->dient_nro == $val->gramap_diente) {
            if ($val->gramap_pieza == 1) {
                $fondo1 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 2) {
                $fondo2 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 3) {
                $fondo3 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 4) {
                $fondo4 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 5) {
                $fondo5 = $val->gramap_fondo;
            }
        }
    }
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $diagnos = $r->gramad_diag_raiz;
        }
    }
    $p = $p + 10.8;
    $pdf->ImageSVG("http://app.cm-optima.com/extras/svg.php?color1=$fondo1&color2=$fondo2&color3=$fondo3&color4=$fondo4&color5=$fondo5", $p, 89, 30, 30);
    $pdf->ImageSVG("http://app.cm-optima.com/extras/diag.php?diagnos=$diagnos", $p, 89, 30, 30);
}





$p = 40.4;
foreach ($diente_3->data as $i => $row) {
    $fondo1 = 'white';
    $fondo2 = 'white';
    $fondo3 = 'white';
    $fondo4 = 'white';
    $fondo5 = 'white';
    $diagnos = '';
    foreach ($diente_pieza_desc3->data as $i => $val) {
        if ($row->dient_nro == $val->gramap_diente) {
            if ($val->gramap_pieza == 1) {
                $fondo1 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 2) {
                $fondo2 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 3) {
                $fondo3 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 4) {
                $fondo4 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 5) {
                $fondo5 = $val->gramap_fondo;
            }
        }
    }
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $diagnos = $r->gramad_diag_raiz;
        }
    }
    $p = $p + 10.8;
    $pdf->ImageSVG("http://app.cm-optima.com/extras/svg.php?color1=$fondo1&color2=$fondo2&color3=$fondo3&color4=$fondo4&color5=$fondo5", $p, 117, 30, 30);
    $pdf->ImageSVG("http://app.cm-optima.com/extras/diag.php?diagnos=$diagnos", $p, 117, 30, 30);
}




$p = 8;
foreach ($diente_4->data as $i => $row) {
    $fondo1 = 'white';
    $fondo2 = 'white';
    $fondo3 = 'white';
    $fondo4 = 'white';
    $fondo5 = 'white';
    $diagnos = '';
    foreach ($diente_pieza_desc4->data as $i => $val) {
        if ($row->dient_nro == $val->gramap_diente) {
            if ($val->gramap_pieza == 1) {
                $fondo1 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 2) {
                $fondo2 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 3) {
                $fondo3 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 4) {
                $fondo4 = $val->gramap_fondo;
            }
            if ($val->gramap_pieza == 5) {
                $fondo5 = $val->gramap_fondo;
            }
        }
    }
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $diagnos = $r->gramad_diag_raiz;
        }
    }
    $p = $p + 10.8;
    $pdf->ImageSVG("http://app.cm-optima.com/extras/svg.php?color1=$fondo1&color2=$fondo2&color3=$fondo3&color4=$fondo4&color5=$fondo5", $p, 128, 30, 30);
    $pdf->ImageSVG("http://app.cm-optima.com/extras/diag.php?diagnos=$diagnos", $p, 128, 30, 30);
}







$pdf->SetFont('helvetica', 'B', $texh2);

$pdf->SetX(17.9);
$pdf->SetTextColor(0, 0, 255);
foreach ($diente_1->data as $i => $row) {
    $text = '';
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $text = $r->gramad_diag_text;
        }
    }
    $pdf->Cell(10.8, 4, $text, 1, 0, 'C', 0);
}


$pdf->ln(4);
$pdf->SetX(50.3);
foreach ($diente_2->data as $i => $row) {
    $text = '';
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $text = $r->gramad_diag_text;
        }
    }
    $pdf->Cell(10.8, 4, $text, 1, 0, 'C', 0);
}



$pdf->SetTextColor(0, 0, 0);
$pdf->ln(6);
$pdf->SetX(17.9);
foreach ($diente_1->data as $i => $row) {
    $pdf->Cell(10.8, 4, $row->dient_nro, 0, 0, 'C', 0);
}

$pdf->ln(28);
$pdf->SetX(50.3);
foreach ($diente_2->data as $i => $row) {
    $pdf->Cell(10.8, 4, $row->dient_nro, 0, 0, 'C', 0);
}

$pdf->ln(10);
$pdf->SetX(50.3);
foreach ($diente_3->data as $i => $row) {
    $pdf->Cell(10.8, 4, $row->dient_nro, 0, 0, 'C', 0);
}

$pdf->ln(28);
$pdf->SetX(17.9);
foreach ($diente_4->data as $i => $row) {
    $pdf->Cell(10.8, 4, $row->dient_nro, 0, 0, 'C', 0);
}


$pdf->SetTextColor(0, 0, 255);
$pdf->ln(6);
$pdf->SetX(50.3);
foreach ($diente_3->data as $i => $row) {
    $text = '';
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $text = $r->gramad_diag_text;
        }
    }
    $pdf->Cell(10.8, 4, $text, 1, 0, 'C', 0);
}

$pdf->ln(4);
$pdf->SetX(17.9);
foreach ($diente_4->data as $i => $row) {
    $text = '';
    foreach ($diente_txt->data as $i => $r) {
        if ($row->dient_nro == $r->gramad_diente) {
            $text = $r->gramad_diag_text;
        }
    }
    $pdf->Cell(10.8, 4, $text, 1, 0, 'C', 0);
}

$pdf->SetTextColor(0, 0, 0);





$grama_pato = $model->grama_pato($_REQUEST['adm']);
$caries = $model->caries($_REQUEST['adm']);
$extraer = $model->extraer($_REQUEST['adm']);
$pieza_caries = $model->pieza_caries($_REQUEST['adm']);
$pieza_extraer = $model->pieza_extraer($_REQUEST['adm']);
$recomendaciones = $model->recomendaciones($_REQUEST['adm']);
$tratamiento = $model->tratamiento($_REQUEST['adm']);
$piezae = '';
$piezaa = '';
foreach ($pieza_extraer->data as $x => $rep) {
    if ($rep->gramad_diag_raiz == 4) {
        if ($x < $rep) {
            $piezae = $rep->gramad_diente . '.' . $piezae;
        } else {
            $piezae = $rep->gramad_diente . ';' . $piezae;
        }
    } else if ($rep->gramad_diag_raiz == 3) {
        if ($x < $rep) {
            $piezaa = $rep->gramad_diente . '.' . $piezaa;
        } else {
            $piezaa = $rep->gramad_diente . ';' . $piezaa;
        }
    }
}
$piezac = '';
foreach ($pieza_caries->data as $i => $r) {
    if ($i < $r) {
        $piezac = $r->gramad_diente . '.' . $piezac;
    } else {
        $piezac = $r->gramad_diente . ';' . $piezac;
    }
}


$pdf->Ln(7);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(85, $h, 'OTRAS PATOLOGIAS', 0, 0, 'C', 1);
$pdf->Cell(5, $h, '', 0, 0, 'C', 0);
$pdf->Cell(90, $h, 'DIAGNOSTICOS', 0, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 6.5);
$pdf->Ln(1);

$total_pato = $grama_pato->total;
$total_diag = 4;

if ($total_diag > $total_pato) {
    $i = 1;
    for ($index = 0; $index < $total_diag; $index++) {
        if ($index < $total_pato) {
            $pdf->Cell(85, 4, $i++ . '.- (Diente N° ' . $grama_pato->data[$index]->gpato_diente . '): ' . $grama_pato->data[$index]->gpato_desc, 'B', 0, 'L', 0);
        } else {
            $pdf->Cell(85, 4, '', 0, 0, 'L', 0);
        }
        $pdf->Cell(5, $h, '', 0, 0, 'C', 0);
        if ($index == 0) {
            $pdf->SetFont('helvetica', 'B', 6.5);
            $pdf->Cell(35, 4, 'N° TOTAL DE CARIES:', 1, 0, 'C', 0);
            $pdf->SetFont('helvetica', '', 6.5);
            $pdf->Cell(10, 4, $caries->data[$index]->caries, 1, 0, 'C', 0);
            $pdf->SetFont('helvetica', 'B', 6.5);
            $pdf->Cell(35, 4, 'N° TOTAL PARA EXTRAER:', 1, 0, 'C', 0);
            $pdf->SetFont('helvetica', '', 6.5);
            $pdf->Cell(10, 4, $extraer->data[$index]->extraer, 1, 1, 'C', 0);
        } else if ($index == 1) {
            $pdf->SetFont('helvetica', 'B', 6.5);
            $pdf->Cell(30, 4, 'PIEZAS CON CARIES :', 1, 0, 'L', 0);
            $pdf->SetFont('helvetica', '', 6.5);
            $pdf->Cell(60, 4, $piezac, 1, 1, 'C', 0);
        } else if ($index == 2) {
            $pdf->SetFont('helvetica', 'B', 6.5);
            $pdf->Cell(30, 4, 'PIEZAS PARA EXTRAER :', 1, 0, 'L', 0);
            $pdf->SetFont('helvetica', '', 6.5);
            $pdf->Cell(60, 4, $piezae, 1, 1, 'C', 0);
        } else if ($index == 3) {
            $pdf->SetFont('helvetica', 'B', 6.5);
            $pdf->Cell(30, 4, 'PIEZAS AUSENTES :', 1, 0, 'L', 0);
            $pdf->SetFont('helvetica', '', 6.5);
            $pdf->Cell(60, 4, $piezaa, 1, 1, 'C', 0);
        }
    }
} else if ($total_pato >= $total_diag) {
    $i = 1;
    for ($index = 0; $index < $total_pato; $index++) {

        $pdf->Cell(85, 4, $i++ . '.- (Diente N° ' . $grama_pato->data[$index]->gpato_diente . '): ' . $grama_pato->data[$index]->gpato_desc, 'B', 0, 'L', 0);

        $pdf->Cell(5, $h, '', 0, 0, 'C', 0);
        if ($index < $total_diag) {
            if ($index == 0) {
                $pdf->SetFont('helvetica', 'B', 6.5);
                $pdf->Cell(35, 4, 'N° TOTAL DE CARIES:', 1, 0, 'C', 0);
                $pdf->SetFont('helvetica', '', 6.5);
                $pdf->Cell(10, 4, $caries->data[$index]->caries, 1, 0, 'C', 0);
                $pdf->SetFont('helvetica', 'B', 6.5);
                $pdf->Cell(35, 4, 'N° TOTAL PARA EXTRAER:', 1, 0, 'C', 0);
                $pdf->SetFont('helvetica', '', 6.5);
                $pdf->Cell(10, 4, $extraer->data[$index]->extraer, 1, 1, 'C', 0);
            } else if ($index == 1) {
                $pdf->SetFont('helvetica', 'B', 6.5);
                $pdf->Cell(30, 4, 'PIEZAS CON CARIES :', 1, 0, 'L', 0);
                $pdf->SetFont('helvetica', '', 6.5);
                $pdf->Cell(60, 4, $piezac, 1, 1, 'C', 0);
            } else if ($index == 2) {
                $pdf->SetFont('helvetica', 'B', 6.5);
                $pdf->Cell(30, 4, 'PIEZAS PARA EXTRAER :', 1, 0, 'L', 0);
                $pdf->SetFont('helvetica', '', 6.5);
                $pdf->Cell(60, 4, $piezae, 1, 1, 'C', 0);
            } else if ($index == 3) {
                $pdf->SetFont('helvetica', 'B', 6.5);
                $pdf->Cell(30, 4, 'PIEZAS AUSENTES :', 1, 0, 'L', 0);
                $pdf->SetFont('helvetica', '', 6.5);
                $pdf->Cell(60, 4, $piezaa, 1, 1, 'C', 0);
            }
        } else {
            $pdf->Cell(90, 4, '', 0, 1, 'L', 0);
        }
    }
}


$pdf->Ln(3);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(180, $h, 'RECOMENDACIÓNES Y OBSERVACIÓNES', 0, 1, 'C', 1);
$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 7);

foreach ($recomendaciones->data as $i => $row) {
    $pdf->ln(1);
    $pdf->SetFont('helvetica', '', 6);
    $pdf->MultiCell(180, 3, $i + 1 . '.- ' . $row->reco_desc, 'B', 'L', 0, 1);
}

$pdf->Ln(3.5);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(180, $h, 'TRATAMIENTOS', 0, 1, 'C', 1);
$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 7);

foreach ($tratamiento->data as $i => $row) {
    $pdf->ln(1);
    $pdf->SetFont('helvetica', '', 6);
    $pdf->MultiCell(180, 3, $i + 1 . '.- ' . $row->trata_desc, 'B', 'L', 0, 1);
}







$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');
$pdf->Output('ODONTOGRAMA_'.$_REQUEST['adm'].'.pdf', 'I');
