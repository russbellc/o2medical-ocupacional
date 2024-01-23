<?php

class model extends core {

    public function list_oftalmo() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $sql1 = ("SELECT
                    adm_id as adm,
                    IF((odo_st) = 1,1,0) as st_id,
                    tfi_desc,emp_desc, pac_ndoc,
                    concat(pac_appat,' ',pac_apmat,' ',pac_nombres)as nombre,pac_sexo
                    ,odo_fech as fecha,odo_usu as usu,odo_id
                    FROM admision
                    inner join paciente on adm_pacid=pac_id
                    inner join pack on adm_pkid=pk_id
                    left join empresa on pk_empid=emp_id
                    left join tficha on adm_tfiid=tfi_id
                    inner join dpack on dpk_pkid=pk_id
                    left join odonto on odo_adm=adm_id
                    where
                    dpk_exid IN (14) ");
        $empresa = $this->user->empresas;
        ($this->user->acceso == 1) ? '' : $sql1.="and emp_id IN ($empresa) ";
        if (!is_null($columna) && !is_null($query)) {
            if ($columna == "1") {
                $sql1.="and adm_id=$query";
            } else if ($columna == "2") {
                $sql1.="and pac_ndoc=$query";
            } else if ($columna == "3") {
                $sql1.="and concat(pac_appat,' ',pac_apmat,' ',pac_nombres) like '%$query%'";
            } else if ($columna == "4" && $this->user->acceso == 1) {
                $sql1.="and emp_desc like '%$query%' or emp_id like '%$query%'";
            } else if ($columna == "5") {
                $sql1.="and tfi_desc LIKE '%$query%'";
            }
        }
        $sql1.=" group by adm_id order by adm_id DESC;";
        $sql = $this->sql($sql1);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_empre() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $q = "SELECT emp_id, emp_desc, emp_acro FROM empresa where ";
        $empresa = $this->user->empresas;
        ($this->user->acceso == 1) ? $q.=" concat(emp_id, emp_desc, emp_acro )like'%$query%';" : $q.=" emp_id IN ($empresa) ";
        $sql = $this->sql($q);
        return $sql;
    }

    public function report_diario($inicio, $final, $empresas) {
        $empresa = $this->user->empresas;
        ($this->user->acceso == 1) ? $emp = " and emp_id like '%$empresas%'" : (strlen($empresas) > 1) ? $emp = " and emp_id like '%$empresas%'" : $emp = " and emp_id IN ($empresa) ";
        $sql = "SELECT
                adm_id AS NRO,emp_acro AS EMPRESA,Date_format(adm_fechc,'%d-%m-%Y') AS FECHA,
                concat(pac_appat,' ',pac_apmat,', ',pac_nombres)as NOMBRES, pac_sexo AS SEXO,tfi_desc AS FICHA,pk_desc AS RUTA
                FROM admision
                inner join pack on pk_id=adm_pkid
                inner join dpack on dpk_pkid=pk_id
                inner join empresa on emp_id=pk_empid
                inner join paciente on pac_id=adm_pacid
                inner join tficha on tfi_id=adm_tfiid
                WHERE dpk_exid IN (14) and adm_fechc BETWEEN '$inicio 00:00:00' AND '$final 23:59:59' $emp
                group by adm_id order by adm_id;";
        $q = $this->sql($sql);
        return $q;
    }

    public function load_diag() {
        $diag = $this->sql("SELECT exa_id, exa_desc FROM examen_odonto ORDER BY exa_id;");
        return $diag;
    }

