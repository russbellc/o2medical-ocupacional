<?php

class model extends core {

    public function list_paciente() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $usuario = $this->user->us_id;
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
                    #if($usuario ='cardio', adm>1026,0=0)
                    ex_arid IN (5)
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
            $verifica = $this->sql("SELECT count(m_cardio_adm)total FROM mod_cardio where m_cardio_adm=$adm_id;");
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
                where ex_arid IN (5) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT 
                m_cardio_id id,m_cardio_st st
                , m_cardio_usu usu, m_cardio_fech_reg fech 
            FROM mod_cardio 
            where m_cardio_adm=$adm_id and m_cardio_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    //LOAD SAVE UPDATE LABORATORIO

    public function load_cardio_pred() {
        $adm = $_POST['adm'];
        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_cardio_pred where m_cardio_pred_adm='$adm' and m_cardio_pred_examen='$examen';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_cardio_pred() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_cardio_st'] = '1'; //ESTADO DEL MODULO PRINCIPAL

        $params[':adm'] = $adm;
        $params[':ex_id'] = $exa;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_cardio_pred_resultado'] = $_POST['m_cardio_pred_resultado'];
        $params[':m_cardio_pred_observaciones'] = $_POST['m_cardio_pred_observaciones'];
        $params[':m_cardio_pred_diagnostico'] = $_POST['m_cardio_pred_diagnostico'];


        $q = "INSERT INTO mod_cardio_pred VALUES 
                (null,
                :adm,
                :ex_id,
                :m_cardio_pred_resultado,
                :m_cardio_pred_observaciones,
                :m_cardio_pred_diagnostico);
                
                INSERT INTO mod_cardio VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_cardio_st,
                :ex_id);";

