<?php

class MYPDF extends TCPDF {

    public $user;

    public function Header() {
        
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
        $this->Cell(0, 10, $this->user->sed_desc, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Pagina - ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$model = new model();
$pdf->user = $model->user;

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
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT, 2);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Saltos de página automáticos.
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el ratio para las imagenes que se puedan utilizar
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFillColor(194, 217, 241);


$pdf->AddPage('P', 'A4');


//$pdf->ImageSVG('images/logo_pdf.svg', 8, 6, '', '', $link = '', '', 'T');
$pac = $model->paciente($_REQUEST['adm']);
$rx_torax = $model->rx_torax_report($_REQUEST['adm']);

$pdf->SetFont('helvetica', 'B', 7);
//$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
$pdf->ImageSVG('images/logo_pdf.svg', 10, 7, 46, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'BU', 11);
$pdf->Ln(2);
$pdf->Cell(0, 0, 'FORMULARIO DE INFORME', 0, 1, 'C', 0);
$pdf->Cell(0, 0, 'RADIOGRAFICO CON METODOLOGIA OIT', 0, 1, 'C', 0);
//$pdf->Cell(180, $h, 'EVALUACION MEDICA PERFIL VISITA A 4000 m.s.n.m.', 0, 1, 'C', 0);
$pdf->Ln(7);
$f = 0;
$h = 4;
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
$pdf->Cell(50, $h, 'DATOS GENERALES', 0, 0, 'L', 1);
$pdf->Cell(130, $h, 'N° HOJA DE RUTA: ' . $pac->data[0]->adm, 0, 1, 'R', 0);
$pdf->Cell(0, 4 * $h, '', 1);
$pdf->ln(0);


$pdf->Cell($w - 20, $h, 'N° DE PLACA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 20, $h, ': ' . $rx_torax->data[0]->m_rx_rayosx_n_placa, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'LECTOR', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $rx_torax->data[0]->m_rx_rayosx_lector, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w - 25, $h, 'NOMBRES', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 25, $h, ': ' . $pac->data[0]->nom_ap, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'TIPO DE FICHA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->tipo, $f, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FECHA DE LECTURA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $rx_torax->data[0]->m_rx_rayosx_fech_lectura, $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w, $h, 'FECHA DE RADIOGRAFIA', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2, $h, ': ' . $pac->data[0]->fech_reg, $f, 1);
//$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w - 25, $h, 'EDAD ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 - 15, $h, ': ' . $pac->data[0]->edad . ' AÑOS', $f, 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(20, $h, 'EMPRESA ', $f, 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell($w2 + 60, $h, ': ' . $pac->data[0]->emp_desc, $f, 1);





$f = 0;
$h = 5;
$w = 40;
$w2 = 50;
$w3 = 8;
$texh = 8;
$texh2 = 8;
$ali = 'L';
$color = 0;

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell($w2, $h, 'I. CALIDAD RADIOGRÁFICA', 0, 1, 'L', 1);
$pdf->ln(0);
$f = 1;

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, '', 'LTR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_calidad == 'Buena' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '1', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Buena', $f, 0, 'L', $color);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, '', 'LTR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_causas == 'Sobre Exposicion' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '1', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Sobre Exposición', $f, 0, 'L', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_causas == 'Escapulas' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '5', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Escapulas', $f, 1, 'L', $color);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'Calidad', 'LR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_calidad == 'Aceptable' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '2', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Aceptable', $f, 0, 'L', $color);
$color = 0;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'Causas', 'LR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_causas == 'Sub Exposicion' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '2', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Sub Exposicion', $f, 0, 'L', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_causas == 'Artefacto' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '6', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Artefacto', $f, 1, 'L', $color);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, 'Radiografica', 'LR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_calidad == 'Baja Calidad' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '3', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Baja Calidad', $f, 0, 'L', $color);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, '', 'LR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_causas == 'Posicion Centrado' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '3', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Posición Centrado', $f, 0, 'L', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_causas == 'Otros' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '7', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Otros', $f, 1, 'L', $color);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, '', 'LBR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_calidad == 'Inaceptable' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '4', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Inaceptable', $f, 0, 'L', $color);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(30, $h, '', 'LBR', 0, $ali);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_causas == 'Inspiracion Insuficiente' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '4', $f, 0, 'C', $color);
$pdf->Cell(35, $h, 'Inspiracion Insuficiente', $f, 0, 'L', $color);
$pdf->Cell(5, $h, '', $f, 0, 'C', 0);
$pdf->Cell(35, $h, '', $f, 1, 'L', 0);
$color = 0;
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->MultiCell(30, 8, 'Comentarios sobre defectos tecnicos', 1, 'L', 0, 0);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(150, 8, $rx_torax->data[0]->m_rx_rayosx_coment_tec, 1, 'L', 0, 1);

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(180, $h, 'II. ANORMALIDADES PARENQUIMATOSAS (si NO hay anormalidades parenquimatosas pase a   III. ANORMALIDADES PLEURALES)', 0, 1, 'L', 1);
$pdf->ln(1);
$f = 1;

