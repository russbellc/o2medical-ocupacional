<?php

class model extends core {

    public function list_espiro() {
        $limit = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $columna = isset($_POST['columna']) ? $_POST['columna'] : NULL;
//        $sede = $this->user->con_sedid;//-{$this->user->acceso}-{$this->user->empresas}
        $query = isset($_POST['query']) ? sprintf($_POST['query']) : NULL;
        $sql1 = ("SELECT
                    adm_id as adm,
                    if((esp_st)=1,1,0) as st_id,
                    tfi_desc,emp_desc, pac_ndoc,
                    concat(pac_appat,' ',pac_apmat,' ',pac_nombres)as nombre,pac_sexo
                    ,esp_fech as fecha,esp_usu as usu,esp_id,tri_talla,tri_peso,tri_img
                    ,if((esp_pdf)=1,1,0) pdf
                    FROM admision
                    inner join paciente on adm_pacid=pac_id
                    inner join pack on adm_pkid=pk_id
                    left join empresa on pk_empid=emp_id
                    left join tficha on adm_tfiid=tfi_id
                    inner join dpack on dpk_pkid=pk_id
                    left join espirometria on esp_adm=adm_id
                    left join triaje on tri_admid=adm_id
                    where
                    dpk_exid=40 ");
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
                WHERE dpk_exid IN (40) and adm_fechc BETWEEN '$inicio 00:00:00' AND '$final 23:59:59' $emp
                group by adm_id order by adm_id;";
        $q = $this->sql($sql);
        return $q;
    }

    public function st_busca_diag() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT concat(cie4_id,' - ',cie4_desc) mmg_espiro_diag
                FROM cie4
                where
                concat(cie4_id, cie4_cie3id, cie4_desc) like'%$query%'
                order by cie4_cie3id;");
        return $sql;
    }

