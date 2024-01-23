<?php

class model extends core
{
    public function list_paciente()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $sql1 = ("SELECT
                    adm_id as adm,adm_foto
                    ,tfi_desc,emp_desc, pac_ndoc
                    ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
                    ,concat(adm_puesto,' - ',adm_area)as puesto,tfi_desc
                    ,concat(pac_appat,' ',pac_apmat,' ',pac_nombres)as nombre
                    ,concat(pac_nombres)as nom
                    ,concat(pac_appat,' ',pac_apmat)as ape
                    ,pac_sexo, adm_fech fecha
                    ,count(adm_id) nro_examenes
                    #,if((aud_pdf)=1,1,0) pdf
                    FROM admision
                    inner join paciente on adm_pac=pac_id
                    inner join pack on adm_ruta=pk_id
                    left join empresa on pk_emp=emp_id
                    left join tficha on adm_tficha=tfi_id
                    inner join dpack on dpk_pkid=pk_id
                    inner join examen on ex_id=dpk_exid
                    where
                    ex_arid IN (8)
                    ");
        $empresa = $this->user->empresas;
        ($this->user->acceso == 1) ? '' : $sql1 .= "and emp_id IN ($empresa) ";
        if (!is_null($columna) && !is_null($query)) {
            if ($columna == "1") {
                $sql1 .= "and adm_id=$query";
            } else if ($columna == "2") {
                $sql1 .= "and pac_ndoc=$query";
            } else if ($columna == "3") {
                $sql1 .= "and concat(pac_appat,' ',pac_apmat,' ',pac_nombres) like '%$query%'";
            } else if ($columna == "4" && $this->user->acceso == 1) {
                $sql1 .= "and emp_desc like '%$query%' or emp_id like '%$query%'";
            } else if ($columna == "5") {
                $sql1 .= "and tfi_desc LIKE '%$query%'";
            }
        }
        $sql1 .= " group by adm_id order by adm_id DESC;";
        $sql = $this->sql($sql1);
        foreach ($sql->data as $i => $value) {
            $adm_id = $value->adm;
            $nro_examenes = $value->nro_examenes;
            $verifica = $this->sql("SELECT count(m_odonto_adm)total FROM mod_odonto where m_odonto_adm=$adm_id;");
            $total = $verifica->data[0]->total;
            $value->st = ($nro_examenes == $total) ? '1' : '0';
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_formatos()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm_id = isset($_POST['adm']) ? $_POST['adm'] : NULL;
        $q = "SELECT adm_id as adm, ex_desc,pk_id,ex_id,pac_sexo
                FROM admision
                inner join pack on adm_ruta=pk_id
                inner join dpack on dpk_pkid=pk_id
                inner join examen on ex_id=dpk_exid
                inner join paciente on pac_id=adm_pac
                where ex_arid IN (8) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_odonto_id id,m_odonto_st st
                , m_odonto_usu usu, m_odonto_fech_reg fech 
            FROM mod_odonto 
            where m_odonto_adm=$adm_id and m_odonto_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE mod_espiro_metria

    public function carga_pdf_espiro_metria($adm)
    {
        $query = "SELECT * FROM mod_espiro_metria
                    where m_odonto_metria_adm='$adm';";
        return $this->sql($query);
    }

    public function load_espiro_metria()
    {
        $adm = $_POST['adm'];
        $query = "SELECT * FROM mod_espiro_metria where m_odonto_metria_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_espiro_metria()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_espiro VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                1,
                :ex_id);";

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_odonto_metria_fuma'] = $_POST['m_odonto_metria_fuma'];
        $params_2[':m_odonto_metria_cap_vital'] = $_POST['m_odonto_metria_cap_vital'];
        $params_2[':m_odonto_metria_FVC'] = $_POST['m_odonto_metria_FVC'];
        $params_2[':m_odonto_metria_FEV1'] = $_POST['m_odonto_metria_FEV1'];
        $params_2[':m_odonto_metria_FEV1_FVC'] = $_POST['m_odonto_metria_FEV1_FVC'];
        $params_2[':m_odonto_metria_PEF'] = $_POST['m_odonto_metria_PEF'];
        $params_2[':m_odonto_metria_FEF2575'] = $_POST['m_odonto_metria_FEF2575'];
        $params_2[':m_odonto_metria_recomendacion'] = $_POST['m_odonto_metria_recomendacion'];
        $params_2[':m_odonto_metria_conclusion'] = $_POST['m_odonto_metria_conclusion'];
        $params_2[':m_odonto_metria_cie10'] = $_POST['m_odonto_metria_cie10'];
        $params_2[':m_odonto_metria_diag'] = $_POST['m_odonto_metria_diag'];

        $q_2 = "INSERT INTO mod_espiro_metria VALUES 
                (null,
                :adm,
                :m_odonto_metria_fuma,
                :m_odonto_metria_cap_vital,
                :m_odonto_metria_FVC,
                :m_odonto_metria_FEV1,
                :m_odonto_metria_FEV1_FVC,
                :m_odonto_metria_PEF,
                :m_odonto_metria_FEF2575,
                :m_odonto_metria_recomendacion,
                :m_odonto_metria_conclusion,
                :m_odonto_metria_cie10,
                :m_odonto_metria_diag,
                null
                );";

        $verifica = $this->sql("SELECT 
                m_odonto_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
                FROM mod_espiro 
                inner join sys_usuario on usu_id=m_odonto_usu 
                where 
                m_odonto_adm='$adm' and m_odonto_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    $this->commit();
                    return $sql_2;
                } else {
                    $this->rollback();
                    return array('success' => false, 'error' => 'Problemas con el registro.');
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        }
    }

    public function update_espiro_metria()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_espiro set
                    m_odonto_usu=:usuario,
                    m_odonto_fech_update=now()
                where
                m_odonto_id=:id and m_odonto_adm=:adm and m_odonto_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_odonto_metria_fuma'] = $_POST['m_odonto_metria_fuma'];
        $params_2[':m_odonto_metria_cap_vital'] = $_POST['m_odonto_metria_cap_vital'];
        $params_2[':m_odonto_metria_FVC'] = $_POST['m_odonto_metria_FVC'];
        $params_2[':m_odonto_metria_FEV1'] = $_POST['m_odonto_metria_FEV1'];
        $params_2[':m_odonto_metria_FEV1_FVC'] = $_POST['m_odonto_metria_FEV1_FVC'];
        $params_2[':m_odonto_metria_PEF'] = $_POST['m_odonto_metria_PEF'];
        $params_2[':m_odonto_metria_FEF2575'] = $_POST['m_odonto_metria_FEF2575'];
        $params_2[':m_odonto_metria_recomendacion'] = $_POST['m_odonto_metria_recomendacion'];
        $params_2[':m_odonto_metria_conclusion'] = $_POST['m_odonto_metria_conclusion'];
        $params_2[':m_odonto_metria_cie10'] = $_POST['m_odonto_metria_cie10'];
        $params_2[':m_odonto_metria_diag'] = $_POST['m_odonto_metria_diag'];

        $q_2 = 'Update mod_espiro_metria set
                    m_odonto_metria_fuma=:m_odonto_metria_fuma,
                    m_odonto_metria_cap_vital=:m_odonto_metria_cap_vital,
                    m_odonto_metria_FVC=:m_odonto_metria_FVC,
                    m_odonto_metria_FEV1=:m_odonto_metria_FEV1,
                    m_odonto_metria_FEV1_FVC=:m_odonto_metria_FEV1_FVC,
                    m_odonto_metria_PEF=:m_odonto_metria_PEF,
                    m_odonto_metria_FEF2575=:m_odonto_metria_FEF2575,
                    m_odonto_metria_recomendacion=:m_odonto_metria_recomendacion,
                    m_odonto_metria_conclusion=:m_odonto_metria_conclusion,
                    m_odonto_metria_cie10=:m_odonto_metria_cie10,
                    m_odonto_metria_diag=:m_odonto_metria_diag
                where
                m_odonto_metria_adm=:adm;';

        $sql_2 = $this->sql($q_2, $params_2);
        if ($sql_2->success) {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success && $sql_1->total == 1) {
                $this->commit();
                return $sql_1;
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    public function load_diag()
    {
        $diag = $this->sql("SELECT exa_id, exa_desc FROM examen_odonto ORDER BY exa_id;");
        return $diag;
    }

    public function diente_1()
    {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=15 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_2()
    {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=25 and dient_ord>=16 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_3()
    {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=35 and dient_ord>=26 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_4()
    {
        $sql = "SELECT dient_nro, dient_pose FROM dientes where dient_ord<=51 and dient_ord>=36 order by dient_ord asc;";
        return $this->sql($sql);
    }

    public function diente_txt($adm)
    {
        $sql = "SELECT gramad_diente, gramad_diag_raiz, gramad_diag_coro
                , gramad_diag_text FROM grama_diente 
                where gramad_adm=$adm
                order by gramad_diente;";
        return $this->sql($sql);
    }

    public function diente_txt2($adm)
    {
        $sql = "SELECT gramad_diente, gramad_diag_raiz, gramad_diag_coro
                , gramad_diag_text FROM grama_diente 
                where gramad_diag_raiz in(3,4,5,8,10,9,7) and gramad_adm=$adm
                order by gramad_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc1($adm)
    {
        $sql = "SELECT
                gramap_diente, gramap_pieza, gramap_diag, gramap_fondo, gramap_borde
                FROM grama_pieza
                where
                gramap_adm=$adm
                and gramap_diente<=28
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc2($adm)
    {
        $sql = "SELECT * FROM grama_pieza where
                gramap_adm=$adm
                and gramap_diente>=51
                and gramap_diente<=65
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc3($adm)
    {
        $sql = "SELECT
                gramap_diente, gramap_pieza, gramap_diag, gramap_fondo, gramap_borde
                FROM grama_pieza
                where
                gramap_adm=$adm
                and gramap_diente>=75 and gramap_diente<=85
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function diente_pieza_desc4($adm)
    {
        $sql = "SELECT
                gramap_diente, gramap_pieza, gramap_diag, gramap_fondo, gramap_borde
                FROM grama_pieza
                where
                gramap_adm=$adm
                and gramap_diente>=30 and gramap_diente<=48
                order by gramap_diente;";
        return $this->sql($sql);
    }

    public function grama_pato($adm)
    {
        $q = "SELECT gpato_diente, upper(gpato_desc) gpato_desc FROM grama_pato where gpato_adm=$adm";
        return $this->sql($q);
    }

    public function caries($adm)
    {
        $q = "SELECT count(gramad_diag_coro)caries FROM grama_diente where gramad_diag_coro in(1) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function extraer($adm)
    {
        $q = "SELECT count(gramad_diag_raiz)extraer FROM grama_diente where gramad_diag_raiz in(4) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function pieza_caries($adm)
    {
        $q = "SELECT gramad_diente, gramad_diag_coro FROM grama_diente where gramad_diag_coro in(1) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function pieza_extraer($adm)
    {
        $q = "SELECT gramad_diente, gramad_diag_raiz FROM grama_diente where gramad_diag_raiz in(3,4) and gramad_adm=$adm order by gramad_diente desc;";
        return $this->sql($q);
    }

    public function recomendaciones($adm)
    {
        $q = "SELECT upper(odonto_reco_desc) reco_desc FROM odonto_recomendacion where odonto_reco_adm=$adm";
        return $this->sql($q);
    }

    public function tratamiento($adm)
    {
        $q = "SELECT upper(odonto_trata_desc) trata_desc FROM odonto_tratamiento where odonto_trata_adm=$adm";
        return $this->sql($q);
    }


    public function carga_dientes()
    {
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

    public function carga_dientes2()
    {
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

    public function carga_dientes3()
    {
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

    public function carga_dientes4()
    {
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

    public function delete2()
    {
        $params = array();
        $params[':pac_id'] = $_POST['pac_id'];
        $params[':dient_nro'] = $_POST['dient_nro'];
        $q = 'delete from grama_diente where gramad_adm=:pac_id and gramad_diente=:dient_nro;
              delete from grama_pieza where gramap_adm=:pac_id and gramap_diente=:dient_nro;';
        return $this->sql($q, $params);
    }

    public function grama_pieza()
    {
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
            $q .= " INSERT INTO grama_diente
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

    public function grama_diente()
    {
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

    public function load_dientes()
    {
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

    // --
    // -- Estructura de tabla para la tabla `odonto_tratamiento`
    // --

    public function list_trata()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT odonto_trata_id,odonto_trata_adm, odonto_trata_desc
                FROM odonto_tratamiento
                where odonto_trata_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function busca_trata()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT odonto_trata_desc FROM odonto_tratamiento
                            where
                            odonto_trata_desc like '%$query%'
                            group by odonto_trata_desc");
        return $sql;
    }

    public function save_trata()
    {
        $params = array();
        $params[':odonto_trata_adm'] = $_POST['odonto_trata_adm'];
        $params[':odonto_trata_usu'] = $this->user->us_id;
        $params[':odonto_trata_st'] = '1';
        $params[':odonto_trata_desc'] = $_POST['odonto_trata_desc'];

        $q = 'INSERT INTO odonto_tratamiento VALUES 
                (NULL,
                :odonto_trata_adm,
                now(),
                :odonto_trata_usu,
                :odonto_trata_st,
                UPPER(:odonto_trata_desc))';
        return $this->sql($q, $params);
    }

    public function update_trata()
    {
        $params = array();
        $params[':odonto_trata_id'] = $_POST['odonto_trata_id'];
        $params[':odonto_trata_adm'] = $_POST['odonto_trata_adm'];
        $params[':odonto_trata_usu'] = $this->user->us_id;
        $params[':odonto_trata_desc'] = $_POST['odonto_trata_desc'];

        $this->begin();
        $q = 'Update odonto_tratamiento set
                odonto_trata_fech=now(),
                odonto_trata_usu=:odonto_trata_usu,
                odonto_trata_desc=UPPER(:odonto_trata_desc)
                where
                odonto_trata_id=:odonto_trata_id and odonto_trata_adm=:odonto_trata_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $odonto_trata_adm = $_POST['odonto_trata_adm'];
            $this->commit();
            return array('success' => true, 'data' => $odonto_trata_adm);
        } else {
            $this->rollback();
        }
    }

    public function load_trata()
    {
        $odonto_trata_id = $_POST['odonto_trata_id'];
        $odonto_trata_adm = $_POST['odonto_trata_adm'];
        $query = "SELECT
            odonto_trata_id, odonto_trata_adm, odonto_trata_desc
            FROM odonto_tratamiento
            where
            odonto_trata_id=$odonto_trata_id and
            odonto_trata_adm='$odonto_trata_adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    // --
    // -- Estructura de tabla para la tabla `odonto_recomendacion`
    // --

    public function list_reco()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT odonto_reco_id,odonto_reco_adm, odonto_reco_desc
                FROM odonto_recomendacion
                where odonto_reco_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function busca_reco()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT odonto_reco_desc FROM odonto_recomendacion
                            where
                            odonto_reco_desc like '%$query%'
                            group by odonto_reco_desc");
        return $sql;
    }

    public function save_reco()
    {
        $params = array();
        $params[':odonto_reco_adm'] = $_POST['odonto_reco_adm'];
        $params[':odonto_reco_usu'] = $this->user->us_id;
        $params[':odonto_reco_st'] = '1';
        $params[':odonto_reco_desc'] = $_POST['odonto_reco_desc'];

        $q = 'INSERT INTO odonto_recomendacion VALUES 
                (NULL,
                :odonto_reco_adm,
                now(),
                :odonto_reco_usu,
                :odonto_reco_st,
                UPPER(:odonto_reco_desc))';
        return $this->sql($q, $params);
    }

    public function update_reco()
    {
        $params = array();
        $params[':odonto_reco_id'] = $_POST['odonto_reco_id'];
        $params[':odonto_reco_adm'] = $_POST['odonto_reco_adm'];
        $params[':odonto_reco_usu'] = $this->user->us_id;
        $params[':odonto_reco_desc'] = $_POST['odonto_reco_desc'];

        $this->begin();
        $q = 'Update odonto_recomendacion set
                odonto_reco_fech=now(),
                odonto_reco_usu=:odonto_reco_usu,
                odonto_reco_desc=UPPER(:odonto_reco_desc)
                where
                odonto_reco_id=:odonto_reco_id and odonto_reco_adm=:odonto_reco_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $odonto_reco_adm = $_POST['odonto_reco_adm'];
            $this->commit();
            return array('success' => true, 'data' => $odonto_reco_adm);
        } else {
            $this->rollback();
        }
    }

    public function load_reco()
    {
        $odonto_reco_id = $_POST['odonto_reco_id'];
        $odonto_reco_adm = $_POST['odonto_reco_adm'];
        $query = "SELECT
            odonto_reco_id, odonto_reco_adm, odonto_reco_desc
            FROM odonto_recomendacion
            where
            odonto_reco_id=$odonto_reco_id and
            odonto_reco_adm='$odonto_reco_adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    /////////////////////////////////

    public function list_cie10()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT cie4_id, cie4_cie3id
                , concat(cie4_id,' - ',cie4_desc) cie4_desc
                FROM cie4
                where
                concat(cie4_id, cie4_cie3id, cie4_desc) like'%$query%'
                order by cie4_cie3id;");
        return $sql;
    }

    public function list_pato()
    {
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
    public function savepato()
    {
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

    public function save_odonto()
    {
        $adm = $_POST['adm'];

        $this->begin();

        $params = array();
        $params[':adm'] = $_POST['adm'];
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':ex_id'] = 12;

        $verifica = $this->sql("SELECT m_odonto_adm FROM mod_odonto where m_odonto_adm=$adm;");
        if ($verifica->total == 0) {
            $q = "INSERT INTO mod_odonto VALUES
                    (NULL,
                    :adm,
                    :sede,
                    :usuario,
                    now(),
                    null,
                    1,
                    :ex_id);";
            $this->commit();
            return $this->sql($q, $params);
        }
    }

    public function update_odonto()
    {
        $params = array();
        $params[':m_odonto_usu'] = $this->user->us_id;
        $params[':m_odonto_adm'] = $_POST['adm'];
        $q = 'update mod_odonto set
                m_odonto_usu=:m_odonto_usu,
                m_odonto_fech_update=now()
                where m_odonto_adm=:m_odonto_adm and m_odonto_examen=12;';
        return $this->sql($q, $params);
    }

    public function datos_report($adm)
    {
        return $this->sql("SELECT
        adm_id AS NRO,emp_desc AS EMPRESA
        ,Date_format(adm_fech,'%d-%m-%Y') AS FECHA,pac_ndoc,
        concat(pac_appat,' ',pac_apmat,', ',pac_nombres)as NOMBRES
        , IF(pac_sexo='M','MASCULINO','FEMENINO') AS SEXO,tfi_desc AS FICHA,concat(adm_puesto,' - ',adm_area)as adm_act
        ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
        FROM admision
        inner join pack on pk_id=adm_ruta
        inner join empresa on emp_id=pk_emp
        inner join paciente on pac_id=adm_pac
        inner join tficha on tfi_id=adm_tficha
        WHERE adm_id=$adm
        group by adm_id order by adm_id;");
    }
}
