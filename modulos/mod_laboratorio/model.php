<?php

class model extends core {

    public function list_paciente() {
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
                    #filtro que verifica que si es masculino para no tomar examenes femeninos
                    if(pac_sexo='M', ex_arid IN (10) and ex_id not in(57,50), ex_arid IN (10))
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
            $verifica = $this->sql("SELECT count(m_laboratorio_adm)total FROM mod_laboratorio where m_laboratorio_adm=$adm_id;");
            $total = $verifica->data[0]->total;
            $value->st = ($nro_examenes == $total) ? '1' : '0';
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_formatos() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm_id = isset($_POST['adm']) ? $_POST['adm'] : NULL;
        $q = "SELECT adm_id as adm, ex_desc,pk_id,ex_id,pac_sexo
                FROM admision
                inner join pack on adm_ruta=pk_id
                inner join dpack on dpk_pkid=pk_id
                inner join examen on ex_id=dpk_exid
                inner join paciente on pac_id=adm_pac
                where 
                #filtro que verifica que si es masculino para no tomar examenes femeninos
                if(pac_sexo='M', ex_arid IN (10) and ex_id not in(57,50), ex_arid IN (10))
                and adm_id=$adm_id order by ex_desc;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_laboratorio_id id,m_laboratorio_st st
                , m_laboratorio_usu usu, m_laboratorio_fech_reg fech 
            FROM mod_laboratorio 
            where m_laboratorio_adm=$adm_id and m_laboratorio_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //INICIO LOAD SAVE UPDATE LABORATORIO

    public function load_examenLab() {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_laboratorio_exam 
            inner join lab_conf on labc_ex=m_lab_exam_examen
                where m_lab_exam_adm='$adm' and m_lab_exam_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_exaLab() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_laboratorio VALUES
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
        $params_2[':ex_id'] = $_POST['ex_id'];
        $params_2[':m_lab_exam_resultado'] = $_POST['m_lab_exam_resultado'];
        $params_2[':m_lab_exam_observaciones'] = $_POST['m_lab_exam_observaciones'];
        $params_2[':m_lab_exam_diagnostico'] = $_POST['m_lab_exam_diagnostico'];


        $q_2 = "INSERT INTO mod_laboratorio_exam VALUES 
                (null,
                :adm,
                :ex_id,
                :m_lab_exam_resultado,
                :m_lab_exam_observaciones,
                :m_lab_exam_diagnostico);";




        $verifica = $this->sql("SELECT 
		m_laboratorio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_laboratorio 
		inner join sys_usuario on usu_id=m_laboratorio_usu 
		where 
		m_laboratorio_adm='$adm' and m_laboratorio_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    if ($_POST['modificar'] == 'on') {
                        $params_3 = array();
                        $params_3[':labc_uni'] = $_POST['labc_uni'];
                        $params_3[':labc_valor'] = $_POST['labc_valor'];
                        $params_3[':ex_id'] = $_POST['ex_id'];

                        $q_3 = "Update lab_conf set
                                    labc_uni=:labc_uni,
                                    labc_valor=:labc_valor
                                where labc_ex=:ex_id;";
                        $sql_3 = $this->sql($q_3, $params_3);
                        if ($sql_3->success) {
                            $this->commit();
                            return $sql_3;
                        } else {
                            $this->rollback();
                            return array('success' => false, 'error' => 'Problemas con el registro.');
                        }
                    } else {
                        $this->commit();
                        return $sql_2;
                    }
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

    public function update_exaLab() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_laboratorio set
                    m_laboratorio_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_laboratorio_adm=:adm and m_laboratorio_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':ex_id'] = $_POST['ex_id'];
        $params_2[':m_lab_exam_resultado'] = $_POST['m_lab_exam_resultado'];
        $params_2[':m_lab_exam_observaciones'] = $_POST['m_lab_exam_observaciones'];
        $params_2[':m_lab_exam_diagnostico'] = $_POST['m_lab_exam_diagnostico'];

        $q_2 = 'Update mod_laboratorio_exam set
                    m_lab_exam_resultado=:m_lab_exam_resultado,
                    m_lab_exam_observaciones=:m_lab_exam_observaciones,
                    m_lab_exam_diagnostico=:m_lab_exam_diagnostico
                where
                m_lab_exam_adm=:adm and m_lab_exam_examen=:ex_id;';

        $sql_1 = $this->sql($q_1, $params_1);
        if ($sql_1->success) {
            $sql_2 = $this->sql($q_2, $params_2);
            if ($sql_2->success) {
                if ($_POST['modificar'] == 'on') {
                    $params_3 = array();
                    $params_3[':labc_uni'] = $_POST['labc_uni'];
                    $params_3[':labc_valor'] = $_POST['labc_valor'];
                    $params_3[':ex_id'] = $_POST['ex_id'];

                    $q_3 = "Update lab_conf set
                                    labc_uni=:labc_uni,
                                    labc_valor=:labc_valor
                                where labc_ex=:ex_id;";
                    $sql_3 = $this->sql($q_3, $params_3);
                    if ($sql_3->success) {
                        $this->commit();
                        return $sql_3;
                    } else {
                        $this->rollback();
                        return array('success' => false, 'error' => 'Problemas con el registro.');
                    }
                } else {
                    $this->commit();
                    return $sql_2;
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    public function st_m_lab_exam_resultado() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $examen = $_POST['examen'];
        $sql = $this->sql("SELECT m_lab_exam_resultado FROM mod_laboratorio_exam
                            where
                            m_lab_exam_examen='$examen'
                                and m_lab_exam_resultado like '%$query%'
                            group by m_lab_exam_resultado");
        return $sql;
    }

    public function load_lab_exam_conf() {
        $examen = $_POST['examen'];
        $query = "SELECT * FROM lab_conf
            where labc_ex='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //FIN LOAD SAVE UPDATE LABORATORIO
    //INICIO LOAD SAVE UPDATE LABORATORIO HEMOGRAMA

    public function load_lab_hemograma() {
        $adm = $_POST['adm'];
        $query = "SELECT * FROM mod_laboratorio_hemograma
            inner join mod_laboratorio_hemo_config on m_hemo_id=1
            where m_lab_hemo_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function load_lab_hemo_conf() {
        $query = "SELECT * FROM mod_laboratorio_hemo_config
            where m_hemo_id=1;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_lab_hemograma() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_laboratorio VALUES
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
        $params_2[':m_lab_hemo_hemoglobina'] = $_POST['m_lab_hemo_hemoglobina'];
        $params_2[':m_lab_hemo_hematocrito'] = $_POST['m_lab_hemo_hematocrito'];
        $params_2[':m_lab_hemo_hematies'] = $_POST['m_lab_hemo_hematies'];
        $params_2[':m_lab_hemo_plaquetas'] = $_POST['m_lab_hemo_plaquetas'];
        $params_2[':m_lab_hemo_leucocitos'] = $_POST['m_lab_hemo_leucocitos'];
        $params_2[':m_lab_hemo_monocitos'] = $_POST['m_lab_hemo_monocitos'];
        $params_2[':m_lab_hemo_linfocitos'] = $_POST['m_lab_hemo_linfocitos'];
        $params_2[':m_lab_hemo_eosinofilos'] = $_POST['m_lab_hemo_eosinofilos'];
        $params_2[':m_lab_hemo_abastonados'] = $_POST['m_lab_hemo_abastonados'];
        $params_2[':m_lab_hemo_basofilos'] = $_POST['m_lab_hemo_basofilos'];
        $params_2[':m_lab_hemo_neutrofilos'] = $_POST['m_lab_hemo_neutrofilos'];
        $params_2[':m_lab_hemo_obs'] = $_POST['m_lab_hemo_obs'];



        $q_2 = "INSERT INTO mod_laboratorio_hemograma VALUES 
                (null,
                :adm,
                :m_lab_hemo_hemoglobina,
                :m_lab_hemo_hematocrito,
                :m_lab_hemo_hematies,
                :m_lab_hemo_plaquetas,
                :m_lab_hemo_leucocitos,
                :m_lab_hemo_monocitos,
                :m_lab_hemo_linfocitos,
                :m_lab_hemo_eosinofilos,
                :m_lab_hemo_abastonados,
                :m_lab_hemo_basofilos,
                :m_lab_hemo_neutrofilos,
                :m_lab_hemo_obs);";

        $verifica = $this->sql("SELECT 
		m_laboratorio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_laboratorio 
		inner join sys_usuario on usu_id=m_laboratorio_usu 
		where 
		m_laboratorio_adm='$adm' and m_laboratorio_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    if ($_POST['modificar'] == 'SI') {
                        $params_3 = array();
                        $params_3[':m_hemo_rango_hemoglobina'] = $_POST['m_hemo_rango_hemoglobina'];
                        $params_3[':m_hemo_unid_hemoglobina'] = $_POST['m_hemo_unid_hemoglobina'];
                        $params_3[':m_hemo_rango_hematocrito'] = $_POST['m_hemo_rango_hematocrito'];
                        $params_3[':m_hemo_unid_hematocrito'] = $_POST['m_hemo_unid_hematocrito'];
                        $params_3[':m_hemo_rango_hematies'] = $_POST['m_hemo_rango_hematies'];
                        $params_3[':m_hemo_unid_hematies'] = $_POST['m_hemo_unid_hematies'];
                        $params_3[':m_hemo_rango_plaquetas'] = $_POST['m_hemo_rango_plaquetas'];
                        $params_3[':m_hemo_unid_plaquetas'] = $_POST['m_hemo_unid_plaquetas'];
                        $params_3[':m_hemo_rango_leucocitos'] = $_POST['m_hemo_rango_leucocitos'];
                        $params_3[':m_hemo_unid_leucocitos'] = $_POST['m_hemo_unid_leucocitos'];
                        $params_3[':m_hemo_rango_monocitos'] = $_POST['m_hemo_rango_monocitos'];
                        $params_3[':m_hemo_unid_monocitos'] = $_POST['m_hemo_unid_monocitos'];
                        $params_3[':m_hemo_rango_linfocitos'] = $_POST['m_hemo_rango_linfocitos'];
                        $params_3[':m_hemo_unid_linfocitos'] = $_POST['m_hemo_unid_linfocitos'];
                        $params_3[':m_hemo_rango_eosinofilos'] = $_POST['m_hemo_rango_eosinofilos'];
                        $params_3[':m_hemo_unid_eosinofilos'] = $_POST['m_hemo_unid_eosinofilos'];
                        $params_3[':m_hemo_rango_abastonados'] = $_POST['m_hemo_rango_abastonados'];
                        $params_3[':m_hemo_unid_abastonados'] = $_POST['m_hemo_unid_abastonados'];
                        $params_3[':m_hemo_rango_basofilos'] = $_POST['m_hemo_rango_basofilos'];
                        $params_3[':m_hemo_unid_basofilos'] = $_POST['m_hemo_unid_basofilos'];
                        $params_3[':m_hemo_rango_neutrofilos'] = $_POST['m_hemo_rango_neutrofilos'];
                        $params_3[':m_hemo_unid_neutrofilos'] = $_POST['m_hemo_unid_neutrofilos'];

                        $q_3 = "Update mod_laboratorio_hemo_config set
                                m_hemo_rango_hemoglobina=:m_hemo_rango_hemoglobina,
                                m_hemo_unid_hemoglobina=:m_hemo_unid_hemoglobina,
                                m_hemo_rango_hematocrito=:m_hemo_rango_hematocrito,
                                m_hemo_unid_hematocrito=:m_hemo_unid_hematocrito,
                                m_hemo_rango_hematies=:m_hemo_rango_hematies,
                                m_hemo_unid_hematies=:m_hemo_unid_hematies,
                                m_hemo_rango_plaquetas=:m_hemo_rango_plaquetas,
                                m_hemo_unid_plaquetas=:m_hemo_unid_plaquetas,
                                m_hemo_rango_leucocitos=:m_hemo_rango_leucocitos,
                                m_hemo_unid_leucocitos=:m_hemo_unid_leucocitos,
                                m_hemo_rango_monocitos=:m_hemo_rango_monocitos,
                                m_hemo_unid_monocitos=:m_hemo_unid_monocitos,
                                m_hemo_rango_linfocitos=:m_hemo_rango_linfocitos,
                                m_hemo_unid_linfocitos=:m_hemo_unid_linfocitos,
                                m_hemo_rango_eosinofilos=:m_hemo_rango_eosinofilos,
                                m_hemo_unid_eosinofilos=:m_hemo_unid_eosinofilos,
                                m_hemo_rango_abastonados=:m_hemo_rango_abastonados,
                                m_hemo_unid_abastonados=:m_hemo_unid_abastonados,
                                m_hemo_rango_basofilos=:m_hemo_rango_basofilos,
                                m_hemo_unid_basofilos=:m_hemo_unid_basofilos,
                                m_hemo_rango_neutrofilos=:m_hemo_rango_neutrofilos,
                                m_hemo_unid_neutrofilos=:m_hemo_unid_neutrofilos,
                                m_hemo_fech_update=now()
                            where m_hemo_id=1;";
                        $sql_3 = $this->sql($q_3, $params_3);
                        if ($sql_3->success) {
                            $this->commit();
                            return $sql_3;
                        } else {
                            $this->rollback();
                            return array('success' => false, 'error' => 'Problemas con el registro.');
                        }
                    } else {
                        $this->commit();
                        return $sql_2;
                    }
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

    public function update_lab_hemograma() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_laboratorio set
                    m_laboratorio_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_laboratorio_adm=:adm and m_laboratorio_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_lab_hemo_hemoglobina'] = $_POST['m_lab_hemo_hemoglobina'];
        $params_2[':m_lab_hemo_hematocrito'] = $_POST['m_lab_hemo_hematocrito'];
        $params_2[':m_lab_hemo_hematies'] = $_POST['m_lab_hemo_hematies'];
        $params_2[':m_lab_hemo_plaquetas'] = $_POST['m_lab_hemo_plaquetas'];
        $params_2[':m_lab_hemo_leucocitos'] = $_POST['m_lab_hemo_leucocitos'];
        $params_2[':m_lab_hemo_monocitos'] = $_POST['m_lab_hemo_monocitos'];
        $params_2[':m_lab_hemo_linfocitos'] = $_POST['m_lab_hemo_linfocitos'];
        $params_2[':m_lab_hemo_eosinofilos'] = $_POST['m_lab_hemo_eosinofilos'];
        $params_2[':m_lab_hemo_abastonados'] = $_POST['m_lab_hemo_abastonados'];
        $params_2[':m_lab_hemo_basofilos'] = $_POST['m_lab_hemo_basofilos'];
        $params_2[':m_lab_hemo_neutrofilos'] = $_POST['m_lab_hemo_neutrofilos'];
        $params_2[':m_lab_hemo_obs'] = $_POST['m_lab_hemo_obs'];

        $q_2 = 'Update mod_laboratorio_hemograma set
                    m_lab_hemo_hemoglobina=:m_lab_hemo_hemoglobina,
                    m_lab_hemo_hematocrito=:m_lab_hemo_hematocrito,
                    m_lab_hemo_hematies=:m_lab_hemo_hematies,
                    m_lab_hemo_plaquetas=:m_lab_hemo_plaquetas,
                    m_lab_hemo_leucocitos=:m_lab_hemo_leucocitos,
                    m_lab_hemo_monocitos=:m_lab_hemo_monocitos,
                    m_lab_hemo_linfocitos=:m_lab_hemo_linfocitos,
                    m_lab_hemo_eosinofilos=:m_lab_hemo_eosinofilos,
                    m_lab_hemo_abastonados=:m_lab_hemo_abastonados,
                    m_lab_hemo_basofilos=:m_lab_hemo_basofilos,
                    m_lab_hemo_neutrofilos=:m_lab_hemo_neutrofilos,
                    m_lab_hemo_obs=:m_lab_hemo_obs
                where
                m_lab_hemo_adm=:adm;';

        $sql_1 = $this->sql($q_1, $params_1);
        if ($sql_1->success) {
            $sql_2 = $this->sql($q_2, $params_2);
            if ($sql_2->success) {
                if ($_POST['modificar'] == 'SI') {
                    $params_3 = array();
                    $params_3[':m_hemo_rango_hemoglobina'] = $_POST['m_hemo_rango_hemoglobina'];
                    $params_3[':m_hemo_unid_hemoglobina'] = $_POST['m_hemo_unid_hemoglobina'];
                    $params_3[':m_hemo_rango_hematocrito'] = $_POST['m_hemo_rango_hematocrito'];
                    $params_3[':m_hemo_unid_hematocrito'] = $_POST['m_hemo_unid_hematocrito'];
                    $params_3[':m_hemo_rango_hematies'] = $_POST['m_hemo_rango_hematies'];
                    $params_3[':m_hemo_unid_hematies'] = $_POST['m_hemo_unid_hematies'];
                    $params_3[':m_hemo_rango_plaquetas'] = $_POST['m_hemo_rango_plaquetas'];
                    $params_3[':m_hemo_unid_plaquetas'] = $_POST['m_hemo_unid_plaquetas'];
                    $params_3[':m_hemo_rango_leucocitos'] = $_POST['m_hemo_rango_leucocitos'];
                    $params_3[':m_hemo_unid_leucocitos'] = $_POST['m_hemo_unid_leucocitos'];
                    $params_3[':m_hemo_rango_monocitos'] = $_POST['m_hemo_rango_monocitos'];
                    $params_3[':m_hemo_unid_monocitos'] = $_POST['m_hemo_unid_monocitos'];
                    $params_3[':m_hemo_rango_linfocitos'] = $_POST['m_hemo_rango_linfocitos'];
                    $params_3[':m_hemo_unid_linfocitos'] = $_POST['m_hemo_unid_linfocitos'];
                    $params_3[':m_hemo_rango_eosinofilos'] = $_POST['m_hemo_rango_eosinofilos'];
                    $params_3[':m_hemo_unid_eosinofilos'] = $_POST['m_hemo_unid_eosinofilos'];
                    $params_3[':m_hemo_rango_abastonados'] = $_POST['m_hemo_rango_abastonados'];
                    $params_3[':m_hemo_unid_abastonados'] = $_POST['m_hemo_unid_abastonados'];
                    $params_3[':m_hemo_rango_basofilos'] = $_POST['m_hemo_rango_basofilos'];
                    $params_3[':m_hemo_unid_basofilos'] = $_POST['m_hemo_unid_basofilos'];
                    $params_3[':m_hemo_rango_neutrofilos'] = $_POST['m_hemo_rango_neutrofilos'];
                    $params_3[':m_hemo_unid_neutrofilos'] = $_POST['m_hemo_unid_neutrofilos'];

                    $q_3 = "Update mod_laboratorio_hemo_config set
                                m_hemo_rango_hemoglobina=:m_hemo_rango_hemoglobina,
                                m_hemo_unid_hemoglobina=:m_hemo_unid_hemoglobina,
                                m_hemo_rango_hematocrito=:m_hemo_rango_hematocrito,
                                m_hemo_unid_hematocrito=:m_hemo_unid_hematocrito,
                                m_hemo_rango_hematies=:m_hemo_rango_hematies,
                                m_hemo_unid_hematies=:m_hemo_unid_hematies,
                                m_hemo_rango_plaquetas=:m_hemo_rango_plaquetas,
                                m_hemo_unid_plaquetas=:m_hemo_unid_plaquetas,
                                m_hemo_rango_leucocitos=:m_hemo_rango_leucocitos,
                                m_hemo_unid_leucocitos=:m_hemo_unid_leucocitos,
                                m_hemo_rango_monocitos=:m_hemo_rango_monocitos,
                                m_hemo_unid_monocitos=:m_hemo_unid_monocitos,
                                m_hemo_rango_linfocitos=:m_hemo_rango_linfocitos,
                                m_hemo_unid_linfocitos=:m_hemo_unid_linfocitos,
                                m_hemo_rango_eosinofilos=:m_hemo_rango_eosinofilos,
                                m_hemo_unid_eosinofilos=:m_hemo_unid_eosinofilos,
                                m_hemo_rango_abastonados=:m_hemo_rango_abastonados,
                                m_hemo_unid_abastonados=:m_hemo_unid_abastonados,
                                m_hemo_rango_basofilos=:m_hemo_rango_basofilos,
                                m_hemo_unid_basofilos=:m_hemo_unid_basofilos,
                                m_hemo_rango_neutrofilos=:m_hemo_rango_neutrofilos,
                                m_hemo_unid_neutrofilos=:m_hemo_unid_neutrofilos,
                                m_hemo_fech_update=now()
                            where m_hemo_id=1;";
                    $sql_3 = $this->sql($q_3, $params_3);
                    if ($sql_3->success) {
                        $this->commit();
                        return $sql_3;
                    } else {
                        $this->rollback();
                        return array('success' => false, 'error' => 'Problemas con el registro.');
                    }
                } else {
                    $this->commit();
                    return $sql_2;
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    //FIN LOAD SAVE UPDATE LABORATORIO HEMOGRAMA
    //INICIO AUTOLOAD LABORATORIO ORINA

    public function st_m_lab_orina_color() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_lab_orina_color FROM mod_laboratorio_orina
                            where
                            m_lab_orina_color like '%$query%'
                            group by m_lab_orina_color");
        return $sql;
    }

    public function st_m_lab_orina_aspecto() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_lab_orina_aspecto FROM mod_laboratorio_orina
                            where
                            m_lab_orina_aspecto like '%$query%'
                            group by m_lab_orina_aspecto");
        return $sql;
    }

    public function st_m_lab_orina_cristales() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_lab_orina_cristales FROM mod_laboratorio_orina
                            where
                            m_lab_orina_cristales like '%$query%'
                            group by m_lab_orina_cristales");
        return $sql;
    }

    public function st_m_lab_orina_germenes() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_lab_orina_germenes FROM mod_laboratorio_orina
                            where
                            m_lab_orina_germenes like '%$query%'
                            group by m_lab_orina_germenes");
        return $sql;
    }

    public function st_m_lab_orina_cel_epitelia() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_lab_orina_cel_epitelia FROM mod_laboratorio_orina
                            where
                            m_lab_orina_cel_epitelia like '%$query%'
                            group by m_lab_orina_cel_epitelia");
        return $sql;
    }

    public function st_m_lab_orina_cilindros() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_lab_orina_cilindros FROM mod_laboratorio_orina
                            where
                            m_lab_orina_cilindros like '%$query%'
                            group by m_lab_orina_cilindros");
        return $sql;
    }

    public function st_m_lab_orina_otros() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_lab_orina_otros FROM mod_laboratorio_orina
                            where
                            m_lab_orina_otros like '%$query%'
                            group by m_lab_orina_otros");
        return $sql;
    }

    //FIN AUTOLOAD LABORATORIO ORINA
    //INICIO LOAD SAVE UPDATE LABORATORIO EXAMEN DE ORINA

    public function load_exa_orina() {
        $adm = $_POST['adm'];
        $query = "SELECT * FROM mod_laboratorio_orina where m_lab_orina_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_lab_orina() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_laboratorio VALUES
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
        $params_2[':m_lab_orina_color'] = $_POST['m_lab_orina_color'];
        $params_2[':m_lab_orina_aspecto'] = $_POST['m_lab_orina_aspecto'];
        $params_2[':m_lab_orina_ph'] = $_POST['m_lab_orina_ph'];
        $params_2[':m_lab_orina_densidad'] = $_POST['m_lab_orina_densidad'];
        $params_2[':m_lab_orina_glucosa'] = $_POST['m_lab_orina_glucosa'];
        $params_2[':m_lab_orina_urobilino'] = $_POST['m_lab_orina_urobilino'];
        $params_2[':m_lab_orina_proteinas'] = $_POST['m_lab_orina_proteinas'];
        $params_2[':m_lab_orina_nitritos'] = $_POST['m_lab_orina_nitritos'];
        $params_2[':m_lab_orina_bilirrubina'] = $_POST['m_lab_orina_bilirrubina'];
        $params_2[':m_lab_orina_hemoglobina'] = $_POST['m_lab_orina_hemoglobina'];
        $params_2[':m_lab_orina_acido_ascorbi'] = $_POST['m_lab_orina_acido_ascorbi'];
        $params_2[':m_lab_orina_esterasa_leuco'] = $_POST['m_lab_orina_esterasa_leuco'];
        $params_2[':m_lab_orina_cuerpo_certoni'] = $_POST['m_lab_orina_cuerpo_certoni'];
        $params_2[':m_lab_orina_leucocitos'] = $_POST['m_lab_orina_leucocitos'];
        $params_2[':m_lab_orina_hematies'] = $_POST['m_lab_orina_hematies'];
        $params_2[':m_lab_orina_cristales'] = $_POST['m_lab_orina_cristales'];
        $params_2[':m_lab_orina_germenes'] = $_POST['m_lab_orina_germenes'];
        $params_2[':m_lab_orina_cel_epitelia'] = $_POST['m_lab_orina_cel_epitelia'];
        $params_2[':m_lab_orina_cilindros'] = $_POST['m_lab_orina_cilindros'];
        $params_2[':m_lab_orina_otros'] = $_POST['m_lab_orina_otros'];
        $params_2[':m_lab_orina_observaciones'] = $_POST['m_lab_orina_observaciones'];


        $q_2 = "INSERT INTO mod_laboratorio_orina VALUES 
                (null,
                :adm,
                :m_lab_orina_color,
                :m_lab_orina_aspecto,
                :m_lab_orina_ph,
                :m_lab_orina_densidad,
                :m_lab_orina_glucosa,
                :m_lab_orina_urobilino,
                :m_lab_orina_proteinas,
                :m_lab_orina_nitritos,
                :m_lab_orina_bilirrubina,
                :m_lab_orina_hemoglobina,
                :m_lab_orina_acido_ascorbi,
                :m_lab_orina_esterasa_leuco,
                :m_lab_orina_cuerpo_certoni,
                :m_lab_orina_leucocitos,
                :m_lab_orina_hematies,
                :m_lab_orina_cristales,
                :m_lab_orina_germenes,
                :m_lab_orina_cel_epitelia,
                :m_lab_orina_cilindros,
                :m_lab_orina_otros,
                :m_lab_orina_observaciones);";

        $verifica = $this->sql("SELECT 
		m_laboratorio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_laboratorio 
		inner join sys_usuario on usu_id=m_laboratorio_usu 
		where 
		m_laboratorio_adm='$adm' and m_laboratorio_examen='$exa';");
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

    public function update_lab_orina() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_laboratorio set
                    m_laboratorio_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_laboratorio_adm=:adm and m_laboratorio_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_lab_orina_color'] = $_POST['m_lab_orina_color'];
        $params_2[':m_lab_orina_aspecto'] = $_POST['m_lab_orina_aspecto'];
        $params_2[':m_lab_orina_ph'] = $_POST['m_lab_orina_ph'];
        $params_2[':m_lab_orina_densidad'] = $_POST['m_lab_orina_densidad'];
        $params_2[':m_lab_orina_glucosa'] = $_POST['m_lab_orina_glucosa'];
        $params_2[':m_lab_orina_urobilino'] = $_POST['m_lab_orina_urobilino'];
        $params_2[':m_lab_orina_proteinas'] = $_POST['m_lab_orina_proteinas'];
        $params_2[':m_lab_orina_nitritos'] = $_POST['m_lab_orina_nitritos'];
        $params_2[':m_lab_orina_bilirrubina'] = $_POST['m_lab_orina_bilirrubina'];
        $params_2[':m_lab_orina_hemoglobina'] = $_POST['m_lab_orina_hemoglobina'];
        $params_2[':m_lab_orina_acido_ascorbi'] = $_POST['m_lab_orina_acido_ascorbi'];
        $params_2[':m_lab_orina_esterasa_leuco'] = $_POST['m_lab_orina_esterasa_leuco'];
        $params_2[':m_lab_orina_cuerpo_certoni'] = $_POST['m_lab_orina_cuerpo_certoni'];
        $params_2[':m_lab_orina_leucocitos'] = $_POST['m_lab_orina_leucocitos'];
        $params_2[':m_lab_orina_hematies'] = $_POST['m_lab_orina_hematies'];
        $params_2[':m_lab_orina_cristales'] = $_POST['m_lab_orina_cristales'];
        $params_2[':m_lab_orina_germenes'] = $_POST['m_lab_orina_germenes'];
        $params_2[':m_lab_orina_cel_epitelia'] = $_POST['m_lab_orina_cel_epitelia'];
        $params_2[':m_lab_orina_cilindros'] = $_POST['m_lab_orina_cilindros'];
        $params_2[':m_lab_orina_otros'] = $_POST['m_lab_orina_otros'];
        $params_2[':m_lab_orina_observaciones'] = $_POST['m_lab_orina_observaciones'];

        $q_2 = 'Update mod_laboratorio_orina set
                    m_lab_orina_color=:m_lab_orina_color,
                    m_lab_orina_aspecto=:m_lab_orina_aspecto,
                    m_lab_orina_ph=:m_lab_orina_ph,
                    m_lab_orina_densidad=:m_lab_orina_densidad,
                    m_lab_orina_glucosa=:m_lab_orina_glucosa,
                    m_lab_orina_urobilino=:m_lab_orina_urobilino,
                    m_lab_orina_proteinas=:m_lab_orina_proteinas,
                    m_lab_orina_nitritos=:m_lab_orina_nitritos,
                    m_lab_orina_bilirrubina=:m_lab_orina_bilirrubina,
                    m_lab_orina_hemoglobina=:m_lab_orina_hemoglobina,
                    m_lab_orina_acido_ascorbi=:m_lab_orina_acido_ascorbi,
                    m_lab_orina_esterasa_leuco=:m_lab_orina_esterasa_leuco,
                    m_lab_orina_cuerpo_certoni=:m_lab_orina_cuerpo_certoni,
                    m_lab_orina_leucocitos=:m_lab_orina_leucocitos,
                    m_lab_orina_hematies=:m_lab_orina_hematies,
                    m_lab_orina_cristales=:m_lab_orina_cristales,
                    m_lab_orina_germenes=:m_lab_orina_germenes,
                    m_lab_orina_cel_epitelia=:m_lab_orina_cel_epitelia,
                    m_lab_orina_cilindros=:m_lab_orina_cilindros,
                    m_lab_orina_otros=:m_lab_orina_otros,
                    m_lab_orina_observaciones=:m_lab_orina_observaciones
                where
                m_lab_orina_adm=:adm;';


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

    //FIN LOAD SAVE UPDATE LABORATORIO EXAMEN DE ORINA
    //INICIO LOAD SAVE UPDATE LABORATORIO PERFIL LIPIDICO

    public function load_lab_p_lipido() {
        $adm = $_POST['adm'];
        $query = "SELECT * FROM mod_laboratorio_p_lipidido
            inner join mod_laboratorio_p_lipidido_config on m_p_lipido_id=1
            where m_lab_p_lipido_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function load_lab_p_lipido_conf() {
        $query = "SELECT * FROM mod_laboratorio_p_lipidido_config
            where m_p_lipido_id=1;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_lab_p_lipido() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_laboratorio VALUES
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
        $params_2[':m_lab_p_lipido_colesterol_hdl'] = $_POST['m_lab_p_lipido_colesterol_hdl'];
        $params_2[':m_lab_p_lipido_colesterol_ldl'] = $_POST['m_lab_p_lipido_colesterol_ldl'];
        $params_2[':m_lab_p_lipido_colesterol_vldl'] = $_POST['m_lab_p_lipido_colesterol_vldl'];
        $params_2[':m_lab_p_lipido_colesterol_total'] = $_POST['m_lab_p_lipido_colesterol_total'];
        $params_2[':m_lab_p_lipido_trigliceridos'] = $_POST['m_lab_p_lipido_trigliceridos'];
        $params_2[':m_lab_p_lipido_riesg_coronario'] = $_POST['m_lab_p_lipido_riesg_coronario'];

        $q_2 = "INSERT INTO mod_laboratorio_p_lipidido VALUES 
                (null,
                :adm,
                :m_lab_p_lipido_colesterol_hdl,
				:m_lab_p_lipido_colesterol_ldl,
				:m_lab_p_lipido_colesterol_vldl,
				:m_lab_p_lipido_colesterol_total,
				:m_lab_p_lipido_trigliceridos,
				:m_lab_p_lipido_riesg_coronario);";

        $verifica = $this->sql("SELECT 
		m_laboratorio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_laboratorio 
		inner join sys_usuario on usu_id=m_laboratorio_usu 
		where 
		m_laboratorio_adm='$adm' and m_laboratorio_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    if ($_POST['modificar'] == 'SI') {
                        $params_3 = array();
                        $params_3[':m_p_lipido_unid_colesterol_hdl'] = $_POST['m_p_lipido_unid_colesterol_hdl'];
                        $params_3[':m_p_lipido_refe_colesterol_hdl'] = $_POST['m_p_lipido_refe_colesterol_hdl'];
                        $params_3[':m_p_lipido_meto_colesterol_hdl'] = $_POST['m_p_lipido_meto_colesterol_hdl'];
                        $params_3[':m_p_lipido_unid_colesterol_ldl'] = $_POST['m_p_lipido_unid_colesterol_ldl'];
                        $params_3[':m_p_lipido_refe_colesterol_ldl'] = $_POST['m_p_lipido_refe_colesterol_ldl'];
                        $params_3[':m_p_lipido_meto_colesterol_ldl'] = $_POST['m_p_lipido_meto_colesterol_ldl'];
                        $params_3[':m_p_lipido_unid_colesterol_vldl'] = $_POST['m_p_lipido_unid_colesterol_vldl'];
                        $params_3[':m_p_lipido_refe_colesterol_vldl'] = $_POST['m_p_lipido_refe_colesterol_vldl'];
                        $params_3[':m_p_lipido_meto_colesterol_vldl'] = $_POST['m_p_lipido_meto_colesterol_vldl'];
                        $params_3[':m_p_lipido_unid_colesterol_total'] = $_POST['m_p_lipido_unid_colesterol_total'];
                        $params_3[':m_p_lipido_refe_colesterol_total'] = $_POST['m_p_lipido_refe_colesterol_total'];
                        $params_3[':m_p_lipido_meto_colesterol_total'] = $_POST['m_p_lipido_meto_colesterol_total'];
                        $params_3[':m_p_lipido_unid_trigliceridos'] = $_POST['m_p_lipido_unid_trigliceridos'];
                        $params_3[':m_p_lipido_refe_trigliceridos'] = $_POST['m_p_lipido_refe_trigliceridos'];
                        $params_3[':m_p_lipido_meto_trigliceridos'] = $_POST['m_p_lipido_meto_trigliceridos'];

                        $q_3 = "Update mod_laboratorio_p_lipidido_config set
                                m_p_lipido_unid_colesterol_hdl=:m_p_lipido_unid_colesterol_hdl,
					m_p_lipido_refe_colesterol_hdl=:m_p_lipido_refe_colesterol_hdl,
					m_p_lipido_meto_colesterol_hdl=:m_p_lipido_meto_colesterol_hdl,
					m_p_lipido_unid_colesterol_ldl=:m_p_lipido_unid_colesterol_ldl,
					m_p_lipido_refe_colesterol_ldl=:m_p_lipido_refe_colesterol_ldl,
					m_p_lipido_meto_colesterol_ldl=:m_p_lipido_meto_colesterol_ldl,
					m_p_lipido_unid_colesterol_vldl=:m_p_lipido_unid_colesterol_vldl,
					m_p_lipido_refe_colesterol_vldl=:m_p_lipido_refe_colesterol_vldl,
					m_p_lipido_meto_colesterol_vldl=:m_p_lipido_meto_colesterol_vldl,
					m_p_lipido_unid_colesterol_total=:m_p_lipido_unid_colesterol_total,
					m_p_lipido_refe_colesterol_total=:m_p_lipido_refe_colesterol_total,
					m_p_lipido_meto_colesterol_total=:m_p_lipido_meto_colesterol_total,
					m_p_lipido_unid_trigliceridos=:m_p_lipido_unid_trigliceridos,
					m_p_lipido_refe_trigliceridos=:m_p_lipido_refe_trigliceridos,
					m_p_lipido_meto_trigliceridos=:m_p_lipido_meto_trigliceridos
                                where m_p_lipido_id=1;";
                        $sql_3 = $this->sql($q_3, $params_3);
                        if ($sql_3->success) {
                            $this->commit();
                            return $sql_3;
                        } else {
                            $this->rollback();
                            return array('success' => false, 'error' => 'Problemas con el registro.');
                        }
                    } else {
                        $this->commit();
                        return $sql_2;
                    }
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

    public function update_lab_p_lipido() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_laboratorio set
                    m_laboratorio_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_laboratorio_adm=:adm and m_laboratorio_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_lab_p_lipido_colesterol_hdl'] = $_POST['m_lab_p_lipido_colesterol_hdl'];
        $params_2[':m_lab_p_lipido_colesterol_ldl'] = $_POST['m_lab_p_lipido_colesterol_ldl'];
        $params_2[':m_lab_p_lipido_colesterol_vldl'] = $_POST['m_lab_p_lipido_colesterol_vldl'];
        $params_2[':m_lab_p_lipido_colesterol_total'] = $_POST['m_lab_p_lipido_colesterol_total'];
        $params_2[':m_lab_p_lipido_trigliceridos'] = $_POST['m_lab_p_lipido_trigliceridos'];
        $params_2[':m_lab_p_lipido_riesg_coronario'] = $_POST['m_lab_p_lipido_riesg_coronario'];


        $q_2 = 'Update mod_laboratorio_p_lipidido set
                    m_lab_p_lipido_colesterol_hdl=:m_lab_p_lipido_colesterol_hdl,
					m_lab_p_lipido_colesterol_ldl=:m_lab_p_lipido_colesterol_ldl,
					m_lab_p_lipido_colesterol_vldl=:m_lab_p_lipido_colesterol_vldl,
					m_lab_p_lipido_colesterol_total=:m_lab_p_lipido_colesterol_total,
					m_lab_p_lipido_trigliceridos=:m_lab_p_lipido_trigliceridos,
					m_lab_p_lipido_riesg_coronario=:m_lab_p_lipido_riesg_coronario
                where
                m_lab_p_lipido_adm=:adm;';

        $sql_1 = $this->sql($q_1, $params_1);
        if ($sql_1->success) {
            $sql_2 = $this->sql($q_2, $params_2);
            if ($sql_2->success) {
                if ($_POST['modificar'] == 'SI') {
                    $params_3 = array();
                    $params_3[':m_p_lipido_unid_colesterol_hdl'] = $_POST['m_p_lipido_unid_colesterol_hdl'];
                    $params_3[':m_p_lipido_refe_colesterol_hdl'] = $_POST['m_p_lipido_refe_colesterol_hdl'];
                    $params_3[':m_p_lipido_meto_colesterol_hdl'] = $_POST['m_p_lipido_meto_colesterol_hdl'];
                    $params_3[':m_p_lipido_unid_colesterol_ldl'] = $_POST['m_p_lipido_unid_colesterol_ldl'];
                    $params_3[':m_p_lipido_refe_colesterol_ldl'] = $_POST['m_p_lipido_refe_colesterol_ldl'];
                    $params_3[':m_p_lipido_meto_colesterol_ldl'] = $_POST['m_p_lipido_meto_colesterol_ldl'];
                    $params_3[':m_p_lipido_unid_colesterol_vldl'] = $_POST['m_p_lipido_unid_colesterol_vldl'];
                    $params_3[':m_p_lipido_refe_colesterol_vldl'] = $_POST['m_p_lipido_refe_colesterol_vldl'];
                    $params_3[':m_p_lipido_meto_colesterol_vldl'] = $_POST['m_p_lipido_meto_colesterol_vldl'];
                    $params_3[':m_p_lipido_unid_colesterol_total'] = $_POST['m_p_lipido_unid_colesterol_total'];
                    $params_3[':m_p_lipido_refe_colesterol_total'] = $_POST['m_p_lipido_refe_colesterol_total'];
                    $params_3[':m_p_lipido_meto_colesterol_total'] = $_POST['m_p_lipido_meto_colesterol_total'];
                    $params_3[':m_p_lipido_unid_trigliceridos'] = $_POST['m_p_lipido_unid_trigliceridos'];
                    $params_3[':m_p_lipido_refe_trigliceridos'] = $_POST['m_p_lipido_refe_trigliceridos'];
                    $params_3[':m_p_lipido_meto_trigliceridos'] = $_POST['m_p_lipido_meto_trigliceridos'];

                    $q_3 = "Update mod_laboratorio_p_lipidido_config set
                    m_p_lipido_unid_colesterol_hdl=:m_p_lipido_unid_colesterol_hdl,
                    m_p_lipido_refe_colesterol_hdl=:m_p_lipido_refe_colesterol_hdl,
                    m_p_lipido_meto_colesterol_hdl=:m_p_lipido_meto_colesterol_hdl,
                    m_p_lipido_unid_colesterol_ldl=:m_p_lipido_unid_colesterol_ldl,
                    m_p_lipido_refe_colesterol_ldl=:m_p_lipido_refe_colesterol_ldl,
                    m_p_lipido_meto_colesterol_ldl=:m_p_lipido_meto_colesterol_ldl,
                    m_p_lipido_unid_colesterol_vldl=:m_p_lipido_unid_colesterol_vldl,
                    m_p_lipido_refe_colesterol_vldl=:m_p_lipido_refe_colesterol_vldl,
                    m_p_lipido_meto_colesterol_vldl=:m_p_lipido_meto_colesterol_vldl,
                    m_p_lipido_unid_colesterol_total=:m_p_lipido_unid_colesterol_total,
                    m_p_lipido_refe_colesterol_total=:m_p_lipido_refe_colesterol_total,
                    m_p_lipido_meto_colesterol_total=:m_p_lipido_meto_colesterol_total,
                    m_p_lipido_unid_trigliceridos=:m_p_lipido_unid_trigliceridos,
                    m_p_lipido_refe_trigliceridos=:m_p_lipido_refe_trigliceridos,
                    m_p_lipido_meto_trigliceridos=:m_p_lipido_meto_trigliceridos
                    where m_p_lipido_id=1;";
                    $sql_3 = $this->sql($q_3, $params_3);
                    if ($sql_3->success) {
                        $this->commit();
                        return $sql_3;
                    } else {
                        $this->rollback();
                        return array('success' => false, 'error' => 'Problemas con el registro.');
                    }
                } else {
                    $this->commit();
                    return $sql_2;
                }
            } else {
                $this->rollback();
                return array('success' => false, 'error' => 'Problemas con el registro.');
            }
        } else {
            $this->rollback();
            return array('success' => false, 'error' => 'Problemas con el registro.');
        }
    }

    //FIN LOAD SAVE UPDATE LABORATORIO PERFIL LIPIDICO

    public function paciente($adm) {
        $sql = $this->sql("SELECT
            adm_id as adm
            ,concat(pac_nombres,', ',pac_appat,' ',pac_apmat) nom_ap
            ,concat(pac_nombres) nombre,concat(pac_appat,' ',pac_apmat) apellidos
            , emp_desc,concat(adm_puesto,' - ',adm_area)as puesto
            ,Date_format(adm_fech,'%d-%m-%Y %h:%i %p') fech_reg_copleto
            ,Date_format(adm_fech,'%d-%m-%Y') fech_reg
            ,if(tdoc_id=0,'NÚMERO DE DNI',tdoc_desc) documento,pac_ndoc
            ,Date_format(pac_fech_nac,'%d-%m-%Y') fech_naci
            ,TIMESTAMPDIFF(YEAR,pac_fech_nac,CURRENT_DATE) as edad
            ,if(pac_sexo='M','MASCULINO','FEMENINO') sexo,tfi_desc tipo
            ,pac_cel,pac_correo, ec_desc ecivil, gi_desc ginstruccion,pac_domdir direc
            ,concat(m.dep_desc,'-' ,l.prov_desc,'-' ,k.dis_desc) ubica_nacimiento
            ,m.dep_desc depa_naci,l.prov_desc prov_naci,k.dis_desc dist_naci
            ,concat(m_ubigeo.dep_desc,'-' ,l_ubigeo.prov_desc,'-' ,k_ubigeo.dis_desc) ubica_ubigeo
            ,m_ubigeo.dep_desc depa_ubigeo,l_ubigeo.prov_desc prov_ubigeo,k_ubigeo.dis_desc dist_ubigeo
            FROM admision
            inner join paciente on pac_id=adm_pac
            inner join pack on pk_id=adm_ruta
            inner join empresa on emp_id=pk_emp
            inner join tficha on tfi_id=adm_tficha
            inner join ecivil on ec_id=pac_ecid
            inner join ginstruccion on gi_id=pac_giid
            inner join tdocumento on tdoc_id=pac_tdocid

            left join distrito k on k.dis_id=pac_domdisid
            left join provincia l on l.prov_id=k.dis_provid
            left join departamento m on m.dep_id=l.prov_depid

            left join distrito k_ubigeo on k_ubigeo.dis_id=pac_ubigeo
            left join provincia l_ubigeo on l_ubigeo.prov_id=k_ubigeo.dis_provid
            left join departamento m_ubigeo on m_ubigeo.dep_id=l_ubigeo.prov_depid
            where
            adm_id=$adm");
        return $sql;
    }

    public function carga_examenes($adm, $sexo) {
        $sql = $this->sql("SELECT adm_id as adm, ex_desc,ex_id
                FROM admision
                inner join pack on adm_ruta=pk_id
                inner join dpack on dpk_pkid=pk_id
                inner join examen on ex_id=dpk_exid
                where
                #filtro que verifica que si es masculino para no tomar examenes femeninos
                if('MASCULINO'='$sexo', ex_arid IN (10) and ex_id not in(57,50), ex_arid IN (10))
                and adm_id=$adm order by ex_id;
            ");
        return $sql;
    }

    public function load_examenes($adm, $examen) {
        $query = "SELECT * FROM mod_laboratorio_exam 
            inner join lab_conf on labc_ex=m_lab_exam_examen
                where m_lab_exam_adm='$adm' and m_lab_exam_examen='$examen';";
        return $this->sql($query);
    }

    public function carga_hemograma_pdf($adm) {
        $query = "SELECT * FROM mod_laboratorio_hemograma
            inner join mod_laboratorio_hemo_config on m_hemo_id=1
            where m_lab_hemo_adm='$adm';";
        return $this->sql($query);
    }

    public function carga_exa_orina_pdf($adm) {
        $query = "SELECT * FROM mod_laboratorio_orina
            where m_lab_orina_adm='$adm';";
        return $this->sql($query);
    }

    public function carga_p_lipido_pdf($adm) {
        $query = "SELECT * FROM mod_laboratorio_p_lipidido
            inner join mod_laboratorio_p_lipidido_config on m_p_lipido_id=1
            where m_lab_p_lipido_adm='$adm';";
        return $this->sql($query);
    }

    public function carga_exa_drogas_pdf($adm) {
        $query = "SELECT * FROM mod_laboratorio_drogas_10
            where m_lab_drogas_10_adm='$adm';";
        return $this->sql($query);
    }

    public function load_exa_drogas_01() {
        $adm = $_POST['adm'];
        $query = "SELECT * FROM mod_laboratorio_drogas_10 where m_lab_drogas_10_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_lab_drogas_01() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_laboratorio VALUES
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
        $params_2[':m_lab_drogas_10_cocaina'] = $_POST['m_lab_drogas_10_cocaina'];
        $params_2[':m_lab_drogas_10_marihuana'] = $_POST['m_lab_drogas_10_marihuana'];
        $params_2[':m_lab_drogas_10_benzodiazepina'] = $_POST['m_lab_drogas_10_benzodiazepina'];
        $params_2[':m_lab_drogas_10_barbiturico'] = $_POST['m_lab_drogas_10_barbiturico'];
        $params_2[':m_lab_drogas_10_anphetamina'] = $_POST['m_lab_drogas_10_anphetamina'];
        $params_2[':m_lab_drogas_10_metadona'] = $_POST['m_lab_drogas_10_metadona'];
        $params_2[':m_lab_drogas_10_methamphentamina'] = $_POST['m_lab_drogas_10_methamphentamina'];
        $params_2[':m_lab_drogas_10_mdma'] = $_POST['m_lab_drogas_10_mdma'];
        $params_2[':m_lab_drogas_10_morphina'] = $_POST['m_lab_drogas_10_morphina'];
        $params_2[':m_lab_drogas_10_phecyclidine'] = $_POST['m_lab_drogas_10_phecyclidine'];

        $q_2 = "INSERT INTO mod_laboratorio_drogas_10 VALUES 
                (null,
                :adm,
                :m_lab_drogas_10_cocaina,
				:m_lab_drogas_10_marihuana,
				:m_lab_drogas_10_benzodiazepina,
				:m_lab_drogas_10_barbiturico,
				:m_lab_drogas_10_anphetamina,
				:m_lab_drogas_10_metadona,
				:m_lab_drogas_10_methamphentamina,
				:m_lab_drogas_10_mdma,
				:m_lab_drogas_10_morphina,
				:m_lab_drogas_10_phecyclidine);";

        $verifica = $this->sql("SELECT 
		m_laboratorio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_laboratorio 
		inner join sys_usuario on usu_id=m_laboratorio_usu 
		where 
		m_laboratorio_adm='$adm' and m_laboratorio_examen='$exa';");
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

    public function update_lab_drogas_01() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_laboratorio set
                    m_laboratorio_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_laboratorio_adm=:adm and m_laboratorio_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_lab_drogas_10_cocaina'] = $_POST['m_lab_drogas_10_cocaina'];
        $params_2[':m_lab_drogas_10_marihuana'] = $_POST['m_lab_drogas_10_marihuana'];
        $params_2[':m_lab_drogas_10_benzodiazepina'] = $_POST['m_lab_drogas_10_benzodiazepina'];
        $params_2[':m_lab_drogas_10_barbiturico'] = $_POST['m_lab_drogas_10_barbiturico'];
        $params_2[':m_lab_drogas_10_anphetamina'] = $_POST['m_lab_drogas_10_anphetamina'];
        $params_2[':m_lab_drogas_10_metadona'] = $_POST['m_lab_drogas_10_metadona'];
        $params_2[':m_lab_drogas_10_methamphentamina'] = $_POST['m_lab_drogas_10_methamphentamina'];
        $params_2[':m_lab_drogas_10_mdma'] = $_POST['m_lab_drogas_10_mdma'];
        $params_2[':m_lab_drogas_10_morphina'] = $_POST['m_lab_drogas_10_morphina'];
        $params_2[':m_lab_drogas_10_phecyclidine'] = $_POST['m_lab_drogas_10_phecyclidine'];

        $q_2 = 'Update mod_laboratorio_drogas_10 set
                    m_lab_drogas_10_cocaina=:m_lab_drogas_10_cocaina,
					m_lab_drogas_10_marihuana=:m_lab_drogas_10_marihuana,
					m_lab_drogas_10_benzodiazepina=:m_lab_drogas_10_benzodiazepina,
					m_lab_drogas_10_barbiturico=:m_lab_drogas_10_barbiturico,
					m_lab_drogas_10_anphetamina=:m_lab_drogas_10_anphetamina,
					m_lab_drogas_10_metadona=:m_lab_drogas_10_metadona,
					m_lab_drogas_10_methamphentamina=:m_lab_drogas_10_methamphentamina,
					m_lab_drogas_10_mdma=:m_lab_drogas_10_mdma,
					m_lab_drogas_10_morphina=:m_lab_drogas_10_morphina,
					m_lab_drogas_10_phecyclidine=:m_lab_drogas_10_phecyclidine
                where
                m_lab_drogas_10_adm=:adm;';


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

}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
?>