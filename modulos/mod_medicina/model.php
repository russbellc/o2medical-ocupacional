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
                    ,count(adm_id) nro_examenes,adm_aptitud
                    #,if((adm_pdf)=1,1,0) pdf
                    FROM admision
                    inner join paciente on adm_pac=pac_id
                    inner join pack on adm_ruta=pk_id
                    left join empresa on pk_emp=emp_id
                    left join tficha on adm_tficha=tfi_id
                    inner join dpack on dpk_pkid=pk_id
                    inner join examen on ex_id=dpk_exid
                    where
                    ex_arid IN (9)
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
            $verifica = $this->sql("SELECT count(m_medicina_adm)total FROM mod_medicina where m_medicina_adm=$adm_id;");
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
                where ex_arid IN (9) and adm_id=$adm_id order by ex_arid;";
        $sql = $this->sql($q);
        foreach ($sql->data as $i => $value) {
            $ex_id = $value->ex_id;
            $verifica = $this->sql("SELECT m_medicina_id id,m_medicina_st st, m_medicina_usu usu, m_medicina_fech_reg fech 
            FROM mod_medicina where m_medicina_adm=$adm_id and m_medicina_examen=$ex_id;");
            $value->st = $verifica->data[0]->st;
            $value->usu = $verifica->data[0]->usu;
            $value->fech = $verifica->data[0]->fech;
            $value->id = $verifica->data[0]->id;
        }
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function list_empre()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $q = "SELECT emp_id, emp_desc, emp_acro FROM empresa where ";
        $empresa = $this->user->empresas;
        ($this->user->acceso == 1) ? $q .= " concat(emp_id, emp_desc, emp_acro )like'%$query%';" : $q .= " emp_id IN ($empresa) ";
        $sql = $this->sql($q);
        return $sql;
    }

    public function st_busca_puesto_postula()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_puesto_postula FROM mod_medicina_anexo16
                            where
                            m_med_puesto_postula like '%$query%'
                            group by m_med_puesto_postula");
        return $sql;
    }

    public function st_busca_area()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_area FROM mod_medicina_anexo16
                            where
                            m_med_area like '%$query%'
                            group by m_med_area");
        return $sql;
    }

    public function st_busca_puesto_actual()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_puesto_actual FROM mod_medicina_anexo16
                            where
                            m_med_puesto_actual like '%$query%'
                            group by m_med_puesto_actual");
        return $sql;
    }

    public function st_busca_eq_opera()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_eq_opera FROM mod_medicina_anexo16
                            where
                            m_med_eq_opera like '%$query%'
                            group by m_med_eq_opera");
        return $sql;
    }

    public function st_busca_piel_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_piel_desc FROM mod_medicina_anexo16
                            where
                            m_med_piel_desc like '%$query%'
                            group by m_med_piel_desc");
        return $sql;
    }

    public function st_busca_piel_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_piel_dx FROM mod_medicina_anexo16
                            where
                            m_med_piel_dx like '%$query%'
                            group by m_med_piel_dx");
        return $sql;
    }

    public function st_busca_cabeza_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_cabeza_desc FROM mod_medicina_anexo16
                            where
                            m_med_cabeza_desc like '%$query%'
                            group by m_med_cabeza_desc");
        return $sql;
    }

    public function st_busca_cabeza_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_cabeza_dx FROM mod_medicina_anexo16
                            where
                            m_med_cabeza_dx like '%$query%'
                            group by m_med_cabeza_dx");
        return $sql;
    }

    public function st_busca_cuello_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_cuello_desc FROM mod_medicina_anexo16
                            where
                            m_med_cuello_desc like '%$query%'
                            group by m_med_cuello_desc");
        return $sql;
    }

    public function st_busca_cuello_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_cuello_dx FROM mod_medicina_anexo16
                            where
                            m_med_cuello_dx like '%$query%'
                            group by m_med_cuello_dx");
        return $sql;
    }

    public function st_busca_nariz_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_nariz_desc FROM mod_medicina_anexo16
                            where
                            m_med_nariz_desc like '%$query%'
                            group by m_med_nariz_desc");
        return $sql;
    }

    public function st_busca_nariz_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_nariz_dx FROM mod_medicina_anexo16
                            where
                            m_med_nariz_dx like '%$query%'
                            group by m_med_nariz_dx");
        return $sql;
    }

    public function st_busca_boca_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_boca_desc FROM mod_medicina_anexo16
                            where
                            m_med_boca_desc like '%$query%'
                            group by m_med_boca_desc");
        return $sql;
    }

    public function st_busca_boca_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_boca_dx FROM mod_medicina_anexo16
                            where
                            m_med_boca_dx like '%$query%'
                            group by m_med_boca_dx");
        return $sql;
    }

    public function st_busca_torax_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_torax_desc FROM mod_medicina_anexo16
                            where
                            m_med_torax_desc like '%$query%'
                            group by m_med_torax_desc");
        return $sql;
    }

    public function st_busca_torax_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_torax_dx FROM mod_medicina_anexo16
                            where
                            m_med_torax_dx like '%$query%'
                            group by m_med_torax_dx");
        return $sql;
    }

    public function st_busca_corazon_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_corazon_desc FROM mod_medicina_anexo16
                            where
                            m_med_corazon_desc like '%$query%'
                            group by m_med_corazon_desc");
        return $sql;
    }

    public function st_busca_corazon_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_corazon_dx FROM mod_medicina_anexo16
                            where
                            m_med_corazon_dx like '%$query%'
                            group by m_med_corazon_dx");
        return $sql;
    }

    public function st_busca_mamas_derecho()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_mamas_derecho FROM mod_medicina_anexo16
                            where
                            m_med_mamas_derecho like '%$query%'
                            group by m_med_mamas_derecho");
        return $sql;
    }

    public function st_busca_mamas_izquier()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_mamas_izquier FROM mod_medicina_anexo16
                            where
                            m_med_mamas_izquier like '%$query%'
                            group by m_med_mamas_izquier");
        return $sql;
    }

    public function st_busca_pulmon_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_pulmon_desc FROM mod_medicina_anexo16
                            where
                            m_med_pulmon_desc like '%$query%'
                            group by m_med_pulmon_desc");
        return $sql;
    }

    public function st_busca_pulmon_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_pulmon_dx FROM mod_medicina_anexo16
                            where
                            m_med_pulmon_dx like '%$query%'
                            group by m_med_pulmon_dx");
        return $sql;
    }

    public function st_busca_abdomen()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_abdomen FROM mod_medicina_anexo16
                            where
                            m_med_abdomen like '%$query%'
                            group by m_med_abdomen");
        return $sql;
    }

    public function st_busca_abdomen_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_abdomen_desc FROM mod_medicina_anexo16
                            where
                            m_med_abdomen_desc like '%$query%'
                            group by m_med_abdomen_desc");
        return $sql;
    }

    public function st_busca_tacto_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_tacto_desc FROM mod_medicina_anexo16
                            where
                            m_med_tacto_desc like '%$query%'
                            group by m_med_tacto_desc");
        return $sql;
    }

    public function st_busca_anillos_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_anillos_desc FROM mod_medicina_anexo16
                            where
                            m_med_anillos_desc like '%$query%'
                            group by m_med_anillos_desc");
        return $sql;
    }

    public function st_busca_hernia_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_hernia_desc FROM mod_medicina_anexo16
                            where
                            m_med_hernia_desc like '%$query%'
                            group by m_med_hernia_desc");
        return $sql;
    }

    public function st_busca_varices_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_varices_desc FROM mod_medicina_anexo16
                            where
                            m_med_varices_desc like '%$query%'
                            group by m_med_varices_desc");
        return $sql;
    }

    public function st_busca_genitales_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_genitales_desc FROM mod_medicina_anexo16
                            where
                            m_med_genitales_desc like '%$query%'
                            group by m_med_genitales_desc");
        return $sql;
    }

    public function st_busca_genitales_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_genitales_dx FROM mod_medicina_anexo16
                            where
                            m_med_genitales_dx like '%$query%'
                            group by m_med_genitales_dx");
        return $sql;
    }

    public function st_busca_ganglios_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_ganglios_desc FROM mod_medicina_anexo16
                            where
                            m_med_ganglios_desc like '%$query%'
                            group by m_med_ganglios_desc");
        return $sql;
    }

    public function st_busca_ganglios_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_ganglios_dx FROM mod_medicina_anexo16
                            where
                            m_med_ganglios_dx like '%$query%'
                            group by m_med_ganglios_dx");
        return $sql;
    }

    public function st_busca_lenguaje_desc()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_lenguaje_desc FROM mod_medicina_anexo16
                            where
                            m_med_lenguaje_desc like '%$query%'
                            group by m_med_lenguaje_desc");
        return $sql;
    }

    public function st_busca_lenguaje_dx()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_med_lenguaje_dx FROM mod_medicina_anexo16
                            where
                            m_med_lenguaje_dx like '%$query%'
                            group by m_med_lenguaje_dx");
        return $sql;
    }

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

    //DIAGNOSTICO
    public function list_diag()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT diag_id, diag_adm, diag_desc
                FROM diagnostico
                where diag_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_diag()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT diag_desc FROM diagnostico
                            where
                            diag_desc like '%$query%'
                            group by diag_desc");
        return $sql;
    }

    public function save_diag()
    {
        $params = array();
        $params[':diag_adm'] = $_POST['diag_adm'];
        ($_POST['diag_tipo'] == 1) ? $params[':diag_desc'] = $_POST['diag_desc'] : $params[':diag_desc'] = $_POST['diag_cie'];

        $q = 'INSERT INTO diagnostico VALUES 
                (NULL,
                :diag_adm,
                UPPER(:diag_desc))';
        return $this->sql($q, $params);
    }

    public function update_diag()
    {
        $params = array();
        $params[':diag_id'] = $_POST['diag_id'];
        $params[':diag_adm'] = $_POST['diag_adm'];
        ($_POST['diag_tipo'] == 1) ? $params[':diag_desc'] = $_POST['diag_desc'] : $params[':diag_desc'] = $_POST['diag_cie'];

        $this->begin();
        $q = 'Update diagnostico set
                diag_desc=UPPER(:diag_desc)
                where
                diag_id=:diag_id and diag_adm=:diag_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['diag_id'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_diag()
    {
        $diag_adm = $_POST['diag_adm'];
        $diag_id = $_POST['diag_id'];
        $query = "SELECT
            diag_id, diag_adm, diag_desc
            FROM diagnostico
            where
            diag_id=$diag_id and
            diag_adm=$diag_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //OBSERVACIONES/////////////////
    public function list_obs()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT obs_id, obs_adm, obs_desc, obs_plazo
                FROM observaciones
                where obs_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_obs()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT obs_desc FROM observaciones
                            where
                            obs_desc like '%$query%'
                            group by obs_desc");
        return $sql;
    }

    public function save_obs()
    {
        $params = array();
        $params[':obs_adm'] = $_POST['obs_adm'];
        $params[':obs_desc'] = $_POST['obs_desc'];
        $params[':obs_plazo'] = $_POST['obs_plazo'];
        $q = 'INSERT INTO observaciones VALUES 
                (NULL,
                :obs_adm,
                UPPER(:obs_desc),
                :obs_plazo)';
        return $this->sql($q, $params);
    }

    public function update_obs()
    {
        $params = array();
        $params[':obs_id'] = $_POST['obs_id'];
        $params[':obs_adm'] = $_POST['obs_adm'];
        $params[':obs_desc'] = $_POST['obs_desc'];
        $params[':obs_plazo'] = $_POST['obs_plazo'];

        $this->begin();
        $q = 'Update observaciones set
                obs_desc=UPPER(:obs_desc),obs_plazo=:obs_plazo
                where
                obs_id=:obs_id and obs_adm=:obs_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $obs_id = $_POST['obs_id'];
            $this->commit();
            return array('success' => true, 'data' => $obs_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_obs()
    {
        $obs_adm = $_POST['obs_adm'];
        $obs_id = $_POST['obs_id'];
        $query = "SELECT
            obs_id, obs_adm, obs_desc, obs_plazo
            FROM observaciones
            where
            obs_id=$obs_id and
            obs_adm=$obs_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //RESTRICCIONES
    public function list_restric()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT restric_id, restric_adm, restric_desc, restric_plazo
                FROM restricciones
                where restric_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_restric()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT restric_desc FROM restricciones
                            where
                            restric_desc like '%$query%'
                            group by restric_desc");
        return $sql;
    }

    public function save_restric()
    {
        $params = array();
        $params[':restric_adm'] = $_POST['restric_adm'];
        $params[':restric_desc'] = $_POST['restric_desc'];
        $params[':restric_plazo'] = $_POST['restric_plazo'];

        $q = 'INSERT INTO restricciones VALUES 
                (NULL,
                :restric_adm,
                UPPER(:restric_desc),
                :restric_plazo)';
        return $this->sql($q, $params);
    }

    public function update_restric()
    {
        $params = array();
        $params[':restric_id'] = $_POST['restric_id'];
        $params[':restric_adm'] = $_POST['restric_adm'];
        $params[':restric_desc'] = $_POST['restric_desc'];
        $params[':restric_plazo'] = $_POST['restric_plazo'];

        $this->begin();
        $q = 'Update restricciones set
                restric_desc=UPPER(:restric_desc),
				restric_plazo=:restric_plazo
                where
                restric_id=:restric_id and restric_adm=:restric_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $restric_id = $_POST['restric_id'];
            $this->commit();
            return array('success' => true, 'data' => $restric_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_restric()
    {
        $restric_adm = $_POST['restric_adm'];
        $restric_id = $_POST['restric_id'];
        $query = "SELECT
            restric_id, restric_adm, restric_desc, restric_plazo
            FROM restricciones
            where
            restric_id=$restric_id and
            restric_adm=$restric_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //INTERCONSULTAS
    public function list_inter()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT inter_id, inter_adm, inter_desc, inter_plazo
                FROM interconsultas
                where inter_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_inter()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT inter_desc FROM interconsultas
                            where
                            inter_desc like '%$query%'
                            group by inter_desc");
        return $sql;
    }

    public function save_inter()
    {
        $params = array();
        $params[':inter_adm'] = $_POST['inter_adm'];
        $params[':inter_desc'] = $_POST['inter_desc'];
        $params[':inter_plazo'] = $_POST['inter_plazo'];

        $q = 'INSERT INTO interconsultas VALUES 
                (NULL,
                :inter_adm,
                UPPER(:inter_desc),
                :inter_plazo)';
        return $this->sql($q, $params);
    }

    public function update_inter()
    {
        $params = array();
        $params[':inter_id'] = $_POST['inter_id'];
        $params[':inter_adm'] = $_POST['inter_adm'];
        $params[':inter_desc'] = $_POST['inter_desc'];
        $params[':inter_plazo'] = $_POST['inter_plazo'];

        $this->begin();
        $q = 'Update interconsultas set
                inter_desc=UPPER(:inter_desc),
				inter_plazo=:inter_plazo
                where
                inter_id=:inter_id and inter_adm=:inter_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $inter_id = $_POST['inter_id'];
            $this->commit();
            return array('success' => true, 'data' => $inter_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_inter()
    {
        $inter_adm = $_POST['inter_adm'];
        $inter_id = $_POST['inter_id'];
        $query = "SELECT
            inter_id, inter_adm, inter_desc, inter_plazo
            FROM interconsultas
            where
            inter_id=$inter_id and
            inter_adm=$inter_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //RECOMENDACIONES
    public function list_recom()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT recom_id, recom_adm, recom_desc, recom_plazo
                FROM recomendaciones
                where recom_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_recom()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT recom_desc FROM recomendaciones
                            where
                            recom_desc like '%$query%'
                            group by recom_desc");
        return $sql;
    }

    public function save_recom()
    {
        $params = array();
        $params[':recom_adm'] = $_POST['recom_adm'];
        $params[':recom_desc'] = $_POST['recom_desc'];
        $params[':recom_plazo'] = $_POST['recom_plazo'];

        $q = 'INSERT INTO recomendaciones VALUES 
                (NULL,
                :recom_adm,
                UPPER(:recom_desc),
                :recom_plazo)';
        return $this->sql($q, $params);
    }

    public function update_recom()
    {
        $params = array();
        $params[':recom_id'] = $_POST['recom_id'];
        $params[':recom_adm'] = $_POST['recom_adm'];
        $params[':recom_desc'] = $_POST['recom_desc'];
        $params[':recom_plazo'] = $_POST['recom_plazo'];

        $this->begin();
        $q = 'Update recomendaciones set
                recom_desc=UPPER(:recom_desc),
				recom_plazo=:recom_plazo
                where
                recom_id=:recom_id and recom_adm=:recom_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $recom_id = $_POST['recom_id'];
            $this->commit();
            return array('success' => true, 'data' => $recom_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_recom()
    {
        $recom_adm = $_POST['recom_adm'];
        $recom_id = $_POST['recom_id'];
        $query = "SELECT
            recom_id, recom_adm, recom_desc, recom_plazo
            FROM recomendaciones
            where
            recom_id=$recom_id and
            recom_adm=$recom_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //SAVE UPDATE LOAD NuevoAnexo16

    public function load_nuevoAnexo16()
    {
        $ficha7c_adm = $_POST['ficha7c_adm'];
        $ficha7c_exa = $_POST['ficha7c_exa'];
        $query = "SELECT * FROM mod_medicina_anexo16 where m_med_adm='$ficha7c_adm' and m_med_exa='$ficha7c_exa';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_nuevoAnexo16()
    {
        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];
        $this->begin();
        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];
        $usuario = $this->user->us_id;

        $params[':m_medicina_st'] = '1'; //modulo principal
        //        $params[':m_med_adm'] = $_POST['adm'];
        //        $params[':m_med_exa'] = $_POST['ex_id'];


        $params[':m_med_contac_nom'] = $_POST['m_med_contac_nom'];
        $params[':m_med_contac_parent'] = $_POST['m_med_contac_parent'];
        $params[':m_med_contac_telf'] = $_POST['m_med_contac_telf'];
        $params[':m_med_puesto_postula'] = $_POST['m_med_puesto_postula'];
        $params[':m_med_area'] = $_POST['m_med_area'];
        $params[':m_med_puesto_actual'] = $_POST['m_med_puesto_actual'];
        $params[':m_med_tiempo'] = $_POST['m_med_tiempo'];
        $params[':m_med_eq_opera'] = $_POST['m_med_eq_opera'];
        $params[':m_med_fech_ingreso'] = $_POST['m_med_fech_ingreso'];


        $timestamp = strtotime($_POST['m_med_fech_ingreso']);
        $m_med_fech_ingreso = ((strlen($_POST['m_med_fech_ingreso']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params[':m_med_fech_ingreso'] = $m_med_fech_ingreso;

        $params[':m_med_reubicacion'] = $_POST['m_med_reubicacion'];
        $params[':m_med_tip_opera'] = $_POST['m_med_tip_opera'];
        $params[':m_med_minerales'] = $_POST['m_med_minerales'];
        $params[':m_med_altura_lab'] = $_POST['m_med_altura_lab'];
        $params[':m_med_rl_bio1'] = $_POST['m_med_rl_bio1'];
        $params[':m_med_rl_ergo1'] = $_POST['m_med_rl_ergo1'];
        $params[':m_med_rl_ergo2'] = $_POST['m_med_rl_ergo2'];
        $params[':m_med_rl_ergo3'] = $_POST['m_med_rl_ergo3'];
        $params[':m_med_rl_ergo4'] = $_POST['m_med_rl_ergo4'];
        $params[':m_med_rl_ergo5'] = $_POST['m_med_rl_ergo5'];
        $params[':m_med_rl_fisico1'] = $_POST['m_med_rl_fisico1'];
        $params[':m_med_rl_fisico2'] = $_POST['m_med_rl_fisico2'];
        $params[':m_med_rl_fisico3'] = $_POST['m_med_rl_fisico3'];
        $params[':m_med_rl_fisico4'] = $_POST['m_med_rl_fisico4'];
        $params[':m_med_rl_fisico5'] = $_POST['m_med_rl_fisico5'];
        $params[':m_med_rl_fisico6'] = $_POST['m_med_rl_fisico6'];
        $params[':m_med_rl_fisico7'] = $_POST['m_med_rl_fisico7'];
        $params[':m_med_rl_fisico8'] = $_POST['m_med_rl_fisico8'];
        $params[':m_med_rl_fisico9'] = $_POST['m_med_rl_fisico9'];
        $params[':m_med_rl_fisico10'] = $_POST['m_med_rl_fisico10'];
        $params[':m_med_rl_psico1'] = $_POST['m_med_rl_psico1'];
        $params[':m_med_rl_psico2'] = $_POST['m_med_rl_psico2'];
        $params[':m_med_rl_psico3'] = $_POST['m_med_rl_psico3'];
        $params[':m_med_rl_psico4'] = $_POST['m_med_rl_psico4'];
        $params[':m_med_rl_quimi1'] = $_POST['m_med_rl_quimi1'];
        $params[':m_med_rl_quimi2'] = $_POST['m_med_rl_quimi2'];
        $params[':m_med_rl_quimi3'] = $_POST['m_med_rl_quimi3'];
        $params[':m_med_rl_quimi4'] = $_POST['m_med_rl_quimi4'];
        $params[':m_med_rl_quimi5'] = $_POST['m_med_rl_quimi5'];
        $params[':m_med_rl_quimi6'] = $_POST['m_med_rl_quimi6'];
        $params[':m_med_rl_quimi7'] = $_POST['m_med_rl_quimi7'];


        $timestamp0 = strtotime($_POST['m_med_muj_fur']);
        $m_med_muj_fur = ((strlen($_POST['m_med_muj_fur']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':m_med_muj_fur'] = $m_med_muj_fur;

        $params[':m_med_muj_rc'] = $_POST['m_med_muj_rc'];
        $params[':m_med_muj_g'] = $_POST['m_med_muj_g'];
        $params[':m_med_muj_p'] = $_POST['m_med_muj_p'];
        $params[':m_med_muj_ult_pap'] = $_POST['m_med_muj_ult_pap'];


        $timestamp1 = strtotime($_POST['m_med_muj_ult_pap']);
        $m_med_muj_ult_pap = ((strlen($_POST['m_med_muj_ult_pap']) > 0) ? date('Y-m-d', $timestamp1) : null);
        $params[':m_med_muj_ult_pap'] = $m_med_muj_ult_pap;

        $params[':m_med_muj_resul'] = $_POST['m_med_muj_resul'];
        $params[':m_med_muj_mac'] = $_POST['m_med_muj_mac'];
        $params[':m_med_muj_obs'] = $_POST['m_med_muj_obs'];
        $params[':m_med_muj_a'] = $_POST['m_med_muj_a'];
        $params[':m_med_muj_b'] = $_POST['m_med_muj_b'];
        $params[':m_med_muj_c'] = $_POST['m_med_muj_c'];
        $params[':m_med_muj_d'] = $_POST['m_med_muj_d'];
        $params[':m_med_muj_e'] = $_POST['m_med_muj_e'];
        $params[':m_med_cardio_op01'] = $_POST['m_med_cardio_op01'];
        $params[':m_med_cardio_op02'] = $_POST['m_med_cardio_op02'];
        $params[':m_med_cardio_desc02'] = $_POST['m_med_cardio_desc02'];
        $params[':m_med_cardio_op03'] = $_POST['m_med_cardio_op03'];
        $params[':m_med_cardio_desc03'] = $_POST['m_med_cardio_desc03'];
        $params[':m_med_cardio_op04'] = $_POST['m_med_cardio_op04'];
        $params[':m_med_cardio_desc04'] = $_POST['m_med_cardio_desc04'];
        $params[':m_med_cardio_op05'] = $_POST['m_med_cardio_op05'];
        $params[':m_med_cardio_desc05'] = $_POST['m_med_cardio_desc05'];
        $params[':m_med_cardio_op06'] = $_POST['m_med_cardio_op06'];
        $params[':m_med_cardio_desc06'] = $_POST['m_med_cardio_desc06'];
        $params[':m_med_cardio_op07'] = $_POST['m_med_cardio_op07'];
        $params[':m_med_cardio_desc07'] = $_POST['m_med_cardio_desc07'];
        $params[':m_med_cardio_op08'] = $_POST['m_med_cardio_op08'];
        $params[':m_med_cardio_desc08'] = $_POST['m_med_cardio_desc08'];
        $params[':m_med_cardio_op09'] = $_POST['m_med_cardio_op09'];
        $params[':m_med_cardio_desc09'] = $_POST['m_med_cardio_desc09'];
        $params[':m_med_cardio_op10'] = $_POST['m_med_cardio_op10'];
        $params[':m_med_cardio_desc10'] = $_POST['m_med_cardio_desc10'];
        $params[':m_med_cardio_op11'] = $_POST['m_med_cardio_op11'];
        $params[':m_med_cardio_desc11'] = $_POST['m_med_cardio_desc11'];
        $params[':m_med_cardio_op12'] = $_POST['m_med_cardio_op12'];
        $params[':m_med_cardio_desc12'] = $_POST['m_med_cardio_desc12'];
        $params[':m_med_cardio_op13'] = $_POST['m_med_cardio_op13'];
        $params[':m_med_cardio_desc13'] = $_POST['m_med_cardio_desc13'];
        $params[':m_med_cardio_op14'] = $_POST['m_med_cardio_op14'];
        $params[':m_med_cardio_desc14'] = $_POST['m_med_cardio_desc14'];
        $params[':m_med_cardio_op15'] = $_POST['m_med_cardio_op15'];
        $params[':m_med_cardio_desc15'] = $_POST['m_med_cardio_desc15'];
        $params[':m_med_cardio_op16'] = $_POST['m_med_cardio_op16'];
        $params[':m_med_cardio_desc16'] = $_POST['m_med_cardio_desc16'];
        $params[':m_med_cardio_op17'] = $_POST['m_med_cardio_op17'];
        $params[':m_med_cardio_desc17'] = $_POST['m_med_cardio_desc17'];
        $params[':m_med_cardio_op18'] = $_POST['m_med_cardio_op18'];
        $params[':m_med_cardio_desc18'] = $_POST['m_med_cardio_desc18'];
        $params[':m_med_tabaco'] = $_POST['m_med_tabaco'];
        $params[':m_med_alcohol'] = $_POST['m_med_alcohol'];
        $params[':m_med_coca'] = $_POST['m_med_coca'];
        $params[':m_med_fam_papa'] = $_POST['m_med_fam_papa'];
        $params[':m_med_fam_mama'] = $_POST['m_med_fam_mama'];
        $params[':m_med_fam_herma'] = $_POST['m_med_fam_herma'];
        $params[':m_med_fam_hijos'] = $_POST['m_med_fam_hijos'];
        $params[':m_med_fam_h_vivos'] = $_POST['m_med_fam_h_vivos'];
        $params[':m_med_fam_h_muertos'] = $_POST['m_med_fam_h_muertos'];
        $params[':m_med_fam_infarto55'] = $_POST['m_med_fam_infarto55'];
        $params[':m_med_fam_infarto65'] = $_POST['m_med_fam_infarto65'];
        $params[':m_med_piel_desc'] = $_POST['m_med_piel_desc'];
        $params[':m_med_piel_dx'] = $_POST['m_med_piel_dx'];
        $params[':m_med_cabeza_desc'] = $_POST['m_med_cabeza_desc'];
        $params[':m_med_cabeza_dx'] = $_POST['m_med_cabeza_dx'];
        $params[':m_med_cuello_desc'] = $_POST['m_med_cuello_desc'];
        $params[':m_med_cuello_dx'] = $_POST['m_med_cuello_dx'];
        $params[':m_med_nariz_desc'] = $_POST['m_med_nariz_desc'];
        $params[':m_med_nariz_dx'] = $_POST['m_med_nariz_dx'];
        $params[':m_med_boca_desc'] = $_POST['m_med_boca_desc'];
        $params[':m_med_boca_dx'] = $_POST['m_med_boca_dx'];
        $params[':m_med_oido_der01'] = $_POST['m_med_oido_der01'];
        $params[':m_med_oido_der02'] = $_POST['m_med_oido_der02'];
        $params[':m_med_oido_der03'] = $_POST['m_med_oido_der03'];
        $params[':m_med_oido_der04'] = $_POST['m_med_oido_der04'];
        $params[':m_med_oido_izq01'] = $_POST['m_med_oido_izq01'];
        $params[':m_med_oido_izq02'] = $_POST['m_med_oido_izq02'];
        $params[':m_med_oido_izq03'] = $_POST['m_med_oido_izq03'];
        $params[':m_med_oido_izq04'] = $_POST['m_med_oido_izq04'];
        $params[':m_med_torax_desc'] = $_POST['m_med_torax_desc'];
        $params[':m_med_torax_dx'] = $_POST['m_med_torax_dx'];
        $params[':m_med_corazon_desc'] = $_POST['m_med_corazon_desc'];
        $params[':m_med_corazon_dx'] = $_POST['m_med_corazon_dx'];
        $params[':m_med_mamas_derecho'] = $_POST['m_med_mamas_derecho'];
        $params[':m_med_mamas_izquier'] = $_POST['m_med_mamas_izquier'];
        $params[':m_med_pulmon_desc'] = $_POST['m_med_pulmon_desc'];
        $params[':m_med_pulmon_dx'] = $_POST['m_med_pulmon_dx'];
        $params[':m_med_osteo_aptitud'] = $_POST['m_med_osteo_aptitud'];
        $params[':m_med_osteo_desc'] = $_POST['m_med_osteo_desc'];
        $params[':m_med_abdomen'] = $_POST['m_med_abdomen'];
        $params[':m_med_abdomen_desc'] = $_POST['m_med_abdomen_desc'];
        $params[':m_med_pru_sup_der'] = $_POST['m_med_pru_sup_der'];
        $params[':m_med_pru_med_der'] = $_POST['m_med_pru_med_der'];
        $params[':m_med_pru_inf_der'] = $_POST['m_med_pru_inf_der'];
        $params[':m_med_ppl_der'] = $_POST['m_med_ppl_der'];
        $params[':m_med_pru_sup_izq'] = $_POST['m_med_pru_sup_izq'];
        $params[':m_med_pru_med_izq'] = $_POST['m_med_pru_med_izq'];
        $params[':m_med_pru_inf_izq'] = $_POST['m_med_pru_inf_izq'];
        $params[':m_med_ppl_izq'] = $_POST['m_med_ppl_izq'];
        $params[':m_med_tacto'] = $_POST['m_med_tacto'];
        $params[':m_med_tacto_desc'] = $_POST['m_med_tacto_desc'];
        $params[':m_med_anillos'] = $_POST['m_med_anillos'];
        $params[':m_med_anillos_desc'] = $_POST['m_med_anillos_desc'];
        $params[':m_med_hernia'] = $_POST['m_med_hernia'];
        $params[':m_med_hernia_desc'] = $_POST['m_med_hernia_desc'];
        $params[':m_med_varices'] = $_POST['m_med_varices'];
        $params[':m_med_varices_desc'] = $_POST['m_med_varices_desc'];
        $params[':m_med_genitales_desc'] = $_POST['m_med_genitales_desc'];
        $params[':m_med_genitales_dx'] = $_POST['m_med_genitales_dx'];
        $params[':m_med_ganglios_desc'] = $_POST['m_med_ganglios_desc'];
        $params[':m_med_ganglios_dx'] = $_POST['m_med_ganglios_dx'];
        $params[':m_med_lenguaje_desc'] = $_POST['m_med_lenguaje_desc'];
        $params[':m_med_lenguaje_dx'] = $_POST['m_med_lenguaje_dx'];
        $params[':m_med_aptitud'] = $_POST['m_med_aptitud'];

        $timestamp2 = strtotime($_POST['m_med_fech_val']);
        $params[':m_med_fech_val'] = date('Y-m-d', $timestamp2);

        $q = "INSERT INTO mod_medicina_anexo16 VALUES 
                (NULL,
                :adm,
                :ex_id,
                :m_med_contac_nom,
                :m_med_contac_parent,
                :m_med_contac_telf,
                :m_med_puesto_postula,
                :m_med_area,
                :m_med_puesto_actual,
                :m_med_tiempo,
                :m_med_eq_opera,
                :m_med_fech_ingreso,
                :m_med_reubicacion,
                :m_med_tip_opera,
                :m_med_minerales,
                :m_med_altura_lab,
                :m_med_rl_bio1,
                :m_med_rl_ergo1,
                :m_med_rl_ergo2,
                :m_med_rl_ergo3,
                :m_med_rl_ergo4,
                :m_med_rl_ergo5,
                :m_med_rl_fisico1,
                :m_med_rl_fisico2,
                :m_med_rl_fisico3,
                :m_med_rl_fisico4,
                :m_med_rl_fisico5,
                :m_med_rl_fisico6,
                :m_med_rl_fisico7,
                :m_med_rl_fisico8,
                :m_med_rl_fisico9,
                :m_med_rl_fisico10,
                :m_med_rl_psico1,
                :m_med_rl_psico2,
                :m_med_rl_psico3,
                :m_med_rl_psico4,
                :m_med_rl_quimi1,
                :m_med_rl_quimi2,
                :m_med_rl_quimi3,
                :m_med_rl_quimi4,
                :m_med_rl_quimi5,
                :m_med_rl_quimi6,
                :m_med_rl_quimi7,
                :m_med_muj_fur,
                :m_med_muj_rc,
                :m_med_muj_g,
                :m_med_muj_p,
                :m_med_muj_ult_pap,
                :m_med_muj_resul,
                :m_med_muj_mac,
                :m_med_muj_obs,
                :m_med_muj_a,
                :m_med_muj_b,
                :m_med_muj_c,
                :m_med_muj_d,
                :m_med_muj_e,
                :m_med_cardio_op01,
                :m_med_cardio_op02,
                :m_med_cardio_desc02,
                :m_med_cardio_op03,
                :m_med_cardio_desc03,
                :m_med_cardio_op04,
                :m_med_cardio_desc04,
                :m_med_cardio_op05,
                :m_med_cardio_desc05,
                :m_med_cardio_op06,
                :m_med_cardio_desc06,
                :m_med_cardio_op07,
                :m_med_cardio_desc07,
                :m_med_cardio_op08,
                :m_med_cardio_desc08,
                :m_med_cardio_op09,
                :m_med_cardio_desc09,
                :m_med_cardio_op10,
                :m_med_cardio_desc10,
                :m_med_cardio_op11,
                :m_med_cardio_desc11,
                :m_med_cardio_op12,
                :m_med_cardio_desc12,
                :m_med_cardio_op13,
                :m_med_cardio_desc13,
                :m_med_cardio_op14,
                :m_med_cardio_desc14,
                :m_med_cardio_op15,
                :m_med_cardio_desc15,
                :m_med_cardio_op16,
                :m_med_cardio_desc16,
                :m_med_cardio_op17,
                :m_med_cardio_desc17,
                :m_med_cardio_op18,
                :m_med_cardio_desc18,
                :m_med_tabaco,
                :m_med_alcohol,
                :m_med_coca,
                :m_med_fam_papa,
                :m_med_fam_mama,
                :m_med_fam_herma,
                :m_med_fam_hijos,
                :m_med_fam_h_vivos,
                :m_med_fam_h_muertos,
                :m_med_fam_infarto55,
                :m_med_fam_infarto65,
                :m_med_piel_desc,
                :m_med_piel_dx,
                :m_med_cabeza_desc,
                :m_med_cabeza_dx,
                :m_med_cuello_desc,
                :m_med_cuello_dx,
                :m_med_nariz_desc,
                :m_med_nariz_dx,
                :m_med_boca_desc,
                :m_med_boca_dx,
                :m_med_oido_der01,
                :m_med_oido_der02,
                :m_med_oido_der03,
                :m_med_oido_der04,
                :m_med_oido_izq01,
                :m_med_oido_izq02,
                :m_med_oido_izq03,
                :m_med_oido_izq04,
                :m_med_torax_desc,
                :m_med_torax_dx,
                :m_med_corazon_desc,
                :m_med_corazon_dx,
                :m_med_mamas_derecho,
                :m_med_mamas_izquier,
                :m_med_pulmon_desc,
                :m_med_pulmon_dx,
                :m_med_osteo_aptitud,
                :m_med_osteo_desc,
                :m_med_abdomen,
                :m_med_abdomen_desc,
                :m_med_pru_sup_der,
                :m_med_pru_med_der,
                :m_med_pru_inf_der,
                :m_med_ppl_der,
                :m_med_pru_sup_izq,
                :m_med_pru_med_izq,
                :m_med_pru_inf_izq,
                :m_med_ppl_izq,
                :m_med_tacto,
                :m_med_tacto_desc,
                :m_med_anillos,
                :m_med_anillos_desc,
                :m_med_hernia,
                :m_med_hernia_desc,
                :m_med_varices,
                :m_med_varices_desc,
                :m_med_genitales_desc,
                :m_med_genitales_dx,
                :m_med_ganglios_desc,
                :m_med_ganglios_dx,
                :m_med_lenguaje_desc,
                :m_med_lenguaje_dx,
                :m_med_aptitud,
                :m_med_fech_val,
                :m_med_medico_ocupa,
                :m_med_medico_auditor);
                
                INSERT INTO mod_medicina VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_medicina_st,
                :ex_id);";

        $verifica = $this->sql("SELECT m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario FROM mod_medicina inner join sys_usuario on usu_id=m_medicina_usu where m_medicina_adm='$adm' and m_medicina_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_med_audita = $this->sql("SELECT medico_id FROM medico where medico_auditor='OK' and medico_st=1");
            $sql_med = $this->sql("SELECT medico_id FROM medico where medico_usu='$usuario' and medico_auditor='NO' and medico_st=1;");
            if ($sql_med->success && $sql_med->total > 0 && $sql_med_audita->total > 0) {
                $medico = $sql_med->data[0]->medico_id;
                $medico_audita = $sql_med_audita->data[0]->medico_id;

                $params[':m_med_medico_ocupa'] = $medico;
                $params[':m_med_medico_auditor'] = $medico_audita;
                $sql = $this->sql($q, $params);
                if ($sql->success) {
                    $adm = $_POST['adm'];
                    $aptitud = $_POST['m_med_aptitud'];
                    $sql_aptitud = $this->sql("update admision set adm_aptitud='$aptitud', adm_val=1 where adm_id=$adm;");
                    if ($sql_aptitud->success) {
                        $this->commit();
                        return $sql;
                    }
                } else {
                    $this->rollback();
                    return array('success' => false);
                }
            } else {
                $this->rollback();
                return array('success' => false);
            }
        }
    }

    public function update_nuevoAnexo16()
    {
        $params = array();

        $usuario = $this->user->us_id;

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        $params[':m_med_contac_nom'] = $_POST['m_med_contac_nom'];
        $params[':m_med_contac_parent'] = $_POST['m_med_contac_parent'];
        $params[':m_med_contac_telf'] = $_POST['m_med_contac_telf'];
        $params[':m_med_puesto_postula'] = $_POST['m_med_puesto_postula'];
        $params[':m_med_area'] = $_POST['m_med_area'];
        $params[':m_med_puesto_actual'] = $_POST['m_med_puesto_actual'];
        $params[':m_med_tiempo'] = $_POST['m_med_tiempo'];
        $params[':m_med_eq_opera'] = $_POST['m_med_eq_opera'];
        $params[':m_med_fech_ingreso'] = $_POST['m_med_fech_ingreso'];

        $timestamp = strtotime($_POST['m_med_fech_ingreso']);
        $params[':m_med_fech_ingreso'] = date('Y-m-d', $timestamp);

        $params[':m_med_reubicacion'] = $_POST['m_med_reubicacion'];
        $params[':m_med_tip_opera'] = $_POST['m_med_tip_opera'];
        $params[':m_med_minerales'] = $_POST['m_med_minerales'];
        $params[':m_med_altura_lab'] = $_POST['m_med_altura_lab'];
        $params[':m_med_rl_bio1'] = $_POST['m_med_rl_bio1'];
        $params[':m_med_rl_ergo1'] = $_POST['m_med_rl_ergo1'];
        $params[':m_med_rl_ergo2'] = $_POST['m_med_rl_ergo2'];
        $params[':m_med_rl_ergo3'] = $_POST['m_med_rl_ergo3'];
        $params[':m_med_rl_ergo4'] = $_POST['m_med_rl_ergo4'];
        $params[':m_med_rl_ergo5'] = $_POST['m_med_rl_ergo5'];
        $params[':m_med_rl_fisico1'] = $_POST['m_med_rl_fisico1'];
        $params[':m_med_rl_fisico2'] = $_POST['m_med_rl_fisico2'];
        $params[':m_med_rl_fisico3'] = $_POST['m_med_rl_fisico3'];
        $params[':m_med_rl_fisico4'] = $_POST['m_med_rl_fisico4'];
        $params[':m_med_rl_fisico5'] = $_POST['m_med_rl_fisico5'];
        $params[':m_med_rl_fisico6'] = $_POST['m_med_rl_fisico6'];
        $params[':m_med_rl_fisico7'] = $_POST['m_med_rl_fisico7'];
        $params[':m_med_rl_fisico8'] = $_POST['m_med_rl_fisico8'];
        $params[':m_med_rl_fisico9'] = $_POST['m_med_rl_fisico9'];
        $params[':m_med_rl_fisico10'] = $_POST['m_med_rl_fisico10'];
        $params[':m_med_rl_psico1'] = $_POST['m_med_rl_psico1'];
        $params[':m_med_rl_psico2'] = $_POST['m_med_rl_psico2'];
        $params[':m_med_rl_psico3'] = $_POST['m_med_rl_psico3'];
        $params[':m_med_rl_psico4'] = $_POST['m_med_rl_psico4'];
        $params[':m_med_rl_quimi1'] = $_POST['m_med_rl_quimi1'];
        $params[':m_med_rl_quimi2'] = $_POST['m_med_rl_quimi2'];
        $params[':m_med_rl_quimi3'] = $_POST['m_med_rl_quimi3'];
        $params[':m_med_rl_quimi4'] = $_POST['m_med_rl_quimi4'];
        $params[':m_med_rl_quimi5'] = $_POST['m_med_rl_quimi5'];
        $params[':m_med_rl_quimi6'] = $_POST['m_med_rl_quimi6'];
        $params[':m_med_rl_quimi7'] = $_POST['m_med_rl_quimi7'];


        $timestamp0 = strtotime($_POST['m_med_muj_fur']);
        $m_med_muj_fur = ((strlen($_POST['m_med_muj_fur']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':m_med_muj_fur'] = $m_med_muj_fur;

        $params[':m_med_muj_rc'] = $_POST['m_med_muj_rc'];
        $params[':m_med_muj_g'] = $_POST['m_med_muj_g'];
        $params[':m_med_muj_p'] = $_POST['m_med_muj_p'];
        $params[':m_med_muj_ult_pap'] = $_POST['m_med_muj_ult_pap'];


        $timestamp1 = strtotime($_POST['m_med_muj_ult_pap']);
        $m_med_muj_ult_pap = ((strlen($_POST['m_med_muj_ult_pap']) > 0) ? date('Y-m-d', $timestamp1) : null);
        $params[':m_med_muj_ult_pap'] = $m_med_muj_ult_pap;

        $params[':m_med_muj_resul'] = $_POST['m_med_muj_resul'];
        $params[':m_med_muj_mac'] = $_POST['m_med_muj_mac'];
        $params[':m_med_muj_obs'] = $_POST['m_med_muj_obs'];
        $params[':m_med_muj_a'] = $_POST['m_med_muj_a'];
        $params[':m_med_muj_b'] = $_POST['m_med_muj_b'];
        $params[':m_med_muj_c'] = $_POST['m_med_muj_c'];
        $params[':m_med_muj_d'] = $_POST['m_med_muj_d'];
        $params[':m_med_muj_e'] = $_POST['m_med_muj_e'];
        $params[':m_med_cardio_op01'] = $_POST['m_med_cardio_op01'];
        $params[':m_med_cardio_op02'] = $_POST['m_med_cardio_op02'];
        $params[':m_med_cardio_desc02'] = $_POST['m_med_cardio_desc02'];
        $params[':m_med_cardio_op03'] = $_POST['m_med_cardio_op03'];
        $params[':m_med_cardio_desc03'] = $_POST['m_med_cardio_desc03'];
        $params[':m_med_cardio_op04'] = $_POST['m_med_cardio_op04'];
        $params[':m_med_cardio_desc04'] = $_POST['m_med_cardio_desc04'];
        $params[':m_med_cardio_op05'] = $_POST['m_med_cardio_op05'];
        $params[':m_med_cardio_desc05'] = $_POST['m_med_cardio_desc05'];
        $params[':m_med_cardio_op06'] = $_POST['m_med_cardio_op06'];
        $params[':m_med_cardio_desc06'] = $_POST['m_med_cardio_desc06'];
        $params[':m_med_cardio_op07'] = $_POST['m_med_cardio_op07'];
        $params[':m_med_cardio_desc07'] = $_POST['m_med_cardio_desc07'];
        $params[':m_med_cardio_op08'] = $_POST['m_med_cardio_op08'];
        $params[':m_med_cardio_desc08'] = $_POST['m_med_cardio_desc08'];
        $params[':m_med_cardio_op09'] = $_POST['m_med_cardio_op09'];
        $params[':m_med_cardio_desc09'] = $_POST['m_med_cardio_desc09'];
        $params[':m_med_cardio_op10'] = $_POST['m_med_cardio_op10'];
        $params[':m_med_cardio_desc10'] = $_POST['m_med_cardio_desc10'];
        $params[':m_med_cardio_op11'] = $_POST['m_med_cardio_op11'];
        $params[':m_med_cardio_desc11'] = $_POST['m_med_cardio_desc11'];
        $params[':m_med_cardio_op12'] = $_POST['m_med_cardio_op12'];
        $params[':m_med_cardio_desc12'] = $_POST['m_med_cardio_desc12'];
        $params[':m_med_cardio_op13'] = $_POST['m_med_cardio_op13'];
        $params[':m_med_cardio_desc13'] = $_POST['m_med_cardio_desc13'];
        $params[':m_med_cardio_op14'] = $_POST['m_med_cardio_op14'];
        $params[':m_med_cardio_desc14'] = $_POST['m_med_cardio_desc14'];
        $params[':m_med_cardio_op15'] = $_POST['m_med_cardio_op15'];
        $params[':m_med_cardio_desc15'] = $_POST['m_med_cardio_desc15'];
        $params[':m_med_cardio_op16'] = $_POST['m_med_cardio_op16'];
        $params[':m_med_cardio_desc16'] = $_POST['m_med_cardio_desc16'];
        $params[':m_med_cardio_op17'] = $_POST['m_med_cardio_op17'];
        $params[':m_med_cardio_desc17'] = $_POST['m_med_cardio_desc17'];
        $params[':m_med_cardio_op18'] = $_POST['m_med_cardio_op18'];
        $params[':m_med_cardio_desc18'] = $_POST['m_med_cardio_desc18'];
        $params[':m_med_tabaco'] = $_POST['m_med_tabaco'];
        $params[':m_med_alcohol'] = $_POST['m_med_alcohol'];
        $params[':m_med_coca'] = $_POST['m_med_coca'];
        $params[':m_med_fam_papa'] = $_POST['m_med_fam_papa'];
        $params[':m_med_fam_mama'] = $_POST['m_med_fam_mama'];
        $params[':m_med_fam_herma'] = $_POST['m_med_fam_herma'];
        $params[':m_med_fam_hijos'] = $_POST['m_med_fam_hijos'];
        $params[':m_med_fam_h_vivos'] = $_POST['m_med_fam_h_vivos'];
        $params[':m_med_fam_h_muertos'] = $_POST['m_med_fam_h_muertos'];
        $params[':m_med_fam_infarto55'] = $_POST['m_med_fam_infarto55'];
        $params[':m_med_fam_infarto65'] = $_POST['m_med_fam_infarto65'];
        $params[':m_med_piel_desc'] = $_POST['m_med_piel_desc'];
        $params[':m_med_piel_dx'] = $_POST['m_med_piel_dx'];
        $params[':m_med_cabeza_desc'] = $_POST['m_med_cabeza_desc'];
        $params[':m_med_cabeza_dx'] = $_POST['m_med_cabeza_dx'];
        $params[':m_med_cuello_desc'] = $_POST['m_med_cuello_desc'];
        $params[':m_med_cuello_dx'] = $_POST['m_med_cuello_dx'];
        $params[':m_med_nariz_desc'] = $_POST['m_med_nariz_desc'];
        $params[':m_med_nariz_dx'] = $_POST['m_med_nariz_dx'];
        $params[':m_med_boca_desc'] = $_POST['m_med_boca_desc'];
        $params[':m_med_boca_dx'] = $_POST['m_med_boca_dx'];
        $params[':m_med_oido_der01'] = $_POST['m_med_oido_der01'];
        $params[':m_med_oido_der02'] = $_POST['m_med_oido_der02'];
        $params[':m_med_oido_der03'] = $_POST['m_med_oido_der03'];
        $params[':m_med_oido_der04'] = $_POST['m_med_oido_der04'];
        $params[':m_med_oido_izq01'] = $_POST['m_med_oido_izq01'];
        $params[':m_med_oido_izq02'] = $_POST['m_med_oido_izq02'];
        $params[':m_med_oido_izq03'] = $_POST['m_med_oido_izq03'];
        $params[':m_med_oido_izq04'] = $_POST['m_med_oido_izq04'];
        $params[':m_med_torax_desc'] = $_POST['m_med_torax_desc'];
        $params[':m_med_torax_dx'] = $_POST['m_med_torax_dx'];
        $params[':m_med_corazon_desc'] = $_POST['m_med_corazon_desc'];
        $params[':m_med_corazon_dx'] = $_POST['m_med_corazon_dx'];
        $params[':m_med_mamas_derecho'] = $_POST['m_med_mamas_derecho'];
        $params[':m_med_mamas_izquier'] = $_POST['m_med_mamas_izquier'];
        $params[':m_med_pulmon_desc'] = $_POST['m_med_pulmon_desc'];
        $params[':m_med_pulmon_dx'] = $_POST['m_med_pulmon_dx'];
        $params[':m_med_osteo_aptitud'] = $_POST['m_med_osteo_aptitud'];
        $params[':m_med_osteo_desc'] = $_POST['m_med_osteo_desc'];
        $params[':m_med_abdomen'] = $_POST['m_med_abdomen'];
        $params[':m_med_abdomen_desc'] = $_POST['m_med_abdomen_desc'];
        $params[':m_med_pru_sup_der'] = $_POST['m_med_pru_sup_der'];
        $params[':m_med_pru_med_der'] = $_POST['m_med_pru_med_der'];
        $params[':m_med_pru_inf_der'] = $_POST['m_med_pru_inf_der'];
        $params[':m_med_ppl_der'] = $_POST['m_med_ppl_der'];
        $params[':m_med_pru_sup_izq'] = $_POST['m_med_pru_sup_izq'];
        $params[':m_med_pru_med_izq'] = $_POST['m_med_pru_med_izq'];
        $params[':m_med_pru_inf_izq'] = $_POST['m_med_pru_inf_izq'];
        $params[':m_med_ppl_izq'] = $_POST['m_med_ppl_izq'];
        $params[':m_med_tacto'] = $_POST['m_med_tacto'];
        $params[':m_med_tacto_desc'] = $_POST['m_med_tacto_desc'];
        $params[':m_med_anillos'] = $_POST['m_med_anillos'];
        $params[':m_med_anillos_desc'] = $_POST['m_med_anillos_desc'];
        $params[':m_med_hernia'] = $_POST['m_med_hernia'];
        $params[':m_med_hernia_desc'] = $_POST['m_med_hernia_desc'];
        $params[':m_med_varices'] = $_POST['m_med_varices'];
        $params[':m_med_varices_desc'] = $_POST['m_med_varices_desc'];
        $params[':m_med_genitales_desc'] = $_POST['m_med_genitales_desc'];
        $params[':m_med_genitales_dx'] = $_POST['m_med_genitales_dx'];
        $params[':m_med_ganglios_desc'] = $_POST['m_med_ganglios_desc'];
        $params[':m_med_ganglios_dx'] = $_POST['m_med_ganglios_dx'];
        $params[':m_med_lenguaje_desc'] = $_POST['m_med_lenguaje_desc'];
        $params[':m_med_lenguaje_dx'] = $_POST['m_med_lenguaje_dx'];
        $params[':m_med_aptitud'] = $_POST['m_med_aptitud'];

        $timestamp2 = strtotime($_POST['m_med_fech_val']);
        $params[':m_med_fech_val'] = date('Y-m-d', $timestamp2);

        $this->begin();
        $q = 'Update mod_medicina_anexo16 set
                m_med_contac_nom=:m_med_contac_nom,
                m_med_contac_parent=:m_med_contac_parent,
                m_med_contac_telf=:m_med_contac_telf,
                m_med_puesto_postula=:m_med_puesto_postula,
                m_med_area=:m_med_area,
                m_med_puesto_actual=:m_med_puesto_actual,
                m_med_tiempo=:m_med_tiempo,
                m_med_eq_opera=:m_med_eq_opera,
                m_med_fech_ingreso=:m_med_fech_ingreso,
                m_med_reubicacion=:m_med_reubicacion,
                m_med_tip_opera=:m_med_tip_opera,
                m_med_minerales=:m_med_minerales,
                m_med_altura_lab=:m_med_altura_lab,
                m_med_rl_bio1=:m_med_rl_bio1,
                m_med_rl_ergo1=:m_med_rl_ergo1,
                m_med_rl_ergo2=:m_med_rl_ergo2,
                m_med_rl_ergo3=:m_med_rl_ergo3,
                m_med_rl_ergo4=:m_med_rl_ergo4,
                m_med_rl_ergo5=:m_med_rl_ergo5,
                m_med_rl_fisico1=:m_med_rl_fisico1,
                m_med_rl_fisico2=:m_med_rl_fisico2,
                m_med_rl_fisico3=:m_med_rl_fisico3,
                m_med_rl_fisico4=:m_med_rl_fisico4,
                m_med_rl_fisico5=:m_med_rl_fisico5,
                m_med_rl_fisico6=:m_med_rl_fisico6,
                m_med_rl_fisico7=:m_med_rl_fisico7,
                m_med_rl_fisico8=:m_med_rl_fisico8,
                m_med_rl_fisico9=:m_med_rl_fisico9,
                m_med_rl_fisico10=:m_med_rl_fisico10,
                m_med_rl_psico1=:m_med_rl_psico1,
                m_med_rl_psico2=:m_med_rl_psico2,
                m_med_rl_psico3=:m_med_rl_psico3,
                m_med_rl_psico4=:m_med_rl_psico4,
                m_med_rl_quimi1=:m_med_rl_quimi1,
                m_med_rl_quimi2=:m_med_rl_quimi2,
                m_med_rl_quimi3=:m_med_rl_quimi3,
                m_med_rl_quimi4=:m_med_rl_quimi4,
                m_med_rl_quimi5=:m_med_rl_quimi5,
                m_med_rl_quimi6=:m_med_rl_quimi6,
                m_med_rl_quimi7=:m_med_rl_quimi7,
                m_med_muj_fur=:m_med_muj_fur,
                m_med_muj_rc=:m_med_muj_rc,
                m_med_muj_g=:m_med_muj_g,
                m_med_muj_p=:m_med_muj_p,
                m_med_muj_ult_pap=:m_med_muj_ult_pap,
                m_med_muj_resul=:m_med_muj_resul,
                m_med_muj_mac=:m_med_muj_mac,
                m_med_muj_obs=:m_med_muj_obs,
                m_med_muj_a=:m_med_muj_a,
                m_med_muj_b=:m_med_muj_b,
                m_med_muj_c=:m_med_muj_c,
                m_med_muj_d=:m_med_muj_d,
                m_med_muj_e=:m_med_muj_e,
                m_med_cardio_op01=:m_med_cardio_op01,
                m_med_cardio_op02=:m_med_cardio_op02,
                m_med_cardio_desc02=:m_med_cardio_desc02,
                m_med_cardio_op03=:m_med_cardio_op03,
                m_med_cardio_desc03=:m_med_cardio_desc03,
                m_med_cardio_op04=:m_med_cardio_op04,
                m_med_cardio_desc04=:m_med_cardio_desc04,
                m_med_cardio_op05=:m_med_cardio_op05,
                m_med_cardio_desc05=:m_med_cardio_desc05,
                m_med_cardio_op06=:m_med_cardio_op06,
                m_med_cardio_desc06=:m_med_cardio_desc06,
                m_med_cardio_op07=:m_med_cardio_op07,
                m_med_cardio_desc07=:m_med_cardio_desc07,
                m_med_cardio_op08=:m_med_cardio_op08,
                m_med_cardio_desc08=:m_med_cardio_desc08,
                m_med_cardio_op09=:m_med_cardio_op09,
                m_med_cardio_desc09=:m_med_cardio_desc09,
                m_med_cardio_op10=:m_med_cardio_op10,
                m_med_cardio_desc10=:m_med_cardio_desc10,
                m_med_cardio_op11=:m_med_cardio_op11,
                m_med_cardio_desc11=:m_med_cardio_desc11,
                m_med_cardio_op12=:m_med_cardio_op12,
                m_med_cardio_desc12=:m_med_cardio_desc12,
                m_med_cardio_op13=:m_med_cardio_op13,
                m_med_cardio_desc13=:m_med_cardio_desc13,
                m_med_cardio_op14=:m_med_cardio_op14,
                m_med_cardio_desc14=:m_med_cardio_desc14,
                m_med_cardio_op15=:m_med_cardio_op15,
                m_med_cardio_desc15=:m_med_cardio_desc15,
                m_med_cardio_op16=:m_med_cardio_op16,
                m_med_cardio_desc16=:m_med_cardio_desc16,
                m_med_cardio_op17=:m_med_cardio_op17,
                m_med_cardio_desc17=:m_med_cardio_desc17,
                m_med_cardio_op18=:m_med_cardio_op18,
                m_med_cardio_desc18=:m_med_cardio_desc18,
                m_med_tabaco=:m_med_tabaco,
                m_med_alcohol=:m_med_alcohol,
                m_med_coca=:m_med_coca,
                m_med_fam_papa=:m_med_fam_papa,
                m_med_fam_mama=:m_med_fam_mama,
                m_med_fam_herma=:m_med_fam_herma,
                m_med_fam_hijos=:m_med_fam_hijos,
                m_med_fam_h_vivos=:m_med_fam_h_vivos,
                m_med_fam_h_muertos=:m_med_fam_h_muertos,
                m_med_fam_infarto55=:m_med_fam_infarto55,
                m_med_fam_infarto65=:m_med_fam_infarto65,
                m_med_piel_desc=:m_med_piel_desc,
                m_med_piel_dx=:m_med_piel_dx,
                m_med_cabeza_desc=:m_med_cabeza_desc,
                m_med_cabeza_dx=:m_med_cabeza_dx,
                m_med_cuello_desc=:m_med_cuello_desc,
                m_med_cuello_dx=:m_med_cuello_dx,
                m_med_nariz_desc=:m_med_nariz_desc,
                m_med_nariz_dx=:m_med_nariz_dx,
                m_med_boca_desc=:m_med_boca_desc,
                m_med_boca_dx=:m_med_boca_dx,
                m_med_oido_der01=:m_med_oido_der01,
                m_med_oido_der02=:m_med_oido_der02,
                m_med_oido_der03=:m_med_oido_der03,
                m_med_oido_der04=:m_med_oido_der04,
                m_med_oido_izq01=:m_med_oido_izq01,
                m_med_oido_izq02=:m_med_oido_izq02,
                m_med_oido_izq03=:m_med_oido_izq03,
                m_med_oido_izq04=:m_med_oido_izq04,
                m_med_torax_desc=:m_med_torax_desc,
                m_med_torax_dx=:m_med_torax_dx,
                m_med_corazon_desc=:m_med_corazon_desc,
                m_med_corazon_dx=:m_med_corazon_dx,
                m_med_mamas_derecho=:m_med_mamas_derecho,
                m_med_mamas_izquier=:m_med_mamas_izquier,
                m_med_pulmon_desc=:m_med_pulmon_desc,
                m_med_pulmon_dx=:m_med_pulmon_dx,
                m_med_osteo_aptitud=:m_med_osteo_aptitud,
                m_med_osteo_desc=:m_med_osteo_desc,
                m_med_abdomen=:m_med_abdomen,
                m_med_abdomen_desc=:m_med_abdomen_desc,
                m_med_pru_sup_der=:m_med_pru_sup_der,
                m_med_pru_med_der=:m_med_pru_med_der,
                m_med_pru_inf_der=:m_med_pru_inf_der,
                m_med_ppl_der=:m_med_ppl_der,
                m_med_pru_sup_izq=:m_med_pru_sup_izq,
                m_med_pru_med_izq=:m_med_pru_med_izq,
                m_med_pru_inf_izq=:m_med_pru_inf_izq,
                m_med_ppl_izq=:m_med_ppl_izq,
                m_med_tacto=:m_med_tacto,
                m_med_tacto_desc=:m_med_tacto_desc,
                m_med_anillos=:m_med_anillos,
                m_med_anillos_desc=:m_med_anillos_desc,
                m_med_hernia=:m_med_hernia,
                m_med_hernia_desc=:m_med_hernia_desc,
                m_med_varices=:m_med_varices,
                m_med_varices_desc=:m_med_varices_desc,
                m_med_genitales_desc=:m_med_genitales_desc,
                m_med_genitales_dx=:m_med_genitales_dx,
                m_med_ganglios_desc=:m_med_ganglios_desc,
                m_med_ganglios_dx=:m_med_ganglios_dx,
                m_med_lenguaje_desc=:m_med_lenguaje_desc,
                m_med_lenguaje_dx=:m_med_lenguaje_dx,
                m_med_aptitud=:m_med_aptitud,
                m_med_fech_val=:m_med_fech_val,
                m_med_medico_ocupa=:m_med_medico_ocupa,
                m_med_medico_auditor=:m_med_medico_auditor
                where
                m_med_adm=:adm and m_med_exa=:ex_id;
                
                update mod_medicina set
                m_medicina_usu=:usuario,
                m_medicina_fech_update=now()
                where
                m_medicina_adm=:adm and m_medicina_examen=:ex_id;';


        $sql_med_audita = $this->sql("SELECT medico_id FROM medico where medico_auditor='OK' and medico_st=1");
        $sql_med = $this->sql("SELECT medico_id FROM medico where medico_usu='$usuario' and medico_auditor='NO' and medico_st=1;");
        if ($sql_med->success && $sql_med->total > 0 && $sql_med_audita->total > 0) {
            $medico = $sql_med->data[0]->medico_id;
            $medico_audita = $sql_med_audita->data[0]->medico_id;

            $params[':m_med_medico_ocupa'] = $medico;
            $params[':m_med_medico_auditor'] = $medico_audita;

            $sql1 = $this->sql($q, $params);
            if ($sql1->success) {
                $adm = $_POST['adm'];
                $aptitud = $_POST['m_med_aptitud'];
                $sql_aptitud = $this->sql("update admision set adm_aptitud='$aptitud', adm_val=1 where adm_id=$adm;");
                if ($sql_aptitud->success) {
                    $this->commit();
                    return $sql1;
                }
            } else {
                $this->rollback();
                return array('success' => false);
            }
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function mod_medicina_antece($adm) {
        $sql = "SELECT
            m_antec_id, m_antec_adm, m_antec_exa,
            m_antec_fech_ini, m_antec_fech_fin, m_antec_suelo,
            m_antec_cargo, m_antec_empresa, m_antec_proyec,
            m_antec_alti,
            if(m_antec_fisico=1,'FISICO','') m_antec_fisico,
            if(m_antec_quinico=1,'QUIMICO','') m_antec_quinico,
            if(m_antec_biologico=1,'BIOLOGICO','') m_antec_biologico,
            if(m_antec_ergonom=1,'ERGONOMICO','') m_antec_ergonom,
            if(m_antec_otros=1,'OTROS','') m_antec_otros,m_antec_obser
            FROM mod_medicina_antece_16v
            WHERE
            m_antec_adm=$adm;";
        $area = $this->sql($sql);
        return $area;
    }

    public function paciente($adm)
    {
        $sql = $this->sql("SELECT
            adm_id as adm
            ,concat(pac_nombres,', ',pac_appat,' ',pac_apmat) nom_ap
            ,concat(pac_nombres) nombre,concat(pac_appat,' ',pac_apmat) apellidos
            , emp_desc,emp_direc,emp_id,concat(adm_puesto,' - ',adm_area)as puesto,adm_puesto,adm_area,pac_profe
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

    public function mod_medicina_anexo16($adm)
    {
        $sql = $this->sql("SELECT * FROM mod_medicina_anexo16
            where
            m_med_adm=$adm;
                ");
        return $sql;
    }

    public function diagnostico($adm)
    {
        $q = "SELECT upper(diag_desc) diag_desc FROM diagnostico where diag_adm=$adm";
        return $this->sql($q);
    }

    public function observaciones($adm)
    {
        $q = "SELECT upper(obs_desc) obs_desc, obs_plazo FROM observaciones where obs_adm=$adm";
        return $this->sql($q);
    }

    public function restricciones($adm)
    {
        $q = "SELECT upper(restric_desc) restric_desc, restric_plazo FROM restricciones where restric_adm=$adm";
        return $this->sql($q);
    }

    public function interconsultas($adm)
    {
        $q = "SELECT upper(inter_desc) inter_desc, inter_plazo FROM interconsultas where inter_adm=$adm";
        return $this->sql($q);
    }

    public function recomendaciones($adm)
    {
        $q = "SELECT upper(recom_desc) recom_desc, recom_plazo FROM recomendaciones where recom_adm=$adm";
        return $this->sql($q);
    }

    public function medico($med)
    {
        $q = "SELECT concat(medico_nombre,', ', medico_apepat,' ', medico_apemat) medico_nombres
            ,medico_cmp
            FROM medico where medico_id =$med";
        return $this->sql($q);
    }

    public function list_ante16()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT m_antec_16_id,m_antec_16_adm,m_antec_16_exa, m_antec_16_ini_fech, m_antec_16_fin_fech, m_antec_16_ocupacion, m_antec_16_empresa
                , m_antec_16_actividad, m_antec_16_area_trab, m_antec_16_altitud
                , m_antec_16_tipo_ope, m_antec_16_time_trab
                FROM mod_medicina_antecede_16
                where m_antec_16_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_m_antec_16_ocupacion()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_16_ocupacion FROM mod_medicina_antecede_16
                            where
                            m_antec_16_ocupacion like '%$query%'
                            group by m_antec_16_ocupacion");
        return $sql;
    }

    public function st_m_antec_16_empresa()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_16_empresa FROM mod_medicina_antecede_16
                            where
                            m_antec_16_empresa like '%$query%'
                            group by m_antec_16_empresa");
        return $sql;
    }

    public function st_m_antec_16_actividad()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_16_actividad FROM mod_medicina_antecede_16
                            where
                            m_antec_16_actividad like '%$query%'
                            group by m_antec_16_actividad");
        return $sql;
    }

    public function st_m_antec_16_area_trab()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_16_area_trab FROM mod_medicina_antecede_16
                            where
                            m_antec_16_area_trab like '%$query%'
                            group by m_antec_16_area_trab");
        return $sql;
    }

    public function load_antec_16()
    {
        $adm = $_POST['m_antec_16_adm'];
        $id = $_POST['m_antec_16_id'];
        $query = "SELECT * FROM mod_medicina_antecede_16
            where
            m_antec_16_id=$id and m_antec_16_adm=$adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_nuevo_antecedentes16()
    {
        $params = array();
        $this->begin();
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];
        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];
        $usuario = $this->user->us_id;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $timestamp = strtotime($_POST['m_antec_16_ini_fech']);
        $m_antec_16_ini_fech = ((strlen($_POST['m_antec_16_ini_fech']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params[':m_antec_16_ini_fech'] = $m_antec_16_ini_fech;

        $timestamp0 = strtotime($_POST['m_antec_16_fin_fech']);
        $m_antec_16_fin_fech = ((strlen($_POST['m_antec_16_fin_fech']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':m_antec_16_fin_fech'] = $m_antec_16_fin_fech;

        $params[':m_antec_16_ocupacion'] = $_POST['m_antec_16_ocupacion'];
        $params[':m_antec_16_empresa'] = $_POST['m_antec_16_empresa'];
        $params[':m_antec_16_actividad'] = $_POST['m_antec_16_actividad'];
        $params[':m_antec_16_area_trab'] = $_POST['m_antec_16_area_trab'];
        $params[':m_antec_16_altitud'] = $_POST['m_antec_16_altitud'];
        $params[':m_antec_16_tipo_ope'] = $_POST['m_antec_16_tipo_ope'];
        $params[':m_antec_16_time_trab'] = $_POST['m_antec_16_time_trab'];
        $params[':m_antec_16_fisico_agen'] = $_POST['m_antec_16_fisico_agen'];
        $params[':m_antec_16_fisico_hora'] = $_POST['m_antec_16_fisico_hora'];
        $params[':m_antec_16_fisico_epp'] = $_POST['m_antec_16_fisico_epp'];
        $params[':m_antec_16_quimico_agen'] = $_POST['m_antec_16_quimico_agen'];
        $params[':m_antec_16_quimico_hora'] = $_POST['m_antec_16_quimico_hora'];
        $params[':m_antec_16_quimico_epp'] = $_POST['m_antec_16_quimico_epp'];
        $params[':m_antec_16_electrico_agen'] = $_POST['m_antec_16_electrico_agen'];
        $params[':m_antec_16_electrico_hora'] = $_POST['m_antec_16_electrico_hora'];
        $params[':m_antec_16_electrico_epp'] = $_POST['m_antec_16_electrico_epp'];
        $params[':m_antec_16_ergo_agen'] = $_POST['m_antec_16_ergo_agen'];
        $params[':m_antec_16_ergo_hora'] = $_POST['m_antec_16_ergo_hora'];
        $params[':m_antec_16_ergo_epp'] = $_POST['m_antec_16_ergo_epp'];
        $params[':m_antec_16_biologico_agen'] = $_POST['m_antec_16_biologico_agen'];
        $params[':m_antec_16_biologico_hora'] = $_POST['m_antec_16_biologico_hora'];
        $params[':m_antec_16_biologico_epp'] = $_POST['m_antec_16_biologico_epp'];
        $params[':m_antec_16_psico_agen'] = $_POST['m_antec_16_psico_agen'];
        $params[':m_antec_16_psico_hora'] = $_POST['m_antec_16_psico_hora'];
        $params[':m_antec_16_psico_epp'] = $_POST['m_antec_16_psico_epp'];
        $params[':m_antec_16_otros_agen'] = $_POST['m_antec_16_otros_agen'];
        $params[':m_antec_16_otros_hora'] = $_POST['m_antec_16_otros_hora'];
        $params[':m_antec_16_otros_epp'] = $_POST['m_antec_16_otros_epp'];
        $params[':m_antec_16_especificar'] = $_POST['m_antec_16_especificar'];



        $q2 = "INSERT INTO mod_medicina_antecede_16 VALUES 
                (NULL,
                :adm,
                :ex_id,
                :m_antec_16_ini_fech,
                :m_antec_16_fin_fech,
                :m_antec_16_ocupacion,
                :m_antec_16_empresa,
                :m_antec_16_actividad,
                :m_antec_16_area_trab,
                :m_antec_16_altitud,
                :m_antec_16_tipo_ope,
                :m_antec_16_time_trab,
                :m_antec_16_fisico_agen,
                :m_antec_16_fisico_hora,
                :m_antec_16_fisico_epp,
                :m_antec_16_quimico_agen,
                :m_antec_16_quimico_hora,
                :m_antec_16_quimico_epp,
                :m_antec_16_electrico_agen,
                :m_antec_16_electrico_hora,
                :m_antec_16_electrico_epp,
                :m_antec_16_ergo_agen,
                :m_antec_16_ergo_hora,
                :m_antec_16_ergo_epp,
                :m_antec_16_biologico_agen,
                :m_antec_16_biologico_hora,
                :m_antec_16_biologico_epp,
                :m_antec_16_psico_agen,
                :m_antec_16_psico_hora,
                :m_antec_16_psico_epp,
                :m_antec_16_otros_agen,
                :m_antec_16_otros_hora,
                :m_antec_16_otros_epp,
                :m_antec_16_especificar);";

        $q = "INSERT INTO mod_medicina_antecede_16 VALUES 
                (NULL,
                :adm,
                :ex_id,
                :m_antec_16_ini_fech,
                :m_antec_16_fin_fech,
                :m_antec_16_ocupacion,
                :m_antec_16_empresa,
                :m_antec_16_actividad,
                :m_antec_16_area_trab,
                :m_antec_16_altitud,
                :m_antec_16_tipo_ope,
                :m_antec_16_time_trab,
                :m_antec_16_fisico_agen,
                :m_antec_16_fisico_hora,
                :m_antec_16_fisico_epp,
                :m_antec_16_quimico_agen,
                :m_antec_16_quimico_hora,
                :m_antec_16_quimico_epp,
                :m_antec_16_electrico_agen,
                :m_antec_16_electrico_hora,
                :m_antec_16_electrico_epp,
                :m_antec_16_ergo_agen,
                :m_antec_16_ergo_hora,
                :m_antec_16_ergo_epp,
                :m_antec_16_biologico_agen,
                :m_antec_16_biologico_hora,
                :m_antec_16_biologico_epp,
                :m_antec_16_psico_agen,
                :m_antec_16_psico_hora,
                :m_antec_16_psico_epp,
                :m_antec_16_otros_agen,
                :m_antec_16_otros_hora,
                :m_antec_16_otros_epp,
                :m_antec_16_especificar);
                
                INSERT INTO mod_medicina VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_medicina_st,
                :ex_id);";

        $verifica = $this->sql("SELECT m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario FROM mod_medicina inner join sys_usuario on usu_id=m_medicina_usu where m_medicina_adm='$adm' and m_medicina_examen='$exa';");
        if ($verifica->total > 0) {
            $sql = $this->sql($q2, $params);
            if ($sql->success) {
                $this->commit();
                return $sql;
            } else {
                $this->rollback();
                return array('success' => false);
            }
        } else {
            $params[':sede'] = $this->user->con_sedid;
            $params[':usuario'] = $this->user->us_id;
            $params[':m_medicina_st'] = '1'; //modulo principal
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

    public function update_nuevo_antecedentes16()
    {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];
        $usuario = $this->user->us_id;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $timestamp = strtotime($_POST['m_antec_16_ini_fech']);
        $m_antec_16_ini_fech = ((strlen($_POST['m_antec_16_ini_fech']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params[':m_antec_16_ini_fech'] = $m_antec_16_ini_fech;

        $timestamp0 = strtotime($_POST['m_antec_16_fin_fech']);
        $m_antec_16_fin_fech = ((strlen($_POST['m_antec_16_fin_fech']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':m_antec_16_fin_fech'] = $m_antec_16_fin_fech;

        $params[':m_antec_16_ocupacion'] = $_POST['m_antec_16_ocupacion'];
        $params[':m_antec_16_empresa'] = $_POST['m_antec_16_empresa'];
        $params[':m_antec_16_actividad'] = $_POST['m_antec_16_actividad'];
        $params[':m_antec_16_area_trab'] = $_POST['m_antec_16_area_trab'];
        $params[':m_antec_16_altitud'] = $_POST['m_antec_16_altitud'];
        $params[':m_antec_16_tipo_ope'] = $_POST['m_antec_16_tipo_ope'];
        $params[':m_antec_16_time_trab'] = $_POST['m_antec_16_time_trab'];
        $params[':m_antec_16_fisico_agen'] = $_POST['m_antec_16_fisico_agen'];
        $params[':m_antec_16_fisico_hora'] = $_POST['m_antec_16_fisico_hora'];
        $params[':m_antec_16_fisico_epp'] = $_POST['m_antec_16_fisico_epp'];
        $params[':m_antec_16_quimico_agen'] = $_POST['m_antec_16_quimico_agen'];
        $params[':m_antec_16_quimico_hora'] = $_POST['m_antec_16_quimico_hora'];
        $params[':m_antec_16_quimico_epp'] = $_POST['m_antec_16_quimico_epp'];
        $params[':m_antec_16_electrico_agen'] = $_POST['m_antec_16_electrico_agen'];
        $params[':m_antec_16_electrico_hora'] = $_POST['m_antec_16_electrico_hora'];
        $params[':m_antec_16_electrico_epp'] = $_POST['m_antec_16_electrico_epp'];
        $params[':m_antec_16_ergo_agen'] = $_POST['m_antec_16_ergo_agen'];
        $params[':m_antec_16_ergo_hora'] = $_POST['m_antec_16_ergo_hora'];
        $params[':m_antec_16_ergo_epp'] = $_POST['m_antec_16_ergo_epp'];
        $params[':m_antec_16_biologico_agen'] = $_POST['m_antec_16_biologico_agen'];
        $params[':m_antec_16_biologico_hora'] = $_POST['m_antec_16_biologico_hora'];
        $params[':m_antec_16_biologico_epp'] = $_POST['m_antec_16_biologico_epp'];
        $params[':m_antec_16_psico_agen'] = $_POST['m_antec_16_psico_agen'];
        $params[':m_antec_16_psico_hora'] = $_POST['m_antec_16_psico_hora'];
        $params[':m_antec_16_psico_epp'] = $_POST['m_antec_16_psico_epp'];
        $params[':m_antec_16_otros_agen'] = $_POST['m_antec_16_otros_agen'];
        $params[':m_antec_16_otros_hora'] = $_POST['m_antec_16_otros_hora'];
        $params[':m_antec_16_otros_epp'] = $_POST['m_antec_16_otros_epp'];
        $params[':m_antec_16_especificar'] = $_POST['m_antec_16_especificar'];

        $this->begin();
        $q = 'Update mod_medicina_antecede_16 set
                m_antec_16_ini_fech=:m_antec_16_ini_fech,
                m_antec_16_fin_fech=:m_antec_16_fin_fech,
                m_antec_16_ocupacion=:m_antec_16_ocupacion,
                m_antec_16_empresa=:m_antec_16_empresa,
                m_antec_16_actividad=:m_antec_16_actividad,
                m_antec_16_area_trab=:m_antec_16_area_trab,
                m_antec_16_altitud=:m_antec_16_altitud,
                m_antec_16_tipo_ope=:m_antec_16_tipo_ope,
                m_antec_16_time_trab=:m_antec_16_time_trab,
                m_antec_16_fisico_agen=:m_antec_16_fisico_agen,
                m_antec_16_fisico_hora=:m_antec_16_fisico_hora,
                m_antec_16_fisico_epp=:m_antec_16_fisico_epp,
                m_antec_16_quimico_agen=:m_antec_16_quimico_agen,
                m_antec_16_quimico_hora=:m_antec_16_quimico_hora,
                m_antec_16_quimico_epp=:m_antec_16_quimico_epp,
                m_antec_16_electrico_agen=:m_antec_16_electrico_agen,
                m_antec_16_electrico_hora=:m_antec_16_electrico_hora,
                m_antec_16_electrico_epp=:m_antec_16_electrico_epp,
                m_antec_16_ergo_agen=:m_antec_16_ergo_agen,
                m_antec_16_ergo_hora=:m_antec_16_ergo_hora,
                m_antec_16_ergo_epp=:m_antec_16_ergo_epp,
                m_antec_16_biologico_agen=:m_antec_16_biologico_agen,
                m_antec_16_biologico_hora=:m_antec_16_biologico_hora,
                m_antec_16_biologico_epp=:m_antec_16_biologico_epp,
                m_antec_16_psico_agen=:m_antec_16_psico_agen,
                m_antec_16_psico_hora=:m_antec_16_psico_hora,
                m_antec_16_psico_epp=:m_antec_16_psico_epp,
                m_antec_16_otros_agen=:m_antec_16_otros_agen,
                m_antec_16_otros_hora=:m_antec_16_otros_hora,
                m_antec_16_otros_epp=:m_antec_16_otros_epp,
                m_antec_16_especificar=:m_antec_16_especificar
                where
                m_antec_16_id=:id and m_antec_16_adm=:adm and m_antec_16_exa=:ex_id;
                
                update mod_medicina set
                m_medicina_usu=:usuario,
                m_medicina_fech_update=now()
                where
                m_medicina_adm=:adm and m_medicina_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function mod_medicina_antecede_16($adm)
    {
        $sql = "SELECT *
            FROM mod_medicina_antecede_16
            WHERE
            m_antec_16_adm=$adm;";
        return $this->sql($sql);
    }

    public function st_m_antec_retiro_cmedico()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_retiro_cmedico FROM mod_medicina_antece_16v
                            where
                            m_antec_retiro_cmedico like '%$query%'
                            group by m_antec_retiro_cmedico");
        return $sql;
    }

    public function st_m_antec_proyec()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_proyec FROM mod_medicina_antece_16v
                            where
                            m_antec_proyec like '%$query%'
                            group by m_antec_proyec");
        return $sql;
    }

    public function st_m_antec_empresa()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_empresa FROM mod_medicina_antece_16v
                            where
                            m_antec_empresa like '%$query%'
                            group by m_antec_empresa");
        return $sql;
    }

    public function st_m_antec_cargo()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT m_antec_cargo FROM mod_medicina_antece_16v
                            where
                            m_antec_cargo like '%$query%'
                            group by m_antec_cargo");
        return $sql;
    }

    public function load_antece_16v()
    {
        $adm = $_POST['m_antec_adm'];
        $id = $_POST['m_antec_id'];
        $query = "SELECT * FROM mod_medicina_antece_16v
            where
            m_antec_id=$id and m_antec_adm=$adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function list_antece_16v()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT *
                FROM mod_medicina_antece_16v
                where m_antec_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function save_nuevo_antece16_viejo()
    {
        $params = array();
        $this->begin();
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];
        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $timestamp = strtotime($_POST['m_antec_fech_ini']);
        $m_antec_fech_ini = ((strlen($_POST['m_antec_fech_ini']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params[':m_antec_fech_ini'] = $m_antec_fech_ini;

        $timestamp0 = strtotime($_POST['m_antec_fech_fin']);
        $m_antec_fech_fin = ((strlen($_POST['m_antec_fech_fin']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':m_antec_fech_fin'] = $m_antec_fech_fin;

        $params[':m_antec_suelo'] = $_POST['m_antec_suelo'];
        $params[':m_antec_cargo'] = $_POST['m_antec_cargo'];
        $params[':m_antec_empresa'] = $_POST['m_antec_empresa'];
        $params[':m_antec_proyec'] = $_POST['m_antec_proyec'];
        $params[':m_antec_alti'] = $_POST['m_antec_alti'];
        $params[':m_antec_fisico'] = $_POST['m_antec_fisico'];
        $params[':m_antec_fisico_hora'] = $_POST['m_antec_fisico_hora'];
        $params[':m_antec_fisico_uso'] = $_POST['m_antec_fisico_uso'];
        $params[':m_antec_quinico'] = $_POST['m_antec_quinico'];
        $params[':m_antec_quinico_hora'] = $_POST['m_antec_quinico_hora'];
        $params[':m_antec_quinico_uso'] = $_POST['m_antec_quinico_uso'];
        $params[':m_antec_biologico'] = $_POST['m_antec_biologico'];
        $params[':m_antec_biologico_hora'] = $_POST['m_antec_biologico_hora'];
        $params[':m_antec_biologico_uso'] = $_POST['m_antec_biologico_uso'];
        $params[':m_antec_ergonom'] = $_POST['m_antec_ergonom'];
        $params[':m_antec_ergonom_hora'] = $_POST['m_antec_ergonom_hora'];
        $params[':m_antec_ergonom_uso'] = $_POST['m_antec_ergonom_uso'];
        $params[':m_antec_otros'] = $_POST['m_antec_otros'];
        $params[':m_antec_otros_hora'] = $_POST['m_antec_otros_hora'];
        $params[':m_antec_otros_uso'] = $_POST['m_antec_otros_uso'];
        $params[':m_antec_obser'] = $_POST['m_antec_obser'];

        $timestamp1 = strtotime($_POST['m_antec_retiro_date']);
        $m_antec_retiro_date = ((strlen($_POST['m_antec_retiro_date']) > 0) ? date('Y-m-d', $timestamp1) : null);
        $params[':m_antec_retiro_date'] = $m_antec_retiro_date;

        $params[':m_antec_retiro_cmedico'] = $_POST['m_antec_retiro_cmedico'];
        $params[':m_antec_retiro_desc'] = $_POST['m_antec_retiro_desc'];



        $q2 = "INSERT INTO mod_medicina_antece_16v VALUES 
                (NULL,
                :adm,
                :ex_id,
                :m_antec_fech_ini,
                :m_antec_fech_fin,
                :m_antec_suelo,
                :m_antec_cargo,
                :m_antec_empresa,
                :m_antec_proyec,
                :m_antec_alti,
                :m_antec_fisico,
                :m_antec_fisico_hora,
                :m_antec_fisico_uso,
                :m_antec_quinico,
                :m_antec_quinico_hora,
                :m_antec_quinico_uso,
                :m_antec_biologico,
                :m_antec_biologico_hora,
                :m_antec_biologico_uso,
                :m_antec_ergonom,
                :m_antec_ergonom_hora,
                :m_antec_ergonom_uso,
                :m_antec_otros,
                :m_antec_otros_hora,
                :m_antec_otros_uso,
                :m_antec_obser,
                :m_antec_retiro_date,
                :m_antec_retiro_cmedico,
                :m_antec_retiro_desc);";

        $q = "INSERT INTO mod_medicina_antece_16v VALUES 
                (NULL,
                :adm,
                :ex_id,
                :m_antec_fech_ini,
                :m_antec_fech_fin,
                :m_antec_suelo,
                :m_antec_cargo,
                :m_antec_empresa,
                :m_antec_proyec,
                :m_antec_alti,
                :m_antec_fisico,
                :m_antec_fisico_hora,
                :m_antec_fisico_uso,
                :m_antec_quinico,
                :m_antec_quinico_hora,
                :m_antec_quinico_uso,
                :m_antec_biologico,
                :m_antec_biologico_hora,
                :m_antec_biologico_uso,
                :m_antec_ergonom,
                :m_antec_ergonom_hora,
                :m_antec_ergonom_uso,
                :m_antec_otros,
                :m_antec_otros_hora,
                :m_antec_otros_uso,
                :m_antec_obser,
                :m_antec_retiro_date,
                :m_antec_retiro_cmedico,
                :m_antec_retiro_desc);
                
                INSERT INTO mod_medicina VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_medicina_st,
                :ex_id);";

        $verifica = $this->sql("SELECT m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario FROM mod_medicina inner join sys_usuario on usu_id=m_medicina_usu where m_medicina_adm='$adm' and m_medicina_examen='$exa';");
        if ($verifica->total > 0) {
            $sql = $this->sql($q2, $params);
            if ($sql->success) {
                $this->commit();
                return $sql;
            } else {
                $this->rollback();
                return array('success' => false);
            }
        } else {
            $params[':sede'] = $this->user->con_sedid;
            $params[':usuario'] = $this->user->us_id;
            $params[':m_medicina_st'] = '1'; //modulo principal
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

    public function update_nuevo_antece16_viejo()
    {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $timestamp = strtotime($_POST['m_antec_fech_ini']);
        $m_antec_fech_ini = ((strlen($_POST['m_antec_fech_ini']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params[':m_antec_fech_ini'] = $m_antec_fech_ini;

        $timestamp0 = strtotime($_POST['m_antec_fech_fin']);
        $m_antec_fech_fin = ((strlen($_POST['m_antec_fech_fin']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':m_antec_fech_fin'] = $m_antec_fech_fin;

        $params[':m_antec_suelo'] = $_POST['m_antec_suelo'];
        $params[':m_antec_cargo'] = $_POST['m_antec_cargo'];
        $params[':m_antec_empresa'] = $_POST['m_antec_empresa'];
        $params[':m_antec_proyec'] = $_POST['m_antec_proyec'];
        $params[':m_antec_alti'] = $_POST['m_antec_alti'];
        $params[':m_antec_fisico'] = $_POST['m_antec_fisico'];
        $params[':m_antec_fisico_hora'] = $_POST['m_antec_fisico_hora'];
        $params[':m_antec_fisico_uso'] = $_POST['m_antec_fisico_uso'];
        $params[':m_antec_quinico'] = $_POST['m_antec_quinico'];
        $params[':m_antec_quinico_hora'] = $_POST['m_antec_quinico_hora'];
        $params[':m_antec_quinico_uso'] = $_POST['m_antec_quinico_uso'];
        $params[':m_antec_biologico'] = $_POST['m_antec_biologico'];
        $params[':m_antec_biologico_hora'] = $_POST['m_antec_biologico_hora'];
        $params[':m_antec_biologico_uso'] = $_POST['m_antec_biologico_uso'];
        $params[':m_antec_ergonom'] = $_POST['m_antec_ergonom'];
        $params[':m_antec_ergonom_hora'] = $_POST['m_antec_ergonom_hora'];
        $params[':m_antec_ergonom_uso'] = $_POST['m_antec_ergonom_uso'];
        $params[':m_antec_otros'] = $_POST['m_antec_otros'];
        $params[':m_antec_otros_hora'] = $_POST['m_antec_otros_hora'];
        $params[':m_antec_otros_uso'] = $_POST['m_antec_otros_uso'];
        $params[':m_antec_obser'] = $_POST['m_antec_obser'];

        $timestamp1 = strtotime($_POST['m_antec_retiro_date']);
        $m_antec_retiro_date = ((strlen($_POST['m_antec_retiro_date']) > 0) ? date('Y-m-d', $timestamp1) : null);
        $params[':m_antec_retiro_date'] = $m_antec_retiro_date;

        $params[':m_antec_retiro_cmedico'] = $_POST['m_antec_retiro_cmedico'];
        $params[':m_antec_retiro_desc'] = $_POST['m_antec_retiro_desc'];

        $this->begin();
        $q = 'Update mod_medicina_antece_16v set
                m_antec_fech_ini=:m_antec_fech_ini,
                m_antec_fech_fin=:m_antec_fech_fin,
                m_antec_suelo=:m_antec_suelo,
                m_antec_cargo=:m_antec_cargo,
                m_antec_empresa=:m_antec_empresa,
                m_antec_proyec=:m_antec_proyec,
                m_antec_alti=:m_antec_alti,
                m_antec_fisico=:m_antec_fisico,
                m_antec_fisico_hora=:m_antec_fisico_hora,
                m_antec_fisico_uso=:m_antec_fisico_uso,
                m_antec_quinico=:m_antec_quinico,
                m_antec_quinico_hora=:m_antec_quinico_hora,
                m_antec_quinico_uso=:m_antec_quinico_uso,
                m_antec_biologico=:m_antec_biologico,
                m_antec_biologico_hora=:m_antec_biologico_hora,
                m_antec_biologico_uso=:m_antec_biologico_uso,
                m_antec_ergonom=:m_antec_ergonom,
                m_antec_ergonom_hora=:m_antec_ergonom_hora,
                m_antec_ergonom_uso=:m_antec_ergonom_uso,
                m_antec_otros=:m_antec_otros,
                m_antec_otros_hora=:m_antec_otros_hora,
                m_antec_otros_uso=:m_antec_otros_uso,
                m_antec_obser=:m_antec_obser,
                m_antec_retiro_date=:m_antec_retiro_date,
                m_antec_retiro_cmedico=:m_antec_retiro_cmedico,
                m_antec_retiro_desc=:m_antec_retiro_desc
                where
                m_antec_id=:id and m_antec_adm=:adm and m_antec_exa=:ex_id;
                
                update mod_medicina set
                m_medicina_usu=:usuario,
                m_medicina_fech_update=now()
                where
                m_medicina_adm=:adm and m_medicina_examen=:ex_id;';

        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function antece_7c($adm)
    {
        $sql = "SELECT * FROM mod_medicina_antece_16v
            WHERE
            m_antec_adm=$adm;";
        return $this->sql($sql);
    }

    //osteo conclusiones y recomendaciones
    public function list_osteo_conclu()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT osteo_conclu_id, osteo_conclu_adm, osteo_conclu_desc
                FROM mod_medicina_osteo_conclusion
                where osteo_conclu_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_osteo_conclu()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT osteo_conclu_desc 
                            FROM mod_medicina_osteo_conclusion
                            where
                            osteo_conclu_desc like '%$query%'
                            group by osteo_conclu_desc");
        return $sql;
    }

    public function save_osteo_conclusion()
    {
        $params = array();
        $params[':osteo_conclu_adm'] = $_POST['osteo_conclu_adm'];
        $params[':osteo_conclu_desc'] = $_POST['osteo_conclu_desc'];

        $q = 'INSERT INTO mod_medicina_osteo_conclusion VALUES 
                (NULL,
                :osteo_conclu_adm,
                UPPER(:osteo_conclu_desc))';
        return $this->sql($q, $params);
    }

    public function update_osteo_conclusion()
    {
        $params = array();
        $params[':osteo_conclu_id'] = $_POST['osteo_conclu_id'];
        $params[':osteo_conclu_adm'] = $_POST['osteo_conclu_adm'];
        $params[':osteo_conclu_desc'] = $_POST['osteo_conclu_desc'];

        $this->begin();
        $q = 'Update mod_medicina_osteo_conclusion set
                osteo_conclu_desc=UPPER(:osteo_conclu_desc)
                where
                osteo_conclu_id=:osteo_conclu_id and osteo_conclu_adm=:osteo_conclu_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $osteo_conclu_id = $_POST['osteo_conclu_id'];
            $this->commit();
            return array('success' => true, 'data' => $osteo_conclu_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_osteo_conclu()
    {
        $osteo_conclu_adm = $_POST['osteo_conclu_adm'];
        $osteo_conclu_id = $_POST['osteo_conclu_id'];
        $query = "SELECT
            osteo_conclu_id, osteo_conclu_adm, osteo_conclu_desc
            FROM mod_medicina_osteo_conclusion
            where
            osteo_conclu_id=$osteo_conclu_id and
            osteo_conclu_adm=$osteo_conclu_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function load_nuevoOsteo()
    {
        $m_osteo_adm = $_POST['m_osteo_adm'];
        $m_osteo_exa = $_POST['m_osteo_exa'];
        $query = "SELECT * FROM mod_medicina_osteo
            where
            m_osteo_exa=$m_osteo_exa and m_osteo_adm=$m_osteo_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_nuevoOsteo()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_medicina_st'] = '1'; //modulo principal

        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_osteo_trauma'] = $_POST['m_osteo_trauma'];
        $params[':m_osteo_degenera'] = $_POST['m_osteo_degenera'];
        $params[':m_osteo_congeni'] = $_POST['m_osteo_congeni'];
        $params[':m_osteo_quirur'] = $_POST['m_osteo_quirur'];
        $params[':m_osteo_trata'] = $_POST['m_osteo_trata'];
        $params[':m_osteo_cuello_dura_3meses'] = $_POST['m_osteo_cuello_dura_3meses'];
        $params[':m_osteo_cuello_time_ini'] = $_POST['m_osteo_cuello_time_ini'];
        $params[':m_osteo_cuello_dura_dolor'] = $_POST['m_osteo_cuello_dura_dolor'];
        $params[':m_osteo_cuello_recib_trata'] = $_POST['m_osteo_cuello_recib_trata'];
        $params[':m_osteo_cuello_dias_trata'] = $_POST['m_osteo_cuello_dias_trata'];

        $params[':m_osteo_espalda_a_dura_3meses'] = $_POST['m_osteo_espalda_a_dura_3meses'];
        $params[':m_osteo_espalda_a_time_ini'] = $_POST['m_osteo_espalda_a_time_ini'];
        $params[':m_osteo_espalda_a_dura_dolor'] = $_POST['m_osteo_espalda_a_dura_dolor'];
        $params[':m_osteo_espalda_a_recib_trata'] = $_POST['m_osteo_espalda_a_recib_trata'];
        $params[':m_osteo_espalda_a_dias_trata'] = $_POST['m_osteo_espalda_a_dias_trata'];
        $params[':m_osteo_espalda_b_dura_3meses'] = $_POST['m_osteo_espalda_b_dura_3meses'];
        $params[':m_osteo_espalda_b_time_ini'] = $_POST['m_osteo_espalda_b_time_ini'];
        $params[':m_osteo_espalda_b_dura_dolor'] = $_POST['m_osteo_espalda_b_dura_dolor'];
        $params[':m_osteo_espalda_b_recib_trata'] = $_POST['m_osteo_espalda_b_recib_trata'];
        $params[':m_osteo_espalda_b_dias_trata'] = $_POST['m_osteo_espalda_b_dias_trata'];
        $params[':m_osteo_hombro_d_dura_3meses'] = $_POST['m_osteo_hombro_d_dura_3meses'];
        $params[':m_osteo_hombro_d_time_ini'] = $_POST['m_osteo_hombro_d_time_ini'];
        $params[':m_osteo_hombro_d_dura_dolor'] = $_POST['m_osteo_hombro_d_dura_dolor'];
        $params[':m_osteo_hombro_d_recib_trata'] = $_POST['m_osteo_hombro_d_recib_trata'];
        $params[':m_osteo_hombro_d_dias_trata'] = $_POST['m_osteo_hombro_d_dias_trata'];
        $params[':m_osteo_hombro_i_dura_3meses'] = $_POST['m_osteo_hombro_i_dura_3meses'];
        $params[':m_osteo_hombro_i_time_ini'] = $_POST['m_osteo_hombro_i_time_ini'];
        $params[':m_osteo_hombro_i_dura_dolor'] = $_POST['m_osteo_hombro_i_dura_dolor'];
        $params[':m_osteo_hombro_i_recib_trata'] = $_POST['m_osteo_hombro_i_recib_trata'];
        $params[':m_osteo_hombro_i_dias_trata'] = $_POST['m_osteo_hombro_i_dias_trata'];
        $params[':m_osteo_codo_d_dura_3meses'] = $_POST['m_osteo_codo_d_dura_3meses'];
        $params[':m_osteo_codo_d_time_ini'] = $_POST['m_osteo_codo_d_time_ini'];
        $params[':m_osteo_codo_d_dura_dolor'] = $_POST['m_osteo_codo_d_dura_dolor'];
        $params[':m_osteo_codo_d_recib_trata'] = $_POST['m_osteo_codo_d_recib_trata'];
        $params[':m_osteo_codo_d_dias_trata'] = $_POST['m_osteo_codo_d_dias_trata'];
        $params[':m_osteo_codo_i_dura_3meses'] = $_POST['m_osteo_codo_i_dura_3meses'];
        $params[':m_osteo_codo_i_time_ini'] = $_POST['m_osteo_codo_i_time_ini'];
        $params[':m_osteo_codo_i_dura_dolor'] = $_POST['m_osteo_codo_i_dura_dolor'];
        $params[':m_osteo_codo_i_recib_trata'] = $_POST['m_osteo_codo_i_recib_trata'];
        $params[':m_osteo_codo_i_dias_trata'] = $_POST['m_osteo_codo_i_dias_trata'];

        $params[':m_osteo_mano_d_dura_3meses'] = $_POST['m_osteo_mano_d_dura_3meses'];
        $params[':m_osteo_mano_d_time_ini'] = $_POST['m_osteo_mano_d_time_ini'];
        $params[':m_osteo_mano_d_dura_dolor'] = $_POST['m_osteo_mano_d_dura_dolor'];
        $params[':m_osteo_mano_d_recib_trata'] = $_POST['m_osteo_mano_d_recib_trata'];
        $params[':m_osteo_mano_d_dias_trata'] = $_POST['m_osteo_mano_d_dias_trata'];
        $params[':m_osteo_mano_i_dura_3meses'] = $_POST['m_osteo_mano_i_dura_3meses'];
        $params[':m_osteo_mano_i_time_ini'] = $_POST['m_osteo_mano_i_time_ini'];
        $params[':m_osteo_mano_i_dura_dolor'] = $_POST['m_osteo_mano_i_dura_dolor'];
        $params[':m_osteo_mano_i_recib_trata'] = $_POST['m_osteo_mano_i_recib_trata'];
        $params[':m_osteo_mano_i_dias_trata'] = $_POST['m_osteo_mano_i_dias_trata'];
        $params[':m_osteo_muslo_d_dura_3meses'] = $_POST['m_osteo_muslo_d_dura_3meses'];
        $params[':m_osteo_muslo_d_time_ini'] = $_POST['m_osteo_muslo_d_time_ini'];
        $params[':m_osteo_muslo_d_dura_dolor'] = $_POST['m_osteo_muslo_d_dura_dolor'];
        $params[':m_osteo_muslo_d_recib_trata'] = $_POST['m_osteo_muslo_d_recib_trata'];
        $params[':m_osteo_muslo_d_dias_trata'] = $_POST['m_osteo_muslo_d_dias_trata'];
        $params[':m_osteo_muslo_i_dura_3meses'] = $_POST['m_osteo_muslo_i_dura_3meses'];
        $params[':m_osteo_muslo_i_time_ini'] = $_POST['m_osteo_muslo_i_time_ini'];
        $params[':m_osteo_muslo_i_dura_dolor'] = $_POST['m_osteo_muslo_i_dura_dolor'];
        $params[':m_osteo_muslo_i_recib_trata'] = $_POST['m_osteo_muslo_i_recib_trata'];
        $params[':m_osteo_muslo_i_dias_trata'] = $_POST['m_osteo_muslo_i_dias_trata'];
        $params[':m_osteo_rodilla_d_dura_3meses'] = $_POST['m_osteo_rodilla_d_dura_3meses'];
        $params[':m_osteo_rodilla_d_time_ini'] = $_POST['m_osteo_rodilla_d_time_ini'];
        $params[':m_osteo_rodilla_d_dura_dolor'] = $_POST['m_osteo_rodilla_d_dura_dolor'];
        $params[':m_osteo_rodilla_d_recib_trata'] = $_POST['m_osteo_rodilla_d_recib_trata'];
        $params[':m_osteo_rodilla_d_dias_trata'] = $_POST['m_osteo_rodilla_d_dias_trata'];
        $params[':m_osteo_rodilla_i_dura_3meses'] = $_POST['m_osteo_rodilla_i_dura_3meses'];
        $params[':m_osteo_rodilla_i_time_ini'] = $_POST['m_osteo_rodilla_i_time_ini'];
        $params[':m_osteo_rodilla_i_dura_dolor'] = $_POST['m_osteo_rodilla_i_dura_dolor'];
        $params[':m_osteo_rodilla_i_recib_trata'] = $_POST['m_osteo_rodilla_i_recib_trata'];
        $params[':m_osteo_rodilla_i_dias_trata'] = $_POST['m_osteo_rodilla_i_dias_trata'];
        $params[':m_osteo_pies_d_dura_3meses'] = $_POST['m_osteo_pies_d_dura_3meses'];
        $params[':m_osteo_pies_d_time_ini'] = $_POST['m_osteo_pies_d_time_ini'];
        $params[':m_osteo_pies_d_dura_dolor'] = $_POST['m_osteo_pies_d_dura_dolor'];
        $params[':m_osteo_pies_d_recib_trata'] = $_POST['m_osteo_pies_d_recib_trata'];
        $params[':m_osteo_pies_d_dias_trata'] = $_POST['m_osteo_pies_d_dias_trata'];
        $params[':m_osteo_pies_i_dura_3meses'] = $_POST['m_osteo_pies_i_dura_3meses'];
        $params[':m_osteo_pies_i_time_ini'] = $_POST['m_osteo_pies_i_time_ini'];
        $params[':m_osteo_pies_i_dura_dolor'] = $_POST['m_osteo_pies_i_dura_dolor'];
        $params[':m_osteo_pies_i_recib_trata'] = $_POST['m_osteo_pies_i_recib_trata'];
        $params[':m_osteo_pies_i_dias_trata'] = $_POST['m_osteo_pies_i_dias_trata'];
        //        
        $params[':m_osteo_anames_obs'] = $_POST['m_osteo_anames_obs'];
        $params[':m_osteo_lordo_cervic'] = $_POST['m_osteo_lordo_cervic'];
        $params[':m_osteo_cifosis'] = $_POST['m_osteo_cifosis'];
        $params[':m_osteo_lordo_lumbar'] = $_POST['m_osteo_lordo_lumbar'];
        $params[':m_osteo_desvia_lat_halla'] = $_POST['m_osteo_desvia_lat_halla'];
        $params[':m_osteo_desvia_lat_escolio'] = $_POST['m_osteo_desvia_lat_escolio'];
        $params[':m_osteo_apofisis'] = $_POST['m_osteo_apofisis'];
        $params[':m_osteo_apofisis_obs'] = $_POST['m_osteo_apofisis_obs'];
        $params[':m_osteo_contra_musc_cervic'] = $_POST['m_osteo_contra_musc_cervic'];
        $params[':m_osteo_contra_musc_cervic_obs'] = $_POST['m_osteo_contra_musc_cervic_obs'];
        $params[':m_osteo_contra_musc_lumbar'] = $_POST['m_osteo_contra_musc_lumbar'];
        $params[':m_osteo_contra_musc_lumbar_obs'] = $_POST['m_osteo_contra_musc_lumbar_obs'];


        $params[':m_osteo_cuello_flex'] = $_POST['m_osteo_cuello_flex'];
        $params[':m_osteo_cuello_flex_lat_d'] = $_POST['m_osteo_cuello_flex_lat_d'];
        $params[':m_osteo_cuello_flex_lat_i'] = $_POST['m_osteo_cuello_flex_lat_i'];
        $params[':m_osteo_cuello_ext'] = $_POST['m_osteo_cuello_ext'];
        $params[':m_osteo_cuello_ext_rot_d'] = $_POST['m_osteo_cuello_ext_rot_d'];
        $params[':m_osteo_cuello_ext_rot_i'] = $_POST['m_osteo_cuello_ext_rot_i'];
        $params[':m_osteo_tronco_flex'] = $_POST['m_osteo_tronco_flex'];
        $params[':m_osteo_tronco_flex_lat_d'] = $_POST['m_osteo_tronco_flex_lat_d'];
        $params[':m_osteo_tronco_flex_lat_i'] = $_POST['m_osteo_tronco_flex_lat_i'];
        $params[':m_osteo_tronco_ext'] = $_POST['m_osteo_tronco_ext'];
        $params[':m_osteo_tronco_ext_rot_d'] = $_POST['m_osteo_tronco_ext_rot_d'];
        $params[':m_osteo_tronco_ext_rot_i'] = $_POST['m_osteo_tronco_ext_rot_i'];
        $params[':m_osteo_hiper_acor_f_coment'] = $_POST['m_osteo_hiper_acor_f_coment'];
        $params[':m_osteo_hombro_flex_der'] = $_POST['m_osteo_hombro_flex_der'];
        $params[':m_osteo_hombro_flex_izq'] = $_POST['m_osteo_hombro_flex_izq'];
        $params[':m_osteo_hombro_flex_fuerza'] = $_POST['m_osteo_hombro_flex_fuerza'];
        $params[':m_osteo_hombro_flex_tono'] = $_POST['m_osteo_hombro_flex_tono'];
        $params[':m_osteo_hombro_flex_color'] = $_POST['m_osteo_hombro_flex_color'];
        $params[':m_osteo_hombro_adu_h_der'] = $_POST['m_osteo_hombro_adu_h_der'];
        $params[':m_osteo_hombro_adu_h_izq'] = $_POST['m_osteo_hombro_adu_h_izq'];
        $params[':m_osteo_hombro_adu_h_fuerza'] = $_POST['m_osteo_hombro_adu_h_fuerza'];
        $params[':m_osteo_hombro_adu_h_tono'] = $_POST['m_osteo_hombro_adu_h_tono'];
        $params[':m_osteo_hombro_adu_h_color'] = $_POST['m_osteo_hombro_adu_h_color'];
        $params[':m_osteo_hombro_ext_der'] = $_POST['m_osteo_hombro_ext_der'];
        $params[':m_osteo_hombro_ext_izq'] = $_POST['m_osteo_hombro_ext_izq'];
        $params[':m_osteo_hombro_ext_fuerza'] = $_POST['m_osteo_hombro_ext_fuerza'];
        $params[':m_osteo_hombro_ext_tono'] = $_POST['m_osteo_hombro_ext_tono'];
        $params[':m_osteo_hombro_ext_color'] = $_POST['m_osteo_hombro_ext_color'];
        $params[':m_osteo_hombro_rot_in_der'] = $_POST['m_osteo_hombro_rot_in_der'];
        $params[':m_osteo_hombro_rot_in_izq'] = $_POST['m_osteo_hombro_rot_in_izq'];
        $params[':m_osteo_hombro_rot_in_fuerza'] = $_POST['m_osteo_hombro_rot_in_fuerza'];
        $params[':m_osteo_hombro_rot_in_tono'] = $_POST['m_osteo_hombro_rot_in_tono'];
        $params[':m_osteo_hombro_rot_in_color'] = $_POST['m_osteo_hombro_rot_in_color'];
        $params[':m_osteo_hombro_abduc_der'] = $_POST['m_osteo_hombro_abduc_der'];
        $params[':m_osteo_hombro_abduc_izq'] = $_POST['m_osteo_hombro_abduc_izq'];
        $params[':m_osteo_hombro_abduc_fuerza'] = $_POST['m_osteo_hombro_abduc_fuerza'];
        $params[':m_osteo_hombro_abduc_tono'] = $_POST['m_osteo_hombro_abduc_tono'];
        $params[':m_osteo_hombro_abduc_color'] = $_POST['m_osteo_hombro_abduc_color'];
        $params[':m_osteo_hombro_rot_ex_der'] = $_POST['m_osteo_hombro_rot_ex_der'];
        $params[':m_osteo_hombro_rot_ex_izq'] = $_POST['m_osteo_hombro_rot_ex_izq'];
        $params[':m_osteo_hombro_rot_ex_fuerza'] = $_POST['m_osteo_hombro_rot_ex_fuerza'];
        $params[':m_osteo_hombro_rot_ex_tono'] = $_POST['m_osteo_hombro_rot_ex_tono'];
        $params[':m_osteo_hombro_rot_ex_color'] = $_POST['m_osteo_hombro_rot_ex_color'];
        $params[':m_osteo_hombro_abd_h_der'] = $_POST['m_osteo_hombro_abd_h_der'];
        $params[':m_osteo_hombro_abd_h_izq'] = $_POST['m_osteo_hombro_abd_h_izq'];
        $params[':m_osteo_hombro_abd_h_fuerza'] = $_POST['m_osteo_hombro_abd_h_fuerza'];
        $params[':m_osteo_hombro_abd_h_tono'] = $_POST['m_osteo_hombro_abd_h_tono'];
        $params[':m_osteo_hombro_abd_h_color'] = $_POST['m_osteo_hombro_abd_h_color'];


        $params[':m_osteo_codo_flex_der'] = $_POST['m_osteo_codo_flex_der'];
        $params[':m_osteo_codo_flex_izq'] = $_POST['m_osteo_codo_flex_izq'];
        $params[':m_osteo_codo_flex_fuerza'] = $_POST['m_osteo_codo_flex_fuerza'];
        $params[':m_osteo_codo_flex_tono'] = $_POST['m_osteo_codo_flex_tono'];
        $params[':m_osteo_codo_flex_color'] = $_POST['m_osteo_codo_flex_color'];
        $params[':m_osteo_codo_supina_der'] = $_POST['m_osteo_codo_supina_der'];
        $params[':m_osteo_codo_supina_izq'] = $_POST['m_osteo_codo_supina_izq'];
        $params[':m_osteo_codo_supina_fuerza'] = $_POST['m_osteo_codo_supina_fuerza'];
        $params[':m_osteo_codo_supina_tono'] = $_POST['m_osteo_codo_supina_tono'];
        $params[':m_osteo_codo_supina_color'] = $_POST['m_osteo_codo_supina_color'];
        $params[':m_osteo_codo_ext_der'] = $_POST['m_osteo_codo_ext_der'];
        $params[':m_osteo_codo_ext_izq'] = $_POST['m_osteo_codo_ext_izq'];
        $params[':m_osteo_codo_ext_fuerza'] = $_POST['m_osteo_codo_ext_fuerza'];
        $params[':m_osteo_codo_ext_tono'] = $_POST['m_osteo_codo_ext_tono'];
        $params[':m_osteo_codo_ext_color'] = $_POST['m_osteo_codo_ext_color'];
        $params[':m_osteo_codo_prona_der'] = $_POST['m_osteo_codo_prona_der'];
        $params[':m_osteo_codo_prona_izq'] = $_POST['m_osteo_codo_prona_izq'];
        $params[':m_osteo_codo_prona_fuerza'] = $_POST['m_osteo_codo_prona_fuerza'];
        $params[':m_osteo_codo_prona_tono'] = $_POST['m_osteo_codo_prona_tono'];
        $params[':m_osteo_codo_prona_color'] = $_POST['m_osteo_codo_prona_color'];


        $params[':m_osteo_muneca_flex_der'] = $_POST['m_osteo_muneca_flex_der'];
        $params[':m_osteo_muneca_flex_izq'] = $_POST['m_osteo_muneca_flex_izq'];
        $params[':m_osteo_muneca_flex_fuerza'] = $_POST['m_osteo_muneca_flex_fuerza'];
        $params[':m_osteo_muneca_flex_tono'] = $_POST['m_osteo_muneca_flex_tono'];
        $params[':m_osteo_muneca_flex_color'] = $_POST['m_osteo_muneca_flex_color'];
        $params[':m_osteo_muneca_des_cubi_der'] = $_POST['m_osteo_muneca_des_cubi_der'];
        $params[':m_osteo_muneca_des_cubi_izq'] = $_POST['m_osteo_muneca_des_cubi_izq'];
        $params[':m_osteo_muneca_des_cubi_fuerza'] = $_POST['m_osteo_muneca_des_cubi_fuerza'];
        $params[':m_osteo_muneca_des_cubi_tono'] = $_POST['m_osteo_muneca_des_cubi_tono'];
        $params[':m_osteo_muneca_des_cubi_color'] = $_POST['m_osteo_muneca_des_cubi_color'];
        $params[':m_osteo_muneca_ext_der'] = $_POST['m_osteo_muneca_ext_der'];
        $params[':m_osteo_muneca_ext_izq'] = $_POST['m_osteo_muneca_ext_izq'];
        $params[':m_osteo_muneca_ext_fuerza'] = $_POST['m_osteo_muneca_ext_fuerza'];
        $params[':m_osteo_muneca_ext_tono'] = $_POST['m_osteo_muneca_ext_tono'];
        $params[':m_osteo_muneca_ext_color'] = $_POST['m_osteo_muneca_ext_color'];
        $params[':m_osteo_muneca_des_radi_der'] = $_POST['m_osteo_muneca_des_radi_der'];
        $params[':m_osteo_muneca_des_radi_izq'] = $_POST['m_osteo_muneca_des_radi_izq'];
        $params[':m_osteo_muneca_des_radi_fuerza'] = $_POST['m_osteo_muneca_des_radi_fuerza'];
        $params[':m_osteo_muneca_des_radi_tono'] = $_POST['m_osteo_muneca_des_radi_tono'];
        $params[':m_osteo_muneca_des_radi_color'] = $_POST['m_osteo_muneca_des_radi_color'];

        $params[':m_osteo_sup_acor_fu_sen_coment'] = $_POST['m_osteo_sup_acor_fu_sen_coment'];
        $params[':m_osteo_cader_flex_der'] = $_POST['m_osteo_cader_flex_der'];
        $params[':m_osteo_cader_flex_izq'] = $_POST['m_osteo_cader_flex_izq'];
        $params[':m_osteo_cader_flex_fuerza'] = $_POST['m_osteo_cader_flex_fuerza'];
        $params[':m_osteo_cader_flex_tono'] = $_POST['m_osteo_cader_flex_tono'];
        $params[':m_osteo_cader_flex_color'] = $_POST['m_osteo_cader_flex_color'];
        $params[':m_osteo_cader_aduc_der'] = $_POST['m_osteo_cader_aduc_der'];
        $params[':m_osteo_cader_aduc_izq'] = $_POST['m_osteo_cader_aduc_izq'];
        $params[':m_osteo_cader_aduc_fuerza'] = $_POST['m_osteo_cader_aduc_fuerza'];
        $params[':m_osteo_cader_aduc_tono'] = $_POST['m_osteo_cader_aduc_tono'];
        $params[':m_osteo_cader_aduc_color'] = $_POST['m_osteo_cader_aduc_color'];
        $params[':m_osteo_cader_ext_der'] = $_POST['m_osteo_cader_ext_der'];
        $params[':m_osteo_cader_ext_izq'] = $_POST['m_osteo_cader_ext_izq'];
        $params[':m_osteo_cader_ext_fuerza'] = $_POST['m_osteo_cader_ext_fuerza'];
        $params[':m_osteo_cader_ext_tono'] = $_POST['m_osteo_cader_ext_tono'];
        $params[':m_osteo_cader_ext_color'] = $_POST['m_osteo_cader_ext_color'];
        $params[':m_osteo_cader_rota_int_der'] = $_POST['m_osteo_cader_rota_int_der'];
        $params[':m_osteo_cader_rota_int_izq'] = $_POST['m_osteo_cader_rota_int_izq'];
        $params[':m_osteo_cader_rota_int_fuerza'] = $_POST['m_osteo_cader_rota_int_fuerza'];
        $params[':m_osteo_cader_rota_int_tono'] = $_POST['m_osteo_cader_rota_int_tono'];
        $params[':m_osteo_cader_rota_int_color'] = $_POST['m_osteo_cader_rota_int_color'];
        $params[':m_osteo_cader_abduc_der'] = $_POST['m_osteo_cader_abduc_der'];
        $params[':m_osteo_cader_abduc_izq'] = $_POST['m_osteo_cader_abduc_izq'];
        $params[':m_osteo_cader_abduc_fuerza'] = $_POST['m_osteo_cader_abduc_fuerza'];
        $params[':m_osteo_cader_abduc_tono'] = $_POST['m_osteo_cader_abduc_tono'];
        $params[':m_osteo_cader_abduc_color'] = $_POST['m_osteo_cader_abduc_color'];
        $params[':m_osteo_cader_rota_ext_der'] = $_POST['m_osteo_cader_rota_ext_der'];
        $params[':m_osteo_cader_rota_ext_izq'] = $_POST['m_osteo_cader_rota_ext_izq'];
        $params[':m_osteo_cader_rota_ext_fuerza'] = $_POST['m_osteo_cader_rota_ext_fuerza'];
        $params[':m_osteo_cader_rota_ext_tono'] = $_POST['m_osteo_cader_rota_ext_tono'];
        $params[':m_osteo_cader_rota_ext_color'] = $_POST['m_osteo_cader_rota_ext_color'];

        $params[':m_osteo_rodill_flex_der'] = $_POST['m_osteo_rodill_flex_der'];
        $params[':m_osteo_rodill_flex_izq'] = $_POST['m_osteo_rodill_flex_izq'];
        $params[':m_osteo_rodill_flex_fuerza'] = $_POST['m_osteo_rodill_flex_fuerza'];
        $params[':m_osteo_rodill_flex_tono'] = $_POST['m_osteo_rodill_flex_tono'];
        $params[':m_osteo_rodill_flex_color'] = $_POST['m_osteo_rodill_flex_color'];
        $params[':m_osteo_rodill_rota_tibi_der'] = $_POST['m_osteo_rodill_rota_tibi_der'];
        $params[':m_osteo_rodill_rota_tibi_izq'] = $_POST['m_osteo_rodill_rota_tibi_izq'];
        $params[':m_osteo_rodill_rota_tibi_fuerza'] = $_POST['m_osteo_rodill_rota_tibi_fuerza'];
        $params[':m_osteo_rodill_rota_tibi_tono'] = $_POST['m_osteo_rodill_rota_tibi_tono'];
        $params[':m_osteo_rodill_rota_tibi_color'] = $_POST['m_osteo_rodill_rota_tibi_color'];
        $params[':m_osteo_rodill_ext_der'] = $_POST['m_osteo_rodill_ext_der'];
        $params[':m_osteo_rodill_ext_izq'] = $_POST['m_osteo_rodill_ext_izq'];
        $params[':m_osteo_rodill_ext_fuerza'] = $_POST['m_osteo_rodill_ext_fuerza'];
        $params[':m_osteo_rodill_ext_tono'] = $_POST['m_osteo_rodill_ext_tono'];
        $params[':m_osteo_rodill_ext_color'] = $_POST['m_osteo_rodill_ext_color'];
        $params[':m_osteo_tobill_dorsi_der'] = $_POST['m_osteo_tobill_dorsi_der'];
        $params[':m_osteo_tobill_dorsi_izq'] = $_POST['m_osteo_tobill_dorsi_izq'];
        $params[':m_osteo_tobill_dorsi_fuerza'] = $_POST['m_osteo_tobill_dorsi_fuerza'];
        $params[':m_osteo_tobill_dorsi_tono'] = $_POST['m_osteo_tobill_dorsi_tono'];
        $params[':m_osteo_tobill_dorsi_color'] = $_POST['m_osteo_tobill_dorsi_color'];
        $params[':m_osteo_tobill_inver_der'] = $_POST['m_osteo_tobill_inver_der'];
        $params[':m_osteo_tobill_inver_izq'] = $_POST['m_osteo_tobill_inver_izq'];
        $params[':m_osteo_tobill_inver_fuerza'] = $_POST['m_osteo_tobill_inver_fuerza'];
        $params[':m_osteo_tobill_inver_tono'] = $_POST['m_osteo_tobill_inver_tono'];
        $params[':m_osteo_tobill_inver_color'] = $_POST['m_osteo_tobill_inver_color'];
        $params[':m_osteo_tobill_flex_plan_der'] = $_POST['m_osteo_tobill_flex_plan_der'];
        $params[':m_osteo_tobill_flex_plan_izq'] = $_POST['m_osteo_tobill_flex_plan_izq'];
        $params[':m_osteo_tobill_flex_plan_fuerza'] = $_POST['m_osteo_tobill_flex_plan_fuerza'];
        $params[':m_osteo_tobill_flex_plan_tono'] = $_POST['m_osteo_tobill_flex_plan_tono'];
        $params[':m_osteo_tobill_flex_plan_color'] = $_POST['m_osteo_tobill_flex_plan_color'];
        $params[':m_osteo_tobill_ever_der'] = $_POST['m_osteo_tobill_ever_der'];
        $params[':m_osteo_tobill_ever_izq'] = $_POST['m_osteo_tobill_ever_izq'];
        $params[':m_osteo_tobill_ever_fuerza'] = $_POST['m_osteo_tobill_ever_fuerza'];
        $params[':m_osteo_tobill_ever_tono'] = $_POST['m_osteo_tobill_ever_tono'];
        $params[':m_osteo_tobill_ever_color'] = $_POST['m_osteo_tobill_ever_color'];
        $params[':m_osteo_inf_acor_fu_sen_coment'] = $_POST['m_osteo_inf_acor_fu_sen_coment'];
        $params[':m_osteo_test_jobe_der'] = $_POST['m_osteo_test_jobe_der'];
        $params[':m_osteo_test_jobe_der_obs'] = $_POST['m_osteo_test_jobe_der_obs'];
        $params[':m_osteo_test_jobe_izq'] = $_POST['m_osteo_test_jobe_izq'];
        $params[':m_osteo_test_jobe_izq_obs'] = $_POST['m_osteo_test_jobe_izq_obs'];
        $params[':m_osteo_mani_apley_der'] = $_POST['m_osteo_mani_apley_der'];
        $params[':m_osteo_mani_apley_der_obs'] = $_POST['m_osteo_mani_apley_der_obs'];
        $params[':m_osteo_mani_apley_izq'] = $_POST['m_osteo_mani_apley_izq'];
        $params[':m_osteo_mani_apley_izq_obs'] = $_POST['m_osteo_mani_apley_izq_obs'];
        $params[':m_osteo_palpa_epi_lat_der'] = $_POST['m_osteo_palpa_epi_lat_der'];
        $params[':m_osteo_palpa_epi_lat_der_obs'] = $_POST['m_osteo_palpa_epi_lat_der_obs'];
        $params[':m_osteo_palpa_epi_lat_izq'] = $_POST['m_osteo_palpa_epi_lat_izq'];
        $params[':m_osteo_palpa_epi_lat_izq_obs'] = $_POST['m_osteo_palpa_epi_lat_izq_obs'];
        $params[':m_osteo_palpa_epi_med_der'] = $_POST['m_osteo_palpa_epi_med_der'];
        $params[':m_osteo_palpa_epi_med_der_obs'] = $_POST['m_osteo_palpa_epi_med_der_obs'];
        $params[':m_osteo_palpa_epi_med_izq'] = $_POST['m_osteo_palpa_epi_med_izq'];
        $params[':m_osteo_palpa_epi_med_izq_obs'] = $_POST['m_osteo_palpa_epi_med_izq_obs'];
        $params[':m_osteo_test_phalen_der'] = $_POST['m_osteo_test_phalen_der'];
        $params[':m_osteo_test_phalen_der_obs'] = $_POST['m_osteo_test_phalen_der_obs'];
        $params[':m_osteo_test_phalen_izq'] = $_POST['m_osteo_test_phalen_izq'];
        $params[':m_osteo_test_phalen_izq_obs'] = $_POST['m_osteo_test_phalen_izq_obs'];
        $params[':m_osteo_test_tinel_der'] = $_POST['m_osteo_test_tinel_der'];
        $params[':m_osteo_test_tinel_der_obs'] = $_POST['m_osteo_test_tinel_der_obs'];
        $params[':m_osteo_test_tinel_izq'] = $_POST['m_osteo_test_tinel_izq'];
        $params[':m_osteo_test_tinel_izq_obs'] = $_POST['m_osteo_test_tinel_izq_obs'];
        $params[':m_osteo_test_finkelstein_der'] = $_POST['m_osteo_test_finkelstein_der'];
        $params[':m_osteo_test_finkelstein_der_obs'] = $_POST['m_osteo_test_finkelstein_der_obs'];
        $params[':m_osteo_test_finkelstein_izq'] = $_POST['m_osteo_test_finkelstein_izq'];
        $params[':m_osteo_test_finkelstein_izq_obs'] = $_POST['m_osteo_test_finkelstein_izq_obs'];
        $params[':m_osteo_mani_lasegue_der'] = $_POST['m_osteo_mani_lasegue_der'];
        $params[':m_osteo_mani_lasegue_der_obs'] = $_POST['m_osteo_mani_lasegue_der_obs'];
        $params[':m_osteo_mani_lasegue_izq'] = $_POST['m_osteo_mani_lasegue_izq'];
        $params[':m_osteo_mani_lasegue_izq_obs'] = $_POST['m_osteo_mani_lasegue_izq_obs'];
        $params[':m_osteo_mani_bradga_der'] = $_POST['m_osteo_mani_bradga_der'];
        $params[':m_osteo_mani_bradga_der_obs'] = $_POST['m_osteo_mani_bradga_der_obs'];
        $params[':m_osteo_mani_bradga_izq'] = $_POST['m_osteo_mani_bradga_izq'];
        $params[':m_osteo_mani_bradga_izq_obs'] = $_POST['m_osteo_mani_bradga_izq_obs'];
        $params[':m_osteo_mani_thomas_der'] = $_POST['m_osteo_mani_thomas_der'];
        $params[':m_osteo_mani_thomas_der_obs'] = $_POST['m_osteo_mani_thomas_der_obs'];
        $params[':m_osteo_mani_thomas_izq'] = $_POST['m_osteo_mani_thomas_izq'];
        $params[':m_osteo_mani_thomas_izq_obs'] = $_POST['m_osteo_mani_thomas_izq_obs'];
        $params[':m_osteo_mani_fabere_der'] = $_POST['m_osteo_mani_fabere_der'];
        $params[':m_osteo_mani_fabere_der_obs'] = $_POST['m_osteo_mani_fabere_der_obs'];
        $params[':m_osteo_mani_fabere_izq'] = $_POST['m_osteo_mani_fabere_izq'];
        $params[':m_osteo_mani_fabere_izq_obs'] = $_POST['m_osteo_mani_fabere_izq_obs'];
        $params[':m_osteo_mani_varo_der'] = $_POST['m_osteo_mani_varo_der'];
        $params[':m_osteo_mani_varo_der_obs'] = $_POST['m_osteo_mani_varo_der_obs'];
        $params[':m_osteo_mani_varo_izq'] = $_POST['m_osteo_mani_varo_izq'];
        $params[':m_osteo_mani_varo_izq_obs'] = $_POST['m_osteo_mani_varo_izq_obs'];
        $params[':m_osteo_mani_cajon_der'] = $_POST['m_osteo_mani_cajon_der'];
        $params[':m_osteo_mani_cajon_der_obs'] = $_POST['m_osteo_mani_cajon_der_obs'];
        $params[':m_osteo_mani_cajon_izq'] = $_POST['m_osteo_mani_cajon_izq'];
        $params[':m_osteo_mani_cajon_izq_obs'] = $_POST['m_osteo_mani_cajon_izq_obs'];
        $params[':m_osteo_refle_bicipi_der'] = $_POST['m_osteo_refle_bicipi_der'];
        $params[':m_osteo_refle_bicipi_der_obs'] = $_POST['m_osteo_refle_bicipi_der_obs'];
        $params[':m_osteo_refle_bicipi_izq'] = $_POST['m_osteo_refle_bicipi_izq'];
        $params[':m_osteo_refle_bicipi_izq_obs'] = $_POST['m_osteo_refle_bicipi_izq_obs'];
        $params[':m_osteo_refle_trici_der'] = $_POST['m_osteo_refle_trici_der'];
        $params[':m_osteo_refle_trici_der_obs'] = $_POST['m_osteo_refle_trici_der_obs'];
        $params[':m_osteo_refle_trici_izq'] = $_POST['m_osteo_refle_trici_izq'];
        $params[':m_osteo_refle_trici_izq_obs'] = $_POST['m_osteo_refle_trici_izq_obs'];
        $params[':m_osteo_refle_patelar_der'] = $_POST['m_osteo_refle_patelar_der'];
        $params[':m_osteo_refle_patelar_der_obs'] = $_POST['m_osteo_refle_patelar_der_obs'];
        $params[':m_osteo_refle_patelar_izq'] = $_POST['m_osteo_refle_patelar_izq'];
        $params[':m_osteo_refle_patelar_izq_obs'] = $_POST['m_osteo_refle_patelar_izq_obs'];
        $params[':m_osteo_refle_aquilia_der'] = $_POST['m_osteo_refle_aquilia_der'];
        $params[':m_osteo_refle_aquilia_der_obs'] = $_POST['m_osteo_refle_aquilia_der_obs'];
        $params[':m_osteo_refle_aquilia_izq'] = $_POST['m_osteo_refle_aquilia_izq'];
        $params[':m_osteo_refle_aquilia_izq_obs'] = $_POST['m_osteo_refle_aquilia_izq_obs'];
        $params[':m_osteo_aptitud'] = $_POST['m_osteo_aptitud'];

        $q = "INSERT INTO mod_medicina_osteo VALUES 
                (null,
                :adm,
                :ex_id,
                :m_osteo_trauma,
                :m_osteo_degenera,
                :m_osteo_congeni,
                :m_osteo_quirur,
                :m_osteo_trata,
                :m_osteo_cuello_dura_3meses,
                :m_osteo_cuello_time_ini,
                :m_osteo_cuello_dura_dolor,
                :m_osteo_cuello_recib_trata,
                :m_osteo_cuello_dias_trata,
                :m_osteo_espalda_a_dura_3meses,
                :m_osteo_espalda_a_time_ini,
                :m_osteo_espalda_a_dura_dolor,
                :m_osteo_espalda_a_recib_trata,
                :m_osteo_espalda_a_dias_trata,
                :m_osteo_espalda_b_dura_3meses,
                :m_osteo_espalda_b_time_ini,
                :m_osteo_espalda_b_dura_dolor,
                :m_osteo_espalda_b_recib_trata,
                :m_osteo_espalda_b_dias_trata,
                :m_osteo_hombro_d_dura_3meses,
                :m_osteo_hombro_d_time_ini,
                :m_osteo_hombro_d_dura_dolor,
                :m_osteo_hombro_d_recib_trata,
                :m_osteo_hombro_d_dias_trata,
                :m_osteo_hombro_i_dura_3meses,
                :m_osteo_hombro_i_time_ini,
                :m_osteo_hombro_i_dura_dolor,
                :m_osteo_hombro_i_recib_trata,
                :m_osteo_hombro_i_dias_trata,
                :m_osteo_codo_d_dura_3meses,
                :m_osteo_codo_d_time_ini,
                :m_osteo_codo_d_dura_dolor,
                :m_osteo_codo_d_recib_trata,
                :m_osteo_codo_d_dias_trata,
                :m_osteo_codo_i_dura_3meses,
                :m_osteo_codo_i_time_ini,
                :m_osteo_codo_i_dura_dolor,
                :m_osteo_codo_i_recib_trata,
                :m_osteo_codo_i_dias_trata,
                :m_osteo_mano_d_dura_3meses,
                :m_osteo_mano_d_time_ini,
                :m_osteo_mano_d_dura_dolor,
                :m_osteo_mano_d_recib_trata,
                :m_osteo_mano_d_dias_trata,
                :m_osteo_mano_i_dura_3meses,
                :m_osteo_mano_i_time_ini,
                :m_osteo_mano_i_dura_dolor,
                :m_osteo_mano_i_recib_trata,
                :m_osteo_mano_i_dias_trata,
                :m_osteo_muslo_d_dura_3meses,
                :m_osteo_muslo_d_time_ini,
                :m_osteo_muslo_d_dura_dolor,
                :m_osteo_muslo_d_recib_trata,
                :m_osteo_muslo_d_dias_trata,
                :m_osteo_muslo_i_dura_3meses,
                :m_osteo_muslo_i_time_ini,
                :m_osteo_muslo_i_dura_dolor,
                :m_osteo_muslo_i_recib_trata,
                :m_osteo_muslo_i_dias_trata,
                :m_osteo_rodilla_d_dura_3meses,
                :m_osteo_rodilla_d_time_ini,
                :m_osteo_rodilla_d_dura_dolor,
                :m_osteo_rodilla_d_recib_trata,
                :m_osteo_rodilla_d_dias_trata,
                :m_osteo_rodilla_i_dura_3meses,
                :m_osteo_rodilla_i_time_ini,
                :m_osteo_rodilla_i_dura_dolor,
                :m_osteo_rodilla_i_recib_trata,
                :m_osteo_rodilla_i_dias_trata,
                :m_osteo_pies_d_dura_3meses,
                :m_osteo_pies_d_time_ini,
                :m_osteo_pies_d_dura_dolor,
                :m_osteo_pies_d_recib_trata,
                :m_osteo_pies_d_dias_trata,
                :m_osteo_pies_i_dura_3meses,
                :m_osteo_pies_i_time_ini,
                :m_osteo_pies_i_dura_dolor,
                :m_osteo_pies_i_recib_trata,
                :m_osteo_pies_i_dias_trata,
                :m_osteo_anames_obs,
                :m_osteo_lordo_cervic,
                :m_osteo_cifosis,
                :m_osteo_lordo_lumbar,
                :m_osteo_desvia_lat_halla,
                :m_osteo_desvia_lat_escolio,
                :m_osteo_apofisis,
                :m_osteo_apofisis_obs,
                :m_osteo_contra_musc_cervic,
                :m_osteo_contra_musc_cervic_obs,
                :m_osteo_contra_musc_lumbar,
                :m_osteo_contra_musc_lumbar_obs,
                :m_osteo_cuello_flex,
                :m_osteo_cuello_flex_lat_d,
                :m_osteo_cuello_flex_lat_i,
                :m_osteo_cuello_ext,
                :m_osteo_cuello_ext_rot_d,
                :m_osteo_cuello_ext_rot_i,
                :m_osteo_tronco_flex,
                :m_osteo_tronco_flex_lat_d,
                :m_osteo_tronco_flex_lat_i,
                :m_osteo_tronco_ext,
                :m_osteo_tronco_ext_rot_d,
                :m_osteo_tronco_ext_rot_i,
                :m_osteo_hiper_acor_f_coment,
                :m_osteo_hombro_flex_der,
                :m_osteo_hombro_flex_izq,
                :m_osteo_hombro_flex_fuerza,
                :m_osteo_hombro_flex_tono,
                :m_osteo_hombro_flex_color,
                :m_osteo_hombro_adu_h_der,
                :m_osteo_hombro_adu_h_izq,
                :m_osteo_hombro_adu_h_fuerza,
                :m_osteo_hombro_adu_h_tono,
                :m_osteo_hombro_adu_h_color,
                :m_osteo_hombro_ext_der,
                :m_osteo_hombro_ext_izq,
                :m_osteo_hombro_ext_fuerza,
                :m_osteo_hombro_ext_tono,
                :m_osteo_hombro_ext_color,

                :m_osteo_hombro_rot_in_der,
                :m_osteo_hombro_rot_in_izq,
                :m_osteo_hombro_rot_in_fuerza,
                :m_osteo_hombro_rot_in_tono,
                :m_osteo_hombro_rot_in_color,

                :m_osteo_hombro_abduc_der,
                :m_osteo_hombro_abduc_izq,
                :m_osteo_hombro_abduc_fuerza,
                :m_osteo_hombro_abduc_tono,
                :m_osteo_hombro_abduc_color,

                :m_osteo_hombro_rot_ex_der,
                :m_osteo_hombro_rot_ex_izq,
                :m_osteo_hombro_rot_ex_fuerza,
                :m_osteo_hombro_rot_ex_tono,
                :m_osteo_hombro_rot_ex_color,

                :m_osteo_hombro_abd_h_der,
                :m_osteo_hombro_abd_h_izq,
                :m_osteo_hombro_abd_h_fuerza,
                :m_osteo_hombro_abd_h_tono,
                :m_osteo_hombro_abd_h_color,
                :m_osteo_codo_flex_der,
                :m_osteo_codo_flex_izq,
                :m_osteo_codo_flex_fuerza,
                :m_osteo_codo_flex_tono,
                :m_osteo_codo_flex_color,


                :m_osteo_codo_supina_der,
                :m_osteo_codo_supina_izq,
                :m_osteo_codo_supina_fuerza,
                :m_osteo_codo_supina_tono,
                :m_osteo_codo_supina_color,
                :m_osteo_codo_ext_der,
                :m_osteo_codo_ext_izq,
                :m_osteo_codo_ext_fuerza,
                :m_osteo_codo_ext_tono,
                :m_osteo_codo_ext_color,
                :m_osteo_codo_prona_der,
                :m_osteo_codo_prona_izq,
                :m_osteo_codo_prona_fuerza,
                :m_osteo_codo_prona_tono,
                :m_osteo_codo_prona_color,
                :m_osteo_muneca_flex_der,
                :m_osteo_muneca_flex_izq,
                :m_osteo_muneca_flex_fuerza,
                :m_osteo_muneca_flex_tono,
                :m_osteo_muneca_flex_color,
                :m_osteo_muneca_des_cubi_der,
                :m_osteo_muneca_des_cubi_izq,
                :m_osteo_muneca_des_cubi_fuerza,
                :m_osteo_muneca_des_cubi_tono,
                :m_osteo_muneca_des_cubi_color,
                :m_osteo_muneca_ext_der,
                :m_osteo_muneca_ext_izq,
                :m_osteo_muneca_ext_fuerza,
                :m_osteo_muneca_ext_tono,
                :m_osteo_muneca_ext_color,
                :m_osteo_muneca_des_radi_der,
                :m_osteo_muneca_des_radi_izq,
                :m_osteo_muneca_des_radi_fuerza,
                :m_osteo_muneca_des_radi_tono,
                :m_osteo_muneca_des_radi_color,
                :m_osteo_sup_acor_fu_sen_coment,
                :m_osteo_cader_flex_der,
                :m_osteo_cader_flex_izq,
                :m_osteo_cader_flex_fuerza,
                :m_osteo_cader_flex_tono,
                :m_osteo_cader_flex_color,
                :m_osteo_cader_aduc_der,
                :m_osteo_cader_aduc_izq,
                :m_osteo_cader_aduc_fuerza,
                :m_osteo_cader_aduc_tono,
                :m_osteo_cader_aduc_color,
                :m_osteo_cader_ext_der,
                :m_osteo_cader_ext_izq,
                :m_osteo_cader_ext_fuerza,
                :m_osteo_cader_ext_tono,
                :m_osteo_cader_ext_color,
                :m_osteo_cader_rota_int_der,
                :m_osteo_cader_rota_int_izq,
                :m_osteo_cader_rota_int_fuerza,
                :m_osteo_cader_rota_int_tono,
                :m_osteo_cader_rota_int_color,
                :m_osteo_cader_abduc_der,
                :m_osteo_cader_abduc_izq,
                :m_osteo_cader_abduc_fuerza,
                :m_osteo_cader_abduc_tono,
                :m_osteo_cader_abduc_color,
                :m_osteo_cader_rota_ext_der,
                :m_osteo_cader_rota_ext_izq,
                :m_osteo_cader_rota_ext_fuerza,
                :m_osteo_cader_rota_ext_tono,
                :m_osteo_cader_rota_ext_color,
                :m_osteo_rodill_flex_der,
                :m_osteo_rodill_flex_izq,
                :m_osteo_rodill_flex_fuerza,
                :m_osteo_rodill_flex_tono,
                :m_osteo_rodill_flex_color,
                :m_osteo_rodill_rota_tibi_der,
                :m_osteo_rodill_rota_tibi_izq,
                :m_osteo_rodill_rota_tibi_fuerza,
                :m_osteo_rodill_rota_tibi_tono,
                :m_osteo_rodill_rota_tibi_color,
                :m_osteo_rodill_ext_der,
                :m_osteo_rodill_ext_izq,
                :m_osteo_rodill_ext_fuerza,
                :m_osteo_rodill_ext_tono,
                :m_osteo_rodill_ext_color,
                :m_osteo_tobill_dorsi_der,
                :m_osteo_tobill_dorsi_izq,
                :m_osteo_tobill_dorsi_fuerza,
                :m_osteo_tobill_dorsi_tono,
                :m_osteo_tobill_dorsi_color,
                :m_osteo_tobill_inver_der,
                :m_osteo_tobill_inver_izq,
                :m_osteo_tobill_inver_fuerza,
                :m_osteo_tobill_inver_tono,
                :m_osteo_tobill_inver_color,
                :m_osteo_tobill_flex_plan_der,
                :m_osteo_tobill_flex_plan_izq,
                :m_osteo_tobill_flex_plan_fuerza,
                :m_osteo_tobill_flex_plan_tono,
                :m_osteo_tobill_flex_plan_color,
                :m_osteo_tobill_ever_der,
                :m_osteo_tobill_ever_izq,
                :m_osteo_tobill_ever_fuerza,
                :m_osteo_tobill_ever_tono,
                :m_osteo_tobill_ever_color,
                :m_osteo_inf_acor_fu_sen_coment,
                :m_osteo_test_jobe_der,
                :m_osteo_test_jobe_der_obs,
                :m_osteo_test_jobe_izq,
                :m_osteo_test_jobe_izq_obs,
                :m_osteo_mani_apley_der,
                :m_osteo_mani_apley_der_obs,
                :m_osteo_mani_apley_izq,
                :m_osteo_mani_apley_izq_obs,
                :m_osteo_palpa_epi_lat_der,
                :m_osteo_palpa_epi_lat_der_obs,
                :m_osteo_palpa_epi_lat_izq,
                :m_osteo_palpa_epi_lat_izq_obs,
                :m_osteo_palpa_epi_med_der,
                :m_osteo_palpa_epi_med_der_obs,
                :m_osteo_palpa_epi_med_izq,
                :m_osteo_palpa_epi_med_izq_obs,
                :m_osteo_test_phalen_der,
                :m_osteo_test_phalen_der_obs,
                :m_osteo_test_phalen_izq,
                :m_osteo_test_phalen_izq_obs,
                :m_osteo_test_tinel_der,
                :m_osteo_test_tinel_der_obs,
                :m_osteo_test_tinel_izq,
                :m_osteo_test_tinel_izq_obs,
                :m_osteo_test_finkelstein_der,
                :m_osteo_test_finkelstein_der_obs,
                :m_osteo_test_finkelstein_izq,
                :m_osteo_test_finkelstein_izq_obs,
                :m_osteo_mani_lasegue_der,
                :m_osteo_mani_lasegue_der_obs,
                :m_osteo_mani_lasegue_izq,
                :m_osteo_mani_lasegue_izq_obs,
                :m_osteo_mani_bradga_der,
                :m_osteo_mani_bradga_der_obs,
                :m_osteo_mani_bradga_izq,
                :m_osteo_mani_bradga_izq_obs,
                :m_osteo_mani_thomas_der,
                :m_osteo_mani_thomas_der_obs,
                :m_osteo_mani_thomas_izq,
                :m_osteo_mani_thomas_izq_obs,
                :m_osteo_mani_fabere_der,
                :m_osteo_mani_fabere_der_obs,
                :m_osteo_mani_fabere_izq,
                :m_osteo_mani_fabere_izq_obs,
                :m_osteo_mani_varo_der,
                :m_osteo_mani_varo_der_obs,
                :m_osteo_mani_varo_izq,
                :m_osteo_mani_varo_izq_obs,
                :m_osteo_mani_cajon_der,
                :m_osteo_mani_cajon_der_obs,
                :m_osteo_mani_cajon_izq,
                :m_osteo_mani_cajon_izq_obs,
                :m_osteo_refle_bicipi_der,
                :m_osteo_refle_bicipi_der_obs,
                :m_osteo_refle_bicipi_izq,
                :m_osteo_refle_bicipi_izq_obs,
                :m_osteo_refle_trici_der,
                :m_osteo_refle_trici_der_obs,
                :m_osteo_refle_trici_izq,
                :m_osteo_refle_trici_izq_obs,
                :m_osteo_refle_patelar_der,
                :m_osteo_refle_patelar_der_obs,
                :m_osteo_refle_patelar_izq,
                :m_osteo_refle_patelar_izq_obs,
                :m_osteo_refle_aquilia_der,
                :m_osteo_refle_aquilia_der_obs,
                :m_osteo_refle_aquilia_izq,
                :m_osteo_refle_aquilia_izq_obs,
                :m_osteo_aptitud);
                
                INSERT INTO mod_medicina VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_medicina_st,
                :ex_id);";

        $verifica = $this->sql("SELECT m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario FROM mod_medicina inner join sys_usuario on usu_id=m_medicina_usu where m_medicina_adm='$adm' and m_medicina_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false);
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

    public function update_nuevoOsteo()
    {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_osteo_trauma'] = $_POST['m_osteo_trauma'];
        $params[':m_osteo_degenera'] = $_POST['m_osteo_degenera'];
        $params[':m_osteo_congeni'] = $_POST['m_osteo_congeni'];
        $params[':m_osteo_quirur'] = $_POST['m_osteo_quirur'];
        $params[':m_osteo_trata'] = $_POST['m_osteo_trata'];
        $params[':m_osteo_cuello_dura_3meses'] = $_POST['m_osteo_cuello_dura_3meses'];
        $params[':m_osteo_cuello_time_ini'] = $_POST['m_osteo_cuello_time_ini'];
        $params[':m_osteo_cuello_dura_dolor'] = $_POST['m_osteo_cuello_dura_dolor'];
        $params[':m_osteo_cuello_recib_trata'] = $_POST['m_osteo_cuello_recib_trata'];
        $params[':m_osteo_cuello_dias_trata'] = $_POST['m_osteo_cuello_dias_trata'];

        $params[':m_osteo_espalda_a_dura_3meses'] = $_POST['m_osteo_espalda_a_dura_3meses'];
        $params[':m_osteo_espalda_a_time_ini'] = $_POST['m_osteo_espalda_a_time_ini'];
        $params[':m_osteo_espalda_a_dura_dolor'] = $_POST['m_osteo_espalda_a_dura_dolor'];
        $params[':m_osteo_espalda_a_recib_trata'] = $_POST['m_osteo_espalda_a_recib_trata'];
        $params[':m_osteo_espalda_a_dias_trata'] = $_POST['m_osteo_espalda_a_dias_trata'];
        $params[':m_osteo_espalda_b_dura_3meses'] = $_POST['m_osteo_espalda_b_dura_3meses'];
        $params[':m_osteo_espalda_b_time_ini'] = $_POST['m_osteo_espalda_b_time_ini'];
        $params[':m_osteo_espalda_b_dura_dolor'] = $_POST['m_osteo_espalda_b_dura_dolor'];
        $params[':m_osteo_espalda_b_recib_trata'] = $_POST['m_osteo_espalda_b_recib_trata'];
        $params[':m_osteo_espalda_b_dias_trata'] = $_POST['m_osteo_espalda_b_dias_trata'];
        $params[':m_osteo_hombro_d_dura_3meses'] = $_POST['m_osteo_hombro_d_dura_3meses'];
        $params[':m_osteo_hombro_d_time_ini'] = $_POST['m_osteo_hombro_d_time_ini'];
        $params[':m_osteo_hombro_d_dura_dolor'] = $_POST['m_osteo_hombro_d_dura_dolor'];
        $params[':m_osteo_hombro_d_recib_trata'] = $_POST['m_osteo_hombro_d_recib_trata'];
        $params[':m_osteo_hombro_d_dias_trata'] = $_POST['m_osteo_hombro_d_dias_trata'];
        $params[':m_osteo_hombro_i_dura_3meses'] = $_POST['m_osteo_hombro_i_dura_3meses'];
        $params[':m_osteo_hombro_i_time_ini'] = $_POST['m_osteo_hombro_i_time_ini'];
        $params[':m_osteo_hombro_i_dura_dolor'] = $_POST['m_osteo_hombro_i_dura_dolor'];
        $params[':m_osteo_hombro_i_recib_trata'] = $_POST['m_osteo_hombro_i_recib_trata'];
        $params[':m_osteo_hombro_i_dias_trata'] = $_POST['m_osteo_hombro_i_dias_trata'];
        $params[':m_osteo_codo_d_dura_3meses'] = $_POST['m_osteo_codo_d_dura_3meses'];
        $params[':m_osteo_codo_d_time_ini'] = $_POST['m_osteo_codo_d_time_ini'];
        $params[':m_osteo_codo_d_dura_dolor'] = $_POST['m_osteo_codo_d_dura_dolor'];
        $params[':m_osteo_codo_d_recib_trata'] = $_POST['m_osteo_codo_d_recib_trata'];
        $params[':m_osteo_codo_d_dias_trata'] = $_POST['m_osteo_codo_d_dias_trata'];
        $params[':m_osteo_codo_i_dura_3meses'] = $_POST['m_osteo_codo_i_dura_3meses'];
        $params[':m_osteo_codo_i_time_ini'] = $_POST['m_osteo_codo_i_time_ini'];
        $params[':m_osteo_codo_i_dura_dolor'] = $_POST['m_osteo_codo_i_dura_dolor'];
        $params[':m_osteo_codo_i_recib_trata'] = $_POST['m_osteo_codo_i_recib_trata'];
        $params[':m_osteo_codo_i_dias_trata'] = $_POST['m_osteo_codo_i_dias_trata'];

        $params[':m_osteo_mano_d_dura_3meses'] = $_POST['m_osteo_mano_d_dura_3meses'];
        $params[':m_osteo_mano_d_time_ini'] = $_POST['m_osteo_mano_d_time_ini'];
        $params[':m_osteo_mano_d_dura_dolor'] = $_POST['m_osteo_mano_d_dura_dolor'];
        $params[':m_osteo_mano_d_recib_trata'] = $_POST['m_osteo_mano_d_recib_trata'];
        $params[':m_osteo_mano_d_dias_trata'] = $_POST['m_osteo_mano_d_dias_trata'];
        $params[':m_osteo_mano_i_dura_3meses'] = $_POST['m_osteo_mano_i_dura_3meses'];
        $params[':m_osteo_mano_i_time_ini'] = $_POST['m_osteo_mano_i_time_ini'];
        $params[':m_osteo_mano_i_dura_dolor'] = $_POST['m_osteo_mano_i_dura_dolor'];
        $params[':m_osteo_mano_i_recib_trata'] = $_POST['m_osteo_mano_i_recib_trata'];
        $params[':m_osteo_mano_i_dias_trata'] = $_POST['m_osteo_mano_i_dias_trata'];
        $params[':m_osteo_muslo_d_dura_3meses'] = $_POST['m_osteo_muslo_d_dura_3meses'];
        $params[':m_osteo_muslo_d_time_ini'] = $_POST['m_osteo_muslo_d_time_ini'];
        $params[':m_osteo_muslo_d_dura_dolor'] = $_POST['m_osteo_muslo_d_dura_dolor'];
        $params[':m_osteo_muslo_d_recib_trata'] = $_POST['m_osteo_muslo_d_recib_trata'];
        $params[':m_osteo_muslo_d_dias_trata'] = $_POST['m_osteo_muslo_d_dias_trata'];
        $params[':m_osteo_muslo_i_dura_3meses'] = $_POST['m_osteo_muslo_i_dura_3meses'];
        $params[':m_osteo_muslo_i_time_ini'] = $_POST['m_osteo_muslo_i_time_ini'];
        $params[':m_osteo_muslo_i_dura_dolor'] = $_POST['m_osteo_muslo_i_dura_dolor'];
        $params[':m_osteo_muslo_i_recib_trata'] = $_POST['m_osteo_muslo_i_recib_trata'];
        $params[':m_osteo_muslo_i_dias_trata'] = $_POST['m_osteo_muslo_i_dias_trata'];
        $params[':m_osteo_rodilla_d_dura_3meses'] = $_POST['m_osteo_rodilla_d_dura_3meses'];
        $params[':m_osteo_rodilla_d_time_ini'] = $_POST['m_osteo_rodilla_d_time_ini'];
        $params[':m_osteo_rodilla_d_dura_dolor'] = $_POST['m_osteo_rodilla_d_dura_dolor'];
        $params[':m_osteo_rodilla_d_recib_trata'] = $_POST['m_osteo_rodilla_d_recib_trata'];
        $params[':m_osteo_rodilla_d_dias_trata'] = $_POST['m_osteo_rodilla_d_dias_trata'];
        $params[':m_osteo_rodilla_i_dura_3meses'] = $_POST['m_osteo_rodilla_i_dura_3meses'];
        $params[':m_osteo_rodilla_i_time_ini'] = $_POST['m_osteo_rodilla_i_time_ini'];
        $params[':m_osteo_rodilla_i_dura_dolor'] = $_POST['m_osteo_rodilla_i_dura_dolor'];
        $params[':m_osteo_rodilla_i_recib_trata'] = $_POST['m_osteo_rodilla_i_recib_trata'];
        $params[':m_osteo_rodilla_i_dias_trata'] = $_POST['m_osteo_rodilla_i_dias_trata'];
        $params[':m_osteo_pies_d_dura_3meses'] = $_POST['m_osteo_pies_d_dura_3meses'];
        $params[':m_osteo_pies_d_time_ini'] = $_POST['m_osteo_pies_d_time_ini'];
        $params[':m_osteo_pies_d_dura_dolor'] = $_POST['m_osteo_pies_d_dura_dolor'];
        $params[':m_osteo_pies_d_recib_trata'] = $_POST['m_osteo_pies_d_recib_trata'];
        $params[':m_osteo_pies_d_dias_trata'] = $_POST['m_osteo_pies_d_dias_trata'];
        $params[':m_osteo_pies_i_dura_3meses'] = $_POST['m_osteo_pies_i_dura_3meses'];
        $params[':m_osteo_pies_i_time_ini'] = $_POST['m_osteo_pies_i_time_ini'];
        $params[':m_osteo_pies_i_dura_dolor'] = $_POST['m_osteo_pies_i_dura_dolor'];
        $params[':m_osteo_pies_i_recib_trata'] = $_POST['m_osteo_pies_i_recib_trata'];
        $params[':m_osteo_pies_i_dias_trata'] = $_POST['m_osteo_pies_i_dias_trata'];
        //        
        $params[':m_osteo_anames_obs'] = $_POST['m_osteo_anames_obs'];
        $params[':m_osteo_lordo_cervic'] = $_POST['m_osteo_lordo_cervic'];
        $params[':m_osteo_cifosis'] = $_POST['m_osteo_cifosis'];
        $params[':m_osteo_lordo_lumbar'] = $_POST['m_osteo_lordo_lumbar'];
        $params[':m_osteo_desvia_lat_halla'] = $_POST['m_osteo_desvia_lat_halla'];
        $params[':m_osteo_desvia_lat_escolio'] = $_POST['m_osteo_desvia_lat_escolio'];
        $params[':m_osteo_apofisis'] = $_POST['m_osteo_apofisis'];
        $params[':m_osteo_apofisis_obs'] = $_POST['m_osteo_apofisis_obs'];
        $params[':m_osteo_contra_musc_cervic'] = $_POST['m_osteo_contra_musc_cervic'];
        $params[':m_osteo_contra_musc_cervic_obs'] = $_POST['m_osteo_contra_musc_cervic_obs'];
        $params[':m_osteo_contra_musc_lumbar'] = $_POST['m_osteo_contra_musc_lumbar'];
        $params[':m_osteo_contra_musc_lumbar_obs'] = $_POST['m_osteo_contra_musc_lumbar_obs'];


        $params[':m_osteo_cuello_flex'] = $_POST['m_osteo_cuello_flex'];
        $params[':m_osteo_cuello_flex_lat_d'] = $_POST['m_osteo_cuello_flex_lat_d'];
        $params[':m_osteo_cuello_flex_lat_i'] = $_POST['m_osteo_cuello_flex_lat_i'];
        $params[':m_osteo_cuello_ext'] = $_POST['m_osteo_cuello_ext'];
        $params[':m_osteo_cuello_ext_rot_d'] = $_POST['m_osteo_cuello_ext_rot_d'];
        $params[':m_osteo_cuello_ext_rot_i'] = $_POST['m_osteo_cuello_ext_rot_i'];
        $params[':m_osteo_tronco_flex'] = $_POST['m_osteo_tronco_flex'];
        $params[':m_osteo_tronco_flex_lat_d'] = $_POST['m_osteo_tronco_flex_lat_d'];
        $params[':m_osteo_tronco_flex_lat_i'] = $_POST['m_osteo_tronco_flex_lat_i'];
        $params[':m_osteo_tronco_ext'] = $_POST['m_osteo_tronco_ext'];
        $params[':m_osteo_tronco_ext_rot_d'] = $_POST['m_osteo_tronco_ext_rot_d'];
        $params[':m_osteo_tronco_ext_rot_i'] = $_POST['m_osteo_tronco_ext_rot_i'];
        $params[':m_osteo_hiper_acor_f_coment'] = $_POST['m_osteo_hiper_acor_f_coment'];
        $params[':m_osteo_hombro_flex_der'] = $_POST['m_osteo_hombro_flex_der'];
        $params[':m_osteo_hombro_flex_izq'] = $_POST['m_osteo_hombro_flex_izq'];
        $params[':m_osteo_hombro_flex_fuerza'] = $_POST['m_osteo_hombro_flex_fuerza'];
        $params[':m_osteo_hombro_flex_tono'] = $_POST['m_osteo_hombro_flex_tono'];
        $params[':m_osteo_hombro_flex_color'] = $_POST['m_osteo_hombro_flex_color'];
        $params[':m_osteo_hombro_adu_h_der'] = $_POST['m_osteo_hombro_adu_h_der'];
        $params[':m_osteo_hombro_adu_h_izq'] = $_POST['m_osteo_hombro_adu_h_izq'];
        $params[':m_osteo_hombro_adu_h_fuerza'] = $_POST['m_osteo_hombro_adu_h_fuerza'];
        $params[':m_osteo_hombro_adu_h_tono'] = $_POST['m_osteo_hombro_adu_h_tono'];
        $params[':m_osteo_hombro_adu_h_color'] = $_POST['m_osteo_hombro_adu_h_color'];
        $params[':m_osteo_hombro_ext_der'] = $_POST['m_osteo_hombro_ext_der'];
        $params[':m_osteo_hombro_ext_izq'] = $_POST['m_osteo_hombro_ext_izq'];
        $params[':m_osteo_hombro_ext_fuerza'] = $_POST['m_osteo_hombro_ext_fuerza'];
        $params[':m_osteo_hombro_ext_tono'] = $_POST['m_osteo_hombro_ext_tono'];
        $params[':m_osteo_hombro_ext_color'] = $_POST['m_osteo_hombro_ext_color'];
        $params[':m_osteo_hombro_rot_in_der'] = $_POST['m_osteo_hombro_rot_in_der'];
        $params[':m_osteo_hombro_rot_in_izq'] = $_POST['m_osteo_hombro_rot_in_izq'];
        $params[':m_osteo_hombro_rot_in_fuerza'] = $_POST['m_osteo_hombro_rot_in_fuerza'];
        $params[':m_osteo_hombro_rot_in_tono'] = $_POST['m_osteo_hombro_rot_in_tono'];
        $params[':m_osteo_hombro_rot_in_color'] = $_POST['m_osteo_hombro_rot_in_color'];
        $params[':m_osteo_hombro_abduc_der'] = $_POST['m_osteo_hombro_abduc_der'];
        $params[':m_osteo_hombro_abduc_izq'] = $_POST['m_osteo_hombro_abduc_izq'];
        $params[':m_osteo_hombro_abduc_fuerza'] = $_POST['m_osteo_hombro_abduc_fuerza'];
        $params[':m_osteo_hombro_abduc_tono'] = $_POST['m_osteo_hombro_abduc_tono'];
        $params[':m_osteo_hombro_abduc_color'] = $_POST['m_osteo_hombro_abduc_color'];
        $params[':m_osteo_hombro_rot_ex_der'] = $_POST['m_osteo_hombro_rot_ex_der'];
        $params[':m_osteo_hombro_rot_ex_izq'] = $_POST['m_osteo_hombro_rot_ex_izq'];
        $params[':m_osteo_hombro_rot_ex_fuerza'] = $_POST['m_osteo_hombro_rot_ex_fuerza'];
        $params[':m_osteo_hombro_rot_ex_tono'] = $_POST['m_osteo_hombro_rot_ex_tono'];
        $params[':m_osteo_hombro_rot_ex_color'] = $_POST['m_osteo_hombro_rot_ex_color'];
        $params[':m_osteo_hombro_abd_h_der'] = $_POST['m_osteo_hombro_abd_h_der'];
        $params[':m_osteo_hombro_abd_h_izq'] = $_POST['m_osteo_hombro_abd_h_izq'];
        $params[':m_osteo_hombro_abd_h_fuerza'] = $_POST['m_osteo_hombro_abd_h_fuerza'];
        $params[':m_osteo_hombro_abd_h_tono'] = $_POST['m_osteo_hombro_abd_h_tono'];
        $params[':m_osteo_hombro_abd_h_color'] = $_POST['m_osteo_hombro_abd_h_color'];


        $params[':m_osteo_codo_flex_der'] = $_POST['m_osteo_codo_flex_der'];
        $params[':m_osteo_codo_flex_izq'] = $_POST['m_osteo_codo_flex_izq'];
        $params[':m_osteo_codo_flex_fuerza'] = $_POST['m_osteo_codo_flex_fuerza'];
        $params[':m_osteo_codo_flex_tono'] = $_POST['m_osteo_codo_flex_tono'];
        $params[':m_osteo_codo_flex_color'] = $_POST['m_osteo_codo_flex_color'];
        $params[':m_osteo_codo_supina_der'] = $_POST['m_osteo_codo_supina_der'];
        $params[':m_osteo_codo_supina_izq'] = $_POST['m_osteo_codo_supina_izq'];
        $params[':m_osteo_codo_supina_fuerza'] = $_POST['m_osteo_codo_supina_fuerza'];
        $params[':m_osteo_codo_supina_tono'] = $_POST['m_osteo_codo_supina_tono'];
        $params[':m_osteo_codo_supina_color'] = $_POST['m_osteo_codo_supina_color'];
        $params[':m_osteo_codo_ext_der'] = $_POST['m_osteo_codo_ext_der'];
        $params[':m_osteo_codo_ext_izq'] = $_POST['m_osteo_codo_ext_izq'];
        $params[':m_osteo_codo_ext_fuerza'] = $_POST['m_osteo_codo_ext_fuerza'];
        $params[':m_osteo_codo_ext_tono'] = $_POST['m_osteo_codo_ext_tono'];
        $params[':m_osteo_codo_ext_color'] = $_POST['m_osteo_codo_ext_color'];
        $params[':m_osteo_codo_prona_der'] = $_POST['m_osteo_codo_prona_der'];
        $params[':m_osteo_codo_prona_izq'] = $_POST['m_osteo_codo_prona_izq'];
        $params[':m_osteo_codo_prona_fuerza'] = $_POST['m_osteo_codo_prona_fuerza'];
        $params[':m_osteo_codo_prona_tono'] = $_POST['m_osteo_codo_prona_tono'];
        $params[':m_osteo_codo_prona_color'] = $_POST['m_osteo_codo_prona_color'];


        $params[':m_osteo_muneca_flex_der'] = $_POST['m_osteo_muneca_flex_der'];
        $params[':m_osteo_muneca_flex_izq'] = $_POST['m_osteo_muneca_flex_izq'];
        $params[':m_osteo_muneca_flex_fuerza'] = $_POST['m_osteo_muneca_flex_fuerza'];
        $params[':m_osteo_muneca_flex_tono'] = $_POST['m_osteo_muneca_flex_tono'];
        $params[':m_osteo_muneca_flex_color'] = $_POST['m_osteo_muneca_flex_color'];
        $params[':m_osteo_muneca_des_cubi_der'] = $_POST['m_osteo_muneca_des_cubi_der'];
        $params[':m_osteo_muneca_des_cubi_izq'] = $_POST['m_osteo_muneca_des_cubi_izq'];
        $params[':m_osteo_muneca_des_cubi_fuerza'] = $_POST['m_osteo_muneca_des_cubi_fuerza'];
        $params[':m_osteo_muneca_des_cubi_tono'] = $_POST['m_osteo_muneca_des_cubi_tono'];
        $params[':m_osteo_muneca_des_cubi_color'] = $_POST['m_osteo_muneca_des_cubi_color'];
        $params[':m_osteo_muneca_ext_der'] = $_POST['m_osteo_muneca_ext_der'];
        $params[':m_osteo_muneca_ext_izq'] = $_POST['m_osteo_muneca_ext_izq'];
        $params[':m_osteo_muneca_ext_fuerza'] = $_POST['m_osteo_muneca_ext_fuerza'];
        $params[':m_osteo_muneca_ext_tono'] = $_POST['m_osteo_muneca_ext_tono'];
        $params[':m_osteo_muneca_ext_color'] = $_POST['m_osteo_muneca_ext_color'];
        $params[':m_osteo_muneca_des_radi_der'] = $_POST['m_osteo_muneca_des_radi_der'];
        $params[':m_osteo_muneca_des_radi_izq'] = $_POST['m_osteo_muneca_des_radi_izq'];
        $params[':m_osteo_muneca_des_radi_fuerza'] = $_POST['m_osteo_muneca_des_radi_fuerza'];
        $params[':m_osteo_muneca_des_radi_tono'] = $_POST['m_osteo_muneca_des_radi_tono'];
        $params[':m_osteo_muneca_des_radi_color'] = $_POST['m_osteo_muneca_des_radi_color'];

        $params[':m_osteo_sup_acor_fu_sen_coment'] = $_POST['m_osteo_sup_acor_fu_sen_coment'];
        $params[':m_osteo_cader_flex_der'] = $_POST['m_osteo_cader_flex_der'];
        $params[':m_osteo_cader_flex_izq'] = $_POST['m_osteo_cader_flex_izq'];
        $params[':m_osteo_cader_flex_fuerza'] = $_POST['m_osteo_cader_flex_fuerza'];
        $params[':m_osteo_cader_flex_tono'] = $_POST['m_osteo_cader_flex_tono'];
        $params[':m_osteo_cader_flex_color'] = $_POST['m_osteo_cader_flex_color'];
        $params[':m_osteo_cader_aduc_der'] = $_POST['m_osteo_cader_aduc_der'];
        $params[':m_osteo_cader_aduc_izq'] = $_POST['m_osteo_cader_aduc_izq'];
        $params[':m_osteo_cader_aduc_fuerza'] = $_POST['m_osteo_cader_aduc_fuerza'];
        $params[':m_osteo_cader_aduc_tono'] = $_POST['m_osteo_cader_aduc_tono'];
        $params[':m_osteo_cader_aduc_color'] = $_POST['m_osteo_cader_aduc_color'];
        $params[':m_osteo_cader_ext_der'] = $_POST['m_osteo_cader_ext_der'];
        $params[':m_osteo_cader_ext_izq'] = $_POST['m_osteo_cader_ext_izq'];
        $params[':m_osteo_cader_ext_fuerza'] = $_POST['m_osteo_cader_ext_fuerza'];
        $params[':m_osteo_cader_ext_tono'] = $_POST['m_osteo_cader_ext_tono'];
        $params[':m_osteo_cader_ext_color'] = $_POST['m_osteo_cader_ext_color'];
        $params[':m_osteo_cader_rota_int_der'] = $_POST['m_osteo_cader_rota_int_der'];
        $params[':m_osteo_cader_rota_int_izq'] = $_POST['m_osteo_cader_rota_int_izq'];
        $params[':m_osteo_cader_rota_int_fuerza'] = $_POST['m_osteo_cader_rota_int_fuerza'];
        $params[':m_osteo_cader_rota_int_tono'] = $_POST['m_osteo_cader_rota_int_tono'];
        $params[':m_osteo_cader_rota_int_color'] = $_POST['m_osteo_cader_rota_int_color'];
        $params[':m_osteo_cader_abduc_der'] = $_POST['m_osteo_cader_abduc_der'];
        $params[':m_osteo_cader_abduc_izq'] = $_POST['m_osteo_cader_abduc_izq'];
        $params[':m_osteo_cader_abduc_fuerza'] = $_POST['m_osteo_cader_abduc_fuerza'];
        $params[':m_osteo_cader_abduc_tono'] = $_POST['m_osteo_cader_abduc_tono'];
        $params[':m_osteo_cader_abduc_color'] = $_POST['m_osteo_cader_abduc_color'];
        $params[':m_osteo_cader_rota_ext_der'] = $_POST['m_osteo_cader_rota_ext_der'];
        $params[':m_osteo_cader_rota_ext_izq'] = $_POST['m_osteo_cader_rota_ext_izq'];
        $params[':m_osteo_cader_rota_ext_fuerza'] = $_POST['m_osteo_cader_rota_ext_fuerza'];
        $params[':m_osteo_cader_rota_ext_tono'] = $_POST['m_osteo_cader_rota_ext_tono'];
        $params[':m_osteo_cader_rota_ext_color'] = $_POST['m_osteo_cader_rota_ext_color'];

        $params[':m_osteo_rodill_flex_der'] = $_POST['m_osteo_rodill_flex_der'];
        $params[':m_osteo_rodill_flex_izq'] = $_POST['m_osteo_rodill_flex_izq'];
        $params[':m_osteo_rodill_flex_fuerza'] = $_POST['m_osteo_rodill_flex_fuerza'];
        $params[':m_osteo_rodill_flex_tono'] = $_POST['m_osteo_rodill_flex_tono'];
        $params[':m_osteo_rodill_flex_color'] = $_POST['m_osteo_rodill_flex_color'];
        $params[':m_osteo_rodill_rota_tibi_der'] = $_POST['m_osteo_rodill_rota_tibi_der'];
        $params[':m_osteo_rodill_rota_tibi_izq'] = $_POST['m_osteo_rodill_rota_tibi_izq'];
        $params[':m_osteo_rodill_rota_tibi_fuerza'] = $_POST['m_osteo_rodill_rota_tibi_fuerza'];
        $params[':m_osteo_rodill_rota_tibi_tono'] = $_POST['m_osteo_rodill_rota_tibi_tono'];
        $params[':m_osteo_rodill_rota_tibi_color'] = $_POST['m_osteo_rodill_rota_tibi_color'];
        $params[':m_osteo_rodill_ext_der'] = $_POST['m_osteo_rodill_ext_der'];
        $params[':m_osteo_rodill_ext_izq'] = $_POST['m_osteo_rodill_ext_izq'];
        $params[':m_osteo_rodill_ext_fuerza'] = $_POST['m_osteo_rodill_ext_fuerza'];
        $params[':m_osteo_rodill_ext_tono'] = $_POST['m_osteo_rodill_ext_tono'];
        $params[':m_osteo_rodill_ext_color'] = $_POST['m_osteo_rodill_ext_color'];
        $params[':m_osteo_tobill_dorsi_der'] = $_POST['m_osteo_tobill_dorsi_der'];
        $params[':m_osteo_tobill_dorsi_izq'] = $_POST['m_osteo_tobill_dorsi_izq'];
        $params[':m_osteo_tobill_dorsi_fuerza'] = $_POST['m_osteo_tobill_dorsi_fuerza'];
        $params[':m_osteo_tobill_dorsi_tono'] = $_POST['m_osteo_tobill_dorsi_tono'];
        $params[':m_osteo_tobill_dorsi_color'] = $_POST['m_osteo_tobill_dorsi_color'];
        $params[':m_osteo_tobill_inver_der'] = $_POST['m_osteo_tobill_inver_der'];
        $params[':m_osteo_tobill_inver_izq'] = $_POST['m_osteo_tobill_inver_izq'];
        $params[':m_osteo_tobill_inver_fuerza'] = $_POST['m_osteo_tobill_inver_fuerza'];
        $params[':m_osteo_tobill_inver_tono'] = $_POST['m_osteo_tobill_inver_tono'];
        $params[':m_osteo_tobill_inver_color'] = $_POST['m_osteo_tobill_inver_color'];
        $params[':m_osteo_tobill_flex_plan_der'] = $_POST['m_osteo_tobill_flex_plan_der'];
        $params[':m_osteo_tobill_flex_plan_izq'] = $_POST['m_osteo_tobill_flex_plan_izq'];
        $params[':m_osteo_tobill_flex_plan_fuerza'] = $_POST['m_osteo_tobill_flex_plan_fuerza'];
        $params[':m_osteo_tobill_flex_plan_tono'] = $_POST['m_osteo_tobill_flex_plan_tono'];
        $params[':m_osteo_tobill_flex_plan_color'] = $_POST['m_osteo_tobill_flex_plan_color'];
        $params[':m_osteo_tobill_ever_der'] = $_POST['m_osteo_tobill_ever_der'];
        $params[':m_osteo_tobill_ever_izq'] = $_POST['m_osteo_tobill_ever_izq'];
        $params[':m_osteo_tobill_ever_fuerza'] = $_POST['m_osteo_tobill_ever_fuerza'];
        $params[':m_osteo_tobill_ever_tono'] = $_POST['m_osteo_tobill_ever_tono'];
        $params[':m_osteo_tobill_ever_color'] = $_POST['m_osteo_tobill_ever_color'];
        $params[':m_osteo_inf_acor_fu_sen_coment'] = $_POST['m_osteo_inf_acor_fu_sen_coment'];
        $params[':m_osteo_test_jobe_der'] = $_POST['m_osteo_test_jobe_der'];
        $params[':m_osteo_test_jobe_der_obs'] = $_POST['m_osteo_test_jobe_der_obs'];
        $params[':m_osteo_test_jobe_izq'] = $_POST['m_osteo_test_jobe_izq'];
        $params[':m_osteo_test_jobe_izq_obs'] = $_POST['m_osteo_test_jobe_izq_obs'];
        $params[':m_osteo_mani_apley_der'] = $_POST['m_osteo_mani_apley_der'];
        $params[':m_osteo_mani_apley_der_obs'] = $_POST['m_osteo_mani_apley_der_obs'];
        $params[':m_osteo_mani_apley_izq'] = $_POST['m_osteo_mani_apley_izq'];
        $params[':m_osteo_mani_apley_izq_obs'] = $_POST['m_osteo_mani_apley_izq_obs'];
        $params[':m_osteo_palpa_epi_lat_der'] = $_POST['m_osteo_palpa_epi_lat_der'];
        $params[':m_osteo_palpa_epi_lat_der_obs'] = $_POST['m_osteo_palpa_epi_lat_der_obs'];
        $params[':m_osteo_palpa_epi_lat_izq'] = $_POST['m_osteo_palpa_epi_lat_izq'];
        $params[':m_osteo_palpa_epi_lat_izq_obs'] = $_POST['m_osteo_palpa_epi_lat_izq_obs'];
        $params[':m_osteo_palpa_epi_med_der'] = $_POST['m_osteo_palpa_epi_med_der'];
        $params[':m_osteo_palpa_epi_med_der_obs'] = $_POST['m_osteo_palpa_epi_med_der_obs'];
        $params[':m_osteo_palpa_epi_med_izq'] = $_POST['m_osteo_palpa_epi_med_izq'];
        $params[':m_osteo_palpa_epi_med_izq_obs'] = $_POST['m_osteo_palpa_epi_med_izq_obs'];
        $params[':m_osteo_test_phalen_der'] = $_POST['m_osteo_test_phalen_der'];
        $params[':m_osteo_test_phalen_der_obs'] = $_POST['m_osteo_test_phalen_der_obs'];
        $params[':m_osteo_test_phalen_izq'] = $_POST['m_osteo_test_phalen_izq'];
        $params[':m_osteo_test_phalen_izq_obs'] = $_POST['m_osteo_test_phalen_izq_obs'];
        $params[':m_osteo_test_tinel_der'] = $_POST['m_osteo_test_tinel_der'];
        $params[':m_osteo_test_tinel_der_obs'] = $_POST['m_osteo_test_tinel_der_obs'];
        $params[':m_osteo_test_tinel_izq'] = $_POST['m_osteo_test_tinel_izq'];
        $params[':m_osteo_test_tinel_izq_obs'] = $_POST['m_osteo_test_tinel_izq_obs'];
        $params[':m_osteo_test_finkelstein_der'] = $_POST['m_osteo_test_finkelstein_der'];
        $params[':m_osteo_test_finkelstein_der_obs'] = $_POST['m_osteo_test_finkelstein_der_obs'];
        $params[':m_osteo_test_finkelstein_izq'] = $_POST['m_osteo_test_finkelstein_izq'];
        $params[':m_osteo_test_finkelstein_izq_obs'] = $_POST['m_osteo_test_finkelstein_izq_obs'];
        $params[':m_osteo_mani_lasegue_der'] = $_POST['m_osteo_mani_lasegue_der'];
        $params[':m_osteo_mani_lasegue_der_obs'] = $_POST['m_osteo_mani_lasegue_der_obs'];
        $params[':m_osteo_mani_lasegue_izq'] = $_POST['m_osteo_mani_lasegue_izq'];
        $params[':m_osteo_mani_lasegue_izq_obs'] = $_POST['m_osteo_mani_lasegue_izq_obs'];
        $params[':m_osteo_mani_bradga_der'] = $_POST['m_osteo_mani_bradga_der'];
        $params[':m_osteo_mani_bradga_der_obs'] = $_POST['m_osteo_mani_bradga_der_obs'];
        $params[':m_osteo_mani_bradga_izq'] = $_POST['m_osteo_mani_bradga_izq'];
        $params[':m_osteo_mani_bradga_izq_obs'] = $_POST['m_osteo_mani_bradga_izq_obs'];
        $params[':m_osteo_mani_thomas_der'] = $_POST['m_osteo_mani_thomas_der'];
        $params[':m_osteo_mani_thomas_der_obs'] = $_POST['m_osteo_mani_thomas_der_obs'];
        $params[':m_osteo_mani_thomas_izq'] = $_POST['m_osteo_mani_thomas_izq'];
        $params[':m_osteo_mani_thomas_izq_obs'] = $_POST['m_osteo_mani_thomas_izq_obs'];
        $params[':m_osteo_mani_fabere_der'] = $_POST['m_osteo_mani_fabere_der'];
        $params[':m_osteo_mani_fabere_der_obs'] = $_POST['m_osteo_mani_fabere_der_obs'];
        $params[':m_osteo_mani_fabere_izq'] = $_POST['m_osteo_mani_fabere_izq'];
        $params[':m_osteo_mani_fabere_izq_obs'] = $_POST['m_osteo_mani_fabere_izq_obs'];
        $params[':m_osteo_mani_varo_der'] = $_POST['m_osteo_mani_varo_der'];
        $params[':m_osteo_mani_varo_der_obs'] = $_POST['m_osteo_mani_varo_der_obs'];
        $params[':m_osteo_mani_varo_izq'] = $_POST['m_osteo_mani_varo_izq'];
        $params[':m_osteo_mani_varo_izq_obs'] = $_POST['m_osteo_mani_varo_izq_obs'];
        $params[':m_osteo_mani_cajon_der'] = $_POST['m_osteo_mani_cajon_der'];
        $params[':m_osteo_mani_cajon_der_obs'] = $_POST['m_osteo_mani_cajon_der_obs'];
        $params[':m_osteo_mani_cajon_izq'] = $_POST['m_osteo_mani_cajon_izq'];
        $params[':m_osteo_mani_cajon_izq_obs'] = $_POST['m_osteo_mani_cajon_izq_obs'];
        $params[':m_osteo_refle_bicipi_der'] = $_POST['m_osteo_refle_bicipi_der'];
        $params[':m_osteo_refle_bicipi_der_obs'] = $_POST['m_osteo_refle_bicipi_der_obs'];
        $params[':m_osteo_refle_bicipi_izq'] = $_POST['m_osteo_refle_bicipi_izq'];
        $params[':m_osteo_refle_bicipi_izq_obs'] = $_POST['m_osteo_refle_bicipi_izq_obs'];
        $params[':m_osteo_refle_trici_der'] = $_POST['m_osteo_refle_trici_der'];
        $params[':m_osteo_refle_trici_der_obs'] = $_POST['m_osteo_refle_trici_der_obs'];
        $params[':m_osteo_refle_trici_izq'] = $_POST['m_osteo_refle_trici_izq'];
        $params[':m_osteo_refle_trici_izq_obs'] = $_POST['m_osteo_refle_trici_izq_obs'];
        $params[':m_osteo_refle_patelar_der'] = $_POST['m_osteo_refle_patelar_der'];
        $params[':m_osteo_refle_patelar_der_obs'] = $_POST['m_osteo_refle_patelar_der_obs'];
        $params[':m_osteo_refle_patelar_izq'] = $_POST['m_osteo_refle_patelar_izq'];
        $params[':m_osteo_refle_patelar_izq_obs'] = $_POST['m_osteo_refle_patelar_izq_obs'];
        $params[':m_osteo_refle_aquilia_der'] = $_POST['m_osteo_refle_aquilia_der'];
        $params[':m_osteo_refle_aquilia_der_obs'] = $_POST['m_osteo_refle_aquilia_der_obs'];
        $params[':m_osteo_refle_aquilia_izq'] = $_POST['m_osteo_refle_aquilia_izq'];
        $params[':m_osteo_refle_aquilia_izq_obs'] = $_POST['m_osteo_refle_aquilia_izq_obs'];
        $params[':m_osteo_aptitud'] = $_POST['m_osteo_aptitud'];

        $this->begin();
        $q = 'Update mod_medicina_osteo set
                m_osteo_trauma=:m_osteo_trauma,
				m_osteo_degenera=:m_osteo_degenera,
				m_osteo_congeni=:m_osteo_congeni,
				m_osteo_quirur=:m_osteo_quirur,
				m_osteo_trata=:m_osteo_trata,
				m_osteo_cuello_dura_3meses=:m_osteo_cuello_dura_3meses,
				m_osteo_cuello_time_ini=:m_osteo_cuello_time_ini,
				m_osteo_cuello_dura_dolor=:m_osteo_cuello_dura_dolor,
				m_osteo_cuello_recib_trata=:m_osteo_cuello_recib_trata,
				m_osteo_cuello_dias_trata=:m_osteo_cuello_dias_trata,
				m_osteo_espalda_a_dura_3meses=:m_osteo_espalda_a_dura_3meses,
				m_osteo_espalda_a_time_ini=:m_osteo_espalda_a_time_ini,
				m_osteo_espalda_a_dura_dolor=:m_osteo_espalda_a_dura_dolor,
				m_osteo_espalda_a_recib_trata=:m_osteo_espalda_a_recib_trata,
				m_osteo_espalda_a_dias_trata=:m_osteo_espalda_a_dias_trata,
				m_osteo_espalda_b_dura_3meses=:m_osteo_espalda_b_dura_3meses,
				m_osteo_espalda_b_time_ini=:m_osteo_espalda_b_time_ini,
				m_osteo_espalda_b_dura_dolor=:m_osteo_espalda_b_dura_dolor,
				m_osteo_espalda_b_recib_trata=:m_osteo_espalda_b_recib_trata,
				m_osteo_espalda_b_dias_trata=:m_osteo_espalda_b_dias_trata,
				m_osteo_hombro_d_dura_3meses=:m_osteo_hombro_d_dura_3meses,
				m_osteo_hombro_d_time_ini=:m_osteo_hombro_d_time_ini,
				m_osteo_hombro_d_dura_dolor=:m_osteo_hombro_d_dura_dolor,
				m_osteo_hombro_d_recib_trata=:m_osteo_hombro_d_recib_trata,
				m_osteo_hombro_d_dias_trata=:m_osteo_hombro_d_dias_trata,
				m_osteo_hombro_i_dura_3meses=:m_osteo_hombro_i_dura_3meses,
				m_osteo_hombro_i_time_ini=:m_osteo_hombro_i_time_ini,
				m_osteo_hombro_i_dura_dolor=:m_osteo_hombro_i_dura_dolor,
				m_osteo_hombro_i_recib_trata=:m_osteo_hombro_i_recib_trata,
				m_osteo_hombro_i_dias_trata=:m_osteo_hombro_i_dias_trata,
				m_osteo_codo_d_dura_3meses=:m_osteo_codo_d_dura_3meses,
				m_osteo_codo_d_time_ini=:m_osteo_codo_d_time_ini,
				m_osteo_codo_d_dura_dolor=:m_osteo_codo_d_dura_dolor,
				m_osteo_codo_d_recib_trata=:m_osteo_codo_d_recib_trata,
				m_osteo_codo_d_dias_trata=:m_osteo_codo_d_dias_trata,
				m_osteo_codo_i_dura_3meses=:m_osteo_codo_i_dura_3meses,
				m_osteo_codo_i_time_ini=:m_osteo_codo_i_time_ini,
				m_osteo_codo_i_dura_dolor=:m_osteo_codo_i_dura_dolor,
				m_osteo_codo_i_recib_trata=:m_osteo_codo_i_recib_trata,
				m_osteo_codo_i_dias_trata=:m_osteo_codo_i_dias_trata,
				m_osteo_mano_d_dura_3meses=:m_osteo_mano_d_dura_3meses,
				m_osteo_mano_d_time_ini=:m_osteo_mano_d_time_ini,
				m_osteo_mano_d_dura_dolor=:m_osteo_mano_d_dura_dolor,
				m_osteo_mano_d_recib_trata=:m_osteo_mano_d_recib_trata,
				m_osteo_mano_d_dias_trata=:m_osteo_mano_d_dias_trata,
				m_osteo_mano_i_dura_3meses=:m_osteo_mano_i_dura_3meses,
				m_osteo_mano_i_time_ini=:m_osteo_mano_i_time_ini,
				m_osteo_mano_i_dura_dolor=:m_osteo_mano_i_dura_dolor,
				m_osteo_mano_i_recib_trata=:m_osteo_mano_i_recib_trata,
				m_osteo_mano_i_dias_trata=:m_osteo_mano_i_dias_trata,
				m_osteo_muslo_d_dura_3meses=:m_osteo_muslo_d_dura_3meses,
				m_osteo_muslo_d_time_ini=:m_osteo_muslo_d_time_ini,
				m_osteo_muslo_d_dura_dolor=:m_osteo_muslo_d_dura_dolor,
				m_osteo_muslo_d_recib_trata=:m_osteo_muslo_d_recib_trata,
				m_osteo_muslo_d_dias_trata=:m_osteo_muslo_d_dias_trata,
				m_osteo_muslo_i_dura_3meses=:m_osteo_muslo_i_dura_3meses,
				m_osteo_muslo_i_time_ini=:m_osteo_muslo_i_time_ini,
				m_osteo_muslo_i_dura_dolor=:m_osteo_muslo_i_dura_dolor,
				m_osteo_muslo_i_recib_trata=:m_osteo_muslo_i_recib_trata,
				m_osteo_muslo_i_dias_trata=:m_osteo_muslo_i_dias_trata,
				m_osteo_rodilla_d_dura_3meses=:m_osteo_rodilla_d_dura_3meses,
				m_osteo_rodilla_d_time_ini=:m_osteo_rodilla_d_time_ini,
				m_osteo_rodilla_d_dura_dolor=:m_osteo_rodilla_d_dura_dolor,
				m_osteo_rodilla_d_recib_trata=:m_osteo_rodilla_d_recib_trata,
				m_osteo_rodilla_d_dias_trata=:m_osteo_rodilla_d_dias_trata,
				m_osteo_rodilla_i_dura_3meses=:m_osteo_rodilla_i_dura_3meses,
				m_osteo_rodilla_i_time_ini=:m_osteo_rodilla_i_time_ini,
				m_osteo_rodilla_i_dura_dolor=:m_osteo_rodilla_i_dura_dolor,
				m_osteo_rodilla_i_recib_trata=:m_osteo_rodilla_i_recib_trata,
				m_osteo_rodilla_i_dias_trata=:m_osteo_rodilla_i_dias_trata,
				m_osteo_pies_d_dura_3meses=:m_osteo_pies_d_dura_3meses,
				m_osteo_pies_d_time_ini=:m_osteo_pies_d_time_ini,
				m_osteo_pies_d_dura_dolor=:m_osteo_pies_d_dura_dolor,
				m_osteo_pies_d_recib_trata=:m_osteo_pies_d_recib_trata,
				m_osteo_pies_d_dias_trata=:m_osteo_pies_d_dias_trata,
				m_osteo_pies_i_dura_3meses=:m_osteo_pies_i_dura_3meses,
				m_osteo_pies_i_time_ini=:m_osteo_pies_i_time_ini,
				m_osteo_pies_i_dura_dolor=:m_osteo_pies_i_dura_dolor,
				m_osteo_pies_i_recib_trata=:m_osteo_pies_i_recib_trata,
				m_osteo_pies_i_dias_trata=:m_osteo_pies_i_dias_trata,
				m_osteo_anames_obs=:m_osteo_anames_obs,
				m_osteo_lordo_cervic=:m_osteo_lordo_cervic,
				m_osteo_cifosis=:m_osteo_cifosis,
				m_osteo_lordo_lumbar=:m_osteo_lordo_lumbar,
				m_osteo_desvia_lat_halla=:m_osteo_desvia_lat_halla,
				m_osteo_desvia_lat_escolio=:m_osteo_desvia_lat_escolio,
				m_osteo_apofisis=:m_osteo_apofisis,
				m_osteo_apofisis_obs=:m_osteo_apofisis_obs,
				m_osteo_contra_musc_cervic=:m_osteo_contra_musc_cervic,
				m_osteo_contra_musc_cervic_obs=:m_osteo_contra_musc_cervic_obs,
				m_osteo_contra_musc_lumbar=:m_osteo_contra_musc_lumbar,
				m_osteo_contra_musc_lumbar_obs=:m_osteo_contra_musc_lumbar_obs,
				m_osteo_cuello_flex=:m_osteo_cuello_flex,
				m_osteo_cuello_flex_lat_d=:m_osteo_cuello_flex_lat_d,
				m_osteo_cuello_flex_lat_i=:m_osteo_cuello_flex_lat_i,
				m_osteo_cuello_ext=:m_osteo_cuello_ext,
				m_osteo_cuello_ext_rot_d=:m_osteo_cuello_ext_rot_d,
				m_osteo_cuello_ext_rot_i=:m_osteo_cuello_ext_rot_i,
				m_osteo_tronco_flex=:m_osteo_tronco_flex,
				m_osteo_tronco_flex_lat_d=:m_osteo_tronco_flex_lat_d,
				m_osteo_tronco_flex_lat_i=:m_osteo_tronco_flex_lat_i,
				m_osteo_tronco_ext=:m_osteo_tronco_ext,
				m_osteo_tronco_ext_rot_d=:m_osteo_tronco_ext_rot_d,
				m_osteo_tronco_ext_rot_i=:m_osteo_tronco_ext_rot_i,
				m_osteo_hiper_acor_f_coment=:m_osteo_hiper_acor_f_coment,
				m_osteo_hombro_flex_der=:m_osteo_hombro_flex_der,
				m_osteo_hombro_flex_izq=:m_osteo_hombro_flex_izq,
				m_osteo_hombro_flex_fuerza=:m_osteo_hombro_flex_fuerza,
				m_osteo_hombro_flex_tono=:m_osteo_hombro_flex_tono,
				m_osteo_hombro_flex_color=:m_osteo_hombro_flex_color,
				m_osteo_hombro_adu_h_der=:m_osteo_hombro_adu_h_der,
				m_osteo_hombro_adu_h_izq=:m_osteo_hombro_adu_h_izq,
				m_osteo_hombro_adu_h_fuerza=:m_osteo_hombro_adu_h_fuerza,
				m_osteo_hombro_adu_h_tono=:m_osteo_hombro_adu_h_tono,
				m_osteo_hombro_adu_h_color=:m_osteo_hombro_adu_h_color,
				m_osteo_hombro_ext_der=:m_osteo_hombro_ext_der,
				m_osteo_hombro_ext_izq=:m_osteo_hombro_ext_izq,
				m_osteo_hombro_ext_fuerza=:m_osteo_hombro_ext_fuerza,
				m_osteo_hombro_ext_tono=:m_osteo_hombro_ext_tono,
				m_osteo_hombro_ext_color=:m_osteo_hombro_ext_color,
				m_osteo_hombro_rot_in_der=:m_osteo_hombro_rot_in_der,
				m_osteo_hombro_rot_in_izq=:m_osteo_hombro_rot_in_izq,
				m_osteo_hombro_rot_in_fuerza=:m_osteo_hombro_rot_in_fuerza,
				m_osteo_hombro_rot_in_tono=:m_osteo_hombro_rot_in_tono,
				m_osteo_hombro_rot_in_color=:m_osteo_hombro_rot_in_color,
				m_osteo_hombro_abduc_der=:m_osteo_hombro_abduc_der,
				m_osteo_hombro_abduc_izq=:m_osteo_hombro_abduc_izq,
				m_osteo_hombro_abduc_fuerza=:m_osteo_hombro_abduc_fuerza,
				m_osteo_hombro_abduc_tono=:m_osteo_hombro_abduc_tono,
				m_osteo_hombro_abduc_color=:m_osteo_hombro_abduc_color,
				m_osteo_hombro_rot_ex_der=:m_osteo_hombro_rot_ex_der,
				m_osteo_hombro_rot_ex_izq=:m_osteo_hombro_rot_ex_izq,
				m_osteo_hombro_rot_ex_fuerza=:m_osteo_hombro_rot_ex_fuerza,
				m_osteo_hombro_rot_ex_tono=:m_osteo_hombro_rot_ex_tono,
				m_osteo_hombro_rot_ex_color=:m_osteo_hombro_rot_ex_color,
				m_osteo_hombro_abd_h_der=:m_osteo_hombro_abd_h_der,
				m_osteo_hombro_abd_h_izq=:m_osteo_hombro_abd_h_izq,
				m_osteo_hombro_abd_h_fuerza=:m_osteo_hombro_abd_h_fuerza,
				m_osteo_hombro_abd_h_tono=:m_osteo_hombro_abd_h_tono,
				m_osteo_hombro_abd_h_color=:m_osteo_hombro_abd_h_color,
				m_osteo_codo_flex_der=:m_osteo_codo_flex_der,
				m_osteo_codo_flex_izq=:m_osteo_codo_flex_izq,
				m_osteo_codo_flex_fuerza=:m_osteo_codo_flex_fuerza,
				m_osteo_codo_flex_tono=:m_osteo_codo_flex_tono,
				m_osteo_codo_flex_color=:m_osteo_codo_flex_color,
				m_osteo_codo_supina_der=:m_osteo_codo_supina_der,
				m_osteo_codo_supina_izq=:m_osteo_codo_supina_izq,
				m_osteo_codo_supina_fuerza=:m_osteo_codo_supina_fuerza,
				m_osteo_codo_supina_tono=:m_osteo_codo_supina_tono,
				m_osteo_codo_supina_color=:m_osteo_codo_supina_color,
				m_osteo_codo_ext_der=:m_osteo_codo_ext_der,
				m_osteo_codo_ext_izq=:m_osteo_codo_ext_izq,
				m_osteo_codo_ext_fuerza=:m_osteo_codo_ext_fuerza,
				m_osteo_codo_ext_tono=:m_osteo_codo_ext_tono,
				m_osteo_codo_ext_color=:m_osteo_codo_ext_color,
				m_osteo_codo_prona_der=:m_osteo_codo_prona_der,
				m_osteo_codo_prona_izq=:m_osteo_codo_prona_izq,
				m_osteo_codo_prona_fuerza=:m_osteo_codo_prona_fuerza,
				m_osteo_codo_prona_tono=:m_osteo_codo_prona_tono,
				m_osteo_codo_prona_color=:m_osteo_codo_prona_color,
				m_osteo_muneca_flex_der=:m_osteo_muneca_flex_der,
				m_osteo_muneca_flex_izq=:m_osteo_muneca_flex_izq,
				m_osteo_muneca_flex_fuerza=:m_osteo_muneca_flex_fuerza,
				m_osteo_muneca_flex_tono=:m_osteo_muneca_flex_tono,
				m_osteo_muneca_flex_color=:m_osteo_muneca_flex_color,
				m_osteo_muneca_des_cubi_der=:m_osteo_muneca_des_cubi_der,
				m_osteo_muneca_des_cubi_izq=:m_osteo_muneca_des_cubi_izq,
				m_osteo_muneca_des_cubi_fuerza=:m_osteo_muneca_des_cubi_fuerza,
				m_osteo_muneca_des_cubi_tono=:m_osteo_muneca_des_cubi_tono,
				m_osteo_muneca_des_cubi_color=:m_osteo_muneca_des_cubi_color,
				m_osteo_muneca_ext_der=:m_osteo_muneca_ext_der,
				m_osteo_muneca_ext_izq=:m_osteo_muneca_ext_izq,
				m_osteo_muneca_ext_fuerza=:m_osteo_muneca_ext_fuerza,
				m_osteo_muneca_ext_tono=:m_osteo_muneca_ext_tono,
				m_osteo_muneca_ext_color=:m_osteo_muneca_ext_color,
				m_osteo_muneca_des_radi_der=:m_osteo_muneca_des_radi_der,
				m_osteo_muneca_des_radi_izq=:m_osteo_muneca_des_radi_izq,
				m_osteo_muneca_des_radi_fuerza=:m_osteo_muneca_des_radi_fuerza,
				m_osteo_muneca_des_radi_tono=:m_osteo_muneca_des_radi_tono,
				m_osteo_muneca_des_radi_color=:m_osteo_muneca_des_radi_color,
				m_osteo_sup_acor_fu_sen_coment=:m_osteo_sup_acor_fu_sen_coment,
				m_osteo_cader_flex_der=:m_osteo_cader_flex_der,
				m_osteo_cader_flex_izq=:m_osteo_cader_flex_izq,
				m_osteo_cader_flex_fuerza=:m_osteo_cader_flex_fuerza,
				m_osteo_cader_flex_tono=:m_osteo_cader_flex_tono,
				m_osteo_cader_flex_color=:m_osteo_cader_flex_color,
				m_osteo_cader_aduc_der=:m_osteo_cader_aduc_der,
				m_osteo_cader_aduc_izq=:m_osteo_cader_aduc_izq,
				m_osteo_cader_aduc_fuerza=:m_osteo_cader_aduc_fuerza,
				m_osteo_cader_aduc_tono=:m_osteo_cader_aduc_tono,
				m_osteo_cader_aduc_color=:m_osteo_cader_aduc_color,
				m_osteo_cader_ext_der=:m_osteo_cader_ext_der,
				m_osteo_cader_ext_izq=:m_osteo_cader_ext_izq,
				m_osteo_cader_ext_fuerza=:m_osteo_cader_ext_fuerza,
				m_osteo_cader_ext_tono=:m_osteo_cader_ext_tono,
				m_osteo_cader_ext_color=:m_osteo_cader_ext_color,
				m_osteo_cader_rota_int_der=:m_osteo_cader_rota_int_der,
				m_osteo_cader_rota_int_izq=:m_osteo_cader_rota_int_izq,
				m_osteo_cader_rota_int_fuerza=:m_osteo_cader_rota_int_fuerza,
				m_osteo_cader_rota_int_tono=:m_osteo_cader_rota_int_tono,
				m_osteo_cader_rota_int_color=:m_osteo_cader_rota_int_color,
				m_osteo_cader_abduc_der=:m_osteo_cader_abduc_der,
				m_osteo_cader_abduc_izq=:m_osteo_cader_abduc_izq,
				m_osteo_cader_abduc_fuerza=:m_osteo_cader_abduc_fuerza,
				m_osteo_cader_abduc_tono=:m_osteo_cader_abduc_tono,
				m_osteo_cader_abduc_color=:m_osteo_cader_abduc_color,
				m_osteo_cader_rota_ext_der=:m_osteo_cader_rota_ext_der,
				m_osteo_cader_rota_ext_izq=:m_osteo_cader_rota_ext_izq,
				m_osteo_cader_rota_ext_fuerza=:m_osteo_cader_rota_ext_fuerza,
				m_osteo_cader_rota_ext_tono=:m_osteo_cader_rota_ext_tono,
				m_osteo_cader_rota_ext_color=:m_osteo_cader_rota_ext_color,
				m_osteo_rodill_flex_der=:m_osteo_rodill_flex_der,
				m_osteo_rodill_flex_izq=:m_osteo_rodill_flex_izq,
				m_osteo_rodill_flex_fuerza=:m_osteo_rodill_flex_fuerza,
				m_osteo_rodill_flex_tono=:m_osteo_rodill_flex_tono,
				m_osteo_rodill_flex_color=:m_osteo_rodill_flex_color,
				m_osteo_rodill_rota_tibi_der=:m_osteo_rodill_rota_tibi_der,
				m_osteo_rodill_rota_tibi_izq=:m_osteo_rodill_rota_tibi_izq,
				m_osteo_rodill_rota_tibi_fuerza=:m_osteo_rodill_rota_tibi_fuerza,
				m_osteo_rodill_rota_tibi_tono=:m_osteo_rodill_rota_tibi_tono,
				m_osteo_rodill_rota_tibi_color=:m_osteo_rodill_rota_tibi_color,
				m_osteo_rodill_ext_der=:m_osteo_rodill_ext_der,
				m_osteo_rodill_ext_izq=:m_osteo_rodill_ext_izq,
				m_osteo_rodill_ext_fuerza=:m_osteo_rodill_ext_fuerza,
				m_osteo_rodill_ext_tono=:m_osteo_rodill_ext_tono,
				m_osteo_rodill_ext_color=:m_osteo_rodill_ext_color,
				m_osteo_tobill_dorsi_der=:m_osteo_tobill_dorsi_der,
				m_osteo_tobill_dorsi_izq=:m_osteo_tobill_dorsi_izq,
				m_osteo_tobill_dorsi_fuerza=:m_osteo_tobill_dorsi_fuerza,
				m_osteo_tobill_dorsi_tono=:m_osteo_tobill_dorsi_tono,
				m_osteo_tobill_dorsi_color=:m_osteo_tobill_dorsi_color,
				m_osteo_tobill_inver_der=:m_osteo_tobill_inver_der,
				m_osteo_tobill_inver_izq=:m_osteo_tobill_inver_izq,
				m_osteo_tobill_inver_fuerza=:m_osteo_tobill_inver_fuerza,
				m_osteo_tobill_inver_tono=:m_osteo_tobill_inver_tono,
				m_osteo_tobill_inver_color=:m_osteo_tobill_inver_color,
				m_osteo_tobill_flex_plan_der=:m_osteo_tobill_flex_plan_der,
				m_osteo_tobill_flex_plan_izq=:m_osteo_tobill_flex_plan_izq,
				m_osteo_tobill_flex_plan_fuerza=:m_osteo_tobill_flex_plan_fuerza,
				m_osteo_tobill_flex_plan_tono=:m_osteo_tobill_flex_plan_tono,
				m_osteo_tobill_flex_plan_color=:m_osteo_tobill_flex_plan_color,
				m_osteo_tobill_ever_der=:m_osteo_tobill_ever_der,
				m_osteo_tobill_ever_izq=:m_osteo_tobill_ever_izq,
				m_osteo_tobill_ever_fuerza=:m_osteo_tobill_ever_fuerza,
				m_osteo_tobill_ever_tono=:m_osteo_tobill_ever_tono,
				m_osteo_tobill_ever_color=:m_osteo_tobill_ever_color,
				m_osteo_inf_acor_fu_sen_coment=:m_osteo_inf_acor_fu_sen_coment,
				m_osteo_test_jobe_der=:m_osteo_test_jobe_der,
				m_osteo_test_jobe_der_obs=:m_osteo_test_jobe_der_obs,
				m_osteo_test_jobe_izq=:m_osteo_test_jobe_izq,
				m_osteo_test_jobe_izq_obs=:m_osteo_test_jobe_izq_obs,
				m_osteo_mani_apley_der=:m_osteo_mani_apley_der,
				m_osteo_mani_apley_der_obs=:m_osteo_mani_apley_der_obs,
				m_osteo_mani_apley_izq=:m_osteo_mani_apley_izq,
				m_osteo_mani_apley_izq_obs=:m_osteo_mani_apley_izq_obs,
				m_osteo_palpa_epi_lat_der=:m_osteo_palpa_epi_lat_der,
				m_osteo_palpa_epi_lat_der_obs=:m_osteo_palpa_epi_lat_der_obs,
				m_osteo_palpa_epi_lat_izq=:m_osteo_palpa_epi_lat_izq,
				m_osteo_palpa_epi_lat_izq_obs=:m_osteo_palpa_epi_lat_izq_obs,
				m_osteo_palpa_epi_med_der=:m_osteo_palpa_epi_med_der,
				m_osteo_palpa_epi_med_der_obs=:m_osteo_palpa_epi_med_der_obs,
				m_osteo_palpa_epi_med_izq=:m_osteo_palpa_epi_med_izq,
				m_osteo_palpa_epi_med_izq_obs=:m_osteo_palpa_epi_med_izq_obs,
				m_osteo_test_phalen_der=:m_osteo_test_phalen_der,
				m_osteo_test_phalen_der_obs=:m_osteo_test_phalen_der_obs,
				m_osteo_test_phalen_izq=:m_osteo_test_phalen_izq,
				m_osteo_test_phalen_izq_obs=:m_osteo_test_phalen_izq_obs,
				m_osteo_test_tinel_der=:m_osteo_test_tinel_der,
				m_osteo_test_tinel_der_obs=:m_osteo_test_tinel_der_obs,
				m_osteo_test_tinel_izq=:m_osteo_test_tinel_izq,
				m_osteo_test_tinel_izq_obs=:m_osteo_test_tinel_izq_obs,
				m_osteo_test_finkelstein_der=:m_osteo_test_finkelstein_der,
				m_osteo_test_finkelstein_der_obs=:m_osteo_test_finkelstein_der_obs,
				m_osteo_test_finkelstein_izq=:m_osteo_test_finkelstein_izq,
				m_osteo_test_finkelstein_izq_obs=:m_osteo_test_finkelstein_izq_obs,
				m_osteo_mani_lasegue_der=:m_osteo_mani_lasegue_der,
				m_osteo_mani_lasegue_der_obs=:m_osteo_mani_lasegue_der_obs,
				m_osteo_mani_lasegue_izq=:m_osteo_mani_lasegue_izq,
				m_osteo_mani_lasegue_izq_obs=:m_osteo_mani_lasegue_izq_obs,
				m_osteo_mani_bradga_der=:m_osteo_mani_bradga_der,
				m_osteo_mani_bradga_der_obs=:m_osteo_mani_bradga_der_obs,
				m_osteo_mani_bradga_izq=:m_osteo_mani_bradga_izq,
				m_osteo_mani_bradga_izq_obs=:m_osteo_mani_bradga_izq_obs,
				m_osteo_mani_thomas_der=:m_osteo_mani_thomas_der,
				m_osteo_mani_thomas_der_obs=:m_osteo_mani_thomas_der_obs,
				m_osteo_mani_thomas_izq=:m_osteo_mani_thomas_izq,
				m_osteo_mani_thomas_izq_obs=:m_osteo_mani_thomas_izq_obs,
				m_osteo_mani_fabere_der=:m_osteo_mani_fabere_der,
				m_osteo_mani_fabere_der_obs=:m_osteo_mani_fabere_der_obs,
				m_osteo_mani_fabere_izq=:m_osteo_mani_fabere_izq,
				m_osteo_mani_fabere_izq_obs=:m_osteo_mani_fabere_izq_obs,
				m_osteo_mani_varo_der=:m_osteo_mani_varo_der,
				m_osteo_mani_varo_der_obs=:m_osteo_mani_varo_der_obs,
				m_osteo_mani_varo_izq=:m_osteo_mani_varo_izq,
				m_osteo_mani_varo_izq_obs=:m_osteo_mani_varo_izq_obs,
				m_osteo_mani_cajon_der=:m_osteo_mani_cajon_der,
				m_osteo_mani_cajon_der_obs=:m_osteo_mani_cajon_der_obs,
				m_osteo_mani_cajon_izq=:m_osteo_mani_cajon_izq,
				m_osteo_mani_cajon_izq_obs=:m_osteo_mani_cajon_izq_obs,
				m_osteo_refle_bicipi_der=:m_osteo_refle_bicipi_der,
				m_osteo_refle_bicipi_der_obs=:m_osteo_refle_bicipi_der_obs,
				m_osteo_refle_bicipi_izq=:m_osteo_refle_bicipi_izq,
				m_osteo_refle_bicipi_izq_obs=:m_osteo_refle_bicipi_izq_obs,
				m_osteo_refle_trici_der=:m_osteo_refle_trici_der,
				m_osteo_refle_trici_der_obs=:m_osteo_refle_trici_der_obs,
				m_osteo_refle_trici_izq=:m_osteo_refle_trici_izq,
				m_osteo_refle_trici_izq_obs=:m_osteo_refle_trici_izq_obs,
				m_osteo_refle_patelar_der=:m_osteo_refle_patelar_der,
				m_osteo_refle_patelar_der_obs=:m_osteo_refle_patelar_der_obs,
				m_osteo_refle_patelar_izq=:m_osteo_refle_patelar_izq,
				m_osteo_refle_patelar_izq_obs=:m_osteo_refle_patelar_izq_obs,
				m_osteo_refle_aquilia_der=:m_osteo_refle_aquilia_der,
				m_osteo_refle_aquilia_der_obs=:m_osteo_refle_aquilia_der_obs,
				m_osteo_refle_aquilia_izq=:m_osteo_refle_aquilia_izq,
				m_osteo_refle_aquilia_izq_obs=:m_osteo_refle_aquilia_izq_obs,
				m_osteo_aptitud=:m_osteo_aptitud
                where
                m_osteo_adm=:adm and m_osteo_exa=:ex_id;
                
                update mod_medicina set
                m_medicina_usu=:usuario,
                m_medicina_fech_update=now()
                where
                m_medicina_id=:id and m_medicina_adm=:adm and m_medicina_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    //medicina anexo 16a_obs
    public function list_16a_obs()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT obs_16a_id, obs_16a_adm, obs_16a_desc,obs_16a_plazo
                FROM mod_medicina_16a_obs
                where obs_16a_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function st_busca_16a_obs()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT obs_16a_desc 
                            FROM mod_medicina_16a_obs
                            where
                            obs_16a_desc like '%$query%'
                            group by obs_16a_desc");
        return $sql;
    }

    public function save_16a_obs()
    {
        $params = array();
        $params[':obs_16a_adm'] = $_POST['obs_16a_adm'];
        $params[':obs_16a_desc'] = $_POST['obs_16a_desc'];
        $params[':obs_16a_plazo'] = $_POST['obs_16a_plazo'];

        $q = 'INSERT INTO mod_medicina_16a_obs VALUES 
                (NULL,
                :obs_16a_adm,
                UPPER(:obs_16a_desc),
                :obs_16a_plazo)';
        return $this->sql($q, $params);
    }

    public function update_16a_obs()
    {
        $params = array();
        $params[':obs_16a_id'] = $_POST['obs_16a_id'];
        $params[':obs_16a_adm'] = $_POST['obs_16a_adm'];
        $params[':obs_16a_desc'] = $_POST['obs_16a_desc'];
        $params[':obs_16a_plazo'] = $_POST['obs_16a_plazo'];

        $this->begin();
        $q = 'Update mod_medicina_16a_obs set
                obs_16a_plazo=:obs_16a_plazo,
                obs_16a_desc=UPPER(:obs_16a_desc)
                where
                obs_16a_id=:obs_16a_id and obs_16a_adm=:obs_16a_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $obs_16a_id = $_POST['obs_16a_id'];
            $this->commit();
            return array('success' => true, 'data' => $obs_16a_id);
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function load_16a_obs()
    {
        $obs_16a_adm = $_POST['obs_16a_adm'];
        $obs_16a_id = $_POST['obs_16a_id'];
        $query = "SELECT
            obs_16a_id, obs_16a_adm, obs_16a_desc,obs_16a_plazo
            FROM mod_medicina_16a_obs
            where
            obs_16a_id=$obs_16a_id and
            obs_16a_adm=$obs_16a_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //SAVE UPDATE LOAD anexo_16a

    public function load_anexo_16a()
    {
        $anexo_16a_adm = $_POST['anexo_16a_adm'];
        $anexo_16a_exa = $_POST['anexo_16a_exa'];
        $query = "SELECT * FROM mod_medicina_16a where anexo_16a_adm='$anexo_16a_adm' and anexo_16a_exa='$anexo_16a_exa';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_anexo_16a()
    {
        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];
        $this->begin();
        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];
        $usuario = $this->user->us_id;

        $params[':m_medicina_st'] = '1'; //modulo principal


        $params[':anexo_16a_seguros'] = $_POST['anexo_16a_seguros'];
        $params[':anexo_16a_clinica'] = $_POST['anexo_16a_clinica'];
        $params[':anexo_16a_anfitrion'] = $_POST['anexo_16a_anfitrion'];

        $timestamp = strtotime($_POST['anexo_16a_fech_visita']);
        $anexo_16a_fech_visita = ((strlen($_POST['anexo_16a_fech_visita']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params[':anexo_16a_fech_visita'] = $anexo_16a_fech_visita;

        $params[':anexo_16a_aptitud'] = $_POST['anexo_16a_aptitud'];

        $timestamp0 = strtotime($_POST['anexo_16a_fech_evalua']);
        $anexo_16a_fech_evalua = ((strlen($_POST['anexo_16a_fech_evalua']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':anexo_16a_fech_evalua'] = $anexo_16a_fech_evalua;

        $params[':anexo_16a_vacuna'] = $_POST['anexo_16a_vacuna'];

        $q = "INSERT INTO mod_medicina_16a VALUES 
                (NULL,
                :adm,
                :ex_id,
                :anexo_16a_seguros,
                :anexo_16a_clinica,
                :anexo_16a_anfitrion,
                :anexo_16a_fech_visita,
                :anexo_16a_aptitud,
                :anexo_16a_fech_evalua,
                :anexo_16a_vacuna,
                :anexo_16a_medico_evalua);
                
                INSERT INTO mod_medicina VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_medicina_st,
                :ex_id);";

        $verifica = $this->sql("SELECT m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario FROM mod_medicina inner join sys_usuario on usu_id=m_medicina_usu where m_medicina_adm='$adm' and m_medicina_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            //$sql_med_audita = $this->sql("SELECT medico_id FROM medico where medico_auditor='OK' and medico_st=1");
            $sql_med = $this->sql("SELECT medico_id FROM medico where medico_usu='$usuario' and medico_auditor='NO' and medico_st=1;");
            if ($sql_med->success && $sql_med->total > 0) {
                $medico = $sql_med->data[0]->medico_id;
                //$medico_audita = $sql_med_audita->data[0]->medico_id;

                $params[':anexo_16a_medico_evalua'] = $medico;
                //$params[':anexo_16a_medico_auditor'] = $medico_audita;
                $sql = $this->sql($q, $params);
                if ($sql->success) {
                    $this->commit();
                    return $sql;
                } else {
                    $this->rollback();
                    return array('success' => false);
                }
            } else {
                $this->rollback();
                return array('success' => false);
            }
        }
    }

    public function update_anexo_16a()
    {
        $params = array();

        $usuario = $this->user->us_id;

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        $params[':anexo_16a_seguros'] = $_POST['anexo_16a_seguros'];
        $params[':anexo_16a_clinica'] = $_POST['anexo_16a_clinica'];
        $params[':anexo_16a_anfitrion'] = $_POST['anexo_16a_anfitrion'];

        $timestamp = strtotime($_POST['anexo_16a_fech_visita']);
        $anexo_16a_fech_visita = ((strlen($_POST['anexo_16a_fech_visita']) > 0) ? date('Y-m-d', $timestamp) : null);
        $params[':anexo_16a_fech_visita'] = $anexo_16a_fech_visita;

        $params[':anexo_16a_aptitud'] = $_POST['anexo_16a_aptitud'];

        $timestamp0 = strtotime($_POST['anexo_16a_fech_evalua']);
        $anexo_16a_fech_evalua = ((strlen($_POST['anexo_16a_fech_evalua']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params[':anexo_16a_fech_evalua'] = $anexo_16a_fech_evalua;

        $params[':anexo_16a_vacuna'] = $_POST['anexo_16a_vacuna'];

        $this->begin();
        $q = 'Update mod_medicina_16a set
                anexo_16a_seguros=:anexo_16a_seguros,
                anexo_16a_clinica=:anexo_16a_clinica,
                anexo_16a_anfitrion=:anexo_16a_anfitrion,
                anexo_16a_fech_visita=:anexo_16a_fech_visita,
                anexo_16a_aptitud=:anexo_16a_aptitud,
                anexo_16a_fech_evalua=:anexo_16a_fech_evalua,
                anexo_16a_vacuna=:anexo_16a_vacuna,
                anexo_16a_medico_evalua=:anexo_16a_medico_evalua
                where
                anexo_16a_adm=:adm and anexo_16a_exa=:ex_id;
                
                update mod_medicina set
                m_medicina_usu=:usuario,
                m_medicina_fech_update=now()
                where
                m_medicina_id=:id and m_medicina_adm=:adm and m_medicina_examen=:ex_id;';


        $sql_med = $this->sql("SELECT medico_id FROM medico where medico_usu='$usuario' and medico_auditor='NO' and medico_st=1;");
        if ($sql_med->success && $sql_med->total > 0) {
            $medico = $sql_med->data[0]->medico_id;
            $params[':anexo_16a_medico_evalua'] = $medico;

            $sql1 = $this->sql($q, $params);
            if ($sql1->success && $sql1->total > 0) {
                $this->commit();
                return array('success' => true, 'data' => $sql1);
            } else {
                $this->rollback();
                return array('success' => false);
            }
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    public function mod_medicina_16a($adm)
    {
        $sql = $this->sql("SELECT
        anexo_16a_seguros, anexo_16a_clinica
        , anexo_16a_anfitrion, anexo_16a_fech_visita, anexo_16a_aptitud,
        anexo_16a_fech_evalua, anexo_16a_vacuna
        ,concat(medico_apepat ,' ',medico_apemat) med_apellidos
        ,medico_nombre,medico_cmp
        FROM mod_medicina_16a m
        INNER JOIN medico on medico_id =anexo_16a_medico_evalua
        where
        anexo_16a_adm=$adm;");
        return $sql;
    }

    public function observaciones_16a($adm)
    {
        $q = "SELECT upper(obs_16a_desc) obs_desc, obs_16a_plazo obs_plazo FROM mod_medicina_16a_obs where obs_16a_adm=$adm";
        return $this->sql($q);
    }

    public function osteo_conclusion($adm)
    {
        $q = "SELECT upper(osteo_conclu_desc) obs_desc
            FROM mod_medicina_osteo_conclusion where osteo_conclu_adm=$adm";
        return $this->sql($q);
    }

    public function mod_medicina_osteo_musc($adm)
    {
        $sql = $this->sql("SELECT *
        FROM mod_medicina_osteo
        where
        m_osteo_adm=$adm;");
        return $sql;
    }

    //LOAD SAVE UPDATE MEDICINA MANEJO

    public function load_medicina_manejo()
    {
        $adm = $_POST['adm'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_medicina_manejo where m_med_manejo_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_medicina_manejo()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];


        $this->begin();

        $params = array();
        $params[':sede'] = $this->user->con_sedid;
        $params[':usuario'] = $this->user->us_id;
        $params[':m_medicina_st'] = '1'; //ESTADO DEL MODULO PRINCIPAL

        $params[':adm'] = $adm;
        $params[':ex_id'] = $exa;

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_med_manejo_mariposa'] = $_POST['m_med_manejo_mariposa'];
        $params[':m_med_manejo_colores'] = $_POST['m_med_manejo_colores'];
        $params[':m_med_manejo_encandila'] = $_POST['m_med_manejo_encandila'];
        $params[':m_med_manejo_recupera'] = $_POST['m_med_manejo_recupera'];
        $params[':m_med_manejo_phoria'] = $_POST['m_med_manejo_phoria'];
        $params[':m_med_manejo_perimetrica'] = $_POST['m_med_manejo_perimetrica'];
        $params[':m_med_manejo_bender'] = $_POST['m_med_manejo_bender'];
        $params[':m_med_manejo_bc4'] = $_POST['m_med_manejo_bc4'];
        $params[':m_med_manejo_toulouse'] = $_POST['m_med_manejo_toulouse'];
        $params[':m_med_manejo_entrevista'] = $_POST['m_med_manejo_entrevista'];
        $params[':m_med_manejo_sensometrico'] = $_POST['m_med_manejo_sensometrico'];
        $params[':m_med_manejo_weschler'] = $_POST['m_med_manejo_weschler'];
        $params[':m_med_manejo_test_puntea'] = $_POST['m_med_manejo_test_puntea'];
        $params[':m_med_manejo_test_palanca'] = $_POST['m_med_manejo_test_palanca'];
        $params[':m_med_manejo_test_reactimetro'] = $_POST['m_med_manejo_test_reactimetro'];
        $params[':m_med_manejo_test_laberinto'] = $_POST['m_med_manejo_test_laberinto'];
        $params[':m_med_manejo_test_bimanual'] = $_POST['m_med_manejo_test_bimanual'];
        $params[':m_med_manejo_test_anticipa'] = $_POST['m_med_manejo_test_anticipa'];
        $params[':m_med_manejo_test_reac_multi'] = $_POST['m_med_manejo_test_reac_multi'];
        $params[':m_med_manejo_test_monotimia'] = $_POST['m_med_manejo_test_monotimia'];
        $params[':m_med_manejo_tipo_equipo'] = $_POST['m_med_manejo_tipo_equipo'];
        $params[':m_med_manejo_aptitud'] = $_POST['m_med_manejo_aptitud'];




        $q = "INSERT INTO mod_medicina_manejo VALUES 
                (null,
                :adm,
                :m_med_manejo_mariposa,
                :m_med_manejo_colores,
                :m_med_manejo_encandila,
                :m_med_manejo_recupera,
                :m_med_manejo_phoria,
                :m_med_manejo_perimetrica,
                :m_med_manejo_bender,
                :m_med_manejo_bc4,
                :m_med_manejo_toulouse,
                :m_med_manejo_entrevista,
                :m_med_manejo_sensometrico,
                :m_med_manejo_weschler,
                :m_med_manejo_test_puntea,
                :m_med_manejo_test_palanca,
                :m_med_manejo_test_reactimetro,
                :m_med_manejo_test_laberinto,
                :m_med_manejo_test_bimanual,
                :m_med_manejo_test_anticipa,
                :m_med_manejo_test_reac_multi,
                :m_med_manejo_test_monotimia,
                :m_med_manejo_tipo_equipo,
                :m_med_manejo_aptitud);
                
                INSERT INTO mod_medicina VALUES
                (NULL,
                :adm,
                :sede,
                :usuario,
                now(),
                null,
                :m_medicina_st,
                :ex_id);";

        $verifica = $this->sql("SELECT 
		m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
		FROM mod_medicina 
		inner join sys_usuario on usu_id=m_medicina_usu 
		where 
		m_medicina_adm='$adm' and m_medicina_examen='$exa';");
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

    public function update_medicina_manejo()
    {
        $params = array();

        $params[':usuario'] = $this->user->us_id;
        $params[':id'] = $_POST['id'];
        $params[':adm'] = $_POST['adm'];
        $params[':ex_id'] = $_POST['ex_id'];

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params[':m_med_manejo_mariposa'] = $_POST['m_med_manejo_mariposa'];
        $params[':m_med_manejo_colores'] = $_POST['m_med_manejo_colores'];
        $params[':m_med_manejo_encandila'] = $_POST['m_med_manejo_encandila'];
        $params[':m_med_manejo_recupera'] = $_POST['m_med_manejo_recupera'];
        $params[':m_med_manejo_phoria'] = $_POST['m_med_manejo_phoria'];
        $params[':m_med_manejo_perimetrica'] = $_POST['m_med_manejo_perimetrica'];
        $params[':m_med_manejo_bender'] = $_POST['m_med_manejo_bender'];
        $params[':m_med_manejo_bc4'] = $_POST['m_med_manejo_bc4'];
        $params[':m_med_manejo_toulouse'] = $_POST['m_med_manejo_toulouse'];
        $params[':m_med_manejo_entrevista'] = $_POST['m_med_manejo_entrevista'];
        $params[':m_med_manejo_sensometrico'] = $_POST['m_med_manejo_sensometrico'];
        $params[':m_med_manejo_weschler'] = $_POST['m_med_manejo_weschler'];
        $params[':m_med_manejo_test_puntea'] = $_POST['m_med_manejo_test_puntea'];
        $params[':m_med_manejo_test_palanca'] = $_POST['m_med_manejo_test_palanca'];
        $params[':m_med_manejo_test_reactimetro'] = $_POST['m_med_manejo_test_reactimetro'];
        $params[':m_med_manejo_test_laberinto'] = $_POST['m_med_manejo_test_laberinto'];
        $params[':m_med_manejo_test_bimanual'] = $_POST['m_med_manejo_test_bimanual'];
        $params[':m_med_manejo_test_anticipa'] = $_POST['m_med_manejo_test_anticipa'];
        $params[':m_med_manejo_test_reac_multi'] = $_POST['m_med_manejo_test_reac_multi'];
        $params[':m_med_manejo_test_monotimia'] = $_POST['m_med_manejo_test_monotimia'];
        $params[':m_med_manejo_tipo_equipo'] = $_POST['m_med_manejo_tipo_equipo'];
        $params[':m_med_manejo_aptitud'] = $_POST['m_med_manejo_aptitud'];

        $this->begin();
        $q = 'Update mod_medicina_manejo set
                    m_med_manejo_mariposa=:m_med_manejo_mariposa,
                    m_med_manejo_colores=:m_med_manejo_colores,
                    m_med_manejo_encandila=:m_med_manejo_encandila,
                    m_med_manejo_recupera=:m_med_manejo_recupera,
                    m_med_manejo_phoria=:m_med_manejo_phoria,
                    m_med_manejo_perimetrica=:m_med_manejo_perimetrica,
                    m_med_manejo_bender=:m_med_manejo_bender,
                    m_med_manejo_bc4=:m_med_manejo_bc4,
                    m_med_manejo_toulouse=:m_med_manejo_toulouse,
                    m_med_manejo_entrevista=:m_med_manejo_entrevista,
                    m_med_manejo_sensometrico=:m_med_manejo_sensometrico,
                    m_med_manejo_weschler=:m_med_manejo_weschler,
                    m_med_manejo_test_puntea=:m_med_manejo_test_puntea,
                    m_med_manejo_test_palanca=:m_med_manejo_test_palanca,
                    m_med_manejo_test_reactimetro=:m_med_manejo_test_reactimetro,
                    m_med_manejo_test_laberinto=:m_med_manejo_test_laberinto,
                    m_med_manejo_test_bimanual=:m_med_manejo_test_bimanual,
                    m_med_manejo_test_anticipa=:m_med_manejo_test_anticipa,
                    m_med_manejo_test_reac_multi=:m_med_manejo_test_reac_multi,
                    m_med_manejo_test_monotimia=:m_med_manejo_test_monotimia,
                    m_med_manejo_tipo_equipo=:m_med_manejo_tipo_equipo,
                    m_med_manejo_aptitud=:m_med_manejo_aptitud
                where
                m_med_manejo_adm=:adm;
                
                Update mod_medicina set
                    m_medicina_usu=:usuario,
                    m_medicina_fech_update=now()
                where
                m_medicina_id=:id and m_medicina_adm=:adm and m_medicina_examen=:ex_id;';


        $sql1 = $this->sql($q, $params);
        if ($sql1->success) {
            $this->commit();
            return $sql1;
        } else {
            $this->rollback();
            return array('success' => false);
        }
    }

    //LOAD SAVE UPDATE MEDICINA MANEJO

    public function list_manejo_conclu()
    {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $adm = $_POST['adm'];
        $q = "SELECT conclu_conduc_med_id, conclu_conduc_med_adm, conclu_conduc_med_desc
                FROM mod_medicina_manejo_conclu
                where conclu_conduc_med_adm=$adm;";
        $sql = $this->sql($q);
        $sql->data = array_slice($sql->data, $start, $limit);
        return $sql;
    }

    public function save_manejo_conclu()
    {
        $params = array();
        $params[':conclu_conduc_med_adm'] = $_POST['conclu_conduc_med_adm'];
        $params[':conclu_conduc_med_desc'] = $_POST['conclu_conduc_med_desc'];

        $q = 'INSERT INTO mod_medicina_manejo_conclu VALUES 
                (NULL,
                :conclu_conduc_med_adm,
                UPPER(:conclu_conduc_med_desc))';
        return $this->sql($q, $params);
    }

    public function update_manejo_conclu()
    {
        $params = array();
        $params[':conclu_conduc_med_id'] = $_POST['conclu_conduc_med_id'];
        $params[':conclu_conduc_med_adm'] = $_POST['conclu_conduc_med_adm'];
        $params[':conclu_conduc_med_desc'] = $_POST['conclu_conduc_med_desc'];

        $this->begin();
        $q = 'Update mod_medicina_manejo_conclu set
                conclu_conduc_med_desc=UPPER(:conclu_conduc_med_desc)
                where
                conclu_conduc_med_id=:conclu_conduc_med_id and conclu_conduc_med_adm=:conclu_conduc_med_adm;';
        $sql1 = $this->sql($q, $params);

        if ($sql1->success) {
            $pac_id = $_POST['conclu_conduc_med_adm'];
            $this->commit();
            return array('success' => true, 'data' => $pac_id);
        } else {
            $this->rollback();
        }
    }

    public function st_busca_manejo_conclu()
    {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT conclu_conduc_med_desc FROM mod_medicina_manejo_conclu
                            where
                            conclu_conduc_med_desc like '%$query%'
                            group by conclu_conduc_med_desc");
        return $sql;
    }

    public function load_manejo_conclu()
    {
        $reco_id = $_POST['conclu_conduc_med_id'];
        $reco_adm = $_POST['conclu_conduc_med_adm'];
        $query = "SELECT
            conclu_conduc_med_id, conclu_conduc_med_adm, conclu_conduc_med_desc
            FROM mod_medicina_manejo_conclu
            where
            conclu_conduc_med_id=$reco_id and
            conclu_conduc_med_adm=$reco_adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function carga_medicina_manejo_pdf($adm)
    {
        $query = "SELECT * FROM mod_medicina_manejo
            where m_med_manejo_adm='$adm';";
        return $this->sql($query);
    }

    public function rpt_conclusion($adm)
    {
        $q = "SELECT upper(conclu_conduc_med_desc) obs_desc
                FROM mod_medicina_manejo_conclu where conclu_conduc_med_adm=$adm";
        return $this->sql($q);
    }

    public function rpt_antecedentes_v1($adm)
    {
        $q = "SELECT concat(m_antec_cargo,'/',m_antec_suelo,'/',m_antec_empresa,'/',m_antec_fech_ini,' hasta ', m_antec_fech_fin,'/',m_antec_obser) obs_desc
                FROM mod_medicina_antece_16v 
                where m_antec_adm=$adm";
        return $this->sql($q);
    }

    public function rpt_triaje($adm)
    {
        $q = "SELECT *
                FROM mod_triaje_triaje 
                where m_tri_triaje_adm=$adm";
        return $this->sql($q);
    }

    public function rpt_oftalmo($adm)
    {
        $q = "SELECT *
                FROM mod_oftalmo_oftalmo 
                where m_oft_oftalmo_adm=$adm";
        return $this->sql($q);
    }

    public function rpt_oftalmo_diag($adm)
    {
        $q = "SELECT upper(diag_ofta_desc) diag
                FROM mod_oftalmo_diag 
                where diag_ofta_adm=$adm";
        $verifica = $this->sql($q);

        $diag = '';
        foreach ($verifica->data as $i => $vali) {
            $diag .= $i + 1 . ')' . $vali->diag . '  -  ';
        }
        foreach ($verifica->data as $i => $vali) {
            $vali->diag_concat = $diag;
        }
        return $verifica;
    }

    public function rpt_audiometria($adm)
    {
        $q = "SELECT *
                FROM mod_audio_audio 
                where m_a_audio_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_osteo_conclu($adm)
    {
        $q = "SELECT upper(osteo_conclu_desc) conclu
                FROM mod_medicina_osteo_conclusion 
                where osteo_conclu_adm=$adm";
        $verifica = $this->sql($q);

        $diag = '';
        foreach ($verifica->data as $i => $vali) {
            $diag .= $i + 1 . ')' . $vali->conclu . '  -  ';
        }
        foreach ($verifica->data as $i => $vali) {
            $vali->conclu_concat = $diag;
        }
        return $verifica;
    }

    public function rpt_osteo($adm)
    {
        $q = "SELECT m_osteo_aptitud FROM mod_medicina_osteo 
                where m_osteo_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_rayosx($adm)
    {
        $q = "SELECT
                    m_rx_rayosx_vertice,
                    m_rx_rayosx_mediastinos,
                    m_rx_rayosx_camp_pulmo,
                    m_rx_rayosx_silueta_card,
                    m_rx_rayosx_hilos,
                    m_rx_rayosx_senos,

                    m_rx_rayosx_obs,
                    m_rx_rayosx_concluciones,

                    m_rx_rayosx_n_placa,
                    m_rx_rayosx_fech_lectura,
                    m_rx_rayosx_calidad,
                    m_rx_rayosx_simbolo,

                    m_rx_rayosx_profusion
                FROM mod_rayosx_rayosx 
                where m_rx_rayosx_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_lab_hemograma($adm)
    {
        $q = "SELECT * FROM mod_laboratorio_hemograma 
                where m_lab_hemo_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_lab_lipido($adm)
    {
        $q = "SELECT * FROM mod_laboratorio_p_lipidido 
                where m_lab_p_lipido_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_lab_examen($adm, $examen)
    {
        $q = "SELECT m_lab_exam_resultado FROM mod_laboratorio_exam 
                where m_lab_exam_adm='$adm' and m_lab_exam_examen='$examen';";
        return $this->sql($q);
    }

    public function rpt_lab_drogas($adm)
    {
        $q = "SELECT * FROM mod_laboratorio_drogas_10
            where m_lab_drogas_10_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_ekg_conclu($adm)
    {
        $q = "SELECT upper(conclusion_cardio_desc) conclu
                FROM mod_cardio_conclusion 
                where conclusion_cardio_adm=$adm";
        $verifica = $this->sql($q);

        $diag = '';
        foreach ($verifica->data as $i => $vali) {
            $diag .= $i + 1 . ')' . $vali->conclu . '  -  ';
        }
        foreach ($verifica->data as $i => $vali) {
            $vali->conclu_concat = $diag;
        }
        return $verifica;
    }

    public function rpt_ekg_desc($adm)
    {
        $q = "SELECT m_car_ekg_descripcion FROM mod_cardio_ekg 
                where m_car_ekg_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_psico_informe($adm)
    {
        $q = "SELECT * FROM mod_psicologia_informe 
                where m_psico_inf_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_medicina_manejo($adm)
    {
        $q = "SELECT
            m_med_manejo_tipo_equipo, m_med_manejo_aptitud
            FROM mod_medicina_manejo 
                where m_med_manejo_adm='$adm';";
        return $this->sql($q);
    }

    public function rpt_psicologia_altura($adm)
    {
        $q = "SELECT
            m_psico_altura_aptitud
            FROM mod_psicologia_altura 
                where m_psico_altura_adm='$adm';";
        return $this->sql($q);
    }

    public function load_medico()
    {
        $sede = $this->user->con_sedid;
        return $this->sql("SELECT medico_id, concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombre
        FROM medico
        where medico_sede=$sede and medico_st=1 and medico_auditor='NO' AND medico_tipo = 'MEDICO';");
    }

    public function busca_medico($medico_id)
    {
        $sede = $this->user->con_sedid;
        return $this->sql("SELECT medico_cmp, concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombres
        FROM medico where medico_id=$medico_id;");
    }

    public function load_medico_auditor()
    {
        $sede = $this->user->con_sedid;
        return $this->sql("SELECT medico_id, concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombre
        FROM medico
        where medico_sede=$sede and medico_st=1 and medico_auditor='OK';");
    }

    public function load_conclusiones()
    {
        $adm = $_POST['adm'];
        $st = $_POST['st'];
        $usuario = $this->user->us_id;
        $medico = "";
        if ($st < '1') {
            $medico = ",(SELECT medico_id FROM medico where medico_usu='$usuario') m_312_medico_ocupa
            ,(SELECT medico_id FROM medico where medico_st=1 and medico_auditor='OK') m_312_medico_auditor";
        }
        $query = "SELECT
            adm_id
            $medico
            FROM admision
            where adm_id=$adm
            group by adm_id order by adm_id;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    //////SAVE AND UPDATE MEDICINA ANEXO 312

    public function load_anexo312()
    {
        $adm = $_POST['adm'];
        $exa = $_POST['exa'];
        $query = "SELECT 
            m_312_id, m_312_adm, m_312_exa, m_312_residencia, m_312_tiempo, m_312_seguro, m_312_nhijos, m_312_dependiente, m_312_pato_ima, m_312_pato_hta, m_312_pato_acv, m_312_pato_tbc, m_312_pato_ets, m_312_pato_vih, m_312_pato_tec, m_312_pato_alergias, m_312_pato_asma, m_312_pato_bronquitis, m_312_pato_diabetes, m_312_pato_hepatitis, m_312_pato_hernia, m_312_pato_lumbalgia, m_312_pato_tifoidea, m_312_pato_neoplasias, m_312_pato_quemaduras, m_312_pato_discopatias, m_312_pato_convulciones, m_312_pato_gastritis, m_312_pato_ulceras, m_312_pato_enf_psiquia, m_312_pato_enf_cardio, m_312_pato_enf_ocular, m_312_pato_enf_reuma, m_312_pato_enf_pulmon, m_312_pato_alt_piel, m_312_pato_tendinitis, m_312_pato_fractura, m_312_pato_anemia, m_312_pato_obesidad, m_312_pato_dislipidem, m_312_pato_intoxica, m_312_pato_cirugia, m_312_pato_otros, m_312_pato_cirugia_desc, m_312_pato_tbc_fecha, m_312_pato_tbc_tratamiento, m_312_pato_alergias_desc, m_312_pato_observaciones, m_312_alcohol_tipo, m_312_alcohol_cantidad, m_312_alcohol_fre, m_312_tabaco_tipo, m_312_tabaco_cantidad, m_312_tabaco_fre, m_312_drogas_tipo, m_312_drogas_cantidad, m_312_drogas_fre, m_312_medicamentos, m_312_padre, m_312_madre, m_312_conyuge, m_312_hijo_vivo, m_312_hijo_fallecido, m_312_anamnesis, m_312_ectoscopia, m_312_est_mental, m_312_piel, m_312_cabeza, m_312_oidos, m_312_nariz, m_312_boca, m_312_faringe, m_312_cuello, m_312_respiratorio, m_312_cardiovascular, m_312_digestivo, m_312_genitou, m_312_locomotor, m_312_marcha, m_312_columna, m_312_mi_superi, m_312_mi_inferi, m_312_linfatico, m_312_nervio, m_312_osteomuscular, m_312_ef_observaciones, m_312_conclu_psico, m_312_conclu_rx, m_312_conclu_lab, m_312_conclu_audio, m_312_conclu_espiro, m_312_conclu_otros, m_312_diag_cie1, m_312_diag_st1, m_312_diag_cie2, m_312_diag_st2, m_312_diag_cie3, m_312_diag_st3, m_312_diag_cie4, m_312_diag_st4, m_312_diag_cie5, m_312_diag_st5, m_312_diag_cie6, m_312_diag_st6, m_312_diag_cie7, m_312_diag_st7, m_312_diag_cie8, m_312_diag_st8, m_312_fech_val, m_312_fech_vence, m_312_time_aptitud, m_312_restricciones, m_312_observaciones, m_312_aptitud, m_312_medico_ocupa, m_312_medico_auditor
            ,(SELECT concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombre FROM medico where medico_id=m_312_medico_ocupa) m_312_medico_ocupa_nom
            ,(SELECT concat(medico_apepat,' ',medico_apemat,', ',medico_nombre)as nombre FROM medico where medico_id=m_312_medico_auditor) m_312_medico_auditor_nom
            FROM mod_medicina_312 
        
            where m_312_adm='$adm' and m_312_exa='$exa';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_anexo312()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_medicina VALUES
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

        $params_2[':m_312_residencia'] = $_POST['m_312_residencia'];
        $params_2[':m_312_tiempo'] = $_POST['m_312_tiempo'];
        $params_2[':m_312_seguro'] = $_POST['m_312_seguro'];
        $params_2[':m_312_nhijos'] = $_POST['m_312_nhijos'];
        $params_2[':m_312_dependiente'] = $_POST['m_312_dependiente'];
        $params_2[':m_312_pato_ima'] = $_POST['m_312_pato_ima'];
        $params_2[':m_312_pato_hta'] = $_POST['m_312_pato_hta'];
        $params_2[':m_312_pato_acv'] = $_POST['m_312_pato_acv'];
        $params_2[':m_312_pato_tbc'] = $_POST['m_312_pato_tbc'];
        $params_2[':m_312_pato_ets'] = $_POST['m_312_pato_ets'];
        $params_2[':m_312_pato_vih'] = $_POST['m_312_pato_vih'];
        $params_2[':m_312_pato_tec'] = $_POST['m_312_pato_tec'];
        $params_2[':m_312_pato_alergias'] = $_POST['m_312_pato_alergias'];
        $params_2[':m_312_pato_asma'] = $_POST['m_312_pato_asma'];
        $params_2[':m_312_pato_bronquitis'] = $_POST['m_312_pato_bronquitis'];
        $params_2[':m_312_pato_diabetes'] = $_POST['m_312_pato_diabetes'];
        $params_2[':m_312_pato_hepatitis'] = $_POST['m_312_pato_hepatitis'];
        $params_2[':m_312_pato_hernia'] = $_POST['m_312_pato_hernia'];
        $params_2[':m_312_pato_lumbalgia'] = $_POST['m_312_pato_lumbalgia'];
        $params_2[':m_312_pato_tifoidea'] = $_POST['m_312_pato_tifoidea'];
        $params_2[':m_312_pato_neoplasias'] = $_POST['m_312_pato_neoplasias'];
        $params_2[':m_312_pato_quemaduras'] = $_POST['m_312_pato_quemaduras'];
        $params_2[':m_312_pato_discopatias'] = $_POST['m_312_pato_discopatias'];
        $params_2[':m_312_pato_convulciones'] = $_POST['m_312_pato_convulciones'];
        $params_2[':m_312_pato_gastritis'] = $_POST['m_312_pato_gastritis'];
        $params_2[':m_312_pato_ulceras'] = $_POST['m_312_pato_ulceras'];
        $params_2[':m_312_pato_enf_psiquia'] = $_POST['m_312_pato_enf_psiquia'];
        $params_2[':m_312_pato_enf_cardio'] = $_POST['m_312_pato_enf_cardio'];
        $params_2[':m_312_pato_enf_ocular'] = $_POST['m_312_pato_enf_ocular'];
        $params_2[':m_312_pato_enf_reuma'] = $_POST['m_312_pato_enf_reuma'];
        $params_2[':m_312_pato_enf_pulmon'] = $_POST['m_312_pato_enf_pulmon'];
        $params_2[':m_312_pato_alt_piel'] = $_POST['m_312_pato_alt_piel'];
        $params_2[':m_312_pato_tendinitis'] = $_POST['m_312_pato_tendinitis'];
        $params_2[':m_312_pato_fractura'] = $_POST['m_312_pato_fractura'];
        $params_2[':m_312_pato_anemia'] = $_POST['m_312_pato_anemia'];
        $params_2[':m_312_pato_obesidad'] = $_POST['m_312_pato_obesidad'];
        $params_2[':m_312_pato_dislipidem'] = $_POST['m_312_pato_dislipidem'];
        $params_2[':m_312_pato_intoxica'] = $_POST['m_312_pato_intoxica'];
        $params_2[':m_312_pato_cirugia'] = $_POST['m_312_pato_cirugia'];
        $params_2[':m_312_pato_otros'] = $_POST['m_312_pato_otros'];
        $params_2[':m_312_pato_cirugia_desc'] = $_POST['m_312_pato_cirugia_desc'];
        $params_2[':m_312_pato_tbc_tratamiento'] = $_POST['m_312_pato_tbc_tratamiento'];
        $params_2[':m_312_pato_alergias_desc'] = $_POST['m_312_pato_alergias_desc'];
        $params_2[':m_312_pato_observaciones'] = $_POST['m_312_pato_observaciones'];
        $params_2[':m_312_alcohol_tipo'] = $_POST['m_312_alcohol_tipo'];
        $params_2[':m_312_alcohol_cantidad'] = $_POST['m_312_alcohol_cantidad'];
        $params_2[':m_312_alcohol_fre'] = $_POST['m_312_alcohol_fre'];
        $params_2[':m_312_tabaco_tipo'] = $_POST['m_312_tabaco_tipo'];
        $params_2[':m_312_tabaco_cantidad'] = $_POST['m_312_tabaco_cantidad'];
        $params_2[':m_312_tabaco_fre'] = $_POST['m_312_tabaco_fre'];
        $params_2[':m_312_drogas_tipo'] = $_POST['m_312_drogas_tipo'];
        $params_2[':m_312_drogas_cantidad'] = $_POST['m_312_drogas_cantidad'];
        $params_2[':m_312_drogas_fre'] = $_POST['m_312_drogas_fre'];
        $params_2[':m_312_medicamentos'] = $_POST['m_312_medicamentos'];
        $params_2[':m_312_padre'] = $_POST['m_312_padre'];
        $params_2[':m_312_madre'] = $_POST['m_312_madre'];
        $params_2[':m_312_conyuge'] = $_POST['m_312_conyuge'];
        $params_2[':m_312_hijo_vivo'] = $_POST['m_312_hijo_vivo'];
        $params_2[':m_312_hijo_fallecido'] = $_POST['m_312_hijo_fallecido'];
        $params_2[':m_312_anamnesis'] = $_POST['m_312_anamnesis'];
        $params_2[':m_312_ectoscopia'] = $_POST['m_312_ectoscopia'];
        $params_2[':m_312_est_mental'] = $_POST['m_312_est_mental'];
        $params_2[':m_312_piel'] = $_POST['m_312_piel'];
        $params_2[':m_312_cabeza'] = $_POST['m_312_cabeza'];
        $params_2[':m_312_oidos'] = $_POST['m_312_oidos'];
        $params_2[':m_312_nariz'] = $_POST['m_312_nariz'];
        $params_2[':m_312_boca'] = $_POST['m_312_boca'];
        $params_2[':m_312_faringe'] = $_POST['m_312_faringe'];
        $params_2[':m_312_cuello'] = $_POST['m_312_cuello'];
        $params_2[':m_312_respiratorio'] = $_POST['m_312_respiratorio'];
        $params_2[':m_312_cardiovascular'] = $_POST['m_312_cardiovascular'];
        $params_2[':m_312_digestivo'] = $_POST['m_312_digestivo'];
        $params_2[':m_312_genitou'] = $_POST['m_312_genitou'];
        $params_2[':m_312_locomotor'] = $_POST['m_312_locomotor'];
        $params_2[':m_312_marcha'] = $_POST['m_312_marcha'];
        $params_2[':m_312_columna'] = $_POST['m_312_columna'];
        $params_2[':m_312_mi_superi'] = $_POST['m_312_mi_superi'];
        $params_2[':m_312_mi_inferi'] = $_POST['m_312_mi_inferi'];
        $params_2[':m_312_linfatico'] = $_POST['m_312_linfatico'];
        $params_2[':m_312_nervio'] = $_POST['m_312_nervio'];
        $params_2[':m_312_osteomuscular'] = $_POST['m_312_osteomuscular'];
        $params_2[':m_312_ef_observaciones'] = $_POST['m_312_ef_observaciones'];
        $params_2[':m_312_conclu_psico'] = $_POST['m_312_conclu_psico'];
        $params_2[':m_312_conclu_rx'] = $_POST['m_312_conclu_rx'];
        $params_2[':m_312_conclu_lab'] = $_POST['m_312_conclu_lab'];
        $params_2[':m_312_conclu_audio'] = $_POST['m_312_conclu_audio'];
        $params_2[':m_312_conclu_espiro'] = $_POST['m_312_conclu_espiro'];
        $params_2[':m_312_conclu_otros'] = $_POST['m_312_conclu_otros'];
        $params_2[':m_312_diag_cie1'] = $_POST['m_312_diag_cie1'];
        $params_2[':m_312_diag_st1'] = $_POST['m_312_diag_st1'];
        $params_2[':m_312_diag_cie2'] = $_POST['m_312_diag_cie2'];
        $params_2[':m_312_diag_st2'] = $_POST['m_312_diag_st2'];
        $params_2[':m_312_diag_cie3'] = $_POST['m_312_diag_cie3'];
        $params_2[':m_312_diag_st3'] = $_POST['m_312_diag_st3'];
        $params_2[':m_312_diag_cie4'] = $_POST['m_312_diag_cie4'];
        $params_2[':m_312_diag_st4'] = $_POST['m_312_diag_st4'];
        $params_2[':m_312_diag_cie5'] = $_POST['m_312_diag_cie5'];
        $params_2[':m_312_diag_st5'] = $_POST['m_312_diag_st5'];
        $params_2[':m_312_diag_cie6'] = $_POST['m_312_diag_cie6'];
        $params_2[':m_312_diag_st6'] = $_POST['m_312_diag_st6'];
        $params_2[':m_312_diag_cie7'] = $_POST['m_312_diag_cie7'];
        $params_2[':m_312_diag_st7'] = $_POST['m_312_diag_st7'];
        $params_2[':m_312_diag_cie8'] = $_POST['m_312_diag_cie8'];
        $params_2[':m_312_diag_st8'] = $_POST['m_312_diag_st8'];
        $params_2[':m_312_time_aptitud'] = $_POST['m_312_time_aptitud'];
        $params_2[':m_312_restricciones'] = $_POST['m_312_restricciones'];
        $params_2[':m_312_observaciones'] = $_POST['m_312_observaciones'];
        $params_2[':m_312_aptitud'] = $_POST['m_312_aptitud'];
        $params_2[':m_312_medico_ocupa'] = $_POST['m_312_medico_ocupa'];
        $params_2[':m_312_medico_auditor'] = $_POST['m_312_medico_auditor'];

        // $params_2[':m_312_pato_tbc_fecha'] = $_POST['m_312_pato_tbc_fecha'];
        $timestamp0 = strtotime($_POST['m_312_pato_tbc_fecha']);
        $m_312_pato_tbc_fecha = ((strlen($_POST['m_312_pato_tbc_fecha']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params_2[':m_312_pato_tbc_fecha'] = $m_312_pato_tbc_fecha;

        // $params_2[':m_312_fech_val'] = $_POST['m_312_fech_val'];
        $timestamp2 = strtotime($_POST['m_312_fech_val']);
        $m_312_fech_val = ((strlen($_POST['m_312_fech_val']) > 0) ? date('Y-m-d', $timestamp2) : null);
        $params_2[':m_312_fech_val'] = $m_312_fech_val;


        // $params_2[':m_312_fech_vence'] = $_POST['m_312_fech_vence'];        
        $timestamp3 = strtotime($_POST['m_312_fech_vence']);
        $m_312_fech_vence = ((strlen($_POST['m_312_fech_vence']) > 0) ? date('Y-m-d', $timestamp3) : null);
        $params_2[':m_312_fech_vence'] = $m_312_fech_vence;



        $q_2 = "INSERT INTO mod_medicina_312 VALUES
                (NULL,
                :adm,
                :ex_id,
                :m_312_residencia,
                :m_312_tiempo,
                :m_312_seguro,
                :m_312_nhijos,
                :m_312_dependiente,
                :m_312_pato_ima,
                :m_312_pato_hta,
                :m_312_pato_acv,
                :m_312_pato_tbc,
                :m_312_pato_ets,
                :m_312_pato_vih,
                :m_312_pato_tec,
                :m_312_pato_alergias,
                :m_312_pato_asma,
                :m_312_pato_bronquitis,
                :m_312_pato_diabetes,
                :m_312_pato_hepatitis,
                :m_312_pato_hernia,
                :m_312_pato_lumbalgia,
                :m_312_pato_tifoidea,
                :m_312_pato_neoplasias,
                :m_312_pato_quemaduras,
                :m_312_pato_discopatias,
                :m_312_pato_convulciones,
                :m_312_pato_gastritis,
                :m_312_pato_ulceras,
                :m_312_pato_enf_psiquia,
                :m_312_pato_enf_cardio,
                :m_312_pato_enf_ocular,
                :m_312_pato_enf_reuma,
                :m_312_pato_enf_pulmon,
                :m_312_pato_alt_piel,
                :m_312_pato_tendinitis,
                :m_312_pato_fractura,
                :m_312_pato_anemia,
                :m_312_pato_obesidad,
                :m_312_pato_dislipidem,
                :m_312_pato_intoxica,
                :m_312_pato_cirugia,
                :m_312_pato_otros,
                :m_312_pato_cirugia_desc,
                :m_312_pato_tbc_fecha,
                :m_312_pato_tbc_tratamiento,
                :m_312_pato_alergias_desc,
                :m_312_pato_observaciones,
                :m_312_alcohol_tipo,
                :m_312_alcohol_cantidad,
                :m_312_alcohol_fre,
                :m_312_tabaco_tipo,
                :m_312_tabaco_cantidad,
                :m_312_tabaco_fre,
                :m_312_drogas_tipo,
                :m_312_drogas_cantidad,
                :m_312_drogas_fre,
                :m_312_medicamentos,
                :m_312_padre,
                :m_312_madre,
                :m_312_conyuge,
                :m_312_hijo_vivo,
                :m_312_hijo_fallecido,
                :m_312_anamnesis,
                :m_312_ectoscopia,
                :m_312_est_mental,
                :m_312_piel,
                :m_312_cabeza,
                :m_312_oidos,
                :m_312_nariz,
                :m_312_boca,
                :m_312_faringe,
                :m_312_cuello,
                :m_312_respiratorio,
                :m_312_cardiovascular,
                :m_312_digestivo,
                :m_312_genitou,
                :m_312_locomotor,
                :m_312_marcha,
                :m_312_columna,
                :m_312_mi_superi,
                :m_312_mi_inferi,
                :m_312_linfatico,
                :m_312_nervio,
                :m_312_osteomuscular,
                :m_312_ef_observaciones,
                :m_312_conclu_psico,
                :m_312_conclu_rx,
                :m_312_conclu_lab,
                :m_312_conclu_audio,
                :m_312_conclu_espiro,
                :m_312_conclu_otros,
                :m_312_diag_cie1,
                :m_312_diag_st1,
                :m_312_diag_cie2,
                :m_312_diag_st2,
                :m_312_diag_cie3,
                :m_312_diag_st3,
                :m_312_diag_cie4,
                :m_312_diag_st4,
                :m_312_diag_cie5,
                :m_312_diag_st5,
                :m_312_diag_cie6,
                :m_312_diag_st6,
                :m_312_diag_cie7,
                :m_312_diag_st7,
                :m_312_diag_cie8,
                :m_312_diag_st8,
                :m_312_fech_val,
                :m_312_fech_vence,
                :m_312_time_aptitud,
                :m_312_restricciones,
                :m_312_observaciones,
                :m_312_aptitud,
                :m_312_medico_ocupa,
                :m_312_medico_auditor
                );";




        $verifica = $this->sql("SELECT m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
				FROM mod_medicina inner join sys_usuario on usu_id=m_medicina_usu 
				where m_medicina_adm='$adm' and m_medicina_examen='$exa';");
        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $sql_1 = $this->sql($q_1, $params_1);
            if ($sql_1->success) {
                $sql_2 = $this->sql($q_2, $params_2);
                if ($sql_2->success) {
                    $params_3 = array();
                    $params_3[':adm'] = $_POST['adm'];
                    $params_3[':aptitud'] = $_POST['m_312_aptitud'];

                    $q_3 = "Update admision set adm_aptitud=:aptitud, adm_val=1 where adm_id=:adm;";

                    $sql_3 = $this->sql($q_3, $params_3);
                    if ($sql_3->success) {
                        $this->commit();
                        return $sql_3;
                    } else {
                        $this->rollback();
                        return array('success' => false, 'error' => 'Problemas con el registro.');
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

    public function update_anexo312()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'update mod_medicina set
                    m_medicina_usu=:usuario,
					m_medicina_fech_update=now()
                where
                m_medicina_id=:id and m_medicina_adm=:adm and m_medicina_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////
        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
        $params_2[':ex_id'] = $_POST['ex_id'];

        $params_2[':m_312_residencia'] = $_POST['m_312_residencia'];
        $params_2[':m_312_tiempo'] = $_POST['m_312_tiempo'];
        $params_2[':m_312_seguro'] = $_POST['m_312_seguro'];
        $params_2[':m_312_nhijos'] = $_POST['m_312_nhijos'];
        $params_2[':m_312_dependiente'] = $_POST['m_312_dependiente'];
        $params_2[':m_312_pato_ima'] = $_POST['m_312_pato_ima'];
        $params_2[':m_312_pato_hta'] = $_POST['m_312_pato_hta'];
        $params_2[':m_312_pato_acv'] = $_POST['m_312_pato_acv'];
        $params_2[':m_312_pato_tbc'] = $_POST['m_312_pato_tbc'];
        $params_2[':m_312_pato_ets'] = $_POST['m_312_pato_ets'];
        $params_2[':m_312_pato_vih'] = $_POST['m_312_pato_vih'];
        $params_2[':m_312_pato_tec'] = $_POST['m_312_pato_tec'];
        $params_2[':m_312_pato_alergias'] = $_POST['m_312_pato_alergias'];
        $params_2[':m_312_pato_asma'] = $_POST['m_312_pato_asma'];
        $params_2[':m_312_pato_bronquitis'] = $_POST['m_312_pato_bronquitis'];
        $params_2[':m_312_pato_diabetes'] = $_POST['m_312_pato_diabetes'];
        $params_2[':m_312_pato_hepatitis'] = $_POST['m_312_pato_hepatitis'];
        $params_2[':m_312_pato_hernia'] = $_POST['m_312_pato_hernia'];
        $params_2[':m_312_pato_lumbalgia'] = $_POST['m_312_pato_lumbalgia'];
        $params_2[':m_312_pato_tifoidea'] = $_POST['m_312_pato_tifoidea'];
        $params_2[':m_312_pato_neoplasias'] = $_POST['m_312_pato_neoplasias'];
        $params_2[':m_312_pato_quemaduras'] = $_POST['m_312_pato_quemaduras'];
        $params_2[':m_312_pato_discopatias'] = $_POST['m_312_pato_discopatias'];
        $params_2[':m_312_pato_convulciones'] = $_POST['m_312_pato_convulciones'];
        $params_2[':m_312_pato_gastritis'] = $_POST['m_312_pato_gastritis'];
        $params_2[':m_312_pato_ulceras'] = $_POST['m_312_pato_ulceras'];
        $params_2[':m_312_pato_enf_psiquia'] = $_POST['m_312_pato_enf_psiquia'];
        $params_2[':m_312_pato_enf_cardio'] = $_POST['m_312_pato_enf_cardio'];
        $params_2[':m_312_pato_enf_ocular'] = $_POST['m_312_pato_enf_ocular'];
        $params_2[':m_312_pato_enf_reuma'] = $_POST['m_312_pato_enf_reuma'];
        $params_2[':m_312_pato_enf_pulmon'] = $_POST['m_312_pato_enf_pulmon'];
        $params_2[':m_312_pato_alt_piel'] = $_POST['m_312_pato_alt_piel'];
        $params_2[':m_312_pato_tendinitis'] = $_POST['m_312_pato_tendinitis'];
        $params_2[':m_312_pato_fractura'] = $_POST['m_312_pato_fractura'];
        $params_2[':m_312_pato_anemia'] = $_POST['m_312_pato_anemia'];
        $params_2[':m_312_pato_obesidad'] = $_POST['m_312_pato_obesidad'];
        $params_2[':m_312_pato_dislipidem'] = $_POST['m_312_pato_dislipidem'];
        $params_2[':m_312_pato_intoxica'] = $_POST['m_312_pato_intoxica'];
        $params_2[':m_312_pato_cirugia'] = $_POST['m_312_pato_cirugia'];
        $params_2[':m_312_pato_otros'] = $_POST['m_312_pato_otros'];
        $params_2[':m_312_pato_cirugia_desc'] = $_POST['m_312_pato_cirugia_desc'];
        $params_2[':m_312_pato_tbc_tratamiento'] = $_POST['m_312_pato_tbc_tratamiento'];
        $params_2[':m_312_pato_alergias_desc'] = $_POST['m_312_pato_alergias_desc'];
        $params_2[':m_312_pato_observaciones'] = $_POST['m_312_pato_observaciones'];
        $params_2[':m_312_alcohol_tipo'] = $_POST['m_312_alcohol_tipo'];
        $params_2[':m_312_alcohol_cantidad'] = $_POST['m_312_alcohol_cantidad'];
        $params_2[':m_312_alcohol_fre'] = $_POST['m_312_alcohol_fre'];
        $params_2[':m_312_tabaco_tipo'] = $_POST['m_312_tabaco_tipo'];
        $params_2[':m_312_tabaco_cantidad'] = $_POST['m_312_tabaco_cantidad'];
        $params_2[':m_312_tabaco_fre'] = $_POST['m_312_tabaco_fre'];
        $params_2[':m_312_drogas_tipo'] = $_POST['m_312_drogas_tipo'];
        $params_2[':m_312_drogas_cantidad'] = $_POST['m_312_drogas_cantidad'];
        $params_2[':m_312_drogas_fre'] = $_POST['m_312_drogas_fre'];
        $params_2[':m_312_medicamentos'] = $_POST['m_312_medicamentos'];
        $params_2[':m_312_padre'] = $_POST['m_312_padre'];
        $params_2[':m_312_madre'] = $_POST['m_312_madre'];
        $params_2[':m_312_conyuge'] = $_POST['m_312_conyuge'];
        $params_2[':m_312_hijo_vivo'] = $_POST['m_312_hijo_vivo'];
        $params_2[':m_312_hijo_fallecido'] = $_POST['m_312_hijo_fallecido'];
        $params_2[':m_312_anamnesis'] = $_POST['m_312_anamnesis'];
        $params_2[':m_312_ectoscopia'] = $_POST['m_312_ectoscopia'];
        $params_2[':m_312_est_mental'] = $_POST['m_312_est_mental'];
        $params_2[':m_312_piel'] = $_POST['m_312_piel'];
        $params_2[':m_312_cabeza'] = $_POST['m_312_cabeza'];
        $params_2[':m_312_oidos'] = $_POST['m_312_oidos'];
        $params_2[':m_312_nariz'] = $_POST['m_312_nariz'];
        $params_2[':m_312_boca'] = $_POST['m_312_boca'];
        $params_2[':m_312_faringe'] = $_POST['m_312_faringe'];
        $params_2[':m_312_cuello'] = $_POST['m_312_cuello'];
        $params_2[':m_312_respiratorio'] = $_POST['m_312_respiratorio'];
        $params_2[':m_312_cardiovascular'] = $_POST['m_312_cardiovascular'];
        $params_2[':m_312_digestivo'] = $_POST['m_312_digestivo'];
        $params_2[':m_312_genitou'] = $_POST['m_312_genitou'];
        $params_2[':m_312_locomotor'] = $_POST['m_312_locomotor'];
        $params_2[':m_312_marcha'] = $_POST['m_312_marcha'];
        $params_2[':m_312_columna'] = $_POST['m_312_columna'];
        $params_2[':m_312_mi_superi'] = $_POST['m_312_mi_superi'];
        $params_2[':m_312_mi_inferi'] = $_POST['m_312_mi_inferi'];
        $params_2[':m_312_linfatico'] = $_POST['m_312_linfatico'];
        $params_2[':m_312_nervio'] = $_POST['m_312_nervio'];
        $params_2[':m_312_osteomuscular'] = $_POST['m_312_osteomuscular'];
        $params_2[':m_312_ef_observaciones'] = $_POST['m_312_ef_observaciones'];
        $params_2[':m_312_conclu_psico'] = $_POST['m_312_conclu_psico'];
        $params_2[':m_312_conclu_rx'] = $_POST['m_312_conclu_rx'];
        $params_2[':m_312_conclu_lab'] = $_POST['m_312_conclu_lab'];
        $params_2[':m_312_conclu_audio'] = $_POST['m_312_conclu_audio'];
        $params_2[':m_312_conclu_espiro'] = $_POST['m_312_conclu_espiro'];
        $params_2[':m_312_conclu_otros'] = $_POST['m_312_conclu_otros'];
        $params_2[':m_312_diag_cie1'] = $_POST['m_312_diag_cie1'];
        $params_2[':m_312_diag_st1'] = $_POST['m_312_diag_st1'];
        $params_2[':m_312_diag_cie2'] = $_POST['m_312_diag_cie2'];
        $params_2[':m_312_diag_st2'] = $_POST['m_312_diag_st2'];
        $params_2[':m_312_diag_cie3'] = $_POST['m_312_diag_cie3'];
        $params_2[':m_312_diag_st3'] = $_POST['m_312_diag_st3'];
        $params_2[':m_312_diag_cie4'] = $_POST['m_312_diag_cie4'];
        $params_2[':m_312_diag_st4'] = $_POST['m_312_diag_st4'];
        $params_2[':m_312_diag_cie5'] = $_POST['m_312_diag_cie5'];
        $params_2[':m_312_diag_st5'] = $_POST['m_312_diag_st5'];
        $params_2[':m_312_diag_cie6'] = $_POST['m_312_diag_cie6'];
        $params_2[':m_312_diag_st6'] = $_POST['m_312_diag_st6'];
        $params_2[':m_312_diag_cie7'] = $_POST['m_312_diag_cie7'];
        $params_2[':m_312_diag_st7'] = $_POST['m_312_diag_st7'];
        $params_2[':m_312_diag_cie8'] = $_POST['m_312_diag_cie8'];
        $params_2[':m_312_diag_st8'] = $_POST['m_312_diag_st8'];
        $params_2[':m_312_time_aptitud'] = $_POST['m_312_time_aptitud'];
        $params_2[':m_312_restricciones'] = $_POST['m_312_restricciones'];
        $params_2[':m_312_observaciones'] = $_POST['m_312_observaciones'];
        $params_2[':m_312_aptitud'] = $_POST['m_312_aptitud'];
        $params_2[':m_312_medico_ocupa'] = $_POST['m_312_medico_ocupa'];
        $params_2[':m_312_medico_auditor'] = $_POST['m_312_medico_auditor'];

        $params_2[':m_312_pato_tbc_fecha'] = $_POST['m_312_pato_tbc_fecha'];
        $timestamp0 = strtotime($_POST['m_312_pato_tbc_fecha']);
        $m_312_pato_tbc_fecha = ((strlen($_POST['m_312_pato_tbc_fecha']) > 0) ? date('Y-m-d', $timestamp0) : null);
        $params_2[':m_312_pato_tbc_fecha'] = $m_312_pato_tbc_fecha;

        // $params_2[':m_312_fech_val'] = $_POST['m_312_fech_val'];
        $timestamp2 = strtotime($_POST['m_312_fech_val']);
        $m_312_fech_val = ((strlen($_POST['m_312_fech_val']) > 0) ? date('Y-m-d', $timestamp2) : null);
        $params_2[':m_312_fech_val'] = $m_312_fech_val;


        // $params_2[':m_312_fech_vence'] = $_POST['m_312_fech_vence'];        
        $timestamp3 = strtotime($_POST['m_312_fech_vence']);
        $m_312_fech_vence = ((strlen($_POST['m_312_fech_vence']) > 0) ? date('Y-m-d', $timestamp3) : null);
        $params_2[':m_312_fech_vence'] = $m_312_fech_vence;

        $q_2 = 'Update mod_medicina_312 set
                    m_312_residencia=:m_312_residencia,
                    m_312_tiempo=:m_312_tiempo,
                    m_312_seguro=:m_312_seguro,
                    m_312_nhijos=:m_312_nhijos,
                    m_312_dependiente=:m_312_dependiente,
                    m_312_pato_ima=:m_312_pato_ima,
                    m_312_pato_hta=:m_312_pato_hta,
                    m_312_pato_acv=:m_312_pato_acv,
                    m_312_pato_tbc=:m_312_pato_tbc,
                    m_312_pato_ets=:m_312_pato_ets,
                    m_312_pato_vih=:m_312_pato_vih,
                    m_312_pato_tec=:m_312_pato_tec,
                    m_312_pato_alergias=:m_312_pato_alergias,
                    m_312_pato_asma=:m_312_pato_asma,
                    m_312_pato_bronquitis=:m_312_pato_bronquitis,
                    m_312_pato_diabetes=:m_312_pato_diabetes,
                    m_312_pato_hepatitis=:m_312_pato_hepatitis,
                    m_312_pato_hernia=:m_312_pato_hernia,
                    m_312_pato_lumbalgia=:m_312_pato_lumbalgia,
                    m_312_pato_tifoidea=:m_312_pato_tifoidea,
                    m_312_pato_neoplasias=:m_312_pato_neoplasias,
                    m_312_pato_quemaduras=:m_312_pato_quemaduras,
                    m_312_pato_discopatias=:m_312_pato_discopatias,
                    m_312_pato_convulciones=:m_312_pato_convulciones,
                    m_312_pato_gastritis=:m_312_pato_gastritis,
                    m_312_pato_ulceras=:m_312_pato_ulceras,
                    m_312_pato_enf_psiquia=:m_312_pato_enf_psiquia,
                    m_312_pato_enf_cardio=:m_312_pato_enf_cardio,
                    m_312_pato_enf_ocular=:m_312_pato_enf_ocular,
                    m_312_pato_enf_reuma=:m_312_pato_enf_reuma,
                    m_312_pato_enf_pulmon=:m_312_pato_enf_pulmon,
                    m_312_pato_alt_piel=:m_312_pato_alt_piel,
                    m_312_pato_tendinitis=:m_312_pato_tendinitis,
                    m_312_pato_fractura=:m_312_pato_fractura,
                    m_312_pato_anemia=:m_312_pato_anemia,
                    m_312_pato_obesidad=:m_312_pato_obesidad,
                    m_312_pato_dislipidem=:m_312_pato_dislipidem,
                    m_312_pato_intoxica=:m_312_pato_intoxica,
                    m_312_pato_cirugia=:m_312_pato_cirugia,
                    m_312_pato_otros=:m_312_pato_otros,
                    m_312_pato_cirugia_desc=:m_312_pato_cirugia_desc,
                    m_312_pato_tbc_fecha=:m_312_pato_tbc_fecha,
                    m_312_pato_tbc_tratamiento=:m_312_pato_tbc_tratamiento,
                    m_312_pato_alergias_desc=:m_312_pato_alergias_desc,
                    m_312_pato_observaciones=:m_312_pato_observaciones,
                    m_312_alcohol_tipo=:m_312_alcohol_tipo,
                    m_312_alcohol_cantidad=:m_312_alcohol_cantidad,
                    m_312_alcohol_fre=:m_312_alcohol_fre,
                    m_312_tabaco_tipo=:m_312_tabaco_tipo,
                    m_312_tabaco_cantidad=:m_312_tabaco_cantidad,
                    m_312_tabaco_fre=:m_312_tabaco_fre,
                    m_312_drogas_tipo=:m_312_drogas_tipo,
                    m_312_drogas_cantidad=:m_312_drogas_cantidad,
                    m_312_drogas_fre=:m_312_drogas_fre,
                    m_312_medicamentos=:m_312_medicamentos,
                    m_312_padre=:m_312_padre,
                    m_312_madre=:m_312_madre,
                    m_312_conyuge=:m_312_conyuge,
                    m_312_hijo_vivo=:m_312_hijo_vivo,
                    m_312_hijo_fallecido=:m_312_hijo_fallecido,
                    m_312_anamnesis=:m_312_anamnesis,
                    m_312_ectoscopia=:m_312_ectoscopia,
                    m_312_est_mental=:m_312_est_mental,
                    m_312_piel=:m_312_piel,
                    m_312_cabeza=:m_312_cabeza,
                    m_312_oidos=:m_312_oidos,
                    m_312_nariz=:m_312_nariz,
                    m_312_boca=:m_312_boca,
                    m_312_faringe=:m_312_faringe,
                    m_312_cuello=:m_312_cuello,
                    m_312_respiratorio=:m_312_respiratorio,
                    m_312_cardiovascular=:m_312_cardiovascular,
                    m_312_digestivo=:m_312_digestivo,
                    m_312_genitou=:m_312_genitou,
                    m_312_locomotor=:m_312_locomotor,
                    m_312_marcha=:m_312_marcha,
                    m_312_columna=:m_312_columna,
                    m_312_mi_superi=:m_312_mi_superi,
                    m_312_mi_inferi=:m_312_mi_inferi,
                    m_312_linfatico=:m_312_linfatico,
                    m_312_nervio=:m_312_nervio,
                    m_312_osteomuscular=:m_312_osteomuscular,
                    m_312_ef_observaciones=:m_312_ef_observaciones,
                    m_312_conclu_psico=:m_312_conclu_psico,
                    m_312_conclu_rx=:m_312_conclu_rx,
                    m_312_conclu_lab=:m_312_conclu_lab,
                    m_312_conclu_audio=:m_312_conclu_audio,
                    m_312_conclu_espiro=:m_312_conclu_espiro,
                    m_312_conclu_otros=:m_312_conclu_otros,
                    m_312_diag_cie1=:m_312_diag_cie1,
                    m_312_diag_st1=:m_312_diag_st1,
                    m_312_diag_cie2=:m_312_diag_cie2,
                    m_312_diag_st2=:m_312_diag_st2,
                    m_312_diag_cie3=:m_312_diag_cie3,
                    m_312_diag_st3=:m_312_diag_st3,
                    m_312_diag_cie4=:m_312_diag_cie4,
                    m_312_diag_st4=:m_312_diag_st4,
                    m_312_diag_cie5=:m_312_diag_cie5,
                    m_312_diag_st5=:m_312_diag_st5,
                    m_312_diag_cie6=:m_312_diag_cie6,
                    m_312_diag_st6=:m_312_diag_st6,
                    m_312_diag_cie7=:m_312_diag_cie7,
                    m_312_diag_st7=:m_312_diag_st7,
                    m_312_diag_cie8=:m_312_diag_cie8,
                    m_312_diag_st8=:m_312_diag_st8,
                    m_312_fech_val=:m_312_fech_val,
                    m_312_fech_vence=:m_312_fech_vence,
                    m_312_time_aptitud=:m_312_time_aptitud,
                    m_312_restricciones=:m_312_restricciones,
                    m_312_observaciones=:m_312_observaciones,
                    m_312_aptitud=:m_312_aptitud,
                    m_312_medico_ocupa=:m_312_medico_ocupa,
                    m_312_medico_auditor=:m_312_medico_auditor        
                where
                m_312_adm=:adm and m_312_exa=:ex_id;';

        $sql_1 = $this->sql($q_1, $params_1);
        if ($sql_1->success) {
            $sql_2 = $this->sql($q_2, $params_2);
            if ($sql_2->success) {
                $params_3 = array();
                $params_3[':adm'] = $_POST['adm'];
                $params_3[':aptitud'] = $_POST['m_312_aptitud'];

                $q_3 = "Update admision set adm_aptitud=:aptitud, adm_val=1 where adm_id=:adm;";

                $sql_3 = $this->sql($q_3, $params_3);
                if ($sql_3->success) {
                    $this->commit();
                    return $sql_3;
                } else {
                    $this->rollback();
                    return array('success' => false, 'error' => 'Problemas con el registro.');
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

    public function mod_medicina_312($adm)
    {
        $sql = $this->sql("SELECT * FROM mod_medicina_312
            where
            m_312_adm=$adm;
                ");
        return $sql;
    }

    //LOAD SAVE UPDATE musculo

    public function load_musculo()
    {
        $adm = $_POST['adm'];
        //        $examen = $_POST['examen'];
        $query = "SELECT * FROM mod_medicina_musculo where m_musc_adm='$adm';";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function save_musculo()
    {

        $adm = $_POST['adm'];
        $exa = $_POST['ex_id'];

        $this->begin();

        $params_1 = array();
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':sede'] = $this->user->con_sedid;
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':ex_id'] = $_POST['ex_id'];

        $q_1 = "INSERT INTO mod_medicina VALUES
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
		
		
        $params_2[':m_musc_flexi_ptos'] = $_POST['m_musc_flexi_ptos'];
        $params_2[':m_musc_flexi_obs'] = $_POST['m_musc_flexi_obs'];
        $params_2[':m_musc_cadera_ptos'] = $_POST['m_musc_cadera_ptos'];
        $params_2[':m_musc_cadera_obs'] = $_POST['m_musc_cadera_obs'];
        $params_2[':m_musc_muslo_ptos'] = $_POST['m_musc_muslo_ptos'];
        $params_2[':m_musc_muslo_obs'] = $_POST['m_musc_muslo_obs'];
        $params_2[':m_musc_abdom_ptos'] = $_POST['m_musc_abdom_ptos'];
        $params_2[':m_musc_abdom_obs'] = $_POST['m_musc_abdom_obs'];
        $params_2[':m_musc_abduc_180_ptos'] = $_POST['m_musc_abduc_180_ptos'];
        $params_2[':m_musc_abduc_180_dolor'] = $_POST['m_musc_abduc_180_dolor'];
        $params_2[':m_musc_abduc_80_ptos'] = $_POST['m_musc_abduc_80_ptos'];
        $params_2[':m_musc_abduc_80_dolor'] = $_POST['m_musc_abduc_80_dolor'];
        $params_2[':m_musc_rota_exter_ptos'] = $_POST['m_musc_rota_exter_ptos'];
        $params_2[':m_musc_rota_exter_dolor'] = $_POST['m_musc_rota_exter_dolor'];
        $params_2[':m_musc_rota_inter_ptos'] = $_POST['m_musc_rota_inter_ptos'];
        $params_2[':m_musc_rota_inter_dolor'] = $_POST['m_musc_rota_inter_dolor'];
        $params_2[':m_musc_ra_obs'] = $_POST['m_musc_ra_obs'];
        $params_2[':m_musc_aptitud'] = $_POST['m_musc_aptitud'];
        $params_2[':m_musc_col_cevical_desvia_lateral'] = $_POST['m_musc_col_cevical_desvia_lateral'];
        $params_2[':m_musc_col_cevical_desvia_antero'] = $_POST['m_musc_col_cevical_desvia_antero'];
        $params_2[':m_musc_col_cevical_palpa_apofisis'] = $_POST['m_musc_col_cevical_palpa_apofisis'];
        $params_2[':m_musc_col_cevical_palpa_contractura'] = $_POST['m_musc_col_cevical_palpa_contractura'];
        $params_2[':m_musc_col_dorsal_desvia_lateral'] = $_POST['m_musc_col_dorsal_desvia_lateral'];
        $params_2[':m_musc_col_dorsal_desvia_antero'] = $_POST['m_musc_col_dorsal_desvia_antero'];
        $params_2[':m_musc_col_dorsal_palpa_apofisis'] = $_POST['m_musc_col_dorsal_palpa_apofisis'];
        $params_2[':m_musc_col_dorsal_palpa_contractura'] = $_POST['m_musc_col_dorsal_palpa_contractura'];
        $params_2[':m_musc_col_lumbar_desvia_lateral'] = $_POST['m_musc_col_lumbar_desvia_lateral'];
        $params_2[':m_musc_col_lumbar_desvia_antero'] = $_POST['m_musc_col_lumbar_desvia_antero'];
        $params_2[':m_musc_col_lumbar_palpa_apofisis'] = $_POST['m_musc_col_lumbar_palpa_apofisis'];
        $params_2[':m_musc_col_lumbar_palpa_contractura'] = $_POST['m_musc_col_lumbar_palpa_contractura'];
        $params_2[':m_musc_col_cevical_flexion'] = $_POST['m_musc_col_cevical_flexion'];
        $params_2[':m_musc_col_cevical_exten'] = $_POST['m_musc_col_cevical_exten'];
        $params_2[':m_musc_col_cevical_lat_izq'] = $_POST['m_musc_col_cevical_lat_izq'];
        $params_2[':m_musc_col_cevical_lat_der'] = $_POST['m_musc_col_cevical_lat_der'];
        $params_2[':m_musc_col_cevical_rota_izq'] = $_POST['m_musc_col_cevical_rota_izq'];
        $params_2[':m_musc_col_cevical_rota_der'] = $_POST['m_musc_col_cevical_rota_der'];
        $params_2[':m_musc_col_cevical_irradia'] = $_POST['m_musc_col_cevical_irradia'];
        $params_2[':m_musc_col_cevical_alt_masa'] = $_POST['m_musc_col_cevical_alt_masa'];
        $params_2[':m_musc_col_dorsal_flexion'] = $_POST['m_musc_col_dorsal_flexion'];
        $params_2[':m_musc_col_dorsal_exten'] = $_POST['m_musc_col_dorsal_exten'];
        $params_2[':m_musc_col_dorsal_lat_izq'] = $_POST['m_musc_col_dorsal_lat_izq'];
        $params_2[':m_musc_col_dorsal_lat_der'] = $_POST['m_musc_col_dorsal_lat_der'];
        $params_2[':m_musc_col_dorsal_rota_izq'] = $_POST['m_musc_col_dorsal_rota_izq'];
        $params_2[':m_musc_col_dorsal_rota_der'] = $_POST['m_musc_col_dorsal_rota_der'];
        $params_2[':m_musc_col_dorsal_irradia'] = $_POST['m_musc_col_dorsal_irradia'];
        $params_2[':m_musc_col_dorsal_alt_masa'] = $_POST['m_musc_col_dorsal_alt_masa'];
        $params_2[':m_musc_col_lumbar_flexion'] = $_POST['m_musc_col_lumbar_flexion'];
        $params_2[':m_musc_col_lumbar_exten'] = $_POST['m_musc_col_lumbar_exten'];
        $params_2[':m_musc_col_lumbar_lat_izq'] = $_POST['m_musc_col_lumbar_lat_izq'];
        $params_2[':m_musc_col_lumbar_lat_der'] = $_POST['m_musc_col_lumbar_lat_der'];
        $params_2[':m_musc_col_lumbar_rota_izq'] = $_POST['m_musc_col_lumbar_rota_izq'];
        $params_2[':m_musc_col_lumbar_rota_der'] = $_POST['m_musc_col_lumbar_rota_der'];
        $params_2[':m_musc_col_lumbar_irradia'] = $_POST['m_musc_col_lumbar_irradia'];
        $params_2[':m_musc_col_lumbar_alt_masa'] = $_POST['m_musc_col_lumbar_alt_masa'];
        $params_2[':m_musc_hombro_der_abduccion'] = $_POST['m_musc_hombro_der_abduccion'];
        $params_2[':m_musc_hombro_der_aduccion'] = $_POST['m_musc_hombro_der_aduccion'];
        $params_2[':m_musc_hombro_der_flexion'] = $_POST['m_musc_hombro_der_flexion'];
        $params_2[':m_musc_hombro_der_extencion'] = $_POST['m_musc_hombro_der_extencion'];
        $params_2[':m_musc_hombro_der_rota_exter'] = $_POST['m_musc_hombro_der_rota_exter'];
        $params_2[':m_musc_hombro_der_rota_inter'] = $_POST['m_musc_hombro_der_rota_inter'];
        $params_2[':m_musc_hombro_der_irradia'] = $_POST['m_musc_hombro_der_irradia'];
        $params_2[':m_musc_hombro_der_alt_masa'] = $_POST['m_musc_hombro_der_alt_masa'];
        $params_2[':m_musc_hombro_izq_abduccion'] = $_POST['m_musc_hombro_izq_abduccion'];
        $params_2[':m_musc_hombro_izq_aduccion'] = $_POST['m_musc_hombro_izq_aduccion'];
        $params_2[':m_musc_hombro_izq_flexion'] = $_POST['m_musc_hombro_izq_flexion'];
        $params_2[':m_musc_hombro_izq_extencion'] = $_POST['m_musc_hombro_izq_extencion'];
        $params_2[':m_musc_hombro_izq_rota_exter'] = $_POST['m_musc_hombro_izq_rota_exter'];
        $params_2[':m_musc_hombro_izq_rota_inter'] = $_POST['m_musc_hombro_izq_rota_inter'];
        $params_2[':m_musc_hombro_izq_irradia'] = $_POST['m_musc_hombro_izq_irradia'];
        $params_2[':m_musc_hombro_izq_alt_masa'] = $_POST['m_musc_hombro_izq_alt_masa'];
        $params_2[':m_musc_codo_der_abduccion'] = $_POST['m_musc_codo_der_abduccion'];
        $params_2[':m_musc_codo_der_aduccion'] = $_POST['m_musc_codo_der_aduccion'];
        $params_2[':m_musc_codo_der_flexion'] = $_POST['m_musc_codo_der_flexion'];
        $params_2[':m_musc_codo_der_extencion'] = $_POST['m_musc_codo_der_extencion'];
        $params_2[':m_musc_codo_der_rota_exter'] = $_POST['m_musc_codo_der_rota_exter'];
        $params_2[':m_musc_codo_der_rota_inter'] = $_POST['m_musc_codo_der_rota_inter'];
        $params_2[':m_musc_codo_der_irradia'] = $_POST['m_musc_codo_der_irradia'];
        $params_2[':m_musc_codo_der_alt_masa'] = $_POST['m_musc_codo_der_alt_masa'];
        $params_2[':m_musc_codo_izq_abduccion'] = $_POST['m_musc_codo_izq_abduccion'];
        $params_2[':m_musc_codo_izq_aduccion'] = $_POST['m_musc_codo_izq_aduccion'];
        $params_2[':m_musc_codo_izq_flexion'] = $_POST['m_musc_codo_izq_flexion'];
        $params_2[':m_musc_codo_izq_extencion'] = $_POST['m_musc_codo_izq_extencion'];
        $params_2[':m_musc_codo_izq_rota_exter'] = $_POST['m_musc_codo_izq_rota_exter'];
        $params_2[':m_musc_codo_izq_rota_inter'] = $_POST['m_musc_codo_izq_rota_inter'];
        $params_2[':m_musc_codo_izq_irradia'] = $_POST['m_musc_codo_izq_irradia'];
        $params_2[':m_musc_codo_izq_alt_masa'] = $_POST['m_musc_codo_izq_alt_masa'];
        $params_2[':m_musc_muneca_der_abduccion'] = $_POST['m_musc_muneca_der_abduccion'];
        $params_2[':m_musc_muneca_der_aduccion'] = $_POST['m_musc_muneca_der_aduccion'];
        $params_2[':m_musc_muneca_der_flexion'] = $_POST['m_musc_muneca_der_flexion'];
        $params_2[':m_musc_muneca_der_extencion'] = $_POST['m_musc_muneca_der_extencion'];
        $params_2[':m_musc_muneca_der_rota_exter'] = $_POST['m_musc_muneca_der_rota_exter'];
        $params_2[':m_musc_muneca_der_rota_inter'] = $_POST['m_musc_muneca_der_rota_inter'];
        $params_2[':m_musc_muneca_der_irradia'] = $_POST['m_musc_muneca_der_irradia'];
        $params_2[':m_musc_muneca_der_alt_masa'] = $_POST['m_musc_muneca_der_alt_masa'];
        $params_2[':m_musc_muneca_izq_abduccion'] = $_POST['m_musc_muneca_izq_abduccion'];
        $params_2[':m_musc_muneca_izq_aduccion'] = $_POST['m_musc_muneca_izq_aduccion'];
        $params_2[':m_musc_muneca_izq_flexion'] = $_POST['m_musc_muneca_izq_flexion'];
        $params_2[':m_musc_muneca_izq_extencion'] = $_POST['m_musc_muneca_izq_extencion'];
        $params_2[':m_musc_muneca_izq_rota_exter'] = $_POST['m_musc_muneca_izq_rota_exter'];
        $params_2[':m_musc_muneca_izq_rota_inter'] = $_POST['m_musc_muneca_izq_rota_inter'];
        $params_2[':m_musc_muneca_izq_irradia'] = $_POST['m_musc_muneca_izq_irradia'];
        $params_2[':m_musc_muneca_izq_alt_masa'] = $_POST['m_musc_muneca_izq_alt_masa'];
        $params_2[':m_musc_mano_der_abduccion'] = $_POST['m_musc_mano_der_abduccion'];
        $params_2[':m_musc_mano_der_aduccion'] = $_POST['m_musc_mano_der_aduccion'];
        $params_2[':m_musc_mano_der_flexion'] = $_POST['m_musc_mano_der_flexion'];
        $params_2[':m_musc_mano_der_extencion'] = $_POST['m_musc_mano_der_extencion'];
        $params_2[':m_musc_mano_der_rota_exter'] = $_POST['m_musc_mano_der_rota_exter'];
        $params_2[':m_musc_mano_der_rota_inter'] = $_POST['m_musc_mano_der_rota_inter'];
        $params_2[':m_musc_mano_der_irradia'] = $_POST['m_musc_mano_der_irradia'];
        $params_2[':m_musc_mano_der_alt_masa'] = $_POST['m_musc_mano_der_alt_masa'];
        $params_2[':m_musc_mano_izq_abduccion'] = $_POST['m_musc_mano_izq_abduccion'];
        $params_2[':m_musc_mano_izq_aduccion'] = $_POST['m_musc_mano_izq_aduccion'];
        $params_2[':m_musc_mano_izq_flexion'] = $_POST['m_musc_mano_izq_flexion'];
        $params_2[':m_musc_mano_izq_extencion'] = $_POST['m_musc_mano_izq_extencion'];
        $params_2[':m_musc_mano_izq_rota_exter'] = $_POST['m_musc_mano_izq_rota_exter'];
        $params_2[':m_musc_mano_izq_rota_inter'] = $_POST['m_musc_mano_izq_rota_inter'];
        $params_2[':m_musc_mano_izq_irradia'] = $_POST['m_musc_mano_izq_irradia'];
        $params_2[':m_musc_mano_izq_alt_masa'] = $_POST['m_musc_mano_izq_alt_masa'];
        $params_2[':m_musc_cadera_der_abduccion'] = $_POST['m_musc_cadera_der_abduccion'];
        $params_2[':m_musc_cadera_der_aduccion'] = $_POST['m_musc_cadera_der_aduccion'];
        $params_2[':m_musc_cadera_der_flexion'] = $_POST['m_musc_cadera_der_flexion'];
        $params_2[':m_musc_cadera_der_extencion'] = $_POST['m_musc_cadera_der_extencion'];
        $params_2[':m_musc_cadera_der_rota_exter'] = $_POST['m_musc_cadera_der_rota_exter'];
        $params_2[':m_musc_cadera_der_rota_inter'] = $_POST['m_musc_cadera_der_rota_inter'];
        $params_2[':m_musc_cadera_der_irradia'] = $_POST['m_musc_cadera_der_irradia'];
        $params_2[':m_musc_cadera_der_alt_masa'] = $_POST['m_musc_cadera_der_alt_masa'];
        $params_2[':m_musc_cadera_izq_abduccion'] = $_POST['m_musc_cadera_izq_abduccion'];
        $params_2[':m_musc_cadera_izq_aduccion'] = $_POST['m_musc_cadera_izq_aduccion'];
        $params_2[':m_musc_cadera_izq_flexion'] = $_POST['m_musc_cadera_izq_flexion'];
        $params_2[':m_musc_cadera_izq_extencion'] = $_POST['m_musc_cadera_izq_extencion'];
        $params_2[':m_musc_cadera_izq_rota_exter'] = $_POST['m_musc_cadera_izq_rota_exter'];
        $params_2[':m_musc_cadera_izq_rota_inter'] = $_POST['m_musc_cadera_izq_rota_inter'];
        $params_2[':m_musc_cadera_izq_irradia'] = $_POST['m_musc_cadera_izq_irradia'];
        $params_2[':m_musc_cadera_izq_alt_masa'] = $_POST['m_musc_cadera_izq_alt_masa'];
        $params_2[':m_musc_rodilla_der_abduccion'] = $_POST['m_musc_rodilla_der_abduccion'];
        $params_2[':m_musc_rodilla_der_aduccion'] = $_POST['m_musc_rodilla_der_aduccion'];
        $params_2[':m_musc_rodilla_der_flexion'] = $_POST['m_musc_rodilla_der_flexion'];
        $params_2[':m_musc_rodilla_der_extencion'] = $_POST['m_musc_rodilla_der_extencion'];
        $params_2[':m_musc_rodilla_der_rota_exter'] = $_POST['m_musc_rodilla_der_rota_exter'];
        $params_2[':m_musc_rodilla_der_rota_inter'] = $_POST['m_musc_rodilla_der_rota_inter'];
        $params_2[':m_musc_rodilla_der_irradia'] = $_POST['m_musc_rodilla_der_irradia'];
        $params_2[':m_musc_rodilla_der_alt_masa'] = $_POST['m_musc_rodilla_der_alt_masa'];
        $params_2[':m_musc_rodilla_izq_abduccion'] = $_POST['m_musc_rodilla_izq_abduccion'];
        $params_2[':m_musc_rodilla_izq_aduccion'] = $_POST['m_musc_rodilla_izq_aduccion'];
        $params_2[':m_musc_rodilla_izq_flexion'] = $_POST['m_musc_rodilla_izq_flexion'];
        $params_2[':m_musc_rodilla_izq_extencion'] = $_POST['m_musc_rodilla_izq_extencion'];
        $params_2[':m_musc_rodilla_izq_rota_exter'] = $_POST['m_musc_rodilla_izq_rota_exter'];
        $params_2[':m_musc_rodilla_izq_rota_inter'] = $_POST['m_musc_rodilla_izq_rota_inter'];
        $params_2[':m_musc_rodilla_izq_irradia'] = $_POST['m_musc_rodilla_izq_irradia'];
        $params_2[':m_musc_rodilla_izq_alt_masa'] = $_POST['m_musc_rodilla_izq_alt_masa'];
        $params_2[':m_musc_tobillo_der_abduccion'] = $_POST['m_musc_tobillo_der_abduccion'];
        $params_2[':m_musc_tobillo_der_aduccion'] = $_POST['m_musc_tobillo_der_aduccion'];
        $params_2[':m_musc_tobillo_der_flexion'] = $_POST['m_musc_tobillo_der_flexion'];
        $params_2[':m_musc_tobillo_der_extencion'] = $_POST['m_musc_tobillo_der_extencion'];
        $params_2[':m_musc_tobillo_der_rota_exter'] = $_POST['m_musc_tobillo_der_rota_exter'];
        $params_2[':m_musc_tobillo_der_rota_inter'] = $_POST['m_musc_tobillo_der_rota_inter'];
        $params_2[':m_musc_tobillo_der_irradia'] = $_POST['m_musc_tobillo_der_irradia'];
        $params_2[':m_musc_tobillo_der_alt_masa'] = $_POST['m_musc_tobillo_der_alt_masa'];
        $params_2[':m_musc_tobillo_izq_abduccion'] = $_POST['m_musc_tobillo_izq_abduccion'];
        $params_2[':m_musc_tobillo_izq_aduccion'] = $_POST['m_musc_tobillo_izq_aduccion'];
        $params_2[':m_musc_tobillo_izq_flexion'] = $_POST['m_musc_tobillo_izq_flexion'];
        $params_2[':m_musc_tobillo_izq_extencion'] = $_POST['m_musc_tobillo_izq_extencion'];
        $params_2[':m_musc_tobillo_izq_rota_exter'] = $_POST['m_musc_tobillo_izq_rota_exter'];
        $params_2[':m_musc_tobillo_izq_rota_inter'] = $_POST['m_musc_tobillo_izq_rota_inter'];
        $params_2[':m_musc_tobillo_izq_irradia'] = $_POST['m_musc_tobillo_izq_irradia'];
        $params_2[':m_musc_tobillo_izq_alt_masa'] = $_POST['m_musc_tobillo_izq_alt_masa'];
        $params_2[':m_musc_colum_punto_ref'] = $_POST['m_musc_colum_punto_ref'];
        $params_2[':m_musc_colum_aptitud'] = $_POST['m_musc_colum_aptitud'];
        $params_2[':m_musc_colum_desc'] = $_POST['m_musc_colum_desc'];
        $params_2[':m_musc_diag_01'] = $_POST['m_musc_diag_01'];
        $params_2[':m_musc_conclu_01'] = $_POST['m_musc_conclu_01'];
        $params_2[':m_musc_recom_01'] = $_POST['m_musc_recom_01'];
        $params_2[':m_musc_diag_02'] = $_POST['m_musc_diag_02'];
        $params_2[':m_musc_conclu_02'] = $_POST['m_musc_conclu_02'];
        $params_2[':m_musc_recom_02'] = $_POST['m_musc_recom_02'];
        $params_2[':m_musc_diag_03'] = $_POST['m_musc_diag_03'];
        $params_2[':m_musc_conclu_03'] = $_POST['m_musc_conclu_03'];
        $params_2[':m_musc_recom_03'] = $_POST['m_musc_recom_03'];



        $q_2 = "INSERT INTO mod_medicina_musculo VALUES 
                (null,
                :adm,
                :ex_id,
                :m_musc_flexi_ptos,
                :m_musc_flexi_obs,
                :m_musc_cadera_ptos,
                :m_musc_cadera_obs,
                :m_musc_muslo_ptos,
                :m_musc_muslo_obs,
                :m_musc_abdom_ptos,
                :m_musc_abdom_obs,
                :m_musc_abduc_180_ptos,
                :m_musc_abduc_180_dolor,
                :m_musc_abduc_80_ptos,
                :m_musc_abduc_80_dolor,
                :m_musc_rota_exter_ptos,
                :m_musc_rota_exter_dolor,
                :m_musc_rota_inter_ptos,
                :m_musc_rota_inter_dolor,
                :m_musc_ra_obs,
                :m_musc_aptitud,
                :m_musc_col_cevical_desvia_lateral,
                :m_musc_col_cevical_desvia_antero,
                :m_musc_col_cevical_palpa_apofisis,
                :m_musc_col_cevical_palpa_contractura,
                :m_musc_col_dorsal_desvia_lateral,
                :m_musc_col_dorsal_desvia_antero,
                :m_musc_col_dorsal_palpa_apofisis,
                :m_musc_col_dorsal_palpa_contractura,
                :m_musc_col_lumbar_desvia_lateral,
                :m_musc_col_lumbar_desvia_antero,
                :m_musc_col_lumbar_palpa_apofisis,
                :m_musc_col_lumbar_palpa_contractura,
                :m_musc_col_cevical_flexion,
                :m_musc_col_cevical_exten,
                :m_musc_col_cevical_lat_izq,
                :m_musc_col_cevical_lat_der,
                :m_musc_col_cevical_rota_izq,
                :m_musc_col_cevical_rota_der,
                :m_musc_col_cevical_irradia,
                :m_musc_col_cevical_alt_masa,
                :m_musc_col_dorsal_flexion,
                :m_musc_col_dorsal_exten,
                :m_musc_col_dorsal_lat_izq,
                :m_musc_col_dorsal_lat_der,
                :m_musc_col_dorsal_rota_izq,
                :m_musc_col_dorsal_rota_der,
                :m_musc_col_dorsal_irradia,
                :m_musc_col_dorsal_alt_masa,
                :m_musc_col_lumbar_flexion,
                :m_musc_col_lumbar_exten,
                :m_musc_col_lumbar_lat_izq,
                :m_musc_col_lumbar_lat_der,
                :m_musc_col_lumbar_rota_izq,
                :m_musc_col_lumbar_rota_der,
                :m_musc_col_lumbar_irradia,
                :m_musc_col_lumbar_alt_masa,
                :m_musc_hombro_der_abduccion,
                :m_musc_hombro_der_aduccion,
                :m_musc_hombro_der_flexion,
                :m_musc_hombro_der_extencion,
                :m_musc_hombro_der_rota_exter,
                :m_musc_hombro_der_rota_inter,
                :m_musc_hombro_der_irradia,
                :m_musc_hombro_der_alt_masa,
                :m_musc_hombro_izq_abduccion,
                :m_musc_hombro_izq_aduccion,
                :m_musc_hombro_izq_flexion,
                :m_musc_hombro_izq_extencion,
                :m_musc_hombro_izq_rota_exter,
                :m_musc_hombro_izq_rota_inter,
                :m_musc_hombro_izq_irradia,
                :m_musc_hombro_izq_alt_masa,
                :m_musc_codo_der_abduccion,
                :m_musc_codo_der_aduccion,
                :m_musc_codo_der_flexion,
                :m_musc_codo_der_extencion,
                :m_musc_codo_der_rota_exter,
                :m_musc_codo_der_rota_inter,
                :m_musc_codo_der_irradia,
                :m_musc_codo_der_alt_masa,
                :m_musc_codo_izq_abduccion,
                :m_musc_codo_izq_aduccion,
                :m_musc_codo_izq_flexion,
                :m_musc_codo_izq_extencion,
                :m_musc_codo_izq_rota_exter,
                :m_musc_codo_izq_rota_inter,
                :m_musc_codo_izq_irradia,
                :m_musc_codo_izq_alt_masa,
                :m_musc_muneca_der_abduccion,
                :m_musc_muneca_der_aduccion,
                :m_musc_muneca_der_flexion,
                :m_musc_muneca_der_extencion,
                :m_musc_muneca_der_rota_exter,
                :m_musc_muneca_der_rota_inter,
                :m_musc_muneca_der_irradia,
                :m_musc_muneca_der_alt_masa,
                :m_musc_muneca_izq_abduccion,
                :m_musc_muneca_izq_aduccion,
                :m_musc_muneca_izq_flexion,
                :m_musc_muneca_izq_extencion,
                :m_musc_muneca_izq_rota_exter,
                :m_musc_muneca_izq_rota_inter,
                :m_musc_muneca_izq_irradia,
                :m_musc_muneca_izq_alt_masa,
                :m_musc_mano_der_abduccion,
                :m_musc_mano_der_aduccion,
                :m_musc_mano_der_flexion,
                :m_musc_mano_der_extencion,
                :m_musc_mano_der_rota_exter,
                :m_musc_mano_der_rota_inter,
                :m_musc_mano_der_irradia,
                :m_musc_mano_der_alt_masa,
                :m_musc_mano_izq_abduccion,
                :m_musc_mano_izq_aduccion,
                :m_musc_mano_izq_flexion,
                :m_musc_mano_izq_extencion,
                :m_musc_mano_izq_rota_exter,
                :m_musc_mano_izq_rota_inter,
                :m_musc_mano_izq_irradia,
                :m_musc_mano_izq_alt_masa,
                :m_musc_cadera_der_abduccion,
                :m_musc_cadera_der_aduccion,
                :m_musc_cadera_der_flexion,
                :m_musc_cadera_der_extencion,
                :m_musc_cadera_der_rota_exter,
                :m_musc_cadera_der_rota_inter,
                :m_musc_cadera_der_irradia,
                :m_musc_cadera_der_alt_masa,
                :m_musc_cadera_izq_abduccion,
                :m_musc_cadera_izq_aduccion,
                :m_musc_cadera_izq_flexion,
                :m_musc_cadera_izq_extencion,
                :m_musc_cadera_izq_rota_exter,
                :m_musc_cadera_izq_rota_inter,
                :m_musc_cadera_izq_irradia,
                :m_musc_cadera_izq_alt_masa,
                :m_musc_rodilla_der_abduccion,
                :m_musc_rodilla_der_aduccion,
                :m_musc_rodilla_der_flexion,
                :m_musc_rodilla_der_extencion,
                :m_musc_rodilla_der_rota_exter,
                :m_musc_rodilla_der_rota_inter,
                :m_musc_rodilla_der_irradia,
                :m_musc_rodilla_der_alt_masa,
                :m_musc_rodilla_izq_abduccion,
                :m_musc_rodilla_izq_aduccion,
                :m_musc_rodilla_izq_flexion,
                :m_musc_rodilla_izq_extencion,
                :m_musc_rodilla_izq_rota_exter,
                :m_musc_rodilla_izq_rota_inter,
                :m_musc_rodilla_izq_irradia,
                :m_musc_rodilla_izq_alt_masa,
                :m_musc_tobillo_der_abduccion,
                :m_musc_tobillo_der_aduccion,
                :m_musc_tobillo_der_flexion,
                :m_musc_tobillo_der_extencion,
                :m_musc_tobillo_der_rota_exter,
                :m_musc_tobillo_der_rota_inter,
                :m_musc_tobillo_der_irradia,
                :m_musc_tobillo_der_alt_masa,
                :m_musc_tobillo_izq_abduccion,
                :m_musc_tobillo_izq_aduccion,
                :m_musc_tobillo_izq_flexion,
                :m_musc_tobillo_izq_extencion,
                :m_musc_tobillo_izq_rota_exter,
                :m_musc_tobillo_izq_rota_inter,
                :m_musc_tobillo_izq_irradia,
                :m_musc_tobillo_izq_alt_masa,
                :m_musc_colum_punto_ref,
                :m_musc_colum_aptitud,
                :m_musc_colum_desc,
                :m_musc_diag_01,
                :m_musc_conclu_01,
                :m_musc_recom_01,
                :m_musc_diag_02,
                :m_musc_conclu_02,
                :m_musc_recom_02,
                :m_musc_diag_03,
                :m_musc_conclu_03,
                :m_musc_recom_03
				);";

        $verifica = $this->sql("SELECT m_medicina_adm, concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario 
				FROM mod_medicina inner join sys_usuario on usu_id=m_medicina_usu 
				where m_medicina_adm='$adm' and m_medicina_examen='$exa';");
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

    public function update_musculo()
    {
        $this->begin();

        $params_1 = array();
        $params_1[':usuario'] = $this->user->us_id;
        $params_1[':id'] = $_POST['id'];
        $params_1[':adm'] = $_POST['adm'];
        $params_1[':ex_id'] = $_POST['ex_id'];
        $q_1 = 'Update mod_medicina set
                    m_medicina_usu=:usuario,
					m_medicina_fech_update=now()
                where
                m_medicina_id=:id and m_medicina_adm=:adm and m_medicina_examen=:ex_id;';

        ///////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////

        $params_2 = array();
        $params_2[':adm'] = $_POST['adm'];
		
        
        $params_2[':m_musc_flexi_ptos'] = $_POST['m_musc_flexi_ptos'];
        $params_2[':m_musc_flexi_obs'] = $_POST['m_musc_flexi_obs'];
        $params_2[':m_musc_cadera_ptos'] = $_POST['m_musc_cadera_ptos'];
        $params_2[':m_musc_cadera_obs'] = $_POST['m_musc_cadera_obs'];
        $params_2[':m_musc_muslo_ptos'] = $_POST['m_musc_muslo_ptos'];
        $params_2[':m_musc_muslo_obs'] = $_POST['m_musc_muslo_obs'];
        $params_2[':m_musc_abdom_ptos'] = $_POST['m_musc_abdom_ptos'];
        $params_2[':m_musc_abdom_obs'] = $_POST['m_musc_abdom_obs'];
        $params_2[':m_musc_abduc_180_ptos'] = $_POST['m_musc_abduc_180_ptos'];
        $params_2[':m_musc_abduc_180_dolor'] = $_POST['m_musc_abduc_180_dolor'];
        $params_2[':m_musc_abduc_80_ptos'] = $_POST['m_musc_abduc_80_ptos'];
        $params_2[':m_musc_abduc_80_dolor'] = $_POST['m_musc_abduc_80_dolor'];
        $params_2[':m_musc_rota_exter_ptos'] = $_POST['m_musc_rota_exter_ptos'];
        $params_2[':m_musc_rota_exter_dolor'] = $_POST['m_musc_rota_exter_dolor'];
        $params_2[':m_musc_rota_inter_ptos'] = $_POST['m_musc_rota_inter_ptos'];
        $params_2[':m_musc_rota_inter_dolor'] = $_POST['m_musc_rota_inter_dolor'];
        $params_2[':m_musc_ra_obs'] = $_POST['m_musc_ra_obs'];
        $params_2[':m_musc_aptitud'] = $_POST['m_musc_aptitud'];
        $params_2[':m_musc_col_cevical_desvia_lateral'] = $_POST['m_musc_col_cevical_desvia_lateral'];
        $params_2[':m_musc_col_cevical_desvia_antero'] = $_POST['m_musc_col_cevical_desvia_antero'];
        $params_2[':m_musc_col_cevical_palpa_apofisis'] = $_POST['m_musc_col_cevical_palpa_apofisis'];
        $params_2[':m_musc_col_cevical_palpa_contractura'] = $_POST['m_musc_col_cevical_palpa_contractura'];
        $params_2[':m_musc_col_dorsal_desvia_lateral'] = $_POST['m_musc_col_dorsal_desvia_lateral'];
        $params_2[':m_musc_col_dorsal_desvia_antero'] = $_POST['m_musc_col_dorsal_desvia_antero'];
        $params_2[':m_musc_col_dorsal_palpa_apofisis'] = $_POST['m_musc_col_dorsal_palpa_apofisis'];
        $params_2[':m_musc_col_dorsal_palpa_contractura'] = $_POST['m_musc_col_dorsal_palpa_contractura'];
        $params_2[':m_musc_col_lumbar_desvia_lateral'] = $_POST['m_musc_col_lumbar_desvia_lateral'];
        $params_2[':m_musc_col_lumbar_desvia_antero'] = $_POST['m_musc_col_lumbar_desvia_antero'];
        $params_2[':m_musc_col_lumbar_palpa_apofisis'] = $_POST['m_musc_col_lumbar_palpa_apofisis'];
        $params_2[':m_musc_col_lumbar_palpa_contractura'] = $_POST['m_musc_col_lumbar_palpa_contractura'];
        $params_2[':m_musc_col_cevical_flexion'] = $_POST['m_musc_col_cevical_flexion'];
        $params_2[':m_musc_col_cevical_exten'] = $_POST['m_musc_col_cevical_exten'];
        $params_2[':m_musc_col_cevical_lat_izq'] = $_POST['m_musc_col_cevical_lat_izq'];
        $params_2[':m_musc_col_cevical_lat_der'] = $_POST['m_musc_col_cevical_lat_der'];
        $params_2[':m_musc_col_cevical_rota_izq'] = $_POST['m_musc_col_cevical_rota_izq'];
        $params_2[':m_musc_col_cevical_rota_der'] = $_POST['m_musc_col_cevical_rota_der'];
        $params_2[':m_musc_col_cevical_irradia'] = $_POST['m_musc_col_cevical_irradia'];
        $params_2[':m_musc_col_cevical_alt_masa'] = $_POST['m_musc_col_cevical_alt_masa'];
        $params_2[':m_musc_col_dorsal_flexion'] = $_POST['m_musc_col_dorsal_flexion'];
        $params_2[':m_musc_col_dorsal_exten'] = $_POST['m_musc_col_dorsal_exten'];
        $params_2[':m_musc_col_dorsal_lat_izq'] = $_POST['m_musc_col_dorsal_lat_izq'];
        $params_2[':m_musc_col_dorsal_lat_der'] = $_POST['m_musc_col_dorsal_lat_der'];
        $params_2[':m_musc_col_dorsal_rota_izq'] = $_POST['m_musc_col_dorsal_rota_izq'];
        $params_2[':m_musc_col_dorsal_rota_der'] = $_POST['m_musc_col_dorsal_rota_der'];
        $params_2[':m_musc_col_dorsal_irradia'] = $_POST['m_musc_col_dorsal_irradia'];
        $params_2[':m_musc_col_dorsal_alt_masa'] = $_POST['m_musc_col_dorsal_alt_masa'];
        $params_2[':m_musc_col_lumbar_flexion'] = $_POST['m_musc_col_lumbar_flexion'];
        $params_2[':m_musc_col_lumbar_exten'] = $_POST['m_musc_col_lumbar_exten'];
        $params_2[':m_musc_col_lumbar_lat_izq'] = $_POST['m_musc_col_lumbar_lat_izq'];
        $params_2[':m_musc_col_lumbar_lat_der'] = $_POST['m_musc_col_lumbar_lat_der'];
        $params_2[':m_musc_col_lumbar_rota_izq'] = $_POST['m_musc_col_lumbar_rota_izq'];
        $params_2[':m_musc_col_lumbar_rota_der'] = $_POST['m_musc_col_lumbar_rota_der'];
        $params_2[':m_musc_col_lumbar_irradia'] = $_POST['m_musc_col_lumbar_irradia'];
        $params_2[':m_musc_col_lumbar_alt_masa'] = $_POST['m_musc_col_lumbar_alt_masa'];
        $params_2[':m_musc_hombro_der_abduccion'] = $_POST['m_musc_hombro_der_abduccion'];
        $params_2[':m_musc_hombro_der_aduccion'] = $_POST['m_musc_hombro_der_aduccion'];
        $params_2[':m_musc_hombro_der_flexion'] = $_POST['m_musc_hombro_der_flexion'];
        $params_2[':m_musc_hombro_der_extencion'] = $_POST['m_musc_hombro_der_extencion'];
        $params_2[':m_musc_hombro_der_rota_exter'] = $_POST['m_musc_hombro_der_rota_exter'];
        $params_2[':m_musc_hombro_der_rota_inter'] = $_POST['m_musc_hombro_der_rota_inter'];
        $params_2[':m_musc_hombro_der_irradia'] = $_POST['m_musc_hombro_der_irradia'];
        $params_2[':m_musc_hombro_der_alt_masa'] = $_POST['m_musc_hombro_der_alt_masa'];
        $params_2[':m_musc_hombro_izq_abduccion'] = $_POST['m_musc_hombro_izq_abduccion'];
        $params_2[':m_musc_hombro_izq_aduccion'] = $_POST['m_musc_hombro_izq_aduccion'];
        $params_2[':m_musc_hombro_izq_flexion'] = $_POST['m_musc_hombro_izq_flexion'];
        $params_2[':m_musc_hombro_izq_extencion'] = $_POST['m_musc_hombro_izq_extencion'];
        $params_2[':m_musc_hombro_izq_rota_exter'] = $_POST['m_musc_hombro_izq_rota_exter'];
        $params_2[':m_musc_hombro_izq_rota_inter'] = $_POST['m_musc_hombro_izq_rota_inter'];
        $params_2[':m_musc_hombro_izq_irradia'] = $_POST['m_musc_hombro_izq_irradia'];
        $params_2[':m_musc_hombro_izq_alt_masa'] = $_POST['m_musc_hombro_izq_alt_masa'];
        $params_2[':m_musc_codo_der_abduccion'] = $_POST['m_musc_codo_der_abduccion'];
        $params_2[':m_musc_codo_der_aduccion'] = $_POST['m_musc_codo_der_aduccion'];
        $params_2[':m_musc_codo_der_flexion'] = $_POST['m_musc_codo_der_flexion'];
        $params_2[':m_musc_codo_der_extencion'] = $_POST['m_musc_codo_der_extencion'];
        $params_2[':m_musc_codo_der_rota_exter'] = $_POST['m_musc_codo_der_rota_exter'];
        $params_2[':m_musc_codo_der_rota_inter'] = $_POST['m_musc_codo_der_rota_inter'];
        $params_2[':m_musc_codo_der_irradia'] = $_POST['m_musc_codo_der_irradia'];
        $params_2[':m_musc_codo_der_alt_masa'] = $_POST['m_musc_codo_der_alt_masa'];
        $params_2[':m_musc_codo_izq_abduccion'] = $_POST['m_musc_codo_izq_abduccion'];
        $params_2[':m_musc_codo_izq_aduccion'] = $_POST['m_musc_codo_izq_aduccion'];
        $params_2[':m_musc_codo_izq_flexion'] = $_POST['m_musc_codo_izq_flexion'];
        $params_2[':m_musc_codo_izq_extencion'] = $_POST['m_musc_codo_izq_extencion'];
        $params_2[':m_musc_codo_izq_rota_exter'] = $_POST['m_musc_codo_izq_rota_exter'];
        $params_2[':m_musc_codo_izq_rota_inter'] = $_POST['m_musc_codo_izq_rota_inter'];
        $params_2[':m_musc_codo_izq_irradia'] = $_POST['m_musc_codo_izq_irradia'];
        $params_2[':m_musc_codo_izq_alt_masa'] = $_POST['m_musc_codo_izq_alt_masa'];
        $params_2[':m_musc_muneca_der_abduccion'] = $_POST['m_musc_muneca_der_abduccion'];
        $params_2[':m_musc_muneca_der_aduccion'] = $_POST['m_musc_muneca_der_aduccion'];
        $params_2[':m_musc_muneca_der_flexion'] = $_POST['m_musc_muneca_der_flexion'];
        $params_2[':m_musc_muneca_der_extencion'] = $_POST['m_musc_muneca_der_extencion'];
        $params_2[':m_musc_muneca_der_rota_exter'] = $_POST['m_musc_muneca_der_rota_exter'];
        $params_2[':m_musc_muneca_der_rota_inter'] = $_POST['m_musc_muneca_der_rota_inter'];
        $params_2[':m_musc_muneca_der_irradia'] = $_POST['m_musc_muneca_der_irradia'];
        $params_2[':m_musc_muneca_der_alt_masa'] = $_POST['m_musc_muneca_der_alt_masa'];
        $params_2[':m_musc_muneca_izq_abduccion'] = $_POST['m_musc_muneca_izq_abduccion'];
        $params_2[':m_musc_muneca_izq_aduccion'] = $_POST['m_musc_muneca_izq_aduccion'];
        $params_2[':m_musc_muneca_izq_flexion'] = $_POST['m_musc_muneca_izq_flexion'];
        $params_2[':m_musc_muneca_izq_extencion'] = $_POST['m_musc_muneca_izq_extencion'];
        $params_2[':m_musc_muneca_izq_rota_exter'] = $_POST['m_musc_muneca_izq_rota_exter'];
        $params_2[':m_musc_muneca_izq_rota_inter'] = $_POST['m_musc_muneca_izq_rota_inter'];
        $params_2[':m_musc_muneca_izq_irradia'] = $_POST['m_musc_muneca_izq_irradia'];
        $params_2[':m_musc_muneca_izq_alt_masa'] = $_POST['m_musc_muneca_izq_alt_masa'];
        $params_2[':m_musc_mano_der_abduccion'] = $_POST['m_musc_mano_der_abduccion'];
        $params_2[':m_musc_mano_der_aduccion'] = $_POST['m_musc_mano_der_aduccion'];
        $params_2[':m_musc_mano_der_flexion'] = $_POST['m_musc_mano_der_flexion'];
        $params_2[':m_musc_mano_der_extencion'] = $_POST['m_musc_mano_der_extencion'];
        $params_2[':m_musc_mano_der_rota_exter'] = $_POST['m_musc_mano_der_rota_exter'];
        $params_2[':m_musc_mano_der_rota_inter'] = $_POST['m_musc_mano_der_rota_inter'];
        $params_2[':m_musc_mano_der_irradia'] = $_POST['m_musc_mano_der_irradia'];
        $params_2[':m_musc_mano_der_alt_masa'] = $_POST['m_musc_mano_der_alt_masa'];
        $params_2[':m_musc_mano_izq_abduccion'] = $_POST['m_musc_mano_izq_abduccion'];
        $params_2[':m_musc_mano_izq_aduccion'] = $_POST['m_musc_mano_izq_aduccion'];
        $params_2[':m_musc_mano_izq_flexion'] = $_POST['m_musc_mano_izq_flexion'];
        $params_2[':m_musc_mano_izq_extencion'] = $_POST['m_musc_mano_izq_extencion'];
        $params_2[':m_musc_mano_izq_rota_exter'] = $_POST['m_musc_mano_izq_rota_exter'];
        $params_2[':m_musc_mano_izq_rota_inter'] = $_POST['m_musc_mano_izq_rota_inter'];
        $params_2[':m_musc_mano_izq_irradia'] = $_POST['m_musc_mano_izq_irradia'];
        $params_2[':m_musc_mano_izq_alt_masa'] = $_POST['m_musc_mano_izq_alt_masa'];
        $params_2[':m_musc_cadera_der_abduccion'] = $_POST['m_musc_cadera_der_abduccion'];
        $params_2[':m_musc_cadera_der_aduccion'] = $_POST['m_musc_cadera_der_aduccion'];
        $params_2[':m_musc_cadera_der_flexion'] = $_POST['m_musc_cadera_der_flexion'];
        $params_2[':m_musc_cadera_der_extencion'] = $_POST['m_musc_cadera_der_extencion'];
        $params_2[':m_musc_cadera_der_rota_exter'] = $_POST['m_musc_cadera_der_rota_exter'];
        $params_2[':m_musc_cadera_der_rota_inter'] = $_POST['m_musc_cadera_der_rota_inter'];
        $params_2[':m_musc_cadera_der_irradia'] = $_POST['m_musc_cadera_der_irradia'];
        $params_2[':m_musc_cadera_der_alt_masa'] = $_POST['m_musc_cadera_der_alt_masa'];
        $params_2[':m_musc_cadera_izq_abduccion'] = $_POST['m_musc_cadera_izq_abduccion'];
        $params_2[':m_musc_cadera_izq_aduccion'] = $_POST['m_musc_cadera_izq_aduccion'];
        $params_2[':m_musc_cadera_izq_flexion'] = $_POST['m_musc_cadera_izq_flexion'];
        $params_2[':m_musc_cadera_izq_extencion'] = $_POST['m_musc_cadera_izq_extencion'];
        $params_2[':m_musc_cadera_izq_rota_exter'] = $_POST['m_musc_cadera_izq_rota_exter'];
        $params_2[':m_musc_cadera_izq_rota_inter'] = $_POST['m_musc_cadera_izq_rota_inter'];
        $params_2[':m_musc_cadera_izq_irradia'] = $_POST['m_musc_cadera_izq_irradia'];
        $params_2[':m_musc_cadera_izq_alt_masa'] = $_POST['m_musc_cadera_izq_alt_masa'];
        $params_2[':m_musc_rodilla_der_abduccion'] = $_POST['m_musc_rodilla_der_abduccion'];
        $params_2[':m_musc_rodilla_der_aduccion'] = $_POST['m_musc_rodilla_der_aduccion'];
        $params_2[':m_musc_rodilla_der_flexion'] = $_POST['m_musc_rodilla_der_flexion'];
        $params_2[':m_musc_rodilla_der_extencion'] = $_POST['m_musc_rodilla_der_extencion'];
        $params_2[':m_musc_rodilla_der_rota_exter'] = $_POST['m_musc_rodilla_der_rota_exter'];
        $params_2[':m_musc_rodilla_der_rota_inter'] = $_POST['m_musc_rodilla_der_rota_inter'];
        $params_2[':m_musc_rodilla_der_irradia'] = $_POST['m_musc_rodilla_der_irradia'];
        $params_2[':m_musc_rodilla_der_alt_masa'] = $_POST['m_musc_rodilla_der_alt_masa'];
        $params_2[':m_musc_rodilla_izq_abduccion'] = $_POST['m_musc_rodilla_izq_abduccion'];
        $params_2[':m_musc_rodilla_izq_aduccion'] = $_POST['m_musc_rodilla_izq_aduccion'];
        $params_2[':m_musc_rodilla_izq_flexion'] = $_POST['m_musc_rodilla_izq_flexion'];
        $params_2[':m_musc_rodilla_izq_extencion'] = $_POST['m_musc_rodilla_izq_extencion'];
        $params_2[':m_musc_rodilla_izq_rota_exter'] = $_POST['m_musc_rodilla_izq_rota_exter'];
        $params_2[':m_musc_rodilla_izq_rota_inter'] = $_POST['m_musc_rodilla_izq_rota_inter'];
        $params_2[':m_musc_rodilla_izq_irradia'] = $_POST['m_musc_rodilla_izq_irradia'];
        $params_2[':m_musc_rodilla_izq_alt_masa'] = $_POST['m_musc_rodilla_izq_alt_masa'];
        $params_2[':m_musc_tobillo_der_abduccion'] = $_POST['m_musc_tobillo_der_abduccion'];
        $params_2[':m_musc_tobillo_der_aduccion'] = $_POST['m_musc_tobillo_der_aduccion'];
        $params_2[':m_musc_tobillo_der_flexion'] = $_POST['m_musc_tobillo_der_flexion'];
        $params_2[':m_musc_tobillo_der_extencion'] = $_POST['m_musc_tobillo_der_extencion'];
        $params_2[':m_musc_tobillo_der_rota_exter'] = $_POST['m_musc_tobillo_der_rota_exter'];
        $params_2[':m_musc_tobillo_der_rota_inter'] = $_POST['m_musc_tobillo_der_rota_inter'];
        $params_2[':m_musc_tobillo_der_irradia'] = $_POST['m_musc_tobillo_der_irradia'];
        $params_2[':m_musc_tobillo_der_alt_masa'] = $_POST['m_musc_tobillo_der_alt_masa'];
        $params_2[':m_musc_tobillo_izq_abduccion'] = $_POST['m_musc_tobillo_izq_abduccion'];
        $params_2[':m_musc_tobillo_izq_aduccion'] = $_POST['m_musc_tobillo_izq_aduccion'];
        $params_2[':m_musc_tobillo_izq_flexion'] = $_POST['m_musc_tobillo_izq_flexion'];
        $params_2[':m_musc_tobillo_izq_extencion'] = $_POST['m_musc_tobillo_izq_extencion'];
        $params_2[':m_musc_tobillo_izq_rota_exter'] = $_POST['m_musc_tobillo_izq_rota_exter'];
        $params_2[':m_musc_tobillo_izq_rota_inter'] = $_POST['m_musc_tobillo_izq_rota_inter'];
        $params_2[':m_musc_tobillo_izq_irradia'] = $_POST['m_musc_tobillo_izq_irradia'];
        $params_2[':m_musc_tobillo_izq_alt_masa'] = $_POST['m_musc_tobillo_izq_alt_masa'];
        $params_2[':m_musc_colum_punto_ref'] = $_POST['m_musc_colum_punto_ref'];
        $params_2[':m_musc_colum_aptitud'] = $_POST['m_musc_colum_aptitud'];
        $params_2[':m_musc_colum_desc'] = $_POST['m_musc_colum_desc'];
        $params_2[':m_musc_diag_01'] = $_POST['m_musc_diag_01'];
        $params_2[':m_musc_conclu_01'] = $_POST['m_musc_conclu_01'];
        $params_2[':m_musc_recom_01'] = $_POST['m_musc_recom_01'];
        $params_2[':m_musc_diag_02'] = $_POST['m_musc_diag_02'];
        $params_2[':m_musc_conclu_02'] = $_POST['m_musc_conclu_02'];
        $params_2[':m_musc_recom_02'] = $_POST['m_musc_recom_02'];
        $params_2[':m_musc_diag_03'] = $_POST['m_musc_diag_03'];
        $params_2[':m_musc_conclu_03'] = $_POST['m_musc_conclu_03'];
        $params_2[':m_musc_recom_03'] = $_POST['m_musc_recom_03'];

        $q_2 = 'Update mod_medicina_musculo set
                    m_musc_flexi_ptos=:m_musc_flexi_ptos,
                    m_musc_flexi_obs=:m_musc_flexi_obs,
                    m_musc_cadera_ptos=:m_musc_cadera_ptos,
                    m_musc_cadera_obs=:m_musc_cadera_obs,
                    m_musc_muslo_ptos=:m_musc_muslo_ptos,
                    m_musc_muslo_obs=:m_musc_muslo_obs,
                    m_musc_abdom_ptos=:m_musc_abdom_ptos,
                    m_musc_abdom_obs=:m_musc_abdom_obs,
                    m_musc_abduc_180_ptos=:m_musc_abduc_180_ptos,
                    m_musc_abduc_180_dolor=:m_musc_abduc_180_dolor,
                    m_musc_abduc_80_ptos=:m_musc_abduc_80_ptos,
                    m_musc_abduc_80_dolor=:m_musc_abduc_80_dolor,
                    m_musc_rota_exter_ptos=:m_musc_rota_exter_ptos,
                    m_musc_rota_exter_dolor=:m_musc_rota_exter_dolor,
                    m_musc_rota_inter_ptos=:m_musc_rota_inter_ptos,
                    m_musc_rota_inter_dolor=:m_musc_rota_inter_dolor,
                    m_musc_ra_obs=:m_musc_ra_obs,
                    m_musc_aptitud=:m_musc_aptitud,
                    m_musc_col_cevical_desvia_lateral=:m_musc_col_cevical_desvia_lateral,
                    m_musc_col_cevical_desvia_antero=:m_musc_col_cevical_desvia_antero,
                    m_musc_col_cevical_palpa_apofisis=:m_musc_col_cevical_palpa_apofisis,
                    m_musc_col_cevical_palpa_contractura=:m_musc_col_cevical_palpa_contractura,
                    m_musc_col_dorsal_desvia_lateral=:m_musc_col_dorsal_desvia_lateral,
                    m_musc_col_dorsal_desvia_antero=:m_musc_col_dorsal_desvia_antero,
                    m_musc_col_dorsal_palpa_apofisis=:m_musc_col_dorsal_palpa_apofisis,
                    m_musc_col_dorsal_palpa_contractura=:m_musc_col_dorsal_palpa_contractura,
                    m_musc_col_lumbar_desvia_lateral=:m_musc_col_lumbar_desvia_lateral,
                    m_musc_col_lumbar_desvia_antero=:m_musc_col_lumbar_desvia_antero,
                    m_musc_col_lumbar_palpa_apofisis=:m_musc_col_lumbar_palpa_apofisis,
                    m_musc_col_lumbar_palpa_contractura=:m_musc_col_lumbar_palpa_contractura,
                    m_musc_col_cevical_flexion=:m_musc_col_cevical_flexion,
                    m_musc_col_cevical_exten=:m_musc_col_cevical_exten,
                    m_musc_col_cevical_lat_izq=:m_musc_col_cevical_lat_izq,
                    m_musc_col_cevical_lat_der=:m_musc_col_cevical_lat_der,
                    m_musc_col_cevical_rota_izq=:m_musc_col_cevical_rota_izq,
                    m_musc_col_cevical_rota_der=:m_musc_col_cevical_rota_der,
                    m_musc_col_cevical_irradia=:m_musc_col_cevical_irradia,
                    m_musc_col_cevical_alt_masa=:m_musc_col_cevical_alt_masa,
                    m_musc_col_dorsal_flexion=:m_musc_col_dorsal_flexion,
                    m_musc_col_dorsal_exten=:m_musc_col_dorsal_exten,
                    m_musc_col_dorsal_lat_izq=:m_musc_col_dorsal_lat_izq,
                    m_musc_col_dorsal_lat_der=:m_musc_col_dorsal_lat_der,
                    m_musc_col_dorsal_rota_izq=:m_musc_col_dorsal_rota_izq,
                    m_musc_col_dorsal_rota_der=:m_musc_col_dorsal_rota_der,
                    m_musc_col_dorsal_irradia=:m_musc_col_dorsal_irradia,
                    m_musc_col_dorsal_alt_masa=:m_musc_col_dorsal_alt_masa,
                    m_musc_col_lumbar_flexion=:m_musc_col_lumbar_flexion,
                    m_musc_col_lumbar_exten=:m_musc_col_lumbar_exten,
                    m_musc_col_lumbar_lat_izq=:m_musc_col_lumbar_lat_izq,
                    m_musc_col_lumbar_lat_der=:m_musc_col_lumbar_lat_der,
                    m_musc_col_lumbar_rota_izq=:m_musc_col_lumbar_rota_izq,
                    m_musc_col_lumbar_rota_der=:m_musc_col_lumbar_rota_der,
                    m_musc_col_lumbar_irradia=:m_musc_col_lumbar_irradia,
                    m_musc_col_lumbar_alt_masa=:m_musc_col_lumbar_alt_masa,
                    m_musc_hombro_der_abduccion=:m_musc_hombro_der_abduccion,
                    m_musc_hombro_der_aduccion=:m_musc_hombro_der_aduccion,
                    m_musc_hombro_der_flexion=:m_musc_hombro_der_flexion,
                    m_musc_hombro_der_extencion=:m_musc_hombro_der_extencion,
                    m_musc_hombro_der_rota_exter=:m_musc_hombro_der_rota_exter,
                    m_musc_hombro_der_rota_inter=:m_musc_hombro_der_rota_inter,
                    m_musc_hombro_der_irradia=:m_musc_hombro_der_irradia,
                    m_musc_hombro_der_alt_masa=:m_musc_hombro_der_alt_masa,
                    m_musc_hombro_izq_abduccion=:m_musc_hombro_izq_abduccion,
                    m_musc_hombro_izq_aduccion=:m_musc_hombro_izq_aduccion,
                    m_musc_hombro_izq_flexion=:m_musc_hombro_izq_flexion,
                    m_musc_hombro_izq_extencion=:m_musc_hombro_izq_extencion,
                    m_musc_hombro_izq_rota_exter=:m_musc_hombro_izq_rota_exter,
                    m_musc_hombro_izq_rota_inter=:m_musc_hombro_izq_rota_inter,
                    m_musc_hombro_izq_irradia=:m_musc_hombro_izq_irradia,
                    m_musc_hombro_izq_alt_masa=:m_musc_hombro_izq_alt_masa,
                    m_musc_codo_der_abduccion=:m_musc_codo_der_abduccion,
                    m_musc_codo_der_aduccion=:m_musc_codo_der_aduccion,
                    m_musc_codo_der_flexion=:m_musc_codo_der_flexion,
                    m_musc_codo_der_extencion=:m_musc_codo_der_extencion,
                    m_musc_codo_der_rota_exter=:m_musc_codo_der_rota_exter,
                    m_musc_codo_der_rota_inter=:m_musc_codo_der_rota_inter,
                    m_musc_codo_der_irradia=:m_musc_codo_der_irradia,
                    m_musc_codo_der_alt_masa=:m_musc_codo_der_alt_masa,
                    m_musc_codo_izq_abduccion=:m_musc_codo_izq_abduccion,
                    m_musc_codo_izq_aduccion=:m_musc_codo_izq_aduccion,
                    m_musc_codo_izq_flexion=:m_musc_codo_izq_flexion,
                    m_musc_codo_izq_extencion=:m_musc_codo_izq_extencion,
                    m_musc_codo_izq_rota_exter=:m_musc_codo_izq_rota_exter,
                    m_musc_codo_izq_rota_inter=:m_musc_codo_izq_rota_inter,
                    m_musc_codo_izq_irradia=:m_musc_codo_izq_irradia,
                    m_musc_codo_izq_alt_masa=:m_musc_codo_izq_alt_masa,
                    m_musc_muneca_der_abduccion=:m_musc_muneca_der_abduccion,
                    m_musc_muneca_der_aduccion=:m_musc_muneca_der_aduccion,
                    m_musc_muneca_der_flexion=:m_musc_muneca_der_flexion,
                    m_musc_muneca_der_extencion=:m_musc_muneca_der_extencion,
                    m_musc_muneca_der_rota_exter=:m_musc_muneca_der_rota_exter,
                    m_musc_muneca_der_rota_inter=:m_musc_muneca_der_rota_inter,
                    m_musc_muneca_der_irradia=:m_musc_muneca_der_irradia,
                    m_musc_muneca_der_alt_masa=:m_musc_muneca_der_alt_masa,
                    m_musc_muneca_izq_abduccion=:m_musc_muneca_izq_abduccion,
                    m_musc_muneca_izq_aduccion=:m_musc_muneca_izq_aduccion,
                    m_musc_muneca_izq_flexion=:m_musc_muneca_izq_flexion,
                    m_musc_muneca_izq_extencion=:m_musc_muneca_izq_extencion,
                    m_musc_muneca_izq_rota_exter=:m_musc_muneca_izq_rota_exter,
                    m_musc_muneca_izq_rota_inter=:m_musc_muneca_izq_rota_inter,
                    m_musc_muneca_izq_irradia=:m_musc_muneca_izq_irradia,
                    m_musc_muneca_izq_alt_masa=:m_musc_muneca_izq_alt_masa,
                    m_musc_mano_der_abduccion=:m_musc_mano_der_abduccion,
                    m_musc_mano_der_aduccion=:m_musc_mano_der_aduccion,
                    m_musc_mano_der_flexion=:m_musc_mano_der_flexion,
                    m_musc_mano_der_extencion=:m_musc_mano_der_extencion,
                    m_musc_mano_der_rota_exter=:m_musc_mano_der_rota_exter,
                    m_musc_mano_der_rota_inter=:m_musc_mano_der_rota_inter,
                    m_musc_mano_der_irradia=:m_musc_mano_der_irradia,
                    m_musc_mano_der_alt_masa=:m_musc_mano_der_alt_masa,
                    m_musc_mano_izq_abduccion=:m_musc_mano_izq_abduccion,
                    m_musc_mano_izq_aduccion=:m_musc_mano_izq_aduccion,
                    m_musc_mano_izq_flexion=:m_musc_mano_izq_flexion,
                    m_musc_mano_izq_extencion=:m_musc_mano_izq_extencion,
                    m_musc_mano_izq_rota_exter=:m_musc_mano_izq_rota_exter,
                    m_musc_mano_izq_rota_inter=:m_musc_mano_izq_rota_inter,
                    m_musc_mano_izq_irradia=:m_musc_mano_izq_irradia,
                    m_musc_mano_izq_alt_masa=:m_musc_mano_izq_alt_masa,
                    m_musc_cadera_der_abduccion=:m_musc_cadera_der_abduccion,
                    m_musc_cadera_der_aduccion=:m_musc_cadera_der_aduccion,
                    m_musc_cadera_der_flexion=:m_musc_cadera_der_flexion,
                    m_musc_cadera_der_extencion=:m_musc_cadera_der_extencion,
                    m_musc_cadera_der_rota_exter=:m_musc_cadera_der_rota_exter,
                    m_musc_cadera_der_rota_inter=:m_musc_cadera_der_rota_inter,
                    m_musc_cadera_der_irradia=:m_musc_cadera_der_irradia,
                    m_musc_cadera_der_alt_masa=:m_musc_cadera_der_alt_masa,
                    m_musc_cadera_izq_abduccion=:m_musc_cadera_izq_abduccion,
                    m_musc_cadera_izq_aduccion=:m_musc_cadera_izq_aduccion,
                    m_musc_cadera_izq_flexion=:m_musc_cadera_izq_flexion,
                    m_musc_cadera_izq_extencion=:m_musc_cadera_izq_extencion,
                    m_musc_cadera_izq_rota_exter=:m_musc_cadera_izq_rota_exter,
                    m_musc_cadera_izq_rota_inter=:m_musc_cadera_izq_rota_inter,
                    m_musc_cadera_izq_irradia=:m_musc_cadera_izq_irradia,
                    m_musc_cadera_izq_alt_masa=:m_musc_cadera_izq_alt_masa,
                    m_musc_rodilla_der_abduccion=:m_musc_rodilla_der_abduccion,
                    m_musc_rodilla_der_aduccion=:m_musc_rodilla_der_aduccion,
                    m_musc_rodilla_der_flexion=:m_musc_rodilla_der_flexion,
                    m_musc_rodilla_der_extencion=:m_musc_rodilla_der_extencion,
                    m_musc_rodilla_der_rota_exter=:m_musc_rodilla_der_rota_exter,
                    m_musc_rodilla_der_rota_inter=:m_musc_rodilla_der_rota_inter,
                    m_musc_rodilla_der_irradia=:m_musc_rodilla_der_irradia,
                    m_musc_rodilla_der_alt_masa=:m_musc_rodilla_der_alt_masa,
                    m_musc_rodilla_izq_abduccion=:m_musc_rodilla_izq_abduccion,
                    m_musc_rodilla_izq_aduccion=:m_musc_rodilla_izq_aduccion,
                    m_musc_rodilla_izq_flexion=:m_musc_rodilla_izq_flexion,
                    m_musc_rodilla_izq_extencion=:m_musc_rodilla_izq_extencion,
                    m_musc_rodilla_izq_rota_exter=:m_musc_rodilla_izq_rota_exter,
                    m_musc_rodilla_izq_rota_inter=:m_musc_rodilla_izq_rota_inter,
                    m_musc_rodilla_izq_irradia=:m_musc_rodilla_izq_irradia,
                    m_musc_rodilla_izq_alt_masa=:m_musc_rodilla_izq_alt_masa,
                    m_musc_tobillo_der_abduccion=:m_musc_tobillo_der_abduccion,
                    m_musc_tobillo_der_aduccion=:m_musc_tobillo_der_aduccion,
                    m_musc_tobillo_der_flexion=:m_musc_tobillo_der_flexion,
                    m_musc_tobillo_der_extencion=:m_musc_tobillo_der_extencion,
                    m_musc_tobillo_der_rota_exter=:m_musc_tobillo_der_rota_exter,
                    m_musc_tobillo_der_rota_inter=:m_musc_tobillo_der_rota_inter,
                    m_musc_tobillo_der_irradia=:m_musc_tobillo_der_irradia,
                    m_musc_tobillo_der_alt_masa=:m_musc_tobillo_der_alt_masa,
                    m_musc_tobillo_izq_abduccion=:m_musc_tobillo_izq_abduccion,
                    m_musc_tobillo_izq_aduccion=:m_musc_tobillo_izq_aduccion,
                    m_musc_tobillo_izq_flexion=:m_musc_tobillo_izq_flexion,
                    m_musc_tobillo_izq_extencion=:m_musc_tobillo_izq_extencion,
                    m_musc_tobillo_izq_rota_exter=:m_musc_tobillo_izq_rota_exter,
                    m_musc_tobillo_izq_rota_inter=:m_musc_tobillo_izq_rota_inter,
                    m_musc_tobillo_izq_irradia=:m_musc_tobillo_izq_irradia,
                    m_musc_tobillo_izq_alt_masa=:m_musc_tobillo_izq_alt_masa,
                    m_musc_colum_punto_ref=:m_musc_colum_punto_ref,
                    m_musc_colum_aptitud=:m_musc_colum_aptitud,
                    m_musc_colum_desc=:m_musc_colum_desc,
                    m_musc_diag_01=:m_musc_diag_01,
                    m_musc_conclu_01=:m_musc_conclu_01,
                    m_musc_recom_01=:m_musc_recom_01,
                    m_musc_diag_02=:m_musc_diag_02,
                    m_musc_conclu_02=:m_musc_conclu_02,
                    m_musc_recom_02=:m_musc_recom_02,
                    m_musc_diag_03=:m_musc_diag_03,
                    m_musc_conclu_03=:m_musc_conclu_03,
                    m_musc_recom_03=:m_musc_recom_03        
                where
                m_musc_adm=:adm;';

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

    public function carga_musculo_pdf($adm)
    {
        $query = "SELECT *
            FROM mod_medicina_musculo
            where m_musc_adm='$adm';";
        return $this->sql($query);
    }
}

//$sesion = new model();
//m_med_fech_val
//val_fech_fin
//val_tiempo
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
