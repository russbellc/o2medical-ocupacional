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
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */
$paciente = $model->paciente($_REQUEST['adm']);
$conclusion = $model->rpt_conclusion($_REQUEST['adm']);
$altura = $model->carga_psicologia_altura_pdf($_REQUEST['adm']);
/* //////////////////////////////////////////////////////
  -----------------variables declaradas------------------
  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ */



$pdf->AddPage('P', 'A4');
$h = 4.3;
$titulo = 7;
$texto = 7;
$salto = 2;
$pdf->SetFont('helvetica', 'B', 7);

$pdf->Image('images/bambas.png', 16, 7, 20, '', 'PNG');
//$pdf->ImageSVG('images/logo_pdf.svg', 157, 7, 40, '', $link = '', '', 'T');

$pdf->SetFont('helvetica', 'B', 10);
//$pdf->Ln(4);
$pdf->Cell(40, $h * 4, '', 0, 0, 'C', 0);
$pdf->MultiCell(100, $h * 4, '
EXAMEN OCUPACIONAL PARA TRABAJOS EN ALTURA MAYOR A  1.80 m', 0, 'C', 0, 0);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(40, $h * 4, $altura->data[0]->m_psico_altura_aptitud, 1, 1, 'C', 1);
//$pdf->Ln(8);

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'EXAMEN MEDICO:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'EMPRESA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->emp_desc, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'APELLIDOS:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->apellidos, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'NOMBRES:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(40, $h, $paciente->data[0]->nombre, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'EDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(15, $h, $paciente->data[0]->edad . ' AÑOS', 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'DNI / ID:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->pac_ndoc, 1, 1, 'C', 0); //////VALUE

$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, 'FECHA DE EXAMEN:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $paciente->data[0]->fech_reg, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(40, $h, 'PUESTO AL QUE POSTULA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(90, $h, $paciente->data[0]->puesto, 1, 1, 'C', 0); //////VALUE
//$h = 5;
$titulo = 6;

$pdf->Ln($salto);

//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 7, '




ANTECEDENTES PSICONEUROLÓGICOS', 1, 'C', 1, 0); //===>TITULO

$pdf->Cell(60, $h, 'TEC MODERADO A GRAVE', 1, 0, 'l', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $altura->data[0]->m_psico_altura_tec_mod_grave, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'DESC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $altura->data[0]->m_psico_altura_tec_mod_grave_desc, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA MATRIZ CENTRAL

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'CONVULSIONES', 1, 0, 'l', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $altura->data[0]->m_psico_altura_convulsiones, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'DESC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $altura->data[0]->m_psico_altura_convulsiones_desc, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'MAREOS, MIOCLONIAS, ACATISIA', 1, 0, 'l', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $altura->data[0]->m_psico_altura_mareo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'DESC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $altura->data[0]->m_psico_altura_mareo_desc, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'PROBLEMAS DE LA AUDICIÓN', 1, 0, 'l', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $altura->data[0]->m_psico_altura_problem_audicion, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'DESC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $altura->data[0]->m_psico_altura_problem_audicion_desc, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(60, $h, 'PROBLEMAS DEL EQUILIBRIO (MENIER, LABERINTITIS)', 1, 0, 'l', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $altura->data[0]->m_psico_altura_problem_equilib, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'DESC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $altura->data[0]->m_psico_altura_problem_equilib_desc, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h * 2, 'FOBIAS', 1, 0, 'C', 1); //===>TITULO
$pdf->Cell(30, $h, 'ACROFOBIA', 1, 0, 'C', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $altura->data[0]->m_psico_altura_acrofobia, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'DESC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $altura->data[0]->m_psico_altura_acrofobia_desc, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA FOBIAS
//=====================================================================>>>>>>>> CABEZERA FOBIAS
//=====================================================================>>>>>>>> CABEZERA FOBIAS



$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0); //===>TITULO
$pdf->Cell(30, $h, 'AGORAFOBIA', 1, 0, 'C', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(10, $h, $altura->data[0]->m_psico_altura_agorafobia, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(10, $h, 'DESC', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(70, $h, $altura->data[0]->m_psico_altura_agorafobia_desc, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA FOBIAS
//=====================================================================>>>>>>>> CABEZERA FOBIAS
//=====================================================================>>>>>>>> CABEZERA FOBIAS




$pdf->Ln($salto);



//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 4, '

ANTECEDENTES DE ALCOHOL Y DROGAS', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(15, $h, 'ALCOHOL', 1, 0, 'C', 1); //===>TITULO


$pdf->Cell(10, $h, 'TIPO:', 1, 0, 'C', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_alcohol_tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'CANTIDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_alcohol_cant, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'FRECUENCIA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_alcohol_frecu, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, 'TABACO', 1, 0, 'C', 1); //===>TITULO


$pdf->Cell(10, $h, 'TIPO:', 1, 0, 'C', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_tabaco_tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'CANTIDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_tabaco_cant, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'FRECUENCIA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_tabaco_frecu, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, 'CAFÉ', 1, 0, 'C', 1); //===>TITULO


$pdf->Cell(10, $h, 'TIPO:', 1, 0, 'C', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_cafe_tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'CANTIDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_cafe_cant, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'FRECUENCIA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_cafe_frecu, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(15, $h, 'DROGAS', 1, 0, 'C', 1); //===>TITULO


$pdf->Cell(10, $h, 'TIPO:', 1, 0, 'C', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_droga_tipo, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(15, $h, 'CANTIDAD:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_droga_cant, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(20, $h, 'FRECUENCIA:', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_droga_frecu, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA




$pdf->Ln($salto);



//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 11, '








TEST DE CAGE', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(110, $h, 'PREGUNTAS', 1, 0, 'L', 1); //===>TITULO
$pdf->Cell(20, $h, 'RESPUESTA', 1, 0, 'C', 1); //===>TITULO
$pdf->Cell(20, $h, 'PUNTAJE', 1, 1, 'C', 1); //===>TITULO
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿LE GUSTA SALIR A DIVERTIRSE?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_01, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_01, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿SE MOLESTA SI LLEGA TARDE A ALGÚN COMPROMISO?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_02, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_02, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿LE HA MOLESTADO ALGUNA VEZ LA GENTE CRITICÁNDOLE SU FORMA DE BEBER?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_03, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_03, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿HA SENTIDO QUE ESTAR EN UNA REUNIÓN DIVIRTIÉNDOSE LO REANIMA?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_04, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_04, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿HA TENIDO ALGUNA VEZ LA IMPRESIÓN DE QUE DEBERÍA BEBER MENOS?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_05, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_05, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿DUERME BIEN?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_06, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_06, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿SE HA SENTIDO ALGUNA VEZ MAL O CULPABLE POR SU COSTUMBRE DE BEBER?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_07, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_07, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿SE PONE NERVIOSO A MENUDO?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_08, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_08, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿ALGUNA VEZ LO PRIMERO QUE HA HECHO POR LA MAÑANA HA SIDO BEBER PARA CALMAR?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_09, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_09, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(110, $h, '¿SUFRE DE DOLORES EN LA ESPALDA AL LEVANTARSE?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_resp_10, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_preg_ptje_10, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA




$pdf->Ln($salto);



//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 13, '








EXAMEN MÉDICO DIRIGIDO', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(130, $h, 'RECIBIÓ ENTRENAMIENTO PARA TRABAJOS EN ALTURAS MAYORES A 1.8M', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_entrena_altura, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
//=====================================================================>>>>>>>> CABEZERA  MATRIZ CENTRAL
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(130, $h, '¿RECIBIÓ ENTRENAMIENTO EN PRIMEROS AUXILIOS?', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_entrena_auxilio, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h * 9, 'EQUILIBRIO', 1, 0, 'C', 1); //===>TITULO
$pdf->Cell(105, $h, 'TÍMPANOS', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_01, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'AUDICIÓN', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_02, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'SUSTENTACIÓN EN UN PIE POR 15 SEGUNDOS', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_03, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'CAMINAR LIBRE SOBRE UNA RECTA 3M SIN DESVÍO', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_04, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'CAMINAR LIBRE CON LOS OJOS VENDADOS 3M SIN DESVÍO', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_05, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'CAMINAR LIBRE CON LOS OJOS VENDADOS EN PUNTA TALÓN 3 M SIN DESVÍO', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_06, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'ROTAR SOBRE UNA SILLA Y LUEGO VERIFICAR EQUILIBRIO DE PIE', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_07, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'ADIADOCOQUINESIA DIRECTA', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_08, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(55, $h, '', 0, 0, 'C', 0);
$pdf->Cell(105, $h, 'ADIADOCOQUINESIA CRUZADA', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(20, $h, $altura->data[0]->m_psico_altura_equilibrio_09, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h, 'EVALUACIÓN OCULAR', 1, 0, 'C', 1);
$pdf->Cell(36, $h, 'NISTAGMUS ESPONTÁNEO', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(27, $h, $altura->data[0]->m_psico_altura_nistagmus_esponta, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'NISTAGMUS PROVOCADO', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(26, $h, $altura->data[0]->m_psico_altura_nistagmus_provoca, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(25, $h, 'TRASTORNOS DE PIE', 1, 0, 'C', 1);
$pdf->Cell(36, $h, 'PIE PLANO:', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(27, $h, $altura->data[0]->m_psico_altura_pie_plano, 1, 0, 'C', 0); //////VALUE
$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(36, $h, 'USA PLANTILLAS:', 1, 0, 'L', 0); //===>TITULO
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(26, $h, $altura->data[0]->m_psico_altura_usa_plantillas, 1, 1, 'C', 0); //////VALUE
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA
//=====================================================================>>>>>>>> CABEZERA


$pdf->Ln($salto);

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->MultiCell(30, $h * 4, '


EVA PSICOLOGICA', 1, 'C', 1, 0); //===>TITULO
$pdf->Cell(70, $h, 'TOULOUSE', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_toulouse, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'ATENCION CONCENTRACION', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'TRASTORNOS', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_bc_2, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'ORIENTACION ESPACIAL', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'HOJA DE ENTREVISTA FORMATO ESTABLECIDO', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_h_entre_form_est, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'RECONOC. DE RIESGOS', 1, 1, 'C', 1); //===>TITULO

$pdf->SetFont('helvetica', 'B', $titulo);
$pdf->Cell(30, $h, '', 0, 0, 'C', 0);
$pdf->Cell(70, $h, 'CUESTIONARIO DE TEMORES', 1, 0, 'C', 1);
$pdf->SetFont('helvetica', '', $texto);
$pdf->Cell(30, $h, $altura->data[0]->m_psico_altura_temores, 1, 0, 'C', 0); //////VALUE
$pdf->Cell(10, $h, '', 0, 0, 'C', 0); //////VALUE
$pdf->Cell(40, $h, 'ACROFOBIA', 1, 1, 'C', 1); //===>TITULO



$pdf->Ln($salto);
$pdf->Ln($salto);


$observ_total = $conclusion->total;

$conteo = array();
foreach ($conclusion->data as $i => $value) {
    $saltos_t = 0;
    foreach ($lim_texto as $a => $val) {
        ($val < strlen($value->obs_desc)) ? $saltos_t = $a + 1 : null;
    }
    array_push($conteo, $saltos_t);
}
$fila_total = array_sum($conteo);

foreach ($conclusion->data as $i => $row2) {
    $salteos = (($conteo[$i] != 0) ? $h_text * ($conteo[$i] + 1) : $h);
    $text_tamaño = (($conteo[$i] != 0) ? $texto - 1 : ((strlen($row2->obs_desc) > 74) ? $texto - 1 : $texto));
    if ($i === 0) {
        $pdf->SetFont('helvetica', 'B', $texto);
        $pdf->Cell(33, (($conteo[$i] != 0) ? $h_text : $h) * ($observ_total + $fila_total), 'RECOMENDACIONES', 1, 0, 'C', 1);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    } else {
        $pdf->SetFont('helvetica', 'B', $text_tamaño);
        $pdf->Cell(33, $salteos, '', 0, 0, 'L', 0);
        $pdf->SetFont('helvetica', '', $text_tamaño);
        $pdf->MultiCell(147, $salteos, $i + 1 . '.- ' . $row2->obs_desc, 1, 'L', 0, 1);
    }
}





//http://localhost/Dropbox/saludocupacional/kaori/system/loader.php?sys_acction=sys_loadreport&sys_modname=mod_medicina&sys_report=inf_nuevo_anexo_16&adm=1003
$pdf->Output('TRAB_ALTURA_180_' . $_REQUEST['adm'] . '.PDF', 'I');