$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(45, 18, '2.1. Zonas afectadas (marque TODAS las zonas afectadas).', 1, 'L', 0, 0);
$pdf->MultiCell(45, 18, '2.2. Profusion (opacidades pequeñas)(escalas de 12 puntos)(consulte las radiografias estandar - marque las sub categoria de profusión)', 1, 'L', 0, 0);
$pdf->MultiCell(45, 18, '2.3. Forma y tamaño :(Consulte las radiografias estandar: se requiere dos simbolos; marque un primario y un secundario)', 1, 'L', 0, 0);
$pdf->MultiCell(45, 18, '2.4. Opacidades (marque 0 si no hay o marque A, B o C)', 1, 'L', 0, 1);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, '', $f, 0, 'C', $color);
$pdf->Cell(15, $h, 'DERECHO', $f, 0, 'C', $color);
$pdf->Cell(15, $h, 'IZQUIERDO', $f, 0, 'C', $color);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '0/-' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '0/-', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '0/0' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '0/0', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '0/1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '0/1', $f, 0, 'C', $color);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(22.5, $h, 'Primaria', $f, 0, 'C', $color);
$pdf->Cell(22.5, $h, 'Secundaria', $f, 0, 'C', $color);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_opacidad == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(45, $h, '0', $f, 1, 'C', $color);




$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'Superior', $f, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_zona_a_sup_der == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_zona_a_sup_izq == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '', $f, 0, 'C', $color);

$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '1/0' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '1/0', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '1/1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '1/1', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '1/2' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '1/2', $f, 0, 'C', $color);

$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_pri == 'p' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'p', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_pri == 's' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 's', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_sec == 'p' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'p', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_sec == 's' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 's', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_opacidad == 'A' ? 1 : 0); //--------------------------->
$pdf->Cell(45, $h, 'A', $f, 1, 'C', $color);





$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'Medio', $f, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_zona_a_med_der == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_zona_a_med_izq == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '', $f, 0, 'C', $color);

$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '2/1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '2/1', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '2/2' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '2/2', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '2/3' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '2/3', $f, 0, 'C', $color);

$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_pri == 'q' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'q', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_pri == 't' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 't', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_sec == 'q' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'q', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_sec == 't' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 't', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_opacidad == 'B' ? 1 : 0); //--------------------------->
$pdf->Cell(45, $h, 'B', $f, 1, 'C', $color);







$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(15, $h, 'Inferior', $f, 0, 'L', 0);
$pdf->SetFont('helvetica', '', $texh2);
$color = ($rx_torax->data[0]->m_rx_rayosx_zona_a_inf_der == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_zona_a_inf_izq == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '', $f, 0, 'C', $color);

$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '3/2' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '3/2', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '3/3' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '3/3', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_profusion == '3/+' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '3/+', $f, 0, 'C', $color);

