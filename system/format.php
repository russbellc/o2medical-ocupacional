<?php

/**
 * Description of format
 *
 * @author jose
 */
class format {

    private $r = NULL;

    function __construct($f) {
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        $this->r = $f;
    }

    public function plain() {
        if (is_string($this->r)) {
            echo $this->r;
        } elseif (is_array($this->r) || is_object($this->r))
            throw new Exception("El valor devuelto es un arreglo u objeto");
        else
            throw new Exception("El valor devuelto es un tipo desconocido");
    }

    public function json() {
        if (!is_array($this->r) && !is_object($this->r)) {
            error_log("El dato no Es un Arreglo o clase");
            throw new Exception("El dato no Es un Arreglo o clase");
        }
        else
            try {
                
		  header('Content-type: application/json');
                $_n = (isset($_REQUEST['sys_acction'])) ? $_REQUEST['sys_acction'] . ".json" : "data.json";
                header("Content-disposition: filename=$_n");
                echo json_encode($this->r);
            } catch (Exception $e) {
                error_log($e->getMessage());
                throw new Exception($e->getMessage());
            }
    }

    private function f_xml($k, $val) {
        $r = "<" . ((is_numeric($k)) ? "row" : $k) . ">";
        if (is_array($val) && count($val) > 0) {
            foreach (array_keys($val) as $x => $y) {
                $r.=$this->f_xml($y, $val[$y]);
            }
        } elseif (is_array($val)) {
            
        } elseif (is_object($val)) {
            $z = get_object_vars($val);
            foreach ($z as $a => $b) {
                $r.=$this->g_xml($a, $b);
            }
        } else {
            $r.=($val == FALSE) ? 0 : $val;
        }
        $r.="</" . ((is_numeric($k)) ? "row" : $k ) . ">";
        return $r;
    }

    private function g_xml($k, $val) {
        $r = "<" . ((is_numeric($k)) ? "row" : $k) . ">";
        if (is_array($val)) {
            foreach ($val as $x => $y) {
                $r.=$this->g_xml($x, $y);
            }
        } elseif (is_object($val)) {
            $z = get_object_vars($val);
            foreach ($z as $a => $b) {
                $r.=$this->g_xml($a, $b);
            }
        } else {
            $r.=@$val;
        }
        $r.="</" . ((is_numeric($k)) ? "row" : $k ) . ">";
        return $r;
    }

    public function xml() {
        header("Content-type: text/xml");
//        header("Content-type: text/plain");
        print("<root>");
        if (is_array($this->r)) {
            foreach (array_keys($this->r) as $i => $k) {
                print( $this->f_xml($k, $this->r[$k]));
            }
        } elseif (is_object($this->r)) {
            $_v = get_object_vars($this->r);
            foreach ($_v as $i => $k) {
                print($this->g_xml($i, $k));
            }
        } else {
            
        }
        print("</root>");
    }

    public function html() {
        echo $this->r;
    }

    public function css() {
        if (!is_array($this->r) && !is_object($this->r)) {
            header("Content-type: text/css");
            header("Content-disposition: filename=style.css");
            echo $this->r;
        } else {
            error_log("No se puede convertir un objeto o array a css");
            throw new Exception("No se puede convertir un objeto o array a css");
        }
    }

    public function js() {
        if (!is_array($this->r) && !is_object($this->r)) {
            header("Content-type: text/javascript");
            header("Content-disposition: filename=script.js");
            echo $this->r;
        } else {
            error_log("No se puede convertir un objeto o array a js");
            throw new Exception("No se puede convertir un objeto o array a js");
        }
    }
    public function pdf(){}
    public function png(){
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($this->r)).' GMT', true, 200);
        header('Content-Length: '.filesize($this->r));
        header('Content-Type: image/png');
        print file_get_contents($this->r);
    }

}

?>