        $verifica = $this->sql("SELECT 
		m_cardio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_cardio 
		inner join sys_usuario on usu_id=m_cardio_usu 
		where 
		m_cardio_adm='$adm' and m_cardio_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql = $this->sql($q, $params);
            if ($sql->success) {
                $this->commit();
                return $sql;
            } else {
                $this->rollback();
                return array('success' => false);
            }
        }
    }

    public function update_cardio_pred() {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_cardio_pred_resultado'] = $_POST['m_cardio_pred_resultado'];
        $params[':m_cardio_pred_observaciones'] = $_POST['m_cardio_pred_observaciones'];
        $params[':m_cardio_pred_diagnostico'] = $_POST['m_cardio_pred_diagnostico'];

        $this->begin();
        $q = 'Update mod_cardio_pred set
                    m_cardio_pred_resultado=:m_cardio_pred_resultado,
                    m_cardio_pred_observaciones=:m_cardio_pred_observaciones,
                    m_cardio_pred_diagnostico=:m_cardio_pred_diagnostico
                where
                m_cardio_pred_adm=:adm and m_cardio_pred_examen=:ex_id;
                
                update mod_cardio set
                    m_cardio_usu=:usuario,
                    m_laboratorio_fech_update=now()
                where
                m_laboratorio_id=:id and m_cardio_adm=:adm and m_cardio_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    //LOAD SAVE UPDATE psico_examen

    public function load_cardio_ekg() {
        $adm = $_POST['adm'];
//        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_cardio_ekg where m_car_ekg_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_cardio_ekg() {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $adm;
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $exa;

        $q_1 = "INSERT INTO mod_cardio VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                1,
                :ex_id);";


        $params_2 = array();
        $params_2[':adm'] = $adm;
        $params_2[':m_car_ekg_frec_auricular'] = $_POST['m_car_ekg_frec_auricular'];
        $params_2[':m_car_ekg_frec_ventricular'] = $_POST['m_car_ekg_frec_ventricular'];
        $params_2[':m_car_ekg_ritmo'] = $_POST['m_car_ekg_ritmo'];
        $params_2[':m_car_ekg_intervalo_p_r'] = $_POST['m_car_ekg_intervalo_p_r'];
        $params_2[':m_car_ekg_qrs'] = $_POST['m_car_ekg_qrs'];
        $params_2[':m_car_ekg_q_t'] = $_POST['m_car_ekg_q_t'];
        $params_2[':m_car_ekg_ap'] = $_POST['m_car_ekg_ap'];
        $params_2[':m_car_ekg_a_qrs'] = $_POST['m_car_ekg_a_qrs'];
        $params_2[':m_car_ekg_at'] = $_POST['m_car_ekg_at'];
        $params_2[':m_car_ekg_onda_p'] = $_POST['m_car_ekg_onda_p'];
        $params_2[':m_car_ekg_complejos_qrs'] = $_POST['m_car_ekg_complejos_qrs'];
        $params_2[':m_car_ekg_segmento_s_t'] = $_POST['m_car_ekg_segmento_s_t'];
        $params_2[':m_car_ekg_onda_t'] = $_POST['m_car_ekg_onda_t'];
        $params_2[':m_car_ekg_quindina'] = $_POST['m_car_ekg_quindina'];
        $params_2[':m_car_ekg_otros_hallazgo'] = $_POST['m_car_ekg_otros_hallazgo'];
        $params_2[':m_car_ekg_antecedentes'] = $_POST['m_car_ekg_antecedentes'];
        $params_2[':m_car_ekg_sintomas'] = $_POST['m_car_ekg_sintomas'];
        $params_2[':m_car_ekg_descripcion'] = $_POST['m_car_ekg_descripcion'];


        $q_2 = "INSERT INTO mod_cardio_ekg VALUES 
                (null,
                :adm,
                :m_car_ekg_frec_auricular,
                :m_car_ekg_frec_ventricular,
                :m_car_ekg_ritmo,
                :m_car_ekg_intervalo_p_r,
                :m_car_ekg_qrs,
                :m_car_ekg_q_t,
                :m_car_ekg_ap,
                :m_car_ekg_a_qrs,
                :m_car_ekg_at,
                :m_car_ekg_onda_p,
                :m_car_ekg_complejos_qrs,
                :m_car_ekg_segmento_s_t,
                :m_car_ekg_onda_t,
                :m_car_ekg_quindina,
                :m_car_ekg_otros_hallazgo,
                :m_car_ekg_antecedentes,
                :m_car_ekg_sintomas,
                :m_car_ekg_descripcion);";

        $verifica = $this->sql("SELECT 
		m_cardio_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_cardio 
		inner join sys_usuario on usu_id=m_cardio_usu 
		where 
		m_cardio_adm='$adm' and m_cardio_examen='$exa';");

        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    $this->commit();
                    return array('success' => true, 'total' => $sql_2->total, 'data' => $sql_2->data);
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

    public function update_cardio_ekg() {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_cardio set
                    m_cardio_usu=:usuario,
                    m_cardio_fech_update=now()
                where
                m_cardio_id=:id and m_cardio_adm=:adm and m_cardio_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':m_car_ekg_frec_auricular'] = $_POST['m_car_ekg_frec_auricular'];
        $params_2[':m_car_ekg_frec_ventricular'] = $_POST['m_car_ekg_frec_ventricular'];
        $params_2[':m_car_ekg_ritmo'] = $_POST['m_car_ekg_ritmo'];
        $params_2[':m_car_ekg_intervalo_p_r'] = $_POST['m_car_ekg_intervalo_p_r'];
        $params_2[':m_car_ekg_qrs'] = $_POST['m_car_ekg_qrs'];
        $params_2[':m_car_ekg_q_t'] = $_POST['m_car_ekg_q_t'];
        $params_2[':m_car_ekg_ap'] = $_POST['m_car_ekg_ap'];
        $params_2[':m_car_ekg_a_qrs'] = $_POST['m_car_ekg_a_qrs'];
        $params_2[':m_car_ekg_at'] = $_POST['m_car_ekg_at'];
        $params_2[':m_car_ekg_onda_p'] = $_POST['m_car_ekg_onda_p'];
        $params_2[':m_car_ekg_complejos_qrs'] = $_POST['m_car_ekg_complejos_qrs'];
        $params_2[':m_car_ekg_segmento_s_t'] = $_POST['m_car_ekg_segmento_s_t'];
        $params_2[':m_car_ekg_onda_t'] = $_POST['m_car_ekg_onda_t'];
        $params_2[':m_car_ekg_quindina'] = $_POST['m_car_ekg_quindina'];
        $params_2[':m_car_ekg_otros_hallazgo'] = $_POST['m_car_ekg_otros_hallazgo'];
        $params_2[':m_car_ekg_antecedentes'] = $_POST['m_car_ekg_antecedentes'];
        $params_2[':m_car_ekg_sintomas'] = $_POST['m_car_ekg_sintomas'];
        $params_2[':m_car_ekg_descripcion'] = $_POST['m_car_ekg_descripcion'];

        $q_2 = 'Update mod_cardio_ekg set
                    m_car_ekg_frec_auricular=:m_car_ekg_frec_auricular,
                    m_car_ekg_frec_ventricular=:m_car_ekg_frec_ventricular,
                    m_car_ekg_ritmo=:m_car_ekg_ritmo,
                    m_car_ekg_intervalo_p_r=:m_car_ekg_intervalo_p_r,
                    m_car_ekg_qrs=:m_car_ekg_qrs,
                    m_car_ekg_q_t=:m_car_ekg_q_t,
                    m_car_ekg_ap=:m_car_ekg_ap,
                    m_car_ekg_a_qrs=:m_car_ekg_a_qrs,
                    m_car_ekg_at=:m_car_ekg_at,
                    m_car_ekg_onda_p=:m_car_ekg_onda_p,
                    m_car_ekg_complejos_qrs=:m_car_ekg_complejos_qrs,
                    m_car_ekg_segmento_s_t=:m_car_ekg_segmento_s_t,
                    m_car_ekg_onda_t=:m_car_ekg_onda_t,
                    m_car_ekg_quindina=:m_car_ekg_quindina,
                    m_car_ekg_otros_hallazgo=:m_car_ekg_otros_hallazgo,
                    m_car_ekg_antecedentes=:m_car_ekg_antecedentes,
                    m_car_ekg_sintomas=:m_car_ekg_sintomas,
                    m_car_ekg_descripcion=:m_car_ekg_descripcion
                where
                m_car_ekg_adm=:adm;';


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

    //LOAD SAVE UPDATE PDF

    public function conclusion($adm) {
        $q = "SELECT upper(conclusion_cardio_desc) conclu FROM mod_cardio_conclusion where conclusion_cardio_adm=$adm";
        $verifica = $this->sql($q);

        $diag = '';
        foreach ($verifica->data as $i => $vali) {
            $diag .= $i + 1 . ')' . $vali->conclu . '  -  ';
        }
        foreach ($verifica->data as $i => $vali) {
            $vali->conclu2 = $diag;
        }
        return $verifica;
    }

    public function list_conclusion() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT *
                FROM mod_cardio_conclusion
                where conclusion_cardio_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_conclu() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT conclusion_cardio_desc 
                            FROM mod_cardio_conclusion
                            where
                            conclusion_cardio_desc like '%$query%'
                            group by conclusion_cardio_desc");
        return $sql;
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

    public function load_conclu() {
        $diag_adm = $_POST['conclusion_cardio_adm'];
        $diag_id = $_POST['conclusion_cardio_id'];
        $query = "SELECT
            conclusion_cardio_id, conclusion_cardio_adm, conclusion_cardio_desc
            FROM mod_cardio_conclusion
            where
            conclusion_cardio_id=$diag_id and
            conclusion_cardio_adm=$diag_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_conclu() {
        $params = array();
        $params[':conclusion_cardio_adm'] = $_POST['conclusion_cardio_adm'];
        ($_POST['conclusion_cardio_tipo'] == 1) ? $params[':conclusion_cardio_desc'] = $_POST['conclusion_cardio_desc'] : $params[':conclusion_cardio_desc'] = $_POST['conclusion_cardio_cie'];

        $q = 'INSERT INTO mod_cardio_conclusion VALUES 
                (NULL,
                :conclusion_cardio_adm,
                UPPER(:conclusion_cardio_desc))';
        return $this->sql($q, $params);
    }

    public function update_conclu() {
        $params = array();
        $params[':conclusion_cardio_id'] = $_POST['conclusion_cardio_id'];
        $params[':conclusion_cardio_adm'] = $_POST['conclusion_cardio_adm'];
        ($_POST['conclusion_cardio_tipo'] == 1) ? $params[':conclusion_cardio_desc'] = $_POST['conclusion_cardio_desc'] : $params[':conclusion_cardio_desc'] = $_POST['conclusion_cardio_cie'];

        $this->begin();
        $q = 'Update mod_cardio_conclusion set
                conclusion_cardio_desc=UPPER(:conclusion_cardio_desc)
                where
                conclusion_cardio_id=:conclusion_cardio_id and conclusion_cardio_adm=:conclusion_cardio_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['conclusion_cardio_id'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
        }
    }

    //PDF

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

    public function mod_cardio_ekg_report($adm) {
        $sql = $this->sql("SELECT *
        FROM mod_cardio_ekg
        where m_car_ekg_adm=$adm;");
        return $sql;
    }

}

//$sesion = new model(); 
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
?>