$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_pri == 'r' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'r', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_pri == 'u' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'u', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_sec == 'r' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'r', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_forma_tama_sec == 'u' ? 1 : 0); //--------------------------->
$pdf->Cell(11.25, $h, 'u', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_opacidad == 'C' ? 1 : 0); //--------------------------->
$pdf->Cell(45, $h, 'C', $f, 1, 'C', $color);
$color = 0;

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(140, $h, 'III. ANORMALIDADES PLEURALES (si NO hay anormalidades parenquimatosas pase a IV. SIMBOLOS)', 0, 0, 'L', 1);
$color = ($rx_torax->data[0]->m_rx_rayosx_anormal_pleural == 'SI' ? 1 : 0); //--------------------------->
$pdf->Cell(20, $h, 'SI', 1, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_anormal_pleural == 'NO' ? 1 : 0); //--------------------------->
$pdf->Cell(20, $h, 'NO', 1, 1, 'C', $color);
$pdf->ln(0);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(180, $h, '3.1. Placas pleurales ( 0 = Ninguna, D = Hemitorax Derecho, I = Hemitorax Izquierdo)', $f, 1, 'L', $color);
$pdf->Cell(43, 8, 'Sitio', 'LTR', 0, 'L', 0);
$pdf->Cell(17, 8, 'Calcificacion', 'LTR', 0, 'L', 0);
$pdf->MultiCell(60, 8, 'Extención (pared toraxica; combinada para placas de perfil y de frente)', 1, 'L', 0, 0);
$pdf->Cell(60, 8, 'Ancho (opcional)(ancho minimo exigido: 3mm)', $f, 1, 'L', 0);

$color = 0;
$pdf->SetFont('helvetica', '', 7);
$pdf->Cell(43, $h, '', 'LR', 0, 'L', $color);
$pdf->Cell(17, $h, '', 'LR', 0, 'L', $color);
$pdf->Cell(5, $h, '1', $f, 0, 'L', $color);
$pdf->Cell(55, $h, '<1/4 de la pared lateral del torax', $f, 0, 'L', $color);
$pdf->Cell(5, $h, 'a', $f, 0, 'L', $color);
$pdf->Cell(55, $h, 'De 3 a 5 mm', $f, 1, 'L', $color);

$pdf->Cell(43, $h, '', 'LR', 0, 'L', $color);
$pdf->Cell(17, $h, '', 'LR', 0, 'L', $color);
$pdf->Cell(5, $h, '2', $f, 0, 'L', $color);
$pdf->Cell(55, $h, 'entre 1/4 y 1/2 de la pared lateral del torax', $f, 0, 'L', $color);
$pdf->Cell(5, $h, 'b', $f, 0, 'L', $color);
$pdf->Cell(55, $h, 'De 5 a 10 mm', $f, 1, 'L', $color);

$pdf->Cell(43, $h, '', 'LRB', 0, 'L', $color);
$pdf->Cell(17, $h, '', 'LRB', 0, 'L', $color);
$pdf->Cell(5, $h, '3', $f, 0, 'L', $color);
$pdf->Cell(55, $h, '>1/2 de la pared lateral del torax', $f, 0, 'L', $color);
$pdf->Cell(5, $h, 'c', $f, 0, 'L', $color);
$pdf->Cell(55, $h, 'Mayor a 10 mm', $f, 1, 'L', $color);

$pdf->SetFont('helvetica', '', $texh2);
$pdf->Cell(28, $h, 'Pared toraxica perfil', $f, 0, 'L', 0);

$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_pared == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_pared == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_pared == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'I', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_pared_calci == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_pared_calci == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_pared_calci == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, 'I', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0D == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '0', $f, 0, 'C', 1);
$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0D == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, 'D', $f, 0, 'C', $color);


$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0I == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '0', $f, 0, 'C', 1);
$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0I == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, 'I', $f, 0, 'C', $color);

$pdf->Cell(30, $h, 'D', $f, 0, 'C', 0);
$pdf->Cell(30, $h, 'I', $f, 1, 'C', 0);


