<?php

ini_set('session.cookie_lifetime', '28800');
session_start();
//ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/error.log");
require 'config.php';
#mostramos errores
//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
#enviamos a un log propio
//ini_set("log_errors", 1);
//ini_set("error_log", __log);
#perzonalizamos excepciones

function sys_ex($exception) {
    $tpl = <<<tpl
   <div style="background-color:#01285c;
    border:solid 3px #001025;
    width:400px;padding:10px;
    color:#ffffff;
    margin-bottom:10px;
    -moz-border-radius: 5px;
    border-radius: 5px;">
    <h3 style="margin:0;   
    padding:3px 0;">Error</h3>
    <hr style="border:solid 1px #001025;">
    <p style="margin:0;
    padding:10px;">%s</p>
</div>
tpl;
    if ($_request['format'] == "json") {
        header('Content-type: text/json');
        header("Content-disposition: filename=error.json"); //asdasdasd
        echo json_encode(array("success" => false, "error" => $exception->getMessage()));
    } else {
        echo json_encode(array("success" => false, "error" => sprintf($tpl, $exception->getMessage())));
    }
}

set_exception_handler('sys_ex');

abstract class core {

    protected $user;
    static $con;

    public function __construct() {
        if (!self::$con) {
            try {
                self::$con = new PDO(sprintf("%s:host=%s;dbname=%s", __driver, __dbserver, __dbname), __dbuser, __dbpass);
                self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->sql("SET NAMES 'utf8'");
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        if (isset($_SESSION[__sesname])) {
            $this->user = new stdClass();
            $this->user->us_id = $_SESSION[__sesname]['usu_id'];
            $this->user->us_nom = $_SESSION[__sesname]['usu_nom'];
            $this->user->con_sedid = $_SESSION[__sesname]['sed_id'];
            $this->user->sed_clinic = $_SESSION[__sesname]['sed_clinic']; //per_desc
            $this->user->per_desc = $_SESSION[__sesname]['per_desc']; //per_id
            $this->user->per_id = $_SESSION[__sesname]['per_id'];
            $this->user->emp_id = $_SESSION[__sesname]['emp_id'];
            $this->user->acceso = $_SESSION[__sesname]['acceso'];
            $this->user->empresas = $_SESSION[__sesname]['empresas'];
            $this->user->emp_rs = $_SESSION[__sesname]['emp_rs'];
            $this->user->sed_desc = $_SESSION[__sesname]['sed_direccion'];
        }
    }

    protected function sql($q, $b = array()) {
        $_c = new stdClass();
        try {
            $st = self::$con->prepare($q);
            $st->execute($b);
            try {
                $_c->data = $st->fetchAll(PDO::FETCH_OBJ);
            } catch (Exception $exc) {
                $_c->data = array();
            }
            $_c->success = true;
            $_c->total = $st->rowCount();
        } catch (ErrorException $e) {
            $_c->success = false;
            $_c->error = $e->getMessage();
        }
        $_c->sql = $q;
        return $_c;
    }

    protected function getId() {
        return self::$con->lastInsertId();
    }

    protected function begin() {
        self::$con->beginTransaction();
    }

    protected function getMonth($m) {
        $month = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre");
        return $month[$m - 1];
    }

    protected function transaction() {
        self::$con->beginTransaction();
    }

    protected function commit() {
        self::$con->commit();
    }

    protected function rollback() {
        self::$con->rollBack();
    }

    protected function xjson($c) {
        try {
            if (file_exists($c)) {
                $response = array();
                $xml = (file_exists($c)) ? utf8_decode(file_get_contents($c)) : $c;
                $sxml = simplexml_load_string($xml);
                foreach ($sxml->row as $row) {
                    $t = array();
                    foreach ($row->attributes() as $i => $atr) {
                        $t[$i] = (string) $atr;
                    }
                    $response[] = (object) $t;
                }
                return $response;
            } else
                return array();
        } catch (Exception $exc) {
            return array();
        }
    }

    public function numtoletras($xcifra) {
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                            } else {
                                $key = (int) substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)) {  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {
                                
                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                }
                                else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = $this->subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO CON $xdecimales/100 NUEVOS SOLES";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN PESO $xdecimales/100 NUEVOS SOLES ";
                        }
                        if ($xcifra >= 2) {
                            $xcadena.= " CON $xdecimales/100 NUEVOS SOLES "; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }

// END FUNCTION

    private function subfijo($xx) { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);

        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
    }

}
