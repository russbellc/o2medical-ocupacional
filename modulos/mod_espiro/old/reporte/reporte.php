<?php

class MYPDF extends TCPDF {

    public function Header() {
        $this->setJPEGQuality(100);
        $this->Image('images/logo.png', 15, 5, 50, '', 'PNG');
        $this->SetY(10);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetX(55);
    }

    function zerofill($entero, $largo) {
        $entero = (int) $entero;
        $largo = (int) $largo;
        $relleno = '';

        if (strlen($entero) < $largo) {
            $relleno = str_repeat(0, $largo - strlen($entero));
        }
        return $relleno . $entero;
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Av. LOS INCAS 1412', 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Pagina - ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


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
$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
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

$pdf->SetFont('helvetica', 'BU', 15);
$pdf->Cell(0, 0, 'CUESTIONARIO DE ESPIROMETRIA', 0, 1, 'C');
$pdf->ln(5);
$h = 6;
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(35,$h,'PRE-OCUPACIONAL',0,0,'R');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(35,$h,'PERIODICO',0,0,'R');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(35,$h,'DE RETIRO',0,0,'R');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(35,$h,'OTRO',0,0,'R');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->ln(4);
$pdf->Cell(40,$h,'APELLIDOS Y NOMBRES:',0,0,'L');
$pdf->Cell(115,$h,'','B',0,'L');
$pdf->Cell(12,$h,'EDAD:',0,0,'L');
$pdf->Cell(13,$h,'','B',1,'L');

$pdf->ln(4);
$pdf->Cell(35,$h,'PUESTO DE TRABAJO:',0,0,'L');
$pdf->Cell(95,$h,'','B',0,'L');
$pdf->Cell(17,$h,'FECHA:',0,0,'L');
$pdf->Cell(32,$h,'','B',1,'L');

$pdf->ln(5);
$pdf->SetFont('helvetica', 'BU', 10);
$pdf->Cell(180,$h,'PREGUNTAS PARA TODOS LOS CANDIDATOS A ESPIROMETRIA (Relacionados a criterios de exclusión).',0,1,'C');

$pdf->SetFont('helvetica', 'B', 9);
//$pdf->ln(1);
$pdf->Cell(5,$h,'',0,0,'L');
$pdf->Cell(150,$h,'',0,0,'L');
$pdf->Cell(10,$h,'Si',0,0,'C');
$pdf->Cell(5,$h,'',0,0,'L');
$pdf->Cell(10,$h,'No',0,1,'C');

$pdf->SetFont('helvetica', 'B', 9);
$pdf->ln(1);
$pdf->Cell(5,$h,'1.-',0,0,'L');
$pdf->Cell(150,$h,'Desprendimiento de retina o una operación de los ojos, torax o abdomen en los ultimos e meses?',0,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'',0,0,'L');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->ln(1);
$pdf->Cell(5,$h,'2.-',0,0,'L');
$pdf->Cell(150,$h,'Ha tenido algun ataque cardiaco o infarto al corazon en los ultimos 3 meses?',0,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'',0,0,'L');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->ln(1);
$pdf->Cell(5,$h,'3.-',0,0,'L');
$pdf->Cell(150,$h,'Ha estado hospitalizado(a) por cualquier otro problema del corazon en los ultimos 3 meses?',0,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'',0,0,'L');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->ln(1);
$pdf->Cell(5,$h,'4.-',0,0,'L');
$pdf->Cell(150,$h,'Está usando medicamentos para Tuberculosis en este momento?',0,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'',0,0,'L');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->ln(1);
$pdf->Cell(5,$h,'5.-',0,0,'L');
$pdf->Cell(150,$h,'En caso de ser mujer. Esta usted embarazada actualmente?',0,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'',0,0,'L');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->ln(5);
$pdf->SetFont('helvetica', 'BU', 10);
$pdf->Cell(180,$h,'PARA SER LLENADO POR EL PROFESIONAL QUE REALIZA LA PRUEBA',0,1,'C');
$pdf->ln(1);
$pdf->SetFillColor(194, 217, 241);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(5,$h,'',1,0,'C',1);
$pdf->Cell(65,$h,'DESCRIPCION',1,0,'C',1);
$pdf->Cell(10,$h,'SI',1,0,'C',1);
$pdf->Cell(10,$h,'NO',1,0,'C',1);
$pdf->Cell(5,$h,'',1,0,'C',1);
$pdf->Cell(65,$h,'DESCRIPCION',1,0,'C',1);
$pdf->Cell(10,$h,'SI',1,0,'C',1);
$pdf->Cell(10,$h,'NO',1,1,'C',1);

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(5,$h,'6',1,0,'C');
$pdf->Cell(65,$h,'Hemoptisis',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'12',1,0,'C');
$pdf->Cell(65,$h,'Infarto Reciente',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(5,$h,'7',1,0,'C');
$pdf->Cell(65,$h,'Pneumotorax',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'13',1,0,'C');
$pdf->Cell(65,$h,'Inestabilidad CV',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(5,$h,'8',1,0,'C');
$pdf->Cell(65,$h,'Traqueostomia',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'14',1,0,'C');
$pdf->Cell(65,$h,'Fiebre, nauseas y vomitos',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(5,$h,'9',1,0,'C');
$pdf->Cell(65,$h,'Sonda pleural',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'15',1,0,'C');
$pdf->Cell(65,$h,'Embarazo avanzado',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(5,$h,'10',1,0,'C');
$pdf->Cell(65,$h,'Aneurisma cerebral abdomen torax',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'16',1,0,'C');
$pdf->Cell(65,$h,'Embarazo complicado',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(5,$h,'11',1,0,'C');
$pdf->Cell(65,$h,'Embolia pulmonar',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(5,$h,'17',1,0,'C');
$pdf->Cell(65,$h,'Paralisis facial',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->ln(5);
$pdf->SetFont('helvetica', 'BU', 10);
$pdf->Cell(180,$h,'PREGUNTAS PARA TODOS LOS ENTREVISTADOS QUE NO TIENEN CRITERIOS DE EXCLUSION',0,1,'C');
$pdf->Cell(180,3,'Y QUE POR LO TANTO DEBEN HACER LA ESPIROMETRIA',0,1,'C');

$pdf->ln(1);
$pdf->SetFillColor(194, 217, 241);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(10,$h,'',1,0,'C',1);
$pdf->Cell(150,$h,'DESCRIPCION',1,0,'C',1);
$pdf->Cell(10,$h,'SI',1,0,'C',1);
$pdf->Cell(10,$h,'NO',1,1,'C',1);

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(10,$h,'1',1,0,'C');
$pdf->Cell(150,$h,'tubo una infeccion respiratoria (resfriado) enlas ultimas 3 semanas?',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(10,$h,'2',1,0,'C');
$pdf->Cell(150,$h,'Tubo infeccion en el oido en las ultimas 3 semanas?',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(10,$h,'3',1,0,'C');
$pdf->Cell(150,$h,'Uso aerosoles (sprays inhalados) o nebulizaciones con broncodilatores en las ultimas 3 horas?',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(10,$h,'4',1,0,'C');
$pdf->Cell(150,$h,'Ha usado algun medicamento broncodilator en las ultimas 8 horas?',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(10,$h,'5',1,0,'C');
$pdf->Cell(150,$h,'fumo (cualquier tipo de cigarro) en las ultimas horas? Si cuantos?--->',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(10,$h,'6',1,0,'C');
$pdf->Cell(150,$h,'Realizo algun ejercicio fisico fuerte (como gimnasia, caminata o trotar) en la ultima hora?',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');

$pdf->SetFont('helvetica', 0, 9);
$pdf->Cell(10,$h,'7',1,0,'C');
$pdf->Cell(150,$h,'comio en la ultima hora?',1,0,'L');
$pdf->Cell(10,$h,'',1,0,'C');
$pdf->Cell(10,$h,'',1,1,'C');
$pdf->ln(25);

$pdf->Cell(20,$h,'',0,0,'C');
$pdf->Cell(55,$h,'','B',0,'C');
$pdf->Cell(30,$h,'',0,0,'C');
$pdf->Cell(55,$h,'','B',0,'C');
$pdf->Cell(20,$h,'',0,1,'C');

$pdf->Cell(20,$h,'',0,0,'C');
$pdf->Cell(55,$h,'PROFESIONAL EVALUADOR',0,0,'C');
$pdf->Cell(30,$h,'',0,0,'C');
$pdf->Cell(55,$h,'FIRMA DEL TRABAJADOR EVALUADO',0,0,'C');
$pdf->Cell(20,$h,'',0,1,'C');



$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');
$pdf->Output('Pscologia', 'I');