$pdf->Cell(28, $h, 'De frente', $f, 0, 'L', $color);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_frente == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_frente == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_frente == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'I', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_frente_calci == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_frente_calci == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_frente_calci == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, 'I', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0D_123 == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, '1', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0D_123 == '2' ? 1 : 0); //--------------------------->
$pdf->Cell(14, $h, '2', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0D_123 == '3' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, '3', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0I_123 == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, '1', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0I_123 == '2' ? 1 : 0); //--------------------------->
$pdf->Cell(14, $h, '2', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_exten_0I_123 == '3' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, '3', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_ancho_D_abc == 'a' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, 'a', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_ancho_D_abc == 'b' ? 1 : 0); //--------------------------->
$pdf->Cell(14, $h, 'b', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_ancho_D_abc == 'c' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, 'c', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_ancho_I_abc == 'a' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, 'a', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_ancho_I_abc == 'b' ? 1 : 0); //--------------------------->
$pdf->Cell(14, $h, 'b', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_ancho_I_abc == 'c' ? 1 : 0); //--------------------------->
$pdf->Cell(8, $h, 'c', $f, 1, 'C', $colores);


$pdf->Cell(28, $h, 'Diafragma', $f, 0, 'L', $color);


$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_diagra == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_diagra == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'D', $f, 0, 'C', $color);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_diagra == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'I', $f, 0, 'C', $color);


$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_diagra_calci == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_diagra_calci == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_sitio_diagra_calci == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5.66, $h, 'I', $f, 0, 'C', $colores);

$pdf->Cell(60, $h, '', $f, 0, 'C', $color);
$pdf->Cell(60, $h, '', $f, 1, 'C', $color);

$pdf->Cell(180, $h, '3.2. Engrosamiento Difuso de la Pleura ( 0 = Ninguna, D = Hemitorax Derecho, I = Hemitorax Izquierdo)', $f, 1, 'L', $color);
$pdf->Cell(45, $h, 'PARED TORACICA', $f, 0, 'C', $color);
$pdf->Cell(45, $h, 'CALCIFICACIÓN', $f, 0, 'C', $color);
$pdf->Cell(10, $h, '', 'LTR', 0, 'C', $color);
$pdf->Cell(40, $h, 'EXTESIÓN', $f, 0, 'C', $color);
$pdf->Cell(40, $h, 'ANCHO', $f, 1, 'C', $color);

$pdf->Cell(30, $h, 'DE PERFIL', $f, 0, 'L', $color);
$colores = ($rx_torax->data[0]->m_rx_rayosx_pared_perfil == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_pared_perfil == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_pared_perfil == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'I', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_calci_perfil == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_calci_perfil == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_calci_perfil == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, 'I', $f, 0, 'C', $colores);

$pdf->Cell(10, $h, '', 'LR', 0, 'C', $color);


$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0D == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(10, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0D == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(10, $h, 'D', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0I == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(10, $h, '0', $f, 0, 'C', 1);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0I == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(10, $h, 'I', $f, 0, 'C', $color);

$pdf->Cell(20, $h, 'D', $f, 0, 'C', 0);
$pdf->Cell(20, $h, 'I', $f, 1, 'C', 0);

$pdf->Cell(30, $h, 'DE FRENTE', $f, 0, 'L', $color);
$colores = ($rx_torax->data[0]->m_rx_rayosx_pared_frente == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_pared_frente == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_pared_frente == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(5, $h, 'I', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_calci_frente == '0' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, '0', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_calci_frente == 'D' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, 'D', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_calci_frente == 'I' ? 1 : 0); //--------------------------->
$pdf->Cell(15, $h, 'I', $f, 0, 'C', $colores);

$pdf->Cell(10, $h, '', 'LRB', 0, 'C', $color);


$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0D_123 == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, '1', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0D_123 == '2' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, '2', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0D_123 == '3' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, '3', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0I_123 == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, '1', $f, 0, 'C', $color);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0I_123 == '2' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, '2', $f, 0, 'C', $color);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_exten_0I_123 == '3' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, '3', $f, 0, 'C', $color);


$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_ancho_D_abc == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, 'a', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_ancho_D_abc == '2' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, 'b', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_ancho_D_abc == '3' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, 'c', $f, 0, 'C', $colores);


$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_ancho_I_abc == '1' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, 'a', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_ancho_I_abc == '2' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, 'b', $f, 0, 'C', $colores);
$colores = ($rx_torax->data[0]->m_rx_rayosx_engro_ancho_I_abc == '3' ? 1 : 0); //--------------------------->
$pdf->Cell(6.66, $h, 'c', $f, 1, 'C', $colores);