    public function st_busca_conclu_5() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_conclu_5 FROM mmg_espiro
                            where
                            mmg_espiro_conclu_5 like '%$query%'
                            group by mmg_espiro_conclu_5");
        return $sql;
    }

    public function st_busca_recome_5() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_recome_5 FROM mmg_espiro
                            where
                            mmg_espiro_recome_5 like '%$query%'
                            group by mmg_espiro_recome_5");
        return $sql;
    }

    public function st_busca_conclu_4() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_conclu_4 FROM mmg_espiro
                            where
                            mmg_espiro_conclu_4 like '%$query%'
                            group by mmg_espiro_conclu_4");
        return $sql;
    }

    public function st_busca_recome_4() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_recome_4 FROM mmg_espiro
                            where
                            mmg_espiro_recome_4 like '%$query%'
                            group by mmg_espiro_recome_4");
        return $sql;
    }

    public function st_busca_conclu_3() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_conclu_3 FROM mmg_espiro
                            where
                            mmg_espiro_conclu_3 like '%$query%'
                            group by mmg_espiro_conclu_3");
        return $sql;
    }

    public function st_busca_recome_3() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_recome_3 FROM mmg_espiro
                            where
                            mmg_espiro_recome_3 like '%$query%'
                            group by mmg_espiro_recome_3");
        return $sql;
    }

    public function st_busca_conclu_2() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_conclu_2 FROM mmg_espiro
                            where
                            mmg_espiro_conclu_2 like '%$query%'
                            group by mmg_espiro_conclu_2");
        return $sql;
    }

    public function st_busca_recome_2() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_recome_2 FROM mmg_espiro
                            where
                            mmg_espiro_recome_2 like '%$query%'
                            group by mmg_espiro_recome_2");
        return $sql;
    }

    public function st_busca_conclu_1() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_conclu_1 FROM mmg_espiro
                            where
                            mmg_espiro_conclu_1 like '%$query%'
                            group by mmg_espiro_conclu_1");
        return $sql;
    }

    public function st_busca_recome_1() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT mmg_espiro_recome_1 FROM mmg_espiro
                            where
                            mmg_espiro_recome_1 like '%$query%'
                            group by mmg_espiro_recome_1");
        return $sql;
    }

    public function load_mmg_espiro() {
        $adm = $_POST['adm'];
        $query = "SELECT
            mmg_espiro_FVC,
            mmg_espiro_FEV1,
            mmg_espiro_FEV1_FVC,
            mmg_espiro_PEF,
            mmg_espiro_FEF2575,
            mmg_espiro_diag_1,
            mmg_espiro_conclu_1,
            mmg_espiro_recome_1,
            mmg_espiro_diag_2,
            mmg_espiro_conclu_2,
            mmg_espiro_recome_2,
            mmg_espiro_diag_3,
            mmg_espiro_conclu_3,
            mmg_espiro_recome_3,
            mmg_espiro_diag_4,
            mmg_espiro_conclu_4,
            mmg_espiro_recome_4,
            mmg_espiro_diag_5,
            mmg_espiro_conclu_5,
            mmg_espiro_recome_5
            FROM mmg_espiro
            where
            mmg_espiro_adm=$adm;";
        $q = $this->sql($query);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function diag_espiro() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT
                            UPPER(esp_diag) as esp_diag
                            FROM espirometria
                            where
                            esp_diag like '%$query%'
                            group by esp_diag;");
        return $sql;
    }

    public function list_cie10() {
        $query = isset($_POST['query']) ? $_POST['query'] : NULL;
        $sql = $this->sql("SELECT cie4_id, cie4_cie3id, cie4_desc FROM cie4 where concat(cie4_id, cie4_cie3id, cie4_desc)like'%$query%';");
        return $sql;
    }

    public function load_espiro() {
        $params = array();
        $params[':adm'] = isset($_POST['adm']) ? $_POST['adm'] : NULL;
        $params[':sede'] = $this->user->con_sedid;
        $query = 'SELECT  esp_vital, esp_recom,esp_diag,
                esp_cie10,(SELECT cie4_desc FROM cie4 where cie4_id=esp_cie10) as esp_cie1002,
                esp_fum, (SELECT cod_desc FROM sys_condicionm where esp_fum=con_ids) as esp_fum02
                FROM espirometria where esp_adm=:adm and esp_sede=:sede;';
        $q = $this->sql($query, $params);
        return array('success' => true, 'data' => $q->data[0]);
    }

    public function list_fuma() {
        return $this->sql("SELECT con_ids,cod_desc FROM sys_condicionm
                            where con_subcon ='espiro_fuma'and con_st=1 order by cod_ord");
    }

    public function save() {
        $params = array();
        $params[':esp_adm'] = $_POST['adm'];
        $params[':esp_sede'] = $this->user->con_sedid;
        $params[':esp_usu'] = $this->user->us_id;
        $params[':esp_st'] = '1';
        $params[':esp_fum'] = (isset($_POST['esp_fum']) && !empty($_POST['esp_fum'])) ? $_POST['esp_fum'] : null;
        $params[':esp_vital'] = $_POST['esp_vital'];
        $params[':esp_recom'] = $_POST['esp_recom'];
        $params[':esp_cie10'] = (isset($_POST['esp_cie10']) && !empty($_POST['esp_cie10'])) ? $_POST['esp_cie10'] : null;
        $params[':esp_diag'] = $_POST['esp_diag'];
//        print_r($params);

        $this->begin();
        $adm = $_POST['adm'];
        $verifica = $this->sql("SELECT esp_st,concat(usu_nombres,' ',usu_appat,' ',usu_apmat) usuario FROM espirometria inner join sys_usuario on usu_id=esp_usu where esp_adm=$adm;");

        if ($verifica->total > 0) {
            $this->rollback();
            return array('success' => false, "error" => 'Paciente ya fue registrado por ' . $verifica->data[0]->usuario);
        } else {
            $q = "INSERT INTO espirometria VALUES
              (NULL,
                :esp_adm,
                :esp_sede,
                :esp_usu,
                now(),
                :esp_st,
                :esp_fum,
                :esp_vital,
                :esp_recom,
                :esp_cie10,
                :esp_diag,
                null);";
            $sql = $this->sql($q, $params);
            if ($sql->success) {
                $mmg_espiro_adm = $_POST['adm'];
                $mmg_espiro_sede = $this->user->con_sedid;
                $mmg_espiro_usu = $this->user->us_id;
                $mmg_espiro_st = '1';

                $mmg_espiro_FVC = $_POST['mmg_espiro_FVC'];
                $mmg_espiro_FEV1 = $_POST['mmg_espiro_FEV1'];
                $mmg_espiro_FEV1_FVC = $_POST['mmg_espiro_FEV1_FVC'];
                $mmg_espiro_PEF = $_POST['mmg_espiro_PEF'];
                $mmg_espiro_FEF2575 = $_POST['mmg_espiro_FEF2575'];
                $mmg_espiro_diag_1 = $_POST['mmg_espiro_diag_1'];
                $mmg_espiro_conclu_1 = $_POST['mmg_espiro_conclu_1'];
                $mmg_espiro_recome_1 = $_POST['mmg_espiro_recome_1'];
                $mmg_espiro_diag_2 = $_POST['mmg_espiro_diag_2'];
                $mmg_espiro_conclu_2 = $_POST['mmg_espiro_conclu_2'];
                $mmg_espiro_recome_2 = $_POST['mmg_espiro_recome_2'];
                $mmg_espiro_diag_3 = $_POST['mmg_espiro_diag_3'];
                $mmg_espiro_conclu_3 = $_POST['mmg_espiro_conclu_3'];
                $mmg_espiro_recome_3 = $_POST['mmg_espiro_recome_3'];
                $mmg_espiro_diag_4 = $_POST['mmg_espiro_diag_4'];
                $mmg_espiro_conclu_4 = $_POST['mmg_espiro_conclu_4'];
                $mmg_espiro_recome_4 = $_POST['mmg_espiro_recome_4'];
                $mmg_espiro_diag_5 = $_POST['mmg_espiro_diag_5'];
                $mmg_espiro_conclu_5 = $_POST['mmg_espiro_conclu_5'];
                $mmg_espiro_recome_5 = $_POST['mmg_espiro_recome_5'];
                $q1 = "INSERT INTO mmg_espiro VALUES
                (NULL,
                '$mmg_espiro_adm',
                '$mmg_espiro_sede',
                '$mmg_espiro_usu',
                now(),
                '$mmg_espiro_st',
                '$mmg_espiro_FVC',
                '$mmg_espiro_FEV1',
                '$mmg_espiro_FEV1_FVC',
                '$mmg_espiro_PEF',
                '$mmg_espiro_FEF2575',
                '$mmg_espiro_diag_1',
                '$mmg_espiro_conclu_1',
                '$mmg_espiro_recome_1',
                '$mmg_espiro_diag_2',
                '$mmg_espiro_conclu_2',
                '$mmg_espiro_recome_2',
                '$mmg_espiro_diag_3',
                '$mmg_espiro_conclu_3',
                '$mmg_espiro_recome_3',
                '$mmg_espiro_diag_4',
                '$mmg_espiro_conclu_4',
                '$mmg_espiro_recome_4',
                '$mmg_espiro_diag_5',
                '$mmg_espiro_conclu_5',
                '$mmg_espiro_recome_5');";
                $sql1 = $this->sql($q1);
                if ($sql1->success) {
                    $this->commit();
                    return $sql1;
                } else {
                    $this->rollback();
                }
            } else {
                $this->rollback();
            }
        }
    }

    public function update() {
        $params = array();
        $params[':esp_id'] = $_POST['esp_id']; //
        $params[':esp_adm'] = $_POST['adm']; //esp_id
        $params[':esp_usu'] = $this->user->us_id;
        $params[':esp_fum'] = (isset($_POST['esp_fum']) && !empty($_POST['esp_fum'])) ? $_POST['esp_fum'] : null;
        $params[':esp_vital'] = $_POST['esp_vital'];
        $params[':esp_recom'] = $_POST['esp_recom'];
        $params[':esp_cie10'] = (isset($_POST['esp_cie10']) && !empty($_POST['esp_cie10'])) ? $_POST['esp_cie10'] : null;
        $params[':esp_diag'] = $_POST['esp_diag'];
        $this->begin();
        $q = 'UPDATE espirometria SET
                    esp_usu=:esp_usu,
                    esp_fech=now(),
                    esp_fum=:esp_fum,
                    esp_vital=:esp_vital,
                    esp_recom=:esp_recom,
                    esp_cie10=:esp_cie10,
                    esp_diag=:esp_diag 
                WHERE esp_adm = :esp_adm and esp_id=:esp_id';
        $sql = $this->sql($q, $params);
        if ($sql->success) {
            $mmg_espiro_adm = $_POST['adm'];
            $mmg_espiro_usu = $this->user->us_id;

            $mmg_espiro_FVC = $_POST['mmg_espiro_FVC'];
            $mmg_espiro_FEV1 = $_POST['mmg_espiro_FEV1'];
            $mmg_espiro_FEV1_FVC = $_POST['mmg_espiro_FEV1_FVC'];
            $mmg_espiro_PEF = $_POST['mmg_espiro_PEF'];
            $mmg_espiro_FEF2575 = $_POST['mmg_espiro_FEF2575'];
            $mmg_espiro_diag_1 = $_POST['mmg_espiro_diag_1'];
            $mmg_espiro_conclu_1 = $_POST['mmg_espiro_conclu_1'];
            $mmg_espiro_recome_1 = $_POST['mmg_espiro_recome_1'];
            $mmg_espiro_diag_2 = $_POST['mmg_espiro_diag_2'];
            $mmg_espiro_conclu_2 = $_POST['mmg_espiro_conclu_2'];
            $mmg_espiro_recome_2 = $_POST['mmg_espiro_recome_2'];
            $mmg_espiro_diag_3 = $_POST['mmg_espiro_diag_3'];
            $mmg_espiro_conclu_3 = $_POST['mmg_espiro_conclu_3'];
            $mmg_espiro_recome_3 = $_POST['mmg_espiro_recome_3'];
            $mmg_espiro_diag_4 = $_POST['mmg_espiro_diag_4'];
            $mmg_espiro_conclu_4 = $_POST['mmg_espiro_conclu_4'];
            $mmg_espiro_recome_4 = $_POST['mmg_espiro_recome_4'];
            $mmg_espiro_diag_5 = $_POST['mmg_espiro_diag_5'];
            $mmg_espiro_conclu_5 = $_POST['mmg_espiro_conclu_5'];
            $mmg_espiro_recome_5 = $_POST['mmg_espiro_recome_5'];
            $q1 = "UPDATE mmg_espiro SET
                mmg_espiro_usu='$mmg_espiro_usu',
                mmg_espiro_fech=now(),
                mmg_espiro_FVC='$mmg_espiro_FVC',
                mmg_espiro_FEV1='$mmg_espiro_FEV1',
                mmg_espiro_FEV1_FVC='$mmg_espiro_FEV1_FVC',
                mmg_espiro_PEF='$mmg_espiro_PEF',
                mmg_espiro_FEF2575='$mmg_espiro_FEF2575',
                mmg_espiro_diag_1='$mmg_espiro_diag_1',
                mmg_espiro_conclu_1='$mmg_espiro_conclu_1',
                mmg_espiro_recome_1='$mmg_espiro_recome_1',
                mmg_espiro_diag_2='$mmg_espiro_diag_2',
                mmg_espiro_conclu_2='$mmg_espiro_conclu_2',
                mmg_espiro_recome_2='$mmg_espiro_recome_2',
                mmg_espiro_diag_3='$mmg_espiro_diag_3',
                mmg_espiro_conclu_3='$mmg_espiro_conclu_3',
                mmg_espiro_recome_3='$mmg_espiro_recome_3',
                mmg_espiro_diag_4='$mmg_espiro_diag_4',
                mmg_espiro_conclu_4='$mmg_espiro_conclu_4',
                mmg_espiro_recome_4='$mmg_espiro_recome_4',
                mmg_espiro_diag_5='$mmg_espiro_diag_5',
                mmg_espiro_conclu_5='$mmg_espiro_conclu_5',
                mmg_espiro_recome_5='$mmg_espiro_recome_5'
              where mmg_espiro_adm=$mmg_espiro_adm;";
            $sql1 = $this->sql($q1, $params);
            if ($sql1->success) {
                $this->commit();
                return $sql1;
            } else {
                $this->rollback();
            }
        } else {
            $this->rollback();
        }
    }

    public function rpt($adm) {
        $sql = $this->sql("
SELECT oftalmologia.*,paciente.*,emp_desc,adm_act,adm_fechc,c1.cie4_desc  c1,c2.cie4_desc  c2,c3.cie4_desc  c3 , TIMESTAMPDIFF(YEAR,pac_nacfec,CURRENT_DATE) as edad FROM oftalmologia
inner join admision on adm_id=ofta_adm
inner join paciente on adm_pacid=pac_id
inner join pack on adm_pkid=pk_id
left join empresa on pk_empid=emp_id
left join cie4 c1 on ofta_cie1=c1.cie4_id
left join cie4 c2 on ofta_cie2=c2.cie4_id
left join cie4 c3 on ofta_cie3=c3.cie4_id
where ofta_adm=$adm");

        $monn = $this->sql("SELECT * FROM sys_condicionm where con_subcon like'%oftalmo%';");
        foreach ($monn->data as $e => $value) {
//            if($sql->data->ofta_usa==$value->con_ids){
//                $sql->data[0]->ofta_usa = $value->cod_desc;
//            }
            if ($sql->data[0]->ofta_colo == $value->con_ids) {
                $sql->data[0]->ofta_colo = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_usa == $value->con_ids) {
                $sql->data[0]->ofta_usa = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_extr == $value->con_ids) {
                $sql->data[0]->ofta_extr = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_intr == $value->con_ids) {
                $sql->data[0]->ofta_intr = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_camp == $value->con_ids) {
                $sql->data[0]->ofta_camp = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_anex == $value->con_ids) {
                $sql->data[0]->ofta_anex = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_slejos_izq == $value->con_ids) {
                $sql->data[0]->ofta_slejos_izq = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_slejos_der == $value->con_ids) {
                $sql->data[0]->ofta_slejos_der = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_scerca_izq == $value->con_ids) {
                $sql->data[0]->ofta_scerca_izq = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_scerca_der == $value->con_ids) {
                $sql->data[0]->ofta_scerca_der = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_clejos_izq == $value->con_ids) {
                $sql->data[0]->ofta_clejos_izq = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_clejos_der == $value->con_ids) {
                $sql->data[0]->ofta_clejos_der = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_ccerca_izq == $value->con_ids) {
                $sql->data[0]->ofta_ccerca_izq = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_ccerca_der == $value->con_ids) {
                $sql->data[0]->ofta_ccerca_der = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_elejos_izq == $value->con_ids) {
                $sql->data[0]->ofta_elejos_izq = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_elejos_der == $value->con_ids) {
                $sql->data[0]->ofta_elejos_der = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_ecerca_izq == $value->con_ids) {
                $sql->data[0]->ofta_ecerca_izq = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_ecerca_der == $value->con_ids) {
                $sql->data[0]->ofta_ecerca_der = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_refl == $value->con_ids) {
                $sql->data[0]->ofta_refl = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_fond == $value->con_ids) {
                $sql->data[0]->ofta_fond = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_tono == $value->con_ids) {
                $sql->data[0]->ofta_tono = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_oj_izq == $value->con_ids) {
                $sql->data[0]->ofta_oj_izq = $value->cod_desc;
            }
            if ($sql->data[0]->ofta_oj_der == $value->con_ids) {
                $sql->data[0]->ofta_oj_der = $value->cod_desc;
            }
        }
//ofta_id, ofta_adm, ofta_sede, ofta_usu, ofta_fech, ofta_st, ofta_usa, ofta_pico, ofta_desl, ofta_sensa, ofta_otro, ofta_visi, ofta_cefa, ofta_quem,
// ofta_comp, ofta_anex, ofta_polo, ofta_extr, ofta_intr, ofta_colo, ofta_camp, ofta_cie1, ofta_cie2, ofta_cie3, ofta_recm, ofta_slejos_izq, 
// ofta_slejos_der, ofta_scerca_izq, ofta_scerca_der, ofta_clejos_izq, ofta_clejos_der, ofta_ccerca_izq, ofta_ccerca_der, ofta_elejos_izq, 
// ofta_elejos_der, ofta_ecerca_izq, ofta_ecerca_der, ofta_refl, ofta_movi, ofta_fond, ofta_fon_obs, ofta_tono, ofta_oj_izq, ofta_oj_der, ofta_obs
        return $sql;
    }

    public function municip($adm) {
        $q = "SELECT 
                    FROM admision
                    where
                    adm_id=$adm
                    group by adm_id order by adm_id DESC";
        return $this->sql($q);
    }

    public function load_pdf() {
        $params = array();
        $params[':adm_id'] = $_POST['adm_id'];
        $q = 'UPDATE espirometria SET esp_pdf=1 WHERE esp_adm=:adm_id;';
        return $this->sql($q, $params);
    }

}

//$sesion = new model();//-{$this->user->acceso}-{$this->user->empresas}
//echo json_encode($sesion->save());
//http://localhost/ocupacional/system/loader.php?sys_acction=sys_loadmodel&sys_modname=mod_hruta
?>