    public function carga_dientes() {
        $dient = $this->sql("SELECT dient_nro, dient_ord FROM dientes where dient_ord<=15 order by dient_ord asc;");
        $pieza = $this->sql("SELECT piez_id, piez_diente, piez_nro FROM pieza where piez_id<=80 order by piez_id;");

        $params = array();
        $params[':pac_id'] = $_POST['adm'];

        $pieza_pro = $this->sql('SELECT
                            gramap_diente, gramap_pieza,
                            gramap_diag, gramap_fondo, gramap_borde
                            FROM grama_pieza
                            where
                            gramap_adm=:pac_id', $params);

        $dient_pro = $this->sql('SELECT
                            gramad_diente,
                            gramad_diag_raiz,
                            gramad_diag_coro,
                            gramad_diag_text
                            FROM grama_diente
                            where
                            gramad_adm=:pac_id', $params);
        foreach ($dient->data as $k => $value) {
            $value->pfondo1 = 'white';
            $value->pfondo2 = 'white';
            $value->pfondo3 = 'white';
            $value->pfondo4 = 'white';
            $value->pfondo5 = 'white';
            $value->pborde1 = 'black';
            $value->pborde2 = 'black';
            $value->pborde3 = 'black';
            $value->pborde4 = 'black';
            $value->pborde5 = 'black';
            foreach ($pieza->data as $k => $values) {
                foreach ($pieza_pro->data as $k => $val) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($values->piez_nro == 1) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 1) {
                                    $value->pfondo1 = $val->gramap_fondo;
                                    $value->pborde1 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 2) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 2) {
                                    $value->pfondo2 = $val->gramap_fondo;
                                    $value->pborde2 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 3) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 3) {
                                    $value->pfondo3 = $val->gramap_fondo;
                                    $value->pborde3 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 4) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 4) {
                                    $value->pfondo4 = $val->gramap_fondo;
                                    $value->pborde4 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 5) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 5) {
                                    $value->pfondo5 = $val->gramap_fondo;
                                    $value->pborde5 = $val->gramap_borde;
                                }
                            }
                        }
                    }
                }
                foreach ($dient_pro->data as $k => $valu) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($valu->gramad_diente == $value->dient_nro) {
                            if ($valu->gramad_diag_raiz == 9) {
                                $value->corona = 'blue';
                            } else if ($valu->gramad_diag_raiz == 10) {
                                $value->corona = 'red';
                            } else if ($valu->gramad_diag_raiz == 7) {
                                $value->placa = 'red';
                            } else if ($valu->gramad_diag_raiz == 3) {
                                $value->barra1 = 'blue';
                                $value->barra11 = '1';
                                $value->barra2 = 'blue';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 4) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                                $value->barra2 = 'red';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 5) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                            } else if ($valu->gramad_diag_raiz == 8) {
                                $value->barra3 = 'blue';
                                $value->barra33 = '1';
                            }
                            $value->dt_diag_text = ($valu->gramad_diag_text == null) ? '' : $valu->gramad_diag_text;
                        }
                    }
                }
            }
        }
        return $dient;
    }

    public function carga_dientes2() {
        $dient = $this->sql("SELECT dient_nro, dient_ord FROM dientes where dient_ord<=25 and dient_ord>=16 order by dient_ord asc;");
        $pieza = $this->sql("SELECT piez_id, piez_diente, piez_nro FROM pieza where piez_id>80 and piez_id<=130 order by piez_id;");

        $params = array();
        $params[':pac_id'] = $_POST['adm'];

        $pieza_pro = $this->sql('SELECT
                            gramap_diente, gramap_pieza,
                            gramap_diag, gramap_fondo, gramap_borde
                            FROM grama_pieza
                            where
                            gramap_adm=:pac_id', $params);

        $dient_pro = $this->sql('SELECT
                            gramad_diente,
                            gramad_diag_raiz,
                            gramad_diag_coro,
                            gramad_diag_text
                            FROM grama_diente
                            where
                            gramad_adm=:pac_id', $params);
        foreach ($dient->data as $k => $value) {
            $value->pfondo1 = 'white';
            $value->pfondo2 = 'white';
            $value->pfondo3 = 'white';
            $value->pfondo4 = 'white';
            $value->pfondo5 = 'white';
            $value->pborde1 = 'black';
            $value->pborde2 = 'black';
            $value->pborde3 = 'black';
            $value->pborde4 = 'black';
            $value->pborde5 = 'black';
            foreach ($pieza->data as $k => $values) {
                foreach ($pieza_pro->data as $k => $val) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($values->piez_nro == 1) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 1) {
                                    $value->pfondo1 = $val->gramap_fondo;
                                    $value->pborde1 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 2) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 2) {
                                    $value->pfondo2 = $val->gramap_fondo;
                                    $value->pborde2 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 3) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 3) {
                                    $value->pfondo3 = $val->gramap_fondo;
                                    $value->pborde3 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 4) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 4) {
                                    $value->pfondo4 = $val->gramap_fondo;
                                    $value->pborde4 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 5) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 5) {
                                    $value->pfondo5 = $val->gramap_fondo;
                                    $value->pborde5 = $val->gramap_borde;
                                }
                            }
                        }
                    }
                }
                foreach ($dient_pro->data as $k => $valu) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($valu->gramad_diente == $value->dient_nro) {
                            if ($valu->gramad_diag_raiz == 9) {
                                $value->corona = 'blue';
                            } else if ($valu->gramad_diag_raiz == 10) {
                                $value->corona = 'red';
                            } else if ($valu->gramad_diag_raiz == 7) {
                                $value->placa = 'red';
                            } else if ($valu->gramad_diag_raiz == 3) {
                                $value->barra1 = 'blue';
                                $value->barra11 = '1';
                                $value->barra2 = 'blue';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 4) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                                $value->barra2 = 'red';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 5) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                            } else if ($valu->gramad_diag_raiz == 8) {
                                $value->barra3 = 'blue';
                                $value->barra33 = '1';
                            }
                            $value->dt_diag_text = ($valu->gramad_diag_text == null) ? '' : $valu->gramad_diag_text;
                        }
                    }
                }
            }
        }
        return $dient;
    }

    public function carga_dientes3() {
        $dient = $this->sql("SELECT dient_nro, dient_ord FROM dientes where dient_ord<=35 and dient_ord>=26 order by dient_ord asc;");
        $pieza = $this->sql("SELECT piez_id, piez_diente, piez_nro FROM pieza where piez_id>130 and piez_id<=180 order by piez_id;");

        $params = array();
        $params[':pac_id'] = $_POST['adm'];

        $pieza_pro = $this->sql('SELECT
                            gramap_diente, gramap_pieza,
                            gramap_diag, gramap_fondo, gramap_borde
                            FROM grama_pieza
                            where
                            gramap_adm=:pac_id', $params);

        $dient_pro = $this->sql('SELECT
                            gramad_diente,
                            gramad_diag_raiz,
                            gramad_diag_coro,
                            gramad_diag_text
                            FROM grama_diente
                            where
                            gramad_adm=:pac_id', $params);
        foreach ($dient->data as $k => $value) {
            $value->pfondo1 = 'white';
            $value->pfondo2 = 'white';
            $value->pfondo3 = 'white';
            $value->pfondo4 = 'white';
            $value->pfondo5 = 'white';
            $value->pborde1 = 'black';
            $value->pborde2 = 'black';
            $value->pborde3 = 'black';
            $value->pborde4 = 'black';
            $value->pborde5 = 'black';
            foreach ($pieza->data as $k => $values) {
                foreach ($pieza_pro->data as $k => $val) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($values->piez_nro == 1) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 1) {
                                    $value->pfondo1 = $val->gramap_fondo;
                                    $value->pborde1 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 2) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 2) {
                                    $value->pfondo2 = $val->gramap_fondo;
                                    $value->pborde2 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 3) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 3) {
                                    $value->pfondo3 = $val->gramap_fondo;
                                    $value->pborde3 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 4) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 4) {
                                    $value->pfondo4 = $val->gramap_fondo;
                                    $value->pborde4 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 5) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 5) {
                                    $value->pfondo5 = $val->gramap_fondo;
                                    $value->pborde5 = $val->gramap_borde;
                                }
                            }
                        }
                    }
                }
                foreach ($dient_pro->data as $k => $valu) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($valu->gramad_diente == $value->dient_nro) {
                            if ($valu->gramad_diag_raiz == 9) {
                                $value->corona = 'blue';
                            } else if ($valu->gramad_diag_raiz == 10) {
                                $value->corona = 'red';
                            } else if ($valu->gramad_diag_raiz == 7) {
                                $value->placa = 'red';
                            } else if ($valu->gramad_diag_raiz == 3) {
                                $value->barra1 = 'blue';
                                $value->barra11 = '1';
                                $value->barra2 = 'blue';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 4) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                                $value->barra2 = 'red';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 5) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                            } else if ($valu->gramad_diag_raiz == 8) {
                                $value->barra3 = 'blue';
                                $value->barra33 = '1';
                            }
                            $value->dt_diag_text = ($valu->gramad_diag_text == null) ? '' : $valu->gramad_diag_text;
                        }
                    }
                }
            }
        }
        return $dient;
    }

    public function carga_dientes4() {
        $dient = $this->sql("SELECT dient_nro, dient_ord FROM dientes where  dient_ord>=36 order by dient_ord asc;");
        $pieza = $this->sql("SELECT piez_id, piez_diente, piez_nro FROM pieza where piez_id>180 order by piez_id;");

        $params = array();
        $params[':pac_id'] = $_POST['adm'];

        $pieza_pro = $this->sql('SELECT
                            gramap_diente, gramap_pieza,
                            gramap_diag, gramap_fondo, gramap_borde
                            FROM grama_pieza
                            where
                            gramap_adm=:pac_id', $params);

        $dient_pro = $this->sql('SELECT
                            gramad_diente,
                            gramad_diag_raiz,
                            gramad_diag_coro,
                            gramad_diag_text
                            FROM grama_diente
                            where
                            gramad_adm=:pac_id', $params);
        foreach ($dient->data as $k => $value) {
            $value->pfondo1 = 'white';
            $value->pfondo2 = 'white';
            $value->pfondo3 = 'white';
            $value->pfondo4 = 'white';
            $value->pfondo5 = 'white';
            $value->pborde1 = 'black';
            $value->pborde2 = 'black';
            $value->pborde3 = 'black';
            $value->pborde4 = 'black';
            $value->pborde5 = 'black';
            foreach ($pieza->data as $k => $values) {
                foreach ($pieza_pro->data as $k => $val) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($values->piez_nro == 1) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 1) {
                                    $value->pfondo1 = $val->gramap_fondo;
                                    $value->pborde1 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 2) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 2) {
                                    $value->pfondo2 = $val->gramap_fondo;
                                    $value->pborde2 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 3) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 3) {
                                    $value->pfondo3 = $val->gramap_fondo;
                                    $value->pborde3 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 4) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 4) {
                                    $value->pfondo4 = $val->gramap_fondo;
                                    $value->pborde4 = $val->gramap_borde;
                                }
                            }
                        }
                        if ($values->piez_nro == 5) {
                            if ($value->dient_nro == $val->gramap_diente) {
                                if ($val->gramap_pieza == 5) {
                                    $value->pfondo5 = $val->gramap_fondo;
                                    $value->pborde5 = $val->gramap_borde;
                                }
                            }
                        }
                    }
                }
                foreach ($dient_pro->data as $k => $valu) {
                    if ($values->piez_diente == $value->dient_nro) {
                        if ($valu->gramad_diente == $value->dient_nro) {
                            if ($valu->gramad_diag_raiz == 9) {
                                $value->corona = 'blue';
                            } else if ($valu->gramad_diag_raiz == 10) {
                                $value->corona = 'red';
                            } else if ($valu->gramad_diag_raiz == 7) {
                                $value->placa = 'red';
                            } else if ($valu->gramad_diag_raiz == 3) {
                                $value->barra1 = 'blue';
                                $value->barra11 = '1';
                                $value->barra2 = 'blue';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 4) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                                $value->barra2 = 'red';
                                $value->barra22 = '1';
                            } else if ($valu->gramad_diag_raiz == 5) {
                                $value->barra1 = 'red';
                                $value->barra11 = '1';
                            } else if ($valu->gramad_diag_raiz == 8) {
                                $value->barra3 = 'blue';
                                $value->barra33 = '1';
                            }
                            $value->dt_diag_text = ($valu->gramad_diag_text == null) ? '' : $valu->gramad_diag_text;
                        }
                    }
                }
            }
        }
        return $dient;
    }

    public function delete2() {
        $params = array();
        $params[':pac_id'] = $_POST['pac_id'];
        $params[':dient_nro'] = $_POST['dient_nro'];
        $q = 'delete from grama_diente where gramad_adm=:pac_id and gramad_diente=:dient_nro;
              delete from grama_pieza where gramap_adm=:pac_id and gramap_diente=:dient_nro;';
        return $this->sql($q, $params);
    }

    public function grama_pieza() {
        $params = array();
        $params[':pac_usu'] = $this->user->us_id;
        $params[':pac_id'] = $_POST['pac_id'];
        $params[':pieza'] = $_POST['pieza'];
        $params[':diente'] = $_POST['diente'];
        $params[':diag'] = $_POST['diag'];
        $params[':fondo'] = $_POST['fondo'];
        $params[':borde'] = $_POST['borde'];
        $this->begin();
        $usu = $this->user->us_id;
        $pac_id = $_POST['pac_id'];
        $diente = $_POST['diente'];
        $pieza = $_POST['pieza'];
        $diag = $_POST['diag'];
        $verifica = $this->sql("SELECT gramap_diag FROM grama_pieza where gramap_adm=$pac_id and gramap_diente=$diente and gramap_pieza=$pieza;");
        $verifica2 = $this->sql("SELECT * FROM grama_diente where gramad_adm=$pac_id and gramad_diente=$diente;");
        $q = 'INSERT INTO grama_pieza 
                    VALUES 
                    (NULL,
                     :pac_id,
                     :pac_usu,
                     now(),
                     :diente,
                     :pieza,
                     :diag,
                     :fondo,
                     :borde);';
        if ($verifica2->total == 0) {
            ($diag == 2) ? $params[':restauracion'] = $_POST['restauracion'] : $params[':restauracion'] = '';
            $q.= " INSERT INTO grama_diente
                    VALUES 
                    (NULL,
                     :pac_id,
                     :pac_usu,
                     now(),
                     :diente,
                     '',
                     :diag,
                     :restauracion);";
            if ($verifica->total == 0) {
                $this->commit();
                $sql1 = $this->sql($q, $params);
                return $sql1;
            }
        } else if ($verifica2->total > 0) {
            $diag2 = $verifica2->data[0]->gramad_diag_raiz;
            $diag3 = $verifica2->data[0]->gramad_diag_coro;
            if ($diag == 1) {
                if ($diag2 == 6 || $diag2 == 11 || $diag2 == 12 || $diag2 == 7 || $diag2 == 9 || $diag2 == 10 || $diag2 == 0) {
                    if ($diag3 == '1') {
                        if ($verifica->total == 0) {
                            $this->commit();
                            $sql2 = $this->sql($q, $params);
                            return $sql2;
                        }
                    } else if ($diag3 != '1') {
                        $q2 = "Update grama_diente set
                        gramad_usu='$usu', 
                        gramad_fech=now(),
                        gramad_diag_coro=$diag
                        where
                        gramad_adm=$pac_id and gramad_diente=$diente;";
                        if ($verifica->total == 0) {
                            $this->commit();
                            $this->sql($q2);
                            $sql2 = $this->sql($q, $params);
                            return $sql2;
                        }
                    }
                }
            } else if ($diag == 2) {
                if ($diag2 == 7 || $diag2 == 9 || $diag2 == 10 || $diag2 == 0) {
                    $diag4 = $verifica2->data[0]->gramad_diag_text;
                    if (strlen($diag4) > 0) {
                        if ($verifica->total == 0) {
                            $this->commit();
                            $sql2 = $this->sql($q, $params);
                            return $sql2;
                        }
                    } else if (strlen($diag4) == 0) {
                        $restauracion = $_POST['restauracion'];
                        $q2 = "Update grama_diente set
                        gramad_usu='$usu', 
                        gramad_fech=now(),
                        gramad_diag_text='$restauracion'
                        where
                        gramad_adm=$pac_id and gramad_diente=$diente;";
                        if ($verifica->total == 0) {
                            $this->commit();
                            $this->sql($q2);
                            $sql2 = $this->sql($q, $params);
                            return $sql2;
                        }
                    }
                }
            }
        }
    }

    public function grama_diente() {
        $params = array();
        $params[':pac_usu'] = $this->user->us_id;
        $params[':pac_id'] = $_POST['pac_id'];
        $params[':diente'] = $_POST['diente'];
        $params[':gramad_diag_raiz'] = $_POST['gramad_diag_raiz'];
        $params[':gramad_diag_coro'] = $_POST['gramad_diag_coro'];
        $params[':gramad_diag_text'] = $_POST['gramad_diag_text'];
        $this->begin();
        $usu = $this->user->us_id;
        $pac_id = $_POST['pac_id'];
        $diente = $_POST['diente'];
        $diag = $_POST['gramad_diag_raiz'];
        $texto = $_POST['gramad_diag_text'];

        $verifica = $this->sql("SELECT *
            FROM grama_diente
            where
            gramad_adm=$pac_id and gramad_diente=$diente;");
        if ($diag != 13) {
            if ($verifica->total == 0) {
                $q = 'INSERT INTO grama_diente
                    VALUES 
                    (NULL,
                     :pac_id,
                     :pac_usu,
                     now(),
                     :diente,
                     :gramad_diag_raiz,
                     :gramad_diag_coro,
                     :gramad_diag_text);';
                $this->commit();
                $sql1 = $this->sql($q, $params);
                return $sql1;
            } else if ($verifica->total > 0) {
                if ($verifica->data[0]->gramad_diag_coro == 2) {
                    if ($diag == 7 || $diag == 9 || $diag == 10) {
                        $q = "Update grama_diente set
                        gramad_usu='$usu', 
                        gramad_fech=now(),
                        gramad_diag_raiz=$diag
                        where
                        gramad_adm=$pac_id and gramad_diente=$diente;";
                        $this->commit();
                        $sql1 = $this->sql($q);
                        return $sql1;
                    }
                } else {
                    if ($diag == 6 || $diag == 11 || $diag == 12 || $diag == 7 || $diag == 9 || $diag == 10) {
                        $q = "Update grama_diente set
                        gramad_usu='$usu', 
                        gramad_fech=now(),
                        gramad_diag_raiz=$diag, 
                        gramad_diag_text='$texto'
                        where
                        gramad_adm=$pac_id and gramad_diente=$diente;";
                        $this->commit();
                        $sql1 = $this->sql($q);
                        return $sql1;
                    }
                }
            }
        }
    }

    public function load_dientes() {
        $params = array();
        $params[':adm'] = $_POST['adm'];
        $dient = $this->sql("SELECT pieza_id, pieza_nro FROM p_diente order by pieza_id");
        $regis_D = $this->sql("SELECT dient_id, dient_adm, dient_diente, dient_diag, dient_1, dient_2, dient_3, dient_4, dient_5
                                FROM diente where dient_adm=:adm;", $params);
        foreach ($dient->data as $e => $val) {
            $val->st = '0';
            $val->img_diag = '0';
            $val->ps1 = 'white';
            $val->ps2 = 'white';
            $val->ps3 = 'white';
            $val->ps4 = 'white';
            $val->ps5 = 'white';
            $val->dient_id = 'null';
            foreach ($regis_D->data as $k => $value) {
                if ($val->pieza_nro == $value->dient_diente) {
                    $val->st = 1;
                    $a = ($val->pieza_nro > 28) ? 'b' : 'a';
                    $b = strlen($value->dient_diag) > 0 ? $a . $value->dient_diag : 0;
                    $val->img_diag = $b;
                    if ($value->dient_1 == 27) {
                        $val->ps1 = 'red';
                    } elseif ($value->dient_1 == 28) {
                        $val->ps1 = 'blue';
                    } else {
                        $val->ps1 = 'white';
                    }
                    if ($value->dient_2 == 27) {
                        $val->ps2 = 'red';
                    } elseif ($value->dient_2 == 28) {
                        $val->ps2 = 'blue';
                    } else {
                        $val->ps2 = 'white';
                    }

                    if ($value->dient_3 == 27) {
                        $val->ps3 = 'red';
                    } elseif ($value->dient_3 == 28) {
                        $val->ps3 = 'blue';
                    } else {
                        $val->ps3 = 'white';
                    }

                    if ($value->dient_4 == 27) {
                        $val->ps4 = 'red';
                    } elseif ($value->dient_4 == 28) {
                        $val->ps4 = 'blue';
                    } else {
                        $val->ps4 = 'white';
                    }


                    if ($value->dient_5 == 27) {
                        $val->ps5 = 'red';
                    } elseif ($value->dient_5 == 28) {
                        $val->ps5 = 'blue';
                    } else {
                        $val->ps5 = 'white';
                    }
                    $val->dient_id = $value->dient_id;
                }
            }
        }
//        print_r($dient);
        return $dient;
    }


    public function diagnostico() {
        $params = array();
        $params[':dient_adm'] = isset($_POST['dient_adm']) ? $_POST['dient_adm'] : NULL; //caries1,ausente1,extraer1
        $query = 'SELECT
                    adm_id as adm,
                    sum(dient_diag=27) AS caries1,
                    sum(dient_diag=28) AS obturada1,
                    sum(dient_diag=29) AS ausente1,
                    sum(dient_diag=30) AS extraer1
                    FROM admision
                    left join diente on dient_adm=adm_id
                    where
                    adm_id=:dient_adm;';
        $q = $this->sql($query, $params);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_o_d_diag() {

        $params[':dient_adm'] = $_POST['dient_adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $params[':dient_diag'] = $_POST['dient_diag'];

//        $this->begin();
//        $adm = $_POST['adm'];
//        $diente = $_POST['dient_diente'];
//        $diag = $_POST['dient_diag'];
//        $verifica = $this->sql("SELECT dient_adm FROM diente where dient_adm=$adm and $diag;");
//
//        if ($verifica->total > 0) {
//            $this->rollback();
//            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
//        } else {
        $params[':dient_usu'] = $this->user->us_id;
        $params[':dient_st'] = '1';

        $q = 'insert into diente values (
            null,
            :dient_adm,
			:dient_usu,
			now(),
			:dient_st,
            :dient_diente,
            :dient_diag,
            null,
            null,
            null,
            null,
            null);';
//            $this->commit();
        return $this->sql($q, $params);
//        }
    }

    public function save_d_diag() {
        $params = array();

        $params[':dient_adm'] = $_POST['dient_adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $params[':dient_diag'] = $_POST['dient_diag'];

        $params[':dient_usu'] = $this->user->us_id;
        $params[':dient_st'] = '1';

        $q = 'insert into diente values (
            null,
            :dient_adm,
			:dient_usu,
			now(),
			:dient_st,
            :dient_diente,
            :dient_diag,
            null,
            null,
            null,
            null,
            null);';
        return $this->sql($q, $params);
    }


    public function save() {
        $params = array();
        $params[':odo_adm'] = $_POST['adm'];
        $params[':odo_sede'] = $this->user->con_sedid;
        $params[':odo_usu'] = $this->user->us_id;
        $params[':odo_st'] = '1';
        $params[':odo_calculos'] = $_POST['odo_calculos'];
        $params[':odon_placa'] = $_POST['odon_placa'];
        $params[':odo_gingivi'] = $_POST['odo_gingivi'];
        $params[':odo_desc'] = $_POST['odo_desc'];
        $params[':odo_obsr'] = $_POST['odo_obsr'];


        $this->begin();
        $adm = $_POST['adm'];
        $verifica = $this->sql("SELECT odo_adm,concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario FROM odontologia inner join sys_usuario on usu_id=odo_usu where odo_adm=$adm;");

        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $q = 'INSERT INTO odontologia VALUES
                (null,
                :odo_adm,
                :odo_sede,
                :odo_usu,
                now(),
                :odo_st,
                :odo_calculos,
                :odon_placa,
                :odo_gingivi,
                :odo_desc,
                :odo_obsr);';
            $this->commit();
            return $this->sql($q, $params);
        }
    }

    public function update_pz1() {
        $params = array();
        $params[':dient_adm'] = $_POST['adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $params[':dient_diag'] = $_POST['dient_diag'];
        $q = 'update diente set
              dient_1=:dient_diag
              where dient_adm=:dient_adm and dient_diente=:dient_diente';
        return $this->sql($q, $params);
    }

    public function update_pz2() {
        $params = array();
        $params[':dient_adm'] = $_POST['adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $params[':dient_diag'] = $_POST['dient_diag'];
        $q = 'update diente set
              dient_2=:dient_diag
              where dient_adm=:dient_adm and dient_diente=:dient_diente';
        return $this->sql($q, $params);
    }

    public function update_pz3() {
        $params = array();
        $params[':dient_adm'] = $_POST['adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $params[':dient_diag'] = $_POST['dient_diag'];
        $q = 'update diente set
              dient_3=:dient_diag
              where dient_adm=:dient_adm and dient_diente=:dient_diente';
        return $this->sql($q, $params);
    }

    public function update_pz4() {
        $params = array();
        $params[':dient_adm'] = $_POST['adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $params[':dient_diag'] = $_POST['dient_diag'];
        $q = 'update diente set
              dient_4=:dient_diag
              where dient_adm=:dient_adm and dient_diente=:dient_diente';
        return $this->sql($q, $params);
    }

    public function update_pz5() {
        $params = array();
        $params[':dient_adm'] = $_POST['adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $params[':dient_diag'] = $_POST['dient_diag'];
        $q = 'update diente set
              dient_5=:dient_diag
              where dient_adm=:dient_adm and dient_diente=:dient_diente';
        return $this->sql($q, $params);
    }

    public function delete() {
        $params = array();
        $params[':dient_adm'] = $_POST['adm'];
        $params[':dient_diente'] = $_POST['dient_diente'];
        $q = 'delete from diente where dient_adm=:dient_adm and dient_diente=:dient_diente';
        return $this->sql($q, $params);
    }

    public function update() {
        $params = array();
        $params[':odo_adm'] = $_POST['adm'];
        $params[':odo_id'] = $_POST['odo_id'];
        $params[':odo_usu'] = $this->user->us_id;
        $params[':odo_calculos'] = $_POST['odo_calculos'];
        $params[':odon_placa'] = $_POST['odon_placa'];
        $params[':odo_gingivi'] = $_POST['odo_gingivi'];
        $params[':odo_desc'] = $_POST['odo_desc'];
        $params[':odo_obsr'] = $_POST['odo_obsr'];
        $q = 'UPDATE odontologia SET
                odo_usu=:odo_usu,
                odo_calculos=:odo_calculos,
                odon_placa=:odon_placa,
                odo_gingivi=:odo_gingivi,
                odo_desc=:odo_desc,
                odo_obsr=:odo_obsr
                where odo_adm=:odo_adm and odo_id=:odo_id;';
        return $this->sql($q, $params);
    }

    public function diente_arriba() {
        $sql = "SELECT
				pieza_nro
				FROM p_diente
				where
				pieza_id<17
                                order by pieza_id";
        return $this->sql($sql);
    }

    public function diag_arriba($adm) {
        $sql = "SELECT dient_diente, dient_diag, dient_1, dient_2, dient_3, dient_4, dient_5 FROM diente
				where
				dient_adm=$adm
				and dient_diente<30";
        return $this->sql($sql);
    }

    public function diente_abajo() {
        $sql = "SELECT
				pieza_nro
				FROM p_diente
				where
				pieza_id>16
                                order by pieza_id";
        return $this->sql($sql);
    }

    public function diag_abajo($adm) {
        $sql = "SELECT dient_diente, dient_diag, dient_1, dient_2, dient_3, dient_4, dient_5 FROM diente
				where
				dient_adm=$adm
				and dient_diente>30";
        return $this->sql($sql);
    }

    public function rpt($adm) {
        $sede = $this->user->con_sedid;
        $sql = $this->sql("SELECT adm_id as adm,
concat(pac_appat,' ',pac_apmat,', ',pac_nombres )as nombres,pac_ndoc,pac_sexo,adm_fechc,adm_act,emp_desc,TIMESTAMPDIFF(YEAR,pac_nacfec,CURRENT_DATE) as edad,tfi_desc,
odo_desc, odo_obsr,
(select count(dient_diente) FROM diente where dient_diag=27 and dient_adm=adm) as caries,
(select count(dient_diente) FROM diente where dient_diag=29 and dient_adm=adm) as ausente,
(select count(dient_diente) FROM diente where dient_diag=30 and dient_adm=adm) as extraer
FROM odontologia
inner join admision on adm_id=odo_adm
inner join paciente on pac_id=adm_pacid
inner join pack on pk_id=adm_pkid
inner join empresa on emp_id=pk_empid
inner join tficha on tfi_id=adm_tfiid
where odo_adm=$adm and odo_sede=$sede;");
//        print_r($sql);// audio_a_oi_diag, audio_a_od_diag
        return $sql;
    }

    public function list_reco() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT reco_id,reco_adm, reco_desc
                FROM reco
                where reco_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function busca_reco() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT reco_desc FROM reco
                            where
                            reco_desc like '%$query%'
                            group by reco_desc");
        return $sql;
    }

    public function save_reco() {
        $params = array();
        $params[':reco_adm'] = $_POST['reco_adm'];
        $params[':reco_usu'] = $this->user->us_id;
        $params[':reco_st'] = '1';
        $params[':reco_desc'] = $_POST['reco_desc'];

        $q = 'INSERT INTO reco VALUES 
                (NULL,
                :reco_adm,
                now(),
                :reco_usu,
                :reco_st,
                UPPER(:reco_desc))';
        return $this->sql($q, $params);
    }

    public function update_reco() {
        $params = array();
        $params[':reco_id'] = $_POST['reco_id'];
        $params[':reco_adm'] = $_POST['reco_adm'];
        $params[':reco_usu'] = $this->user->us_id;
        $params[':reco_desc'] = $_POST['reco_desc'];

        $this->begin();
        $q = 'Update reco set
                reco_fech=now(),
                reco_usu=:reco_usu,
                reco_desc=UPPER(:reco_desc)
                where
                reco_id=:reco_id and reco_adm=:reco_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $reco_adm = $_POST['reco_adm'];
            $this->commit();
            return array('success' => true, 'data' => $reco_adm);
        } else {
            $this->rollback();
        }
    }

    public function load_reco() {
        $reco_id = $_POST['reco_id'];
        $reco_adm = $_POST['reco_adm'];
        $query = "SELECT
            reco_id, reco_adm, reco_desc
            FROM reco
            where
            reco_id=$reco_id and
            reco_adm='$reco_adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function list_cie10() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT cie4_id, cie4_cie3id
                , concat(cie4_id,' - ',cie4_desc) cie4_desc
                FROM cie4
                where
                concat(cie4_id, cie4_cie3id, cie4_desc) like'%$query%'
                order by cie4_cie3id;");
        return $sql;
    }

    public function list_pato() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT gpato_diente, gpato_desc
                FROM grama_pato
                where gpato_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_trata() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT trata_id,trata_adm, trata_desc
                FROM trata
                where trata_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function busca_trata() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT trata_desc FROM trata
                            where
                            trata_desc like '%$query%'
                            group by trata_desc");
        return $sql;
    }

    public function save_trata() {
        $params = array();
        $params[':trata_adm'] = $_POST['trata_adm'];
        $params[':trata_usu'] = $this->user->us_id;
        $params[':trata_st'] = '1';
        $params[':trata_desc'] = $_POST['trata_desc'];

        $q = 'INSERT INTO trata VALUES 
                (NULL,
                :trata_adm,
                now(),
                :trata_usu,
                :trata_st,
                UPPER(:trata_desc))';
        return $this->sql($q, $params);
    }

    public function update_trata() {
        $params = array();
        $params[':trata_id'] = $_POST['trata_id'];
        $params[':trata_adm'] = $_POST['trata_adm'];
        $params[':trata_usu'] = $this->user->us_id;
        $params[':trata_desc'] = $_POST['trata_desc'];

        $this->begin();
        $q = 'Update trata set
                trata_fech=now(),
                trata_usu=:trata_usu,
                trata_desc=UPPER(:trata_desc)
                where
                trata_id=:trata_id and trata_adm=:trata_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $trata_adm = $_POST['trata_adm'];
            $this->commit();
            return array('success' => true, 'data' => $trata_adm);
        } else {
            $this->rollback();
        }
    }

    public function load_trata() {
        $trata_id = $_POST['trata_id'];
        $trata_adm = $_POST['trata_adm'];
        $query = "SELECT
            trata_id, trata_adm, trata_desc
            FROM trata
            where
            trata_id=$trata_id and
            trata_adm='$trata_adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_odonto() {
        $params = array();
        $params[':odo_usu'] = $this->user->us_id;
        $params[':odo_adm'] = $_POST['adm'];
        $params[':odo_st'] = '1';
        $this->begin();
        $adm = $_POST['adm'];
        $verifica = $this->sql("SELECT odo_adm FROM odonto where odo_adm=$adm;");
        if ($verifica->total == 0) {
            $q = 'INSERT INTO odonto VALUES 
                (NULL,
                :odo_adm,
                now(),
                :odo_usu,
                :odo_st)';
            $this->commit();
            return $this->sql($q, $params);
        }
    }

    public function update_odonto() {
        $params = array();
        $params[':odo_usu'] = $this->user->us_id;
        $params[':odo_adm'] = $_POST['adm'];
        $q = 'update odonto set
              odo_usu=:odo_usu,
              odo_fech=now()
              where odo_adm=:odo_adm;';
        return $this->sql($q, $params);
    }

    public function savepato() {
        $params = array();
        $params[':gpato_usu'] = $this->user->us_id;
        $params[':gpato_adm'] = $_POST['adm'];
        $params[':gpato_diente'] = $_POST['odo_pieza'];
        $params[':gpato_desc'] = $_POST['odo_cie1'];

        $q = 'INSERT INTO grama_pato VALUES 
                (NULL,
                :gpato_adm,
                :gpato_usu,
                now(),
                :gpato_diente,
                UPPER(:gpato_desc))';
        return $this->sql($q, $params);
    }

    public function datos_report($adm) {
        $sql = $this->sql("SELECT
            adm_id AS NRO,emp_desc AS EMPRESA
            ,Date_format(adm_fechc,'%d-%m-%Y') AS FECHA,pac_ndoc,
            concat(pac_appat,' ',pac_apmat,', ',pac_nombres)as NOMBRES
            , IF(pac_sexo='M','MASCULINO','FEMENINO') AS SEXO,tfi_desc AS FICHA,upper(adm_act) adm_act
            ,TIMESTAMPDIFF(YEAR,pac_nacfec,CURRENT_DATE) as edad
            FROM admision
            inner join pack on pk_id=adm_pkid
            inner join empresa on emp_id=pk_empid
            inner join paciente on pac_id=adm_pacid
            inner join tficha on tfi_id=adm_tfiid
            WHERE adm_id=$adm
            group by adm_id order by adm_id;");
        return $sql;
    }

    public function diente_1() {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=15 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_2() {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=25 and dient_ord>=16 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_3() {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=35 and dient_ord>=26 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_4() {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=51 and dient_ord>=36 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_txt($adm) {
        $sql = "SELECT gramad_diente, gramad_diag_raiz, gramad_diag_coro
                , gramad_diag_text FROM grama_diente 
                where gramad_adm=$adm
                order by gramad_diente;";
        return $this->sql($sql);
    }

    public function diente_txt2($adm) {
        $sql = "SELECT gramad_diente, gramad_diag_raiz, gramad_diag_coro
                , gramad_diag_text FROM grama_diente 
                where gramad_diag_raiz in(3,4,5,8,10,9,7) and gramad_adm=$adm
                order by gramad_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc1($adm) {
        $sql = "SELECT
                gramap_diente, gramap_pieza, gramap_diag, gramap_fondo, gramap_borde
                FROM grama_pieza
                where
                gramap_adm=$adm
                and gramap_diente<=28
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc2($adm) {
        $sql = "SELECT * FROM grama_pieza where
                gramap_adm=$adm
                and gramap_diente>=51
                and gramap_diente<=65
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc3($adm) {
        $sql = "SELECT
                gramap_diente, gramap_pieza, gramap_diag, gramap_fondo, gramap_borde
                FROM grama_pieza
                where
                gramap_adm=$adm
                and gramap_diente>=75 and gramap_diente<=85
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc4($adm) {
        $sql = "SELECT
                gramap_diente, gramap_pieza, gramap_diag, gramap_fondo, gramap_borde
                FROM grama_pieza
                where
                gramap_adm=$adm
                and gramap_diente>=30 and gramap_diente<=48
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function grama_pato($adm) {
        $q = "SELECT gpato_diente, upper(gpato_desc) gpato_desc FROM grama_pato where gpato_adm=$adm";
        return $this->sql($q);
    }

    public function caries($adm) {
        $q = "SELECT count(gramad_diag_coro)caries FROM grama_diente where gramad_diag_coro in(1) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function extraer($adm) {
        $q = "SELECT count(gramad_diag_raiz)extraer FROM grama_diente where gramad_diag_raiz in(4) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function pieza_caries($adm) {
        $q = "SELECT gramad_diente, gramad_diag_coro FROM grama_diente where gramad_diag_coro in(1) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function pieza_extraer($adm) {
        $q = "SELECT gramad_diente, gramad_diag_raiz FROM grama_diente where gramad_diag_raiz in(3,4) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function recomendaciones($adm) {
        $q = "SELECT upper(reco_desc) reco_desc FROM reco where reco_adm=$adm";
        return $this->sql($q);
    }

    public function tratamiento($adm) {
        $q = "SELECT upper(trata_desc) trata_desc FROM trata where trata_adm=$adm";
        return $this->sql($q);
    }

}

//$sesion = new model();
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