$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(160, $h, 'IV. SIMBOLOS (Rodee con un circulo la respuesta adecuada; si rodea od escriba a continuacion un COMENTARIO)', 0, 0, 'L', 1);
$pdf->Cell(20, $h, $rx_torax->data[0]->m_rx_rayosx_simbolo, 1, 1, 'C', 0);
$pdf->ln(0);

$color = ($rx_torax->data[0]->m_rx_rayosx_aa == 'aa' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'aa', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_at == 'at' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'at', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_ax == 'ax' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'ax', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_bu == 'bu' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'bu', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_ca == 'ca' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'ca', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_cg == 'cg' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'cg', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_cn == 'cn' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'cn', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_co == 'co' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'co', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_cp == 'cp' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'cp', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_cv == 'cv' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'cv', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_di == 'di' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'di', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_ef == 'ef' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'ef', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_em == 'em' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'em', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_es == 'es' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'es', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_od == 'od' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'od', 'LTR', 1, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_fr == 'fr' ? 1 : 0); //---------->

$pdf->Cell(12, $h, 'fr', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_hi == 'hi' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'hi', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_ho == 'ho' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'ho', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_ids == 'id' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'id', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_ih == 'ih' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'ih', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_kl == 'kl' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'kl', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_me == 'me' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'me', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_pa == 'pa' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'pa', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_pb == 'pb' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'pb', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_pi == 'pi' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'pi', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_px == 'px' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'px', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_ra == 'ra' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'ra', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_rp == 'rp' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'rp', $f, 0, 'C', $color);
$color = ($rx_torax->data[0]->m_rx_rayosx_tb == 'tb' ? 1 : 0); //---------->
$pdf->Cell(12, $h, 'tb', $f, 0, 'C', $color);
$pdf->Cell(12, $h, '', 'LBR', 1, 'C', 0);

$pdf->SetFont('helvetica', 'B', $texh);
$pdf->MultiCell(30, 8, 'COMENTARIOS', 1, 'L', 0, 0);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(150, 8, $rx_torax->data[0]->m_rx_rayosx_coment, 1, 'L', 0, 1);


$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(120, $h, 'DESCRIPCION DE ANORMALIDADES ENCONTRADAS', 0, 1, 'L', 1);
$pdf->ln(0);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(120, 4, $rx_torax->data[0]->m_rx_rayosx_obs, 1, 'L', 0, 1);

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', $texh);
$pdf->Cell(120, $h, 'CONCLUSIONES RADIOGRAFICAS', 0, 1, 'L', 1);
$pdf->ln(0);
$pdf->SetFont('helvetica', '', $texh2);
$pdf->MultiCell(120, 8, $rx_torax->data[0]->m_rx_rayosx_concluciones, 1, 'L', 0, 1);
//$pdf->Ln(3);
//$pdf->Cell(45, 20, '', 0, 0, 'C');
//$pdf->Cell(90, 20, '', 'B', 0, 'C');
//$pdf->Cell(45, 20, '', 0, 1, 'C');
//$pdf->Cell(45, $h, '', 0, 0, 'C');
//$pdf->Cell(90, $h, 'Dr. Rolando Valencia Portugal  CMP: 20613  RNE: 20066', 0, 0, 'C'); //DR. ..................................
//$pdf->Cell(45, $h, '', 0, 1, 'C');
//$pdf->Cell(45, $h, '', 0, 0, 'C');
//$pdf->SetFont('helvetica', 'B', $texh);
//$pdf->Cell(90, $h, 'FIRMA Y SELLO DEL MEDICO', 0, 0, 'C');
//$pdf->SetFont('helvetica', '', $texh2);
//$pdf->Cell(45, $h, '', 0, 1, 'C');

$pdf->setVisibility('screen');
$pdf->SetAlpha(0.1);
$pdf->ImageSVG("images/fondo_pdf.svg", 50, 90, 110, '', $link = '', 'PNG');
$pdf->SetAlpha(0.9);
$pdf->setVisibility('all');

//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('rayosx_' . $_REQUEST['adm'] . '.PDF', 'I